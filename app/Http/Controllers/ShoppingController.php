<?php

namespace App\Http\Controllers;

use App\Models\ShoppingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingController extends Controller
{
    /**
     * Display a listing of shopping items.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Build query
        $query = ShoppingItem::where('user_id', $user->id);
        
        // Search
        $search = $request->input('search', '');
        if ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }
        
        // Filter by category
        $category = $request->input('category', 'all');
        if ($category != 'all') {
            $query->where('category', $category);
        }
        
        // Filter by status
        $status = $request->input('status', 'all');
        if ($status == 'checked') {
            $query->where('is_checked', true);
        } elseif ($status == 'unchecked') {
            $query->where('is_checked', false);
        } elseif ($status == 'low_stock') {
            $query->whereColumn('stock_quantity', '<=', 'min_stock');
        }
        
        // Get items
        $items = $query->orderBy('is_checked', 'asc')
                       ->orderBy('created_at', 'desc')
                       ->paginate(15);
        
        // Stats
        $stats = [
            'total' => ShoppingItem::where('user_id', $user->id)->count(),
            'checked' => ShoppingItem::where('user_id', $user->id)->where('is_checked', true)->count(),
            'unchecked' => ShoppingItem::where('user_id', $user->id)->where('is_checked', false)->count(),
            'low_stock' => ShoppingItem::where('user_id', $user->id)
                ->whereColumn('stock_quantity', '<=', 'min_stock')
                ->where('is_checked', false)
                ->count(),
        ];
        
        return view('shopping.index', compact('items', 'stats', 'search', 'category', 'status'));
    }

    /**
     * Show the form for creating a new shopping item.
     */
    public function create()
    {
        return view('shopping.create');
    }

    /**
     * Store a newly created shopping item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string|in:groceries,hygiene,drinks,others',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'estimated_price' => 'nullable|numeric|min:0',
            'is_checked' => 'nullable|boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['stock_quantity'] = $validated['stock_quantity'] ?? 0;
        $validated['min_stock'] = $validated['min_stock'] ?? 1;
        $validated['is_checked'] = $request->has('is_checked');

        ShoppingItem::create($validated);

        return redirect()->route('shopping.index')
                         ->with('success', 'Item belanja berhasil ditambahkan! 🛒');
    }

    /**
     * Display the specified shopping item.
     */
    public function show(ShoppingItem $shopping)
    {
        if ($shopping->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk melihat item ini.');
        }
        
        return view('shopping.show', compact('shopping'));
    }

    /**
     * Show the form for editing the specified shopping item.
     */
/**
 * Show the form for editing the specified shopping item.
 */
public function edit(ShoppingItem $shopping)
{
    // Check authorization
    if ($shopping->user_id !== Auth::id()) {
        abort(403, 'Anda tidak memiliki izin untuk mengedit item ini.');
    }
    
    // Kirim data ke view dengan nama variabel yang sama
    return view('shopping.edit', compact('shopping'));
}

    /**
     * Update the specified shopping item in storage.
     */
    public function update(Request $request, ShoppingItem $shopping)
    {
        if ($shopping->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk mengupdate item ini.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string|in:groceries,hygiene,drinks,others',
            'stock_quantity' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'estimated_price' => 'nullable|numeric|min:0',
            'is_checked' => 'nullable|boolean',
        ]);

        $validated['stock_quantity'] = $validated['stock_quantity'] ?? 0;
        $validated['min_stock'] = $validated['min_stock'] ?? 1;
        $validated['is_checked'] = $request->has('is_checked');

        $shopping->update($validated);

        return redirect()->route('shopping.index')
                         ->with('success', 'Item belanja berhasil diperbarui!');
    }

    /**
     * Remove the specified shopping item from storage.
     */
    public function destroy(ShoppingItem $shopping)
    {
        if ($shopping->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus item ini.');
        }
        
        $shopping->delete();

        return redirect()->route('shopping.index')
                         ->with('success', 'Item belanja berhasil dihapus!');
    }

    /**
     * Toggle checked status of shopping item.
     */
    public function toggleCheck(ShoppingItem $shopping)
    {
        // Check authorization
        if ($shopping->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengubah item ini.'
            ], 403);
        }

        try {
            // Toggle status
            $shopping->is_checked = !$shopping->is_checked;
            $shopping->save();

            $message = $shopping->is_checked 
                ? 'Item berhasil dibeli! ✅' 
                : 'Item dibatalkan dari daftar belanja';

            return response()->json([
                'success' => true,
                'is_checked' => $shopping->is_checked,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk check multiple items.
     */
    public function bulkCheck(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:shopping_items,id'
        ]);

        $count = ShoppingItem::where('user_id', Auth::id())
            ->whereIn('id', $validated['ids'])
            ->update(['is_checked' => true]);

        return redirect()->route('shopping.index')
                         ->with('success', $count . ' item berhasil ditandai sebagai sudah dibeli!');
    }

    /**
     * Bulk uncheck multiple items.
     */
    public function bulkUncheck(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:shopping_items,id'
        ]);

        $count = ShoppingItem::where('user_id', Auth::id())
            ->whereIn('id', $validated['ids'])
            ->update(['is_checked' => false]);

        return redirect()->route('shopping.index')
                         ->with('success', $count . ' item berhasil dibatalkan!');
    }

    /**
     * Get low stock items for notification.
     */
    public function getLowStock()
    {
        $items = ShoppingItem::where('user_id', Auth::id())
            ->whereColumn('stock_quantity', '<=', 'min_stock')
            ->where('is_checked', false)
            ->get();

        return response()->json([
            'count' => $items->count(),
            'items' => $items->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'stock' => $item->stock_quantity,
                    'min_stock' => $item->min_stock,
                    'category' => $item->getCategoryLabel(),
                ];
            })
        ]);
    }

    /**
     * Get shopping statistics.
     */
    public function getStats()
    {
        $user = Auth::user();
        
        $stats = [
            'total' => ShoppingItem::where('user_id', $user->id)->count(),
            'checked' => ShoppingItem::where('user_id', $user->id)->where('is_checked', true)->count(),
            'unchecked' => ShoppingItem::where('user_id', $user->id)->where('is_checked', false)->count(),
            'low_stock' => ShoppingItem::where('user_id', $user->id)
                ->whereColumn('stock_quantity', '<=', 'min_stock')
                ->where('is_checked', false)
                ->count(),
            'by_category' => ShoppingItem::where('user_id', $user->id)
                ->selectRaw('category, COUNT(*) as count, SUM(is_checked) as checked_count')
                ->groupBy('category')
                ->get()
                ->map(function($item) {
                    return [
                        'category' => $item->category,
                        'label' => ShoppingItem::CATEGORIES[$item->category] ?? $item->category,
                        'total' => $item->count,
                        'checked' => $item->checked_count,
                        'unchecked' => $item->count - $item->checked_count,
                    ];
                }),
        ];

        return response()->json($stats);
    }
}