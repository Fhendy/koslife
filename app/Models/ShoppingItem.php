<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingItem extends Model
{
    use HasFactory;

    const CATEGORIES = [
        'groceries' => 'Sembako',
        'hygiene' => 'Kebersihan',
        'drinks' => 'Minuman',
        'others' => 'Lainnya'
    ];

    protected $fillable = [
        'user_id', 'name', 'category', 'stock_quantity',
        'min_stock', 'is_checked', 'estimated_price'
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
        'min_stock' => 'integer',
        'is_checked' => 'boolean',
        'estimated_price' => 'decimal:2'
    ];

    // ========== RELATIONS ==========
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== SCOPES ==========
    public function scopeChecked($query)
    {
        return $query->where('is_checked', true);
    }

    public function scopeUnchecked($query)
    {
        return $query->where('is_checked', false);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock');
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // ========== HELPERS ==========
    public static function getCategories()
    {
        return self::CATEGORIES;
    }

    public function getCategoryLabel()
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getCategoryIcon()
    {
        return match($this->category) {
            'groceries' => '🛒',
            'hygiene' => '🧴',
            'drinks' => '🥤',
            'others' => '📦',
            default => '📦'
        };
    }

    public function isLowStock()
    {
        return $this->stock_quantity <= $this->min_stock;
    }

    public function getStockStatus()
    {
        if ($this->stock_quantity <= 0) return 'habis';
        if ($this->isLowStock()) return 'menipis';
        return 'tersedia';
    }

    public function getStockStatusColor()
    {
        return match($this->getStockStatus()) {
            'habis' => 'red',
            'menipis' => 'yellow',
            'tersedia' => 'green',
            default => 'gray'
        };
    }
}