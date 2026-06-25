<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealBudget extends Model
{
    use HasFactory;

    const MEAL_TYPES = [
        'breakfast' => 'Sarapan',
        'lunch' => 'Makan Siang',
        'dinner' => 'Makan Malam',
        'snack' => 'Camilan'
    ];

    protected $fillable = [
        'user_id', 'meal_date', 'meal_type', 'amount', 'description'
    ];

    protected $casts = [
        'meal_date' => 'date',
        'amount' => 'decimal:2'
    ];

    // ========== RELATIONS ==========
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== SCOPES ==========
    public function scopeToday($query)
    {
        return $query->whereDate('meal_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('meal_date', now()->month)
                     ->whereYear('meal_date', now()->year);
    }

    public function scopeType($query, $type)
    {
        return $query->where('meal_type', $type);
    }

    // ========== HELPERS ==========
    public static function getMealTypes()
    {
        return self::MEAL_TYPES;
    }

    public function getMealTypeLabel()
    {
        return self::MEAL_TYPES[$this->meal_type] ?? $this->meal_type;
    }

    public function getMealTypeIcon()
    {
        return match($this->meal_type) {
            'breakfast' => '🌅',
            'lunch' => '☀️',
            'dinner' => '🌙',
            'snack' => '🍿',
            default => '🍽️'
        };
    }
}