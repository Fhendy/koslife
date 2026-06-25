<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'habit_id', 'log_date', 'is_completed'
    ];

    protected $casts = [
        'log_date' => 'date',
        'is_completed' => 'boolean'
    ];

    // ========== RELATIONS ==========
    public function habit()
    {
        return $this->belongsTo(Habit::class);
    }

    // ========== SCOPES ==========
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('log_date', $date);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('log_date', now()->month)
                     ->whereYear('log_date', now()->year);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeIncomplete($query)
    {
        return $query->where('is_completed', false);
    }
}