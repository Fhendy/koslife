<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    const CATEGORIES = [
        'school' => 'Sekolah',
        'pkl' => 'PKL',
        'finance' => 'Keuangan',
        'personal' => 'Pribadi',
        'idea' => 'Ide'
    ];

    const COLORS = [
        '#FEF3C7' => 'Kuning',
        '#DBEAFE' => 'Biru',
        '#D1FAE5' => 'Hijau',
        '#FCE4EC' => 'Merah Muda',
        '#F3E8FF' => 'Ungu',
        '#FFFFFF' => 'Putih'
    ];

    protected $fillable = [
        'user_id', 'title', 'content', 'category',
        'is_pinned', 'color'
    ];

    protected $casts = [
        'is_pinned' => 'boolean'
    ];

    // ========== RELATIONS ==========
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== SCOPES ==========
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'LIKE', "%{$search}%")
                     ->orWhere('content', 'LIKE', "%{$search}%");
    }

    // ========== HELPERS ==========
    public static function getCategories()
    {
        return self::CATEGORIES;
    }

    public static function getColors()
    {
        return self::COLORS;
    }

    public function getCategoryLabel()
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getCategoryIcon()
    {
        return match($this->category) {
            'school' => '📚',
            'pkl' => '💼',
            'finance' => '💰',
            'personal' => '👤',
            'idea' => '💡',
            default => '📝'
        };
    }

    public function getExcerpt($length = 100)
    {
        return strlen($this->content) > $length 
            ? substr($this->content, 0, $length) . '...' 
            : $this->content;
    }
}