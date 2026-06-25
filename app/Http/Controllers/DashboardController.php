<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Transaction;
use App\Models\MealBudget;
use App\Models\Schedule;
use App\Models\FocusSession;
use App\Models\Habit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // Financial Summary
        $financial = $this->getFinancialSummary($user);
        
        // Task Summary
        $taskSummary = $this->getTaskSummary($user);
        
        // Productivity
        $productivity = $this->getProductivitySummary($user);
        
        // Today's Schedule
        $todaySchedules = Schedule::where('user_id', $user->id)
            ->whereDate('start_time', $today)
            ->orderBy('start_time')
            ->limit(5)
            ->get();
        
        // Daily Meal Budget
        $mealBudget = $this->getMealBudget($user, $today);
        
        // Recent Transactions
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->orderBy('transaction_date', 'desc')
            ->limit(5)
            ->get();
        
        // Upcoming Tasks
        $upcomingTasks = Task::where('user_id', $user->id)
            ->where('status', '!=', 'completed')
            ->whereDate('deadline', '>=', $today)
            ->orderBy('deadline')
            ->limit(5)
            ->get();
        
        // Chart Data
        $chartData = $this->getChartData($user);
        
        // ===== TAMBAHKAN HABITS =====
        $habits = Habit::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('streak', 'desc')
            ->get();
        
        // ===== TAMBAHKAN STATISTIK TAMBAHAN =====
        $stats = $this->getAdditionalStats($user);
        
        return view('dashboard.index', compact(
            'financial', 'taskSummary', 'productivity',
            'todaySchedules', 'mealBudget', 'recentTransactions',
            'upcomingTasks', 'chartData', 'habits', 'stats'
        ));
    }
    
    /**
     * Get Financial Summary
     */
    private function getFinancialSummary($user)
    {
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        
        $income = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->sum('amount');
            
        $expense = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->sum('amount');
            
        $balance = $income - $expense;
        
        return [
            'balance' => $balance,
            'income' => $income,
            'expense' => $expense,
            'percentage' => $income > 0 ? round(($expense / $income) * 100) : 0,
            'savings_goal' => $user->savings_goal ?? 0,
            'savings_progress' => ($user->savings_goal ?? 0) > 0 
                ? min(round(($balance / ($user->savings_goal ?? 1)) * 100), 100) 
                : 0
        ];
    }
    
    /**
     * Get Task Summary
     */
    private function getTaskSummary($user)
    {
        $today = Carbon::today();
        
        return [
            'total' => Task::where('user_id', $user->id)->count(),
            'completed' => Task::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'active' => Task::where('user_id', $user->id)
                ->whereIn('status', ['not_started', 'in_progress'])
                ->whereDate('deadline', '>=', $today)
                ->count(),
            'overdue' => Task::where('user_id', $user->id)
                ->whereIn('status', ['not_started', 'in_progress'])
                ->whereDate('deadline', '<', $today)
                ->count(),
            'completion_rate' => Task::where('user_id', $user->id)->count() > 0
                ? round((Task::where('user_id', $user->id)->where('status', 'completed')->count() / Task::where('user_id', $user->id)->count()) * 100)
                : 0
        ];
    }
    
    /**
     * Get Productivity Summary
     */
    private function getProductivitySummary($user)
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        
        $focusToday = FocusSession::where('user_id', $user->id)
            ->whereDate('started_at', $today)
            ->where('status', 'completed')
            ->sum('duration');
            
        $focusWeek = FocusSession::where('user_id', $user->id)
            ->whereBetween('started_at', [$weekStart, Carbon::now()])
            ->where('status', 'completed')
            ->sum('duration');
            
        $focusMonth = FocusSession::where('user_id', $user->id)
            ->whereBetween('started_at', [$monthStart, Carbon::now()])
            ->where('status', 'completed')
            ->sum('duration');
            
        $sessionsToday = FocusSession::where('user_id', $user->id)
            ->whereDate('started_at', $today)
            ->where('status', 'completed')
            ->count();
            
        $totalSessions = FocusSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
            
        $streak = $this->calculateStreak($user);
        
        // Get best session
        $bestSession = FocusSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('duration', 'desc')
            ->first();
        
        return [
            'focus_today' => round($focusToday / 60, 1),
            'focus_week' => round($focusWeek / 60, 1),
            'focus_month' => round($focusMonth / 60, 1),
            'sessions_today' => $sessionsToday,
            'total_sessions' => $totalSessions,
            'streak' => $streak,
            'daily_target' => $user->daily_focus_target ?? 2,
            'best_session' => $bestSession ? round($bestSession->duration / 60, 1) : 0,
        ];
    }
    
    /**
     * Calculate Habit Streak
     */
    private function calculateStreak($user)
    {
        // Ambil semua habit user
        $habits = Habit::where('user_id', $user->id)->get();
        
        if ($habits->isEmpty()) {
            return 0;
        }
        
        $streak = 0;
        $today = now()->startOfDay();
        $date = $today->copy();
        
        // Cek streak dari hari ini ke belakang
        while (true) {
            $hasCompleted = false;
            
            foreach ($habits as $habit) {
                $hasLog = $habit->logs()
                    ->whereDate('log_date', $date)
                    ->where('is_completed', true)
                    ->exists();
                    
                if ($hasLog) {
                    $hasCompleted = true;
                    break;
                }
            }
            
            if (!$hasCompleted) {
                break;
            }
            
            $streak++;
            $date->subDay();
        }
        
        return $streak;
    }
    
    /**
     * Get Meal Budget
     */
    private function getMealBudget($user, $date)
    {
        $totalSpent = MealBudget::where('user_id', $user->id)
            ->whereDate('meal_date', $date)
            ->sum('amount');
            
        $budget = $user->daily_meal_budget ?? 50000;
            
        return [
            'budget' => $budget,
            'spent' => $totalSpent,
            'remaining' => $budget - $totalSpent,
            'percentage' => $budget > 0 
                ? min(round(($totalSpent / $budget) * 100), 100)
                : 0
        ];
    }
    
    /**
     * Get Chart Data
     */
    private function getChartData($user)
    {
        $days = collect(range(6, 0))->map(function($day) {
            return Carbon::today()->subDays($day)->format('Y-m-d');
        });
        
        $expenseData = [];
        $incomeData = [];
        $labels = [];
        
        foreach ($days as $date) {
            $expenseData[] = Transaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereDate('transaction_date', $date)
                ->sum('amount');
                
            $incomeData[] = Transaction::where('user_id', $user->id)
                ->where('type', 'income')
                ->whereDate('transaction_date', $date)
                ->sum('amount');
                
            $labels[] = Carbon::parse($date)->format('d M');
        }
        
        return [
            'labels' => $labels,
            'expense' => $expenseData,
            'income' => $incomeData,
        ];
    }
    
    /**
     * Get Additional Statistics
     */
    private function getAdditionalStats($user)
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        
        // Total habits
        $totalHabits = Habit::where('user_id', $user->id)->count();
        $activeHabits = Habit::where('user_id', $user->id)
            ->where('is_active', true)
            ->count();
        
        // Today's completed habits
        $completedToday = Habit::where('user_id', $user->id)
            ->whereHas('logs', function($query) use ($today) {
                $query->whereDate('log_date', $today)
                      ->where('is_completed', true);
            })
            ->count();
        
        // Total notes
        $totalNotes = \App\Models\Note::where('user_id', $user->id)->count();
        $pinnedNotes = \App\Models\Note::where('user_id', $user->id)
            ->where('is_pinned', true)
            ->count();
        
        // Upcoming reminders
        $upcomingReminders = \App\Models\Reminder::where('user_id', $user->id)
            ->where('is_notified', false)
            ->where('reminder_time', '>=', now())
            ->count();
        
        // Shopping items
        $shoppingItems = \App\Models\ShoppingItem::where('user_id', $user->id)
            ->where('is_checked', false)
            ->count();
        
        // Low stock items
        $lowStock = \App\Models\ShoppingItem::where('user_id', $user->id)
            ->whereColumn('stock_quantity', '<=', 'min_stock')
            ->count();
        
        return [
            'total_habits' => $totalHabits,
            'active_habits' => $activeHabits,
            'completed_today' => $completedToday,
            'total_notes' => $totalNotes,
            'pinned_notes' => $pinnedNotes,
            'upcoming_reminders' => $upcomingReminders,
            'shopping_items' => $shoppingItems,
            'low_stock' => $lowStock,
        ];
    }
    
    /**
     * Get Financial Summary for API
     */
    public function getFinancialSummaryApi()
    {
        $user = Auth::user();
        return response()->json($this->getFinancialSummary($user));
    }
    
    /**
     * Get Productivity Summary for API
     */
    public function getProductivitySummaryApi()
    {
        $user = Auth::user();
        return response()->json($this->getProductivitySummary($user));
    }
    
    /**
     * Get Chart Data for API
     */
    public function getChartDataApi()
    {
        $user = Auth::user();
        return response()->json($this->getChartData($user));
    }
    
    /**
     * Get All Dashboard Data for API
     */
    public function getAllDataApi()
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        return response()->json([
            'financial' => $this->getFinancialSummary($user),
            'tasks' => $this->getTaskSummary($user),
            'productivity' => $this->getProductivitySummary($user),
            'meal_budget' => $this->getMealBudget($user, $today),
            'chart' => $this->getChartData($user),
            'stats' => $this->getAdditionalStats($user),
        ]);
    }
}