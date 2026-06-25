<?php

namespace App\Http\Controllers;

use App\Models\MealBudget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MealBudgetController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // Today's meals
        $todayMeals = MealBudget::where('user_id', $user->id)
            ->whereDate('meal_date', $today)
            ->orderBy('meal_type')
            ->get();
            
        // Today's total spent
        $todaySpent = $todayMeals->sum('amount');
        
        // Monthly meals
        $monthlyMeals = MealBudget::where('user_id', $user->id)
            ->whereMonth('meal_date', now()->month)
            ->whereYear('meal_date', now()->year)
            ->orderBy('meal_date', 'desc')
            ->get();
            
        $monthlySpent = $monthlyMeals->sum('amount');
        
        // Stats by meal type
        $statsByType = MealBudget::where('user_id', $user->id)
            ->whereMonth('meal_date', now()->month)
            ->whereYear('meal_date', now()->year)
            ->selectRaw('meal_type, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('meal_type')
            ->get();
            
        // Daily average
        $daysInMonth = now()->daysInMonth;
        $avgDaily = $daysInMonth > 0 ? round($monthlySpent / $daysInMonth) : 0;
        
        // Budget percentage
        $budget = $user->daily_meal_budget ?? 50000;
        $budgetPercentage = $budget > 0 ? min(round(($todaySpent / $budget) * 100), 100) : 0;
        
        return view('meal-budget.index', compact(
            'todayMeals', 
            'todaySpent', 
            'monthlyMeals',
            'monthlySpent',
            'statsByType', 
            'avgDaily', 
            'user',
            'budgetPercentage'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'meal_date' => 'nullable|date',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['meal_date'] = $validated['meal_date'] ?? today();

        MealBudget::create($validated);

        return redirect()->route('meal-budget.index')
                         ->with('success', 'Makanan berhasil ditambahkan! 🍽️');
    }

    public function destroy(MealBudget $mealBudget)
    {
        if ($mealBudget->user_id !== Auth::id()) {
            abort(403);
        }
        
        $mealBudget->delete();
        
        return redirect()->route('meal-budget.index')
                         ->with('success', 'Data makanan berhasil dihapus!');
    }

    public function getDailyStats($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        $user = Auth::user();
        
        $meals = MealBudget::where('user_id', $user->id)
            ->whereDate('meal_date', $date)
            ->orderBy('meal_type')
            ->get();
            
        $total = $meals->sum('amount');
        $budget = $user->daily_meal_budget ?? 50000;
        
        return response()->json([
            'date' => $date->format('Y-m-d'),
            'meals' => $meals,
            'total' => $total,
            'budget' => $budget,
            'remaining' => $budget - $total,
            'percentage' => $budget > 0 ? min(round(($total / $budget) * 100), 100) : 0,
        ]);
    }
}