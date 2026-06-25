<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'priority',
        'status', 'deadline', 'attachment', 'category',
        'notes', 'completed_at'
    ];

    protected $casts = [
        'deadline' => 'date',
        'completed_at' => 'datetime'
    ];

    // ========== RELATIONS ==========
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== SCOPES ==========
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
                     ->whereDate('deadline', '<', Carbon::today());
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('status', '!=', 'completed')
                     ->whereBetween('deadline', [
                         Carbon::today(),
                         Carbon::today()->addDays($days)
                     ]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['not_started', 'in_progress']);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // ========== HELPERS ==========
    public function isOverdue()
    {
        return $this->status !== 'completed' && Carbon::now()->gt($this->deadline);
    }

    public function getDaysUntilDeadline()
    {
        return Carbon::now()->diffInDays($this->deadline, false);
    }

    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'not_started' => 'gray',
            'in_progress' => 'blue',
            'completed' => 'green',
            'overdue' => 'red',
            default => 'gray'
        };
    }

    public function getPriorityBadgeColor()
    {
        return match($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'blue',
            default => 'gray'
        };
    }

    public function getPriorityIcon()
    {
        return match($this->priority) {
            'high' => 'fa-arrow-up',
            'medium' => 'fa-minus',
            'low' => 'fa-arrow-down',
            default => 'fa-circle'
        };
    }

    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    public function markAsInProgress()
    {
        $this->status = 'in_progress';
        $this->save();
    }

    public function markAsNotStarted()
    {
        $this->status = 'not_started';
        $this->save();
    }
}