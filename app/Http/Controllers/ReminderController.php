<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReminderController extends Controller
{
    /**
     * Display a listing of reminders.
     */
    public function index(Request $request)
    {
        $query = Reminder::where('user_id', Auth::id());
        
        // Search
        $search = $request->input('search', '');
        if ($search) {
            $query->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%');
        }
        
        // Filter by type
        $type = $request->input('type', 'all');
        if ($type != 'all') {
            $query->where('type', $type);
        }
        
        // Filter by status
        $status = $request->input('status', 'all');
        if ($status == 'upcoming') {
            $query->where('is_notified', false)
                  ->where('reminder_time', '>=', Carbon::now());
        } elseif ($status == 'overdue') {
            $query->where('is_notified', false)
                  ->where('reminder_time', '<', Carbon::now());
        } elseif ($status == 'notified') {
            $query->where('is_notified', true);
        }
        
        $reminders = $query->orderBy('reminder_time', 'asc')
                           ->paginate(15);
        
        // Stats
        $stats = [
            'total' => Reminder::where('user_id', Auth::id())->count(),
            'upcoming' => Reminder::where('user_id', Auth::id())
                ->where('is_notified', false)
                ->where('reminder_time', '>=', Carbon::now())
                ->count(),
            'overdue' => Reminder::where('user_id', Auth::id())
                ->where('is_notified', false)
                ->where('reminder_time', '<', Carbon::now())
                ->count(),
            'notified' => Reminder::where('user_id', Auth::id())
                ->where('is_notified', true)
                ->count(),
        ];
        
        return view('reminders.index', compact('reminders', 'stats', 'search', 'type', 'status'));
    }

    /**
     * Show the form for creating a new reminder.
     */
    public function create()
    {
        return view('reminders.create');
    }

    /**
     * Store a newly created reminder in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'reminder_time' => 'required|date',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_notified'] = false;

        Reminder::create($validated);

        return redirect()->route('reminders.index')
                         ->with('success', 'Reminder berhasil dibuat! 🔔');
    }

    /**
     * Display the specified reminder.
     */
    public function show(Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            abort(403);
        }
        return view('reminders.show', compact('reminder'));
    }

    /**
     * Show the form for editing the specified reminder.
     */
    public function edit(Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            abort(403);
        }
        return view('reminders.edit', compact('reminder'));
    }

    /**
     * Update the specified reminder in storage.
     */
    public function update(Request $request, Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'reminder_time' => 'required|date',
        ]);

        $reminder->update($validated);

        return redirect()->route('reminders.index')
                         ->with('success', 'Reminder berhasil diperbarui!');
    }

    /**
     * Remove the specified reminder from storage.
     */
    public function destroy(Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            abort(403);
        }
        
        $reminder->delete();

        return redirect()->route('reminders.index')
                         ->with('success', 'Reminder berhasil dihapus!');
    }

    /**
     * Mark reminder as notified.
     */
    public function notify(Reminder $reminder)
    {
        if ($reminder->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin'
            ], 403);
        }
        
        $reminder->is_notified = true;
        $reminder->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Reminder ditandai sebagai sudah diberitahu! ✅'
        ]);
    }

    /**
     * Get upcoming reminders for notification.
     */
    public function getUpcoming()
    {
        $reminders = Reminder::where('user_id', Auth::id())
            ->where('is_notified', false)
            ->where('reminder_time', '>=', Carbon::now())
            ->orderBy('reminder_time', 'asc')
            ->limit(10)
            ->get()
            ->map(function($reminder) {
                return [
                    'id' => $reminder->id,
                    'title' => $reminder->title,
                    'type' => $reminder->getTypeLabel(),
                    'time' => $reminder->reminder_time->diffForHumans(),
                    'datetime' => $reminder->reminder_time->format('Y-m-d H:i:s'),
                ];
            });
            
        return response()->json($reminders);
    }
}