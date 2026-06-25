<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'icon', 'target_frequency',
        'streak', 'best_streak', 'is_active'
    ];

    protected $casts = [
        'streak' => 'integer',
        'best_streak' => 'integer',
        'is_active' => 'boolean'
    ];

    // ========== RELATIONS ==========
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(HabitLog::class);
    }

    public function todayLog()
    {
        return $this->hasOne(HabitLog::class)
                    ->whereDate('log_date', today());
    }

    public function thisMonthLogs()
    {
        return $this->logs()
                    ->whereMonth('log_date', now()->month)
                    ->whereYear('log_date', now()->year);
    }

    // ========== SCOPES ==========
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // ========== HELPERS ==========
    public function isLoggedToday()
    {
        return $this->logs()
                    ->whereDate('log_date', today())
                    ->exists();
    }

    public function isCompletedToday()
    {
        return $this->logs()
                    ->whereDate('log_date', today())
                    ->where('is_completed', true)
                    ->exists();
    }

    public function updateStreak()
    {
        $today = now()->startOfDay();
        $streak = 0;
        $date = $today->copy();

        while (true) {
            $hasLog = $this->logs()
                ->whereDate('log_date', $date)
                ->where('is_completed', true)
                ->exists();

            if (!$hasLog) {
                break;
            }

            $streak++;
            $date->subDay();
        }

        $this->streak = $streak;
        
        if ($streak > $this->best_streak) {
            $this->best_streak = $streak;
        }
        
        $this->save();
        
        return $streak;
    }

    public function getCompletionRate($days = 30)
    {
        $start = now()->subDays($days);
        $logs = $this->logs()
            ->whereBetween('log_date', [$start, now()])
            ->get();
            
        $total = $logs->count();
        $completed = $logs->where('is_completed', true)->count();
        
        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }

    public function getStreakStatus()
    {
        if ($this->streak === 0) return 'Belum mulai';
        if ($this->streak < 3) return 'Baru mulai 🔥';
        if ($this->streak < 7) return 'Mulai konsisten 🔥🔥';
        if ($this->streak < 14) return 'Konsisten! 🔥🔥🔥';
        if ($this->streak < 30) return 'Sangat konsisten! 💪';
        return 'Luar biasa! 🏆';
    }
}