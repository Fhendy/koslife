<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    const INCOME_CATEGORIES = [
        'uang_saku' => 'Uang Saku',
        'gaji_pkl' => 'Gaji PKL',
        'freelance' => 'Freelance',
        'bonus' => 'Bonus',
        'lainnya' => 'Lainnya'
    ];

    const EXPENSE_CATEGORIES = [
        'makan' => 'Makan',
        'transportasi' => 'Transportasi',
        'jajan' => 'Jajan',
        'belanja' => 'Belanja',
        'kos' => 'Kos',
        'internet' => 'Internet',
        'pendidikan' => 'Pendidikan',
        'hiburan' => 'Hiburan',
        'lainnya' => 'Lainnya'
    ];

    protected $fillable = [
        'user_id', 'type', 'category', 'amount',
        'description', 'transaction_date', 'is_debt',
        'debtor_name', 'payment_status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'is_debt' => 'boolean'
    ];

    // ========== RELATIONS ==========
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== SCOPES ==========
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('transaction_date', now()->month)
                     ->whereYear('transaction_date', now()->year);
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('transaction_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('transaction_date', today());
    }

    public function scopeDebt($query)
    {
        return $query->where('is_debt', true);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    // ========== HELPERS ==========
    public static function getCategories($type)
    {
        return $type === 'income' 
            ? self::INCOME_CATEGORIES 
            : self::EXPENSE_CATEGORIES;
    }

    public function getFormattedAmount()
    {
        $sign = $this->type === 'income' ? '+' : '-';
        return $sign . ' Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getCategoryLabel()
    {
        $categories = $this->type === 'income' 
            ? self::INCOME_CATEGORIES 
            : self::EXPENSE_CATEGORIES;
        
        return $categories[$this->category] ?? $this->category;
    }

    public function getTypeIcon()
    {
        return $this->type === 'income' ? 'fa-arrow-up' : 'fa-arrow-down';
    }

    public function getTypeColor()
    {
        return $this->type === 'income' ? 'green' : 'red';
    }

    public function getPaymentStatusBadge()
    {
        return match($this->payment_status) {
            'paid' => 'success',
            'unpaid' => 'danger',
            default => 'secondary'
        };
    }
}