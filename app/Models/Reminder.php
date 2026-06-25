<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    const TYPES = [
        'task' => 'Deadline Tugas',
        'bill' => 'Tagihan',
        'schedule' => 'Jadwal',
        'habit' => 'Habit',
        'custom' => 'Custom'
    ];

    protected $fillable = [
        'user_id', 'title', 'description', 'type',
        'reminder_time', 'is_notified'
    ];

    protected $casts = [
        'reminder_time' => 'datetime',
        'is_notified' => 'boolean'
    ];

    // ========== RELATIONS ==========
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== SCOPES ==========
    public function scopeToday($query)
    {
        return $query->whereDate('reminder_time', today());
    }

    public function scopeUpcoming($query, $hours = 24)
    {
        return $query->whereBetween('reminder_time', [
            now(),
            now()->addHours($hours)
        ]);
    }

    public function scopeNotNotified($query)
    {
        return $query->where('is_notified', false);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    // ========== HELPERS ==========
    public static function getTypes()
    {
        return self::TYPES;
    }

    public function getTypeLabel()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getTypeIcon()
    {
        return match($this->type) {
            'task' => '📋',
            'bill' => '💰',
            'schedule' => '📅',
            'habit' => '✅',
            'custom' => '🔔',
            default => '🔔'
        };
    }

    public function getTimeRemaining()
    {
        $diff = now()->diffInMinutes($this->reminder_time, false);
        
        if ($diff < 0) {
            return 'Terlewat';
        }
        
        if ($diff < 60) {
            return $diff . ' menit lagi';
        }
        
        $hours = floor($diff / 60);
        $minutes = $diff % 60;
        
        if ($hours < 24) {
            return $hours . ' jam ' . ($minutes > 0 ? $minutes . ' menit' : '') . ' lagi';
        }
        
        $days = floor($hours / 24);
        return $days . ' hari lagi';
    }

    public function isUpcoming()
    {
        return $this->reminder_time->isFuture();
    }

    public function isPast()
    {
        return $this->reminder_time->isPast();
    }

    public function getStatus()
    {
        if ($this->is_notified) return 'notified';
        if ($this->isPast()) return 'overdue';
        if ($this->isUpcoming()) return 'upcoming';
        return 'unknown';
    }

    public function getStatusBadge()
    {
        return match($this->getStatus()) {
            'notified' => 'success',
            'overdue' => 'danger',
            'upcoming' => 'warning',
            default => 'secondary'
        };
    }
}