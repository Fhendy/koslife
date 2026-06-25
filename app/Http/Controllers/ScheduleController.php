<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display a listing of schedules.
     */
    public function index(Request $request)
    {
        $query = Schedule::where('user_id', Auth::id());

        // Search
        $search = $request->input('search', '');
        if ($search) {
            $query->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%');
        }

        // Filter by category
        $category = $request->input('category', 'all');
        if ($category != 'all') {
            $query->where('category', $category);
        }

        $schedules = $query->orderBy('start_time', 'asc')->paginate(15);

        // Stats
        $stats = [
            'total' => Schedule::where('user_id', Auth::id())->count(),
            'today' => Schedule::where('user_id', Auth::id())
                ->whereDate('start_time', Carbon::today())
                ->count(),
            'upcoming' => Schedule::where('user_id', Auth::id())
                ->whereDate('start_time', '>', Carbon::today())
                ->count(),
            'past' => Schedule::where('user_id', Auth::id())
                ->whereDate('start_time', '<', Carbon::today())
                ->count(),
        ];

        return view('schedule.index', compact('schedules', 'stats', 'search', 'category'));
    }

    /**
     * Show the calendar view.
     */
    public function calendar()
    {
        // Get stats for dashboard
        $stats = [
            'total' => Schedule::where('user_id', Auth::id())->count(),
            'today' => Schedule::where('user_id', Auth::id())
                ->whereDate('start_time', Carbon::today())
                ->count(),
            'upcoming' => Schedule::where('user_id', Auth::id())
                ->whereDate('start_time', '>', Carbon::today())
                ->count(),
            'past' => Schedule::where('user_id', Auth::id())
                ->whereDate('start_time', '<', Carbon::today())
                ->count(),
        ];

        // Get upcoming events
        $upcomingEvents = Schedule::where('user_id', Auth::id())
            ->whereDate('start_time', '>=', Carbon::today())
            ->orderBy('start_time', 'asc')
            ->limit(10)
            ->get();

        return view('schedule.calendar', compact('stats', 'upcomingEvents'));
    }

    /**
     * Get events for FullCalendar.
     */
    public function events(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $events = Schedule::where('user_id', Auth::id())
            ->whereBetween('start_time', [$start, $end])
            ->get()
            ->map(function($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'start' => $schedule->start_time->format('Y-m-d H:i:s'),
                    'end' => $schedule->end_time->format('Y-m-d H:i:s'),
                    'color' => $schedule->color,
                    'allDay' => $schedule->is_all_day,
                    'textColor' => '#ffffff',
                ];
            });

        return response()->json($events);
    }

    /**
     * Show the form for creating a new schedule.
     */
    public function create()
    {
        return view('schedule.create');
    }

    /**
     * Store a newly created schedule in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:school,pkl,organization,meeting,personal',
            'location' => 'nullable|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'color' => 'nullable|string',
            'is_all_day' => 'nullable|boolean',
            'reminder_minutes' => 'nullable|integer|min:0',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_all_day'] = $request->has('is_all_day');
        $validated['color'] = $validated['color'] ?? Schedule::COLORS[$validated['category']] ?? '#4F46E5';

        Schedule::create($validated);

        return redirect()->route('calendar.index')
                         ->with('success', 'Jadwal berhasil ditambahkan! 📅');
    }

    /**
     * Display the specified schedule.
     */
    public function show(Schedule $schedule)
    {
        if ($schedule->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk melihat jadwal ini.');
        }

        return view('schedule.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified schedule.
     */
    public function edit(Schedule $schedule)
    {
        if ($schedule->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit jadwal ini.');
        }

        return view('schedule.edit', compact('schedule'));
    }

    /**
     * Update the specified schedule in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        if ($schedule->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk mengupdate jadwal ini.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:school,pkl,organization,meeting,personal',
            'location' => 'nullable|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'color' => 'nullable|string',
            'is_all_day' => 'nullable|boolean',
            'reminder_minutes' => 'nullable|integer|min:0',
        ]);

        $validated['is_all_day'] = $request->has('is_all_day');
        $validated['color'] = $validated['color'] ?? Schedule::COLORS[$validated['category']] ?? '#4F46E5';

        $schedule->update($validated);

        return redirect()->route('calendar.index')
                         ->with('success', 'Jadwal berhasil diperbarui!');
    }

    /**
     * Remove the specified schedule from storage.
     */
    public function destroy(Schedule $schedule)
    {
        if ($schedule->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus jadwal ini.');
        }

        $schedule->delete();

        return redirect()->route('calendar.index')
                         ->with('success', 'Jadwal berhasil dihapus!');
    }

    /**
     * Get upcoming events for dashboard.
     */
    public function getUpcoming()
    {
        $events = Schedule::where('user_id', Auth::id())
            ->whereDate('start_time', '>=', Carbon::today())
            ->orderBy('start_time', 'asc')
            ->limit(5)
            ->get()
            ->map(function($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'start' => $schedule->start_time->format('Y-m-d H:i:s'),
                    'end' => $schedule->end_time->format('Y-m-d H:i:s'),
                    'color' => $schedule->color,
                    'category' => $schedule->category,
                    'location' => $schedule->location,
                ];
            });

        return response()->json($events);
    }

    /**
     * Get today's events.
     */
    public function getToday()
    {
        $events = Schedule::where('user_id', Auth::id())
            ->whereDate('start_time', Carbon::today())
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json($events);
    }
}