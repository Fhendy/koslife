<?php

use App\Http\Controllers\{
    DashboardController,
    TaskController,
    FinanceController,
    MealBudgetController,
    ScheduleController,
    FocusController,
    HabitController,
    NoteController,
    ReminderController,
    ProfileController,
    ShoppingController
};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Dashboard API Routes
Route::prefix('api/dashboard')->group(function () {
    Route::get('/financial', [DashboardController::class, 'getFinancialSummaryApi'])->name('api.dashboard.financial');
    Route::get('/productivity', [DashboardController::class, 'getProductivitySummaryApi'])->name('api.dashboard.productivity');
    Route::get('/chart', [DashboardController::class, 'getChartDataApi'])->name('api.dashboard.chart');
    Route::get('/all', [DashboardController::class, 'getAllDataApi'])->name('api.dashboard.all');
});
    // Tasks
    Route::resource('tasks', TaskController::class);
    Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggleComplete'])->name('tasks.toggle');
    
    // Finance
    Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::post('finance/income', [FinanceController::class, 'storeIncome'])->name('finance.income.store');
    Route::post('finance/expense', [FinanceController::class, 'storeExpense'])->name('finance.expense.store');
    Route::delete('finance/{transaction}', [FinanceController::class, 'destroy'])->name('finance.destroy');
    Route::get('finance/export/{format}', [FinanceController::class, 'export'])->name('finance.export');
    Route::get('finance/summary', [FinanceController::class, 'getSummary'])->name('finance.summary');
    
    // Meal Budget
    Route::get('meal-budget', [MealBudgetController::class, 'index'])->name('meal-budget.index');
    Route::post('meal-budget', [MealBudgetController::class, 'store'])->name('meal-budget.store');
    Route::delete('meal-budget/{mealBudget}', [MealBudgetController::class, 'destroy'])->name('meal-budget.destroy');
    Route::get('meal-budget/stats/{date?}', [MealBudgetController::class, 'getDailyStats'])->name('meal-budget.stats');
    
// Shopping
Route::resource('shopping', ShoppingController::class);
Route::patch('shopping/{shopping}/toggle', [ShoppingController::class, 'toggleCheck'])->name('shopping.toggle');
Route::post('shopping/bulk-check', [ShoppingController::class, 'bulkCheck'])->name('shopping.bulk-check');
Route::post('shopping/bulk-uncheck', [ShoppingController::class, 'bulkUncheck'])->name('shopping.bulk-uncheck');
Route::get('shopping/low-stock', [ShoppingController::class, 'getLowStock'])->name('shopping.low-stock');
Route::get('shopping/stats', [ShoppingController::class, 'getStats'])->name('shopping.stats');

    // Schedule
    Route::get('calendar', [ScheduleController::class, 'calendar'])->name('calendar.index');
    Route::get('schedules/events', [ScheduleController::class, 'events'])->name('schedules.events');
    Route::resource('schedules', ScheduleController::class);
    
// Focus Mode
Route::get('focus', [FocusController::class, 'index'])->name('focus.index');
Route::get('focus/stats', [FocusController::class, 'stats'])->name('focus.stats');
Route::post('focus/start', [FocusController::class, 'start'])->name('focus.start');
Route::post('focus/{session}/pause', [FocusController::class, 'pause'])->name('focus.pause');
Route::post('focus/{session}/resume', [FocusController::class, 'resume'])->name('focus.resume');
Route::post('focus/{session}/stop', [FocusController::class, 'stop'])->name('focus.stop');
Route::post('focus/{session}/complete', [FocusController::class, 'complete'])->name('focus.complete');

    // Habits
    Route::resource('habits', HabitController::class);
    Route::post('habits/{habit}/log', [HabitController::class, 'log'])->name('habits.log');
    Route::post('habits/{habit}/toggle', [HabitController::class, 'toggle'])->name('habits.toggle');
    Route::get('habits/{habit}/stats', [HabitController::class, 'getStats'])->name('habits.stats');
    
    // Notes
    Route::resource('notes', NoteController::class);
    Route::post('notes/{note}/pin', [NoteController::class, 'togglePin'])->name('notes.pin');
    
    // Reminders
    Route::resource('reminders', ReminderController::class);
    Route::post('reminders/{reminder}/notify', [ReminderController::class, 'notify'])->name('reminders.notify');
    Route::get('reminders/upcoming', [ReminderController::class, 'getUpcoming'])->name('reminders.upcoming');
    
    // Profile
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
Route::delete('/profile/clear-data', [ProfileController::class, 'clearData'])->name('profile.clear-data');
});

require __DIR__.'/auth.php';