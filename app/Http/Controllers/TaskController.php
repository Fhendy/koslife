<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index(Request $request)
    {
        $query = Task::where('user_id', Auth::id());

        // Get filter values
        $search = $request->input('search', '');
        $status = $request->input('status', 'all');
        $priority = $request->input('priority', 'all');
        $category = $request->input('category', 'all');

        // Apply filters
        if ($status != 'all') {
            $query->where('status', $status);
        }

        if ($priority != 'all') {
            $query->where('priority', $priority);
        }

        if ($category != 'all') {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%');
            });
        }

        $tasks = $query->orderBy('deadline', 'asc')->paginate(10);
        
        // Stats
        $stats = [
            'total' => Task::where('user_id', Auth::id())->count(),
            'completed' => Task::where('user_id', Auth::id())->where('status', 'completed')->count(),
            'active' => Task::where('user_id', Auth::id())->whereIn('status', ['not_started', 'in_progress'])->count(),
            'overdue' => Task::where('user_id', Auth::id())->overdue()->count(),
        ];

        return view('tasks.index', compact('tasks', 'stats', 'search', 'status', 'priority', 'category'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:not_started,in_progress,completed',
            'deadline' => 'required|date|after:today',
            'category' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,png',
        ]);

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('tasks', 'public');
            $validated['attachment'] = $path;
        }

        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        Task::create($validated);

        return redirect()->route('tasks.index')
                         ->with('success', 'Tugas berhasil ditambahkan! 🎉');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        // Check authorization
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk melihat tugas ini.');
        }
        
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        // Check authorization
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit tugas ini.');
        }
        
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Check authorization
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk mengupdate tugas ini.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:not_started,in_progress,completed',
            'deadline' => 'required|date',
            'category' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,png',
        ]);

        if ($request->hasFile('attachment')) {
            // Delete old attachment
            if ($task->attachment) {
                Storage::disk('public')->delete($task->attachment);
            }
            $path = $request->file('attachment')->store('tasks', 'public');
            $validated['attachment'] = $path;
        }

        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return redirect()->route('tasks.index')
                         ->with('success', 'Tugas berhasil diperbarui!');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        // Check authorization
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus tugas ini.');
        }
        
        if ($task->attachment) {
            Storage::disk('public')->delete($task->attachment);
        }
        
        $task->delete();

        return redirect()->route('tasks.index')
                         ->with('success', 'Tugas berhasil dihapus!');
    }

    /**
     * Toggle task completion status.
     * (HANYA SATU METHOD toggleComplete)
     */
    public function toggleComplete(Task $task)
    {
        // Check authorization
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengubah tugas ini.'
            ], 403);
        }

        try {
            // Toggle status
            if ($task->status === 'completed') {
                $task->status = 'not_started';
                $task->completed_at = null;
                $message = 'Tugas dibuka kembali';
            } else {
                $task->status = 'completed';
                $task->completed_at = now();
                $message = 'Selamat! Tugas selesai 🎉';
            }
            
            $task->save();

            return response()->json([
                'success' => true,
                'status' => $task->status,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status tugas: ' . $e->getMessage()
            ], 500);
        }
    }
}