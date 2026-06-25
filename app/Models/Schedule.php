<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Schedule extends Model
{
    use HasFactory;

    const CATEGORIES = [
        'school' => 'Sekolah',
        'pkl' => 'PKL',
        'organization' => 'Organisasi',
        'meeting' => 'Meeting',
        'personal' => 'Pribadi'
    ];

    const COLORS = [
        'school' => '#4F46E5',
        'pkl' => '#EF4444',
        'organization' => '#F59E0B',
        'meeting' => '#10B981',
        'personal' => '#8B5CF6'
    ];

    protected $fillable = [
        'user_id', 'title', 'description', 'category',
        'location', 'start_time', 'end_time', 'color',
        'is_all_day', 'reminder_minutes'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_all_day' => 'boolean',
        'reminder_minutes' => 'integer'
    ];

    // ========== RELATIONS ==========
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== SCOPES ==========
    public function scopeToday($query)
    {
        return $query->whereDate('start_time', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('start_time', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->whereBetween('start_time', [
            Carbon::now(),
            Carbon::now()->addDays($days)
        ]);
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

    public static function getColors()
    {
        return self::COLORS;
    }

    public function getCategoryLabel()
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getDuration()
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    public function getDurationFormatted()
    {
        $minutes = $this->getDuration();
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0) {
            return $hours . ' jam ' . ($mins > 0 ? $mins . ' menit' : '');
        }
        return $mins . ' menit';
    }

    public function isToday()
    {
        return $this->start_time->isToday();
    }

    public function isUpcoming()
    {
        return $this->start_time->isFuture();
    }

    public function isPast()
    {
        return $this->end_time->isPast();
    }

    public function getStatus()
    {
        if ($this->isPast()) return 'selesai';
        if ($this->isToday()) return 'hari_ini';
        if ($this->isUpcoming()) return 'akan_datang';
        return 'unknown';
    }

    public function getStatusBadge()
    {
        return match($this->getStatus()) {
            'selesai' => 'success',
            'hari_ini' => 'primary',
            'akan_datang' => 'warning',
            default => 'secondary'
        };
    }
}