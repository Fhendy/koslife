<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan - KosLife</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 40px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #4F46E5;
            font-size: 28px;
            margin: 0;
        }
        .header p {
            color: #666;
            margin: 5px 0 0;
        }
        .summary {
            display: flex;
            justify-content: space-around;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .summary-item {
            text-align: center;
        }
        .summary-item .label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
        }
        .summary-item .value {
            font-size: 20px;
            font-weight: bold;
            margin-top: 5px;
        }
        .summary-item .value.income { color: #22C55E; }
        .summary-item .value.expense { color: #EF4444; }
        .summary-item .value.balance { color: #4F46E5; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #4F46E5;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }
        tr:hover {
            background: #f9fafb;
        }
        .badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-income { background: #dcfce7; color: #166534; }
        .badge-expense { background: #fee2e2; color: #991b1b; }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #888;
            font-size: 12px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>💰 Laporan Keuangan KosLife</h1>
        <p>{{ now()->translatedFormat('l, d F Y') }}</p>
        <p style="color: #888; font-size: 12px;">Periode: {{ \Carbon\Carbon::now()->startOfMonth()->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Pemasukan</div>
            <div class="value income">+Rp {{ number_format($transactions->where('type', 'income')->sum('amount'), 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Pengeluaran</div>
            <div class="value expense">-Rp {{ number_format($transactions->where('type', 'expense')->sum('amount'), 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Saldo Akhir</div>
            <div class="value balance">Rp {{ number_format($transactions->where('type', 'income')->sum('amount') - $transactions->where('type', 'expense')->sum('amount'), 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Transaksi</div>
            <div class="value" style="color: #6B7280;">{{ $transactions->count() }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>Tipe</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->translatedFormat('d M Y') }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>{{ $transaction->getCategoryLabel() }}</td>
                    <td>
                        <span class="badge {{ $transaction->type === 'income' ? 'badge-income' : 'badge-expense' }}">
                            {{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                        </span>
                    </td>
                    <td class="text-right font-bold" style="color: {{ $transaction->type === 'income' ? '#22C55E' : '#EF4444' }}">
                        {{ $transaction->type === 'income' ? '+' : '-' }}
                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 40px; color: #888;">
                        <i class="fas fa-inbox" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
                        Belum ada transaksi
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f8f9fa; font-weight: bold;">
                <td colspan="4" class="text-right">Total</td>
                <td class="text-right">
                    Rp {{ number_format($transactions->sum('amount'), 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Dicetak dari KosLife - Personal Dashboard Anak Kos</p>
        <p style="margin-top: 5px;">© {{ date('Y') }} KosLife. All rights reserved.</p>
    </div>
</body>
</html>