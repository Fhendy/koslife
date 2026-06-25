<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionExport;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceController extends Controller
{
    /**
     * Display a listing of the transactions.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get monthly summary
        $monthlyIncome = $user->getMonthlyIncome();
        $monthlyExpense = $user->getMonthlyExpense();
        $balance = $user->getBalance();
        
        // Get transactions with filters
        $query = Transaction::where('user_id', $user->id);
        
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }
        
        if ($request->has('search') && $request->search) {
            $query->where('description', 'LIKE', '%' . $request->search . '%');
        }
        
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }
        
        $transactions = $query->orderBy('transaction_date', 'desc')
                              ->paginate(15);
        
        // Get category breakdown
        $expenseByCategory = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
            
        $incomeByCategory = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
        
        // ===== TAMBAHKAN UNTUK STATISTIK =====
        // Today's expense
        $todayExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereDate('transaction_date', now()->today())
            ->sum('amount');
        
        // Daily average expense
        $daysInMonth = now()->daysInMonth;
        $monthlyExpenseTotal = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');
        $avgDaily = $daysInMonth > 0 ? round($monthlyExpenseTotal / $daysInMonth) : 0;
        
        return view('finance.index', compact(
            'transactions', 
            'monthlyIncome', 
            'monthlyExpense', 
            'balance', 
            'expenseByCategory', 
            'incomeByCategory',
            'todayExpense',
            'avgDaily'
        ));
    }

    /**
     * Store a new income transaction.
     */
    public function storeIncome(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'is_debt' => 'nullable|boolean',
            'debtor_name' => 'nullable|string|max:100',
            'payment_status' => 'nullable|in:paid,unpaid',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['type'] = 'income';
        $validated['is_debt'] = $request->has('is_debt');
        $validated['payment_status'] = $validated['payment_status'] ?? 'paid';

        Transaction::create($validated);

        return redirect()->route('finance.index')
                         ->with('success', 'Pemasukan berhasil ditambahkan! ✅');
    }

    /**
     * Store a new expense transaction.
     */
    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'is_debt' => 'nullable|boolean',
            'debtor_name' => 'nullable|string|max:100',
            'payment_status' => 'nullable|in:paid,unpaid',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['type'] = 'expense';
        $validated['is_debt'] = $request->has('is_debt');
        $validated['payment_status'] = $validated['payment_status'] ?? 'paid';

        Transaction::create($validated);

        return redirect()->route('finance.index')
                         ->with('success', 'Pengeluaran berhasil ditambahkan! ✅');
    }

    /**
     * Remove the specified transaction.
     */
    public function destroy(Transaction $transaction)
    {
        // Check authorization
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus transaksi ini.');
        }
        
        $transaction->delete();
        
        return redirect()->route('finance.index')
                         ->with('success', 'Transaksi berhasil dihapus!');
    }

    /**
     * Export transactions to PDF or Excel.
     */
    public function export($format = 'pdf')
    {
        $user = Auth::user();
        
        $transactions = Transaction::where('user_id', $user->id)
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->orderBy('transaction_date', 'desc')
            ->get();
            
        if ($format === 'excel') {
            return Excel::download(new TransactionExport($transactions), 'transactions-' . now()->format('Y-m-d') . '.xlsx');
        }
        
        $pdf = Pdf::loadView('finance.export-pdf', compact('transactions'));
        return $pdf->download('transactions-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Get financial summary for charts.
     */
    public function getSummary()
    {
        $user = Auth::user();
        
        // Get last 7 days data
        $summary = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateString = $date->format('Y-m-d');
            
            $summary[] = [
                'date' => $date->format('d M'),
                'income' => Transaction::where('user_id', $user->id)
                    ->where('type', 'income')
                    ->whereDate('transaction_date', $dateString)
                    ->sum('amount'),
                'expense' => Transaction::where('user_id', $user->id)
                    ->where('type', 'expense')
                    ->whereDate('transaction_date', $dateString)
                    ->sum('amount'),
            ];
        }
        
        return response()->json($summary);
    }

    /**
     * Get monthly summary for dashboard.
     */
    public function getMonthlySummary()
    {
        $user = Auth::user();
        
        $monthlyIncome = $user->getMonthlyIncome();
        $monthlyExpense = $user->getMonthlyExpense();
        $balance = $user->getBalance();
        
        return response()->json([
            'income' => $monthlyIncome,
            'expense' => $monthlyExpense,
            'balance' => $balance,
        ]);
    }

    /**
     * Get category breakdown.
     */
    public function getCategoryBreakdown()
    {
        $user = Auth::user();
        
        $expenseByCategory = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
            
        $incomeByCategory = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
        
        return response()->json([
            'expense' => $expenseByCategory,
            'income' => $incomeByCategory,
        ]);
    }
}