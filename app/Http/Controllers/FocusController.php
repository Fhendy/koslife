<?php

namespace App\Http\Controllers;

use App\Models\FocusSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FocusController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();

        // Today's stats - hanya yang completed
        $todaySessions = FocusSession::where('user_id', $user->id)
            ->whereDate('started_at', $today)
            ->get();

        $todayMinutes = $todaySessions->where('status', 'completed')->sum('duration');
        $todayCompleted = $todaySessions->where('status', 'completed')->count();

        // Weekly stats
        $weeklySessions = FocusSession::where('user_id', $user->id)
            ->whereBetween('started_at', [$weekStart, Carbon::now()])
            ->get();

        $weeklyMinutes = $weeklySessions->where('status', 'completed')->sum('duration');

        // Best session
        $bestSession = FocusSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('duration', 'desc')
            ->first();

        $presets = FocusSession::getPresetTimers();

        return view('focus.index', compact(
            'todayMinutes', 'todayCompleted',
            'weeklyMinutes', 'bestSession', 'presets'
        ));
    }

    public function start(Request $request)
    {
        $validated = $request->validate([
            'task' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'session_type' => 'nullable|string',
        ]);

        // Simpan DURASI dalam MENIT (bukan detik)
        $session = FocusSession::create([
            'user_id' => Auth::id(),
            'task' => $validated['task'],
            'duration' => $validated['duration'], // MENIT
            'session_type' => $validated['session_type'] ?? 'custom',
            'status' => 'in_progress',
            'started_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'session' => $session,
            'message' => 'Sesi fokus dimulai! 🎯',
        ]);
    }

    public function pause($sessionId)
    {
        $session = FocusSession::where('user_id', Auth::id())
            ->where('id', $sessionId)
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak ditemukan'
            ], 404);
        }

        if ($session->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak dalam status berjalan'
            ], 400);
        }

        $session->status = 'paused';
        $session->save();

        return response()->json([
            'success' => true,
            'message' => 'Sesi dijeda ⏸️',
        ]);
    }

    public function resume($sessionId)
    {
        $session = FocusSession::where('user_id', Auth::id())
            ->where('id', $sessionId)
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak ditemukan'
            ], 404);
        }

        if ($session->status !== 'paused') {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak dalam status dijeda'
            ], 400);
        }

        $session->status = 'in_progress';
        $session->save();

        return response()->json([
            'success' => true,
            'message' => 'Sesi dilanjutkan ▶️',
        ]);
    }

    public function stop($sessionId)
    {
        $session = FocusSession::where('user_id', Auth::id())
            ->where('id', $sessionId)
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak ditemukan'
            ], 404);
        }

        if ($session->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Sesi sudah selesai'
            ], 400);
        }

        $session->status = 'interrupted';
        $session->ended_at = Carbon::now();
        $session->save();

        return response()->json([
            'success' => true,
            'message' => 'Sesi dihentikan ⏹️',
        ]);
    }

    public function complete($sessionId)
    {
        $session = FocusSession::where('user_id', Auth::id())
            ->where('id', $sessionId)
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi tidak ditemukan'
            ], 404);
        }

        $session->status = 'completed';
        $session->ended_at = Carbon::now();
        $session->save();

        return response()->json([
            'success' => true,
            'message' => 'Sesi selesai! 🎉',
            'duration' => $session->duration,
        ]);
    }

    public function stats()
    {
        $user = Auth::user();

        // Ambil semua sesi (termasuk in_progress dan paused)
        $sessions = FocusSession::where('user_id', $user->id)
            ->orderBy('started_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($session) {
                $statusLabels = [
                    'completed' => 'Selesai',
                    'interrupted' => 'Terinterupsi',
                    'in_progress' => 'Berjalan',
                    'paused' => 'Dijeda',
                    'cancelled' => 'Dibatalkan'
                ];
                
                return [
                    'id' => $session->id,
                    'task' => $session->task,
                    'duration' => $this->formatDuration($session->duration),
                    'duration_minutes' => $session->duration,
                    'date' => $session->started_at->timezone(config('app.timezone', 'Asia/Jakarta'))
                        ->translatedFormat('d M Y H:i'),
                    'status' => $session->status,
                    'status_label' => $statusLabels[$session->status] ?? $session->status,
                    'icon' => $session->getIcon(),
                    'session_type' => $session->session_type,
                ];
            });

        // Weekly chart data
        $weeklyData = collect(range(6, 0))->map(function ($day) use ($user) {
            $date = Carbon::today()->subDays($day);
            $minutes = FocusSession::where('user_id', $user->id)
                ->whereDate('started_at', $date)
                ->where('status', 'completed')
                ->sum('duration');
            return [
                'date' => $date->format('d M'),
                'minutes' => $minutes,
            ];
        });

        // Stats by type
        $byType = FocusSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->selectRaw('session_type, COUNT(*) as count, SUM(duration) as total_minutes')
            ->groupBy('session_type')
            ->get();

        // Monthly data
        $monthlyData = collect(range(29, 0))->map(function ($day) use ($user) {
            $date = Carbon::today()->subDays($day);
            $minutes = FocusSession::where('user_id', $user->id)
                ->whereDate('started_at', $date)
                ->where('status', 'completed')
                ->sum('duration');
            return [
                'date' => $date->format('d'),
                'minutes' => $minutes,
            ];
        });

        $totalSessions = FocusSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
            
        $totalMinutes = FocusSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('duration');
            
        $avgDaily = $totalMinutes > 0 ? round($totalMinutes / 30 / 60, 1) : 0;

        return response()->json([
            'sessions' => $sessions,
            'weekly' => $weeklyData,
            'by_type' => $byType,
            'monthly' => $monthlyData,
            'total_sessions' => $totalSessions,
            'total_hours' => round($totalMinutes / 60, 1),
            'avg_daily' => $avgDaily,
            'best_streak' => $this->calculateStreak($user),
        ]);
    }

    /**
     * Format durasi dalam menit ke format yang mudah dibaca
     * Contoh: 25 → "25m", 90 → "1j 30m"
     */
    private function formatDuration($minutes)
    {
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

    private function calculateStreak($user): int
    {
        $streak = 0;
        $date = Carbon::today();

        while (true) {
            $has = FocusSession::where('user_id', $user->id)
                ->whereDate('started_at', $date)
                ->where('status', 'completed')
                ->exists();

            if (!$has) break;
            
            $streak++;
            $date->subDay();
        }

        return $streak;
    }
}