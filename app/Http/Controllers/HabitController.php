<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HabitController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Habit::where('user_id', $user->id);
        
        // Search
        $search = $request->input('search', '');
        if ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }
        
        // Filter by status
        $status = $request->input('status', 'all');
        if ($status == 'active') {
            $query->where('is_active', true);
        } elseif ($status == 'inactive') {
            $query->where('is_active', false);
        }
        
        // Use paginate for pagination
        $habits = $query->orderBy('is_active', 'desc')
                        ->orderBy('streak', 'desc')
                        ->paginate(12);
        
        // Stats
        $stats = [
            'total' => Habit::where('user_id', $user->id)->count(),
            'completed_today' => Habit::where('user_id', $user->id)
                ->whereHas('logs', function($query) {
                    $query->whereDate('log_date', Carbon::today())
                          ->where('is_completed', true);
                })
                ->count(),
            'streak' => $this->calculateOverallStreak($user),
            'completion_rate' => $this->calculateCompletionRate($user),
        ];
        
        return view('habits.index', compact('habits', 'stats', 'search', 'status'));
    }

    public function create()
    {
        return view('habits.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:10',
            'target_frequency' => 'required|in:daily,weekly,monthly',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['icon'] = $validated['icon'] ?? '✅';
        $validated['is_active'] = $request->has('is_active');

        Habit::create($validated);

        return redirect()->route('habits.index')
                         ->with('success', 'Habit berhasil ditambahkan! 🎯');
    }

    public function show(Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }
        return view('habits.show', compact('habit'));
    }

    public function edit(Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }
        return view('habits.edit', compact('habit'));
    }

    public function update(Request $request, Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:10',
            'target_frequency' => 'required|in:daily,weekly,monthly',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $habit->update($validated);

        return redirect()->route('habits.index')
                         ->with('success', 'Habit berhasil diperbarui!');
    }

    public function destroy(Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }
        
        $habit->delete();

        return redirect()->route('habits.index')
                         ->with('success', 'Habit berhasil dihapus!');
    }

    public function log(Habit $habit, Request $request)
    {
        if ($habit->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin'
            ], 403);
        }
        
        $date = $request->input('date', Carbon::today());
        $isCompleted = $request->input('is_completed', true);
        
        // Check if already logged
        $existingLog = HabitLog::where('habit_id', $habit->id)
            ->whereDate('log_date', $date)
            ->first();
            
        if ($existingLog) {
            $existingLog->update(['is_completed' => $isCompleted]);
        } else {
            HabitLog::create([
                'habit_id' => $habit->id,
                'log_date' => $date,
                'is_completed' => $isCompleted,
            ]);
        }
        
        // Update streak
        $habit->updateStreak();
        
        return response()->json([
            'success' => true,
            'streak' => $habit->streak,
            'message' => $isCompleted ? 'Habit selesai! ✅' : 'Habit dibatalkan ❌',
        ]);
    }

    public function toggle(Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }
        
        $habit->is_active = !$habit->is_active;
        $habit->save();
        
        return redirect()->route('habits.index')
                         ->with('success', $habit->is_active ? 'Habit diaktifkan!' : 'Habit dinonaktifkan!');
    }

    public function getStats(Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Last 30 days
        $monthlyLogs = $habit->logs()
            ->whereBetween('log_date', [Carbon::now()->subDays(30), Carbon::now()])
            ->get()
            ->groupBy(function($log) {
                return $log->log_date->format('Y-m-d');
            });
            
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $log = $monthlyLogs->get($date);
            $data[] = [
                'date' => $date,
                'completed' => $log ? $log->first()->is_completed : false,
            ];
        }
        
        return response()->json([
            'habit' => $habit->name,
            'streak' => $habit->streak,
            'best_streak' => $habit->best_streak,
            'completion_rate' => $habit->getCompletionRate(),
            'data' => $data,
        ]);
    }

    // ─── helpers ─────────────────────────────────────────────────────────────

    private function calculateOverallStreak($user): int
    {
        $habits = Habit::where('user_id', $user->id)->get();
        if ($habits->isEmpty()) return 0;
        
        $streak = 0;
        $date = Carbon::today();
        
        while (true) {
            $hasCompleted = false;
            foreach ($habits as $habit) {
                if ($habit->logs()->whereDate('log_date', $date)->where('is_completed', true)->exists()) {
                    $hasCompleted = true;
                    break;
                }
            }
            
            if (!$hasCompleted) break;
            
            $streak++;
            $date->subDay();
        }
        
        return $streak;
    }

    private function calculateCompletionRate($user): int
    {
        $habits = Habit::where('user_id', $user->id)->get();
        if ($habits->isEmpty()) return 0;
        
        $completed = 0;
        $total = 0;
        
        foreach ($habits as $habit) {
            $total++;
            if ($habit->logs()->whereDate('log_date', Carbon::today())->where('is_completed', true)->exists()) {
                $completed++;
            }
        }
        
        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }
}