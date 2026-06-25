<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'avatar',
        'daily_meal_budget', 'savings_goal', 'currency',
        'theme', 'pomodoro_focus', 'pomodoro_break', 
        'daily_focus_target'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'daily_meal_budget' => 'decimal:2',
        'savings_goal' => 'decimal:2',
        'pomodoro_focus' => 'integer',
        'pomodoro_break' => 'integer',
        'daily_focus_target' => 'integer'
    ];

    // ========== RELATIONS ==========
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function mealBudgets()
    {
        return $this->hasMany(MealBudget::class);
    }

    public function shoppingItems()
    {
        return $this->hasMany(ShoppingItem::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function focusSessions()
    {
        return $this->hasMany(FocusSession::class);
    }

    public function habits()
    {
        return $this->hasMany(Habit::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    // ========== HELPERS ==========
    public function getMonthlyIncome()
    {
        return $this->transactions()
            ->where('type', 'income')
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount');
    }

    public function getMonthlyExpense()
    {
        return $this->transactions()
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount');
    }

    public function getBalance()
    {
        return $this->getMonthlyIncome() - $this->getMonthlyExpense();
    }

    public function getTodayMealSpent()
    {
        return $this->mealBudgets()
            ->whereDate('meal_date', today())
            ->sum('amount');
    }

    public function getTodayFocusHours()
    {
        return $this->focusSessions()
            ->whereDate('started_at', today())
            ->where('status', 'completed')
            ->sum('duration') / 60;
    }

    public function getActiveTasksCount()
    {
        return $this->tasks()
            ->whereIn('status', ['not_started', 'in_progress'])
            ->whereDate('deadline', '>=', today())
            ->count();
    }

    public function getOverdueTasksCount()
    {
        return $this->tasks()
            ->whereIn('status', ['not_started', 'in_progress'])
            ->whereDate('deadline', '<', today())
            ->count();
    }
}