<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Note::where('user_id', Auth::id());
        
        // Search
        $search = $request->input('search', '');
        if ($search) {
            $query->search($search);
        }
        
        // Filter by category
        $category = $request->input('category', 'all');
        if ($category != 'all') {
            $query->where('category', $category);
        }
        
        // Sort
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'updated':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            default: // latest
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        // Pinned first
        $notes = $query->orderBy('is_pinned', 'desc')
                       ->paginate(12);
        
        // Categories
        $categories = Note::where('user_id', Auth::id())
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();
        
        // Stats
        $stats = [
            'total' => Note::where('user_id', Auth::id())->count(),
            'pinned' => Note::where('user_id', Auth::id())->where('is_pinned', true)->count(),
        ];
        
        // Last updated - FIX: ambil updated_at lalu diffForHumans
        $lastUpdatedNote = Note::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->first();
        
        $lastUpdated = $lastUpdatedNote ? $lastUpdatedNote->updated_at->diffForHumans() : '-';

        return view('notes.index', compact(
            'notes', 
            'categories', 
            'stats', 
            'lastUpdated', 
            'search', 
            'category', 
            'sort'
        ));
    }


    public function create()
    {
        return view('notes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'color' => 'nullable|string',
            'is_pinned' => 'nullable|boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_pinned'] = $request->has('is_pinned');

        Note::create($validated);

        return redirect()->route('notes.index')
                         ->with('success', 'Catatan berhasil dibuat! 📝');
    }

    public function show(Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }
        return view('notes.show', compact('note'));
    }

    public function edit(Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }
        return view('notes.edit', compact('note'));
    }

    public function update(Request $request, Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'color' => 'nullable|string',
            'is_pinned' => 'nullable|boolean',
        ]);

        $validated['is_pinned'] = $request->has('is_pinned');

        $note->update($validated);

        return redirect()->route('notes.index')
                         ->with('success', 'Catatan berhasil diperbarui!');
    }

    public function destroy(Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }
        
        $note->delete();

        return redirect()->route('notes.index')
                         ->with('success', 'Catatan berhasil dihapus!');
    }

    public function togglePin(Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin'
            ], 403);
        }
        
        $note->is_pinned = !$note->is_pinned;
        $note->save();
        
        return response()->json([
            'success' => true,
            'is_pinned' => $note->is_pinned,
            'message' => $note->is_pinned ? 'Catatan disematkan! 📌' : 'Catatan dilepas!',
        ]);
    }
}