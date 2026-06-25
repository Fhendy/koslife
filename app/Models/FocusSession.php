<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FocusSession extends Model
{
    use HasFactory;

    const SESSION_TYPES = [
        'study' => 'Belajar',
        'pkl' => 'PKL',
        'deep_work' => 'Deep Work',
        'custom' => 'Custom'
    ];

    const PRESET_TIMERS = [
        'study' => ['focus' => 25, 'break' => 5],
        'pkl' => ['focus' => 50, 'break' => 10],
        'deep_work' => ['focus' => 90, 'break' => 15],
        'custom' => ['focus' => 25, 'break' => 5],
    ];

    protected $fillable = [
        'user_id', 'task', 'duration', 'session_type',
        'status', 'started_at', 'ended_at'
    ];

    protected $casts = [
        'duration' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime'
    ];

    // ========== RELATIONS ==========
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== SCOPES ==========
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('started_at', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('started_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    // ========== HELPERS ==========
    public static function getSessionTypes()
    {
        return self::SESSION_TYPES;
    }

    public static function getPresetTimers()
    {
        return self::PRESET_TIMERS;
    }

    public function getSessionTypeLabel()
    {
        return self::SESSION_TYPES[$this->session_type] ?? $this->session_type;
    }

    public function getDurationFormatted()
    {
        $minutes = $this->duration;
        if ($minutes < 60) {
            return $minutes . 'm';
        }
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        if ($mins > 0) {
            return $hours . 'j ' . $mins . 'm';
        }
        return $hours . 'j';
    }

    public function getIcon()
    {
        return match($this->session_type) {
            'study' => '📚',
            'pkl' => '💼',
            'deep_work' => '🧠',
            default => '⏱️'
        };
    }
}