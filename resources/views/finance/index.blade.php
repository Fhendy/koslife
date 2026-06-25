@extends('layouts.app')

@section('title', 'Manajemen Keuangan')
@section('breadcrumb', 'Keuangan')
@section('page-title', '💰 Manajemen Keuangan')
@section('page-description', 'Kelola pemasukan, pengeluaran, dan keuangan Anda')

@section('content')
<div x-data="financeManager()" x-init="initFinance()" class="space-y-6">
    
   {{-- ===== SALDO & STATISTIK ===== --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <!-- Saldo Card -->
    <div class="card md:col-span-2 bg-white dark:bg-gray-800">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Saldo</p>
                <p class="text-3xl md:text-4xl font-extrabold mt-1 text-gray-900 dark:text-white">
                    Rp {{ number_format($balance ?? 0, 0, ',', '.') }}
                </p>
            </div>
            <div class="w-14 h-14 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <i class="fas fa-wallet text-2xl text-indigo-600 dark:text-indigo-400"></i>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Pemasukan Bulan Ini</p>
                <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">
                    +Rp {{ number_format($monthlyIncome ?? 0, 0, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Pengeluaran Bulan Ini</p>
                <p class="text-lg font-bold text-red-600 dark:text-red-400 mt-0.5">
                    -Rp {{ number_format($monthlyExpense ?? 0, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
    
    <!-- Total Transaksi -->
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Transaksi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $transactions->total() ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                <i class="fas fa-receipt text-xl text-blue-600 dark:text-blue-400"></i>
            </div>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
            <i class="fas fa-arrow-up text-green-500 mr-1"></i>
            {{ $transactions->where('type', 'income')->count() }} pemasukan
            <span class="mx-1">•</span>
            <i class="fas fa-arrow-down text-red-500 mr-1"></i>
            {{ $transactions->where('type', 'expense')->count() }} pengeluaran
        </p>
    </div>
    
    <!-- Rata-rata Harian -->
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Rata-rata Harian</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    Rp {{ number_format($avgDaily ?? 0, 0, ',', '.') }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                <i class="fas fa-calendar-day text-xl text-purple-600 dark:text-purple-400"></i>
            </div>
        </div>
        <div class="mt-2 flex items-center gap-2 text-xs">
            <span class="text-gray-500 dark:text-gray-400">Hari ini: </span>
            <span class="font-medium text-gray-700 dark:text-gray-300">
                Rp {{ number_format($todayExpense ?? 0, 0, ',', '.') }}
            </span>
        </div>
    </div>
</div>
    
    {{-- ===== CHART & KATEGORI ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-chart-pie text-indigo-500 mr-2"></i>
                    Grafik Keuangan
                </h3>
                <select x-model="chartPeriod" 
                        @change="updateChart()"
                        class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                    <option value="year">Tahun Ini</option>
                </select>
            </div>
            <div class="h-64">
                <canvas id="financeChart"></canvas>
            </div>
        </div>
        
        <!-- Kategori -->
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-tags text-indigo-500 mr-2"></i>
                Kategori Pengeluaran
            </h3>
            @if(isset($expenseByCategory) && $expenseByCategory->count() > 0)
                <div class="space-y-3 max-h-64 overflow-y-auto pr-2 scrollbar-hide">
                    @foreach($expenseByCategory as $category)
                        @php
                            $totalExpense = $monthlyExpense ?? 0;
                            $percentage = $totalExpense > 0 ? round(($category->total / $totalExpense) * 100) : 0;
                            $colors = [
                                'makan' => 'bg-emerald-500',
                                'transportasi' => 'bg-blue-500',
                                'jajan' => 'bg-yellow-500',
                                'belanja' => 'bg-purple-500',
                                'kos' => 'bg-red-500',
                                'internet' => 'bg-indigo-500',
                                'pendidikan' => 'bg-orange-500',
                                'hiburan' => 'bg-pink-500',
                                'lainnya' => 'bg-gray-500'
                            ];
                            $color = $colors[$category->category] ?? 'bg-gray-500';
                            $categoryLabel = ucfirst(str_replace('_', ' ', $category->category));
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-700 dark:text-gray-300">
                                    <span class="inline-block w-3 h-3 rounded-full {{ $color }} mr-2"></span>
                                    {{ $categoryLabel }}
                                </span>
                                <span class="text-gray-500 dark:text-gray-400">
                                    Rp {{ number_format($category->total, 0, ',', '.') }}
                                    <span class="ml-1 text-xs">({{ $percentage }}%)</span>
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                                <div class="{{ $color }} h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-chart-pie text-3xl mb-2 block text-gray-300 dark:text-gray-600"></i>
                    <p class="text-sm">Belum ada data pengeluaran</p>
                    <p class="text-xs mt-1">Mulai catat pengeluaran Anda</p>
                </div>
            @endif
        </div>
    </div>
    
    {{-- ===== TRANSAKSI ===== --}}
    <div class="card">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-list text-indigo-500 mr-2"></i>
                Riwayat Transaksi
            </h3>
            
            <div class="flex flex-wrap gap-2">
                <!-- Quick Add Buttons -->
                <button @click="$dispatch('open-modal', 'add-income')" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors text-sm">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Pemasukan</span>
                </button>
                
                <button @click="$dispatch('open-modal', 'add-expense')" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Pengeluaran</span>
                </button>
                
                <div class="relative group">
                    <button class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm">
                        <i class="fas fa-file-export"></i>
                        <span class="hidden sm:inline">Export</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <div class="absolute right-0 mt-1 w-40 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 hidden group-hover:block z-10">
                        <a href="{{ route('finance.export', 'pdf') }}" 
                           class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-t-lg transition-colors">
                            <i class="fas fa-file-pdf text-red-500"></i> PDF
                        </a>
                        <a href="{{ route('finance.export', 'excel') }}" 
                           class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-b-lg transition-colors">
                            <i class="fas fa-file-excel text-green-500"></i> Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="flex flex-wrap gap-3 mb-4">
            <div class="flex-1 min-w-[150px] relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" 
                       x-model="search"
                       x-debounce="300"
                       placeholder="Cari transaksi..." 
                       class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            
            <select x-model="type" 
                    @change="applyFilters()"
                    class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="all">Semua Tipe</option>
                <option value="income">📈 Pemasukan</option>
                <option value="expense">📉 Pengeluaran</option>
            </select>
            
            <select x-model="category" 
                    @change="applyFilters()"
                    class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="all">Semua Kategori</option>
                <optgroup label="📈 Pemasukan">
                    <option value="uang_saku">Uang Saku</option>
                    <option value="gaji_pkl">Gaji PKL</option>
                    <option value="freelance">Freelance</option>
                    <option value="bonus">Bonus</option>
                    <option value="lainnya">Lainnya</option>
                </optgroup>
                <optgroup label="📉 Pengeluaran">
                    <option value="makan">Makan</option>
                    <option value="transportasi">Transportasi</option>
                    <option value="jajan">Jajan</option>
                    <option value="belanja">Belanja</option>
                    <option value="kos">Kos</option>
                    <option value="internet">Internet</option>
                    <option value="pendidikan">Pendidikan</option>
                    <option value="hiburan">Hiburan</option>
                    <option value="lainnya">Lainnya</option>
                </optgroup>
            </select>
            
            <input type="date" 
                   x-model="startDate"
                   @change="applyFilters()"
                   class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            
            <span class="text-gray-400 dark:text-gray-500 self-center text-sm">→</span>
            
            <input type="date" 
                   x-model="endDate"
                   @change="applyFilters()"
                   class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            
            <button @click="resetFilters()" 
                    class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                <i class="fas fa-times"></i> Reset
            </button>
        </div>
        
        <!-- Transaction List -->
        @if(isset($transactions) && $transactions->count() > 0)
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($transactions as $transaction)
                    <div class="py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 px-2 rounded-lg transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <!-- Icon -->
                                <div class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0
                                            {{ $transaction->type === 'income' ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-red-100 dark:bg-red-900/30' }}">
                                    <i class="fas {{ $transaction->type === 'income' ? 'fa-arrow-up text-emerald-600 dark:text-emerald-400' : 'fa-arrow-down text-red-600 dark:text-red-400' }}"></i>
                                </div>
                                
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ $transaction->description }}
                                        </p>
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                            {{ $transaction->getCategoryLabel() }}
                                        </span>
                                        @if($transaction->is_debt)
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400">
                                                <i class="fas fa-handshake mr-1"></i>
                                                Hutang
                                            </span>
                                        @endif
                                        @if($transaction->payment_status === 'unpaid')
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                Belum Dibayar
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        <span>
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            {{ \Carbon\Carbon::parse($transaction->transaction_date)->translatedFormat('d M Y') }}
                                        </span>
                                        @if($transaction->debtor_name)
                                            <span>
                                                <i class="fas fa-user mr-1"></i>
                                                {{ $transaction->debtor_name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <p class="font-semibold {{ $transaction->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}
                                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </p>
                                
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <form action="{{ route('finance.destroy', $transaction) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')"
                                                class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-coins text-3xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada transaksi</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mulai catat pemasukan dan pengeluaran Anda</p>
                <div class="flex flex-wrap items-center justify-center gap-3 mt-4">
                    <button @click="$dispatch('open-modal', 'add-income')" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors">
                        <i class="fas fa-plus"></i> Tambah Pemasukan
                    </button>
                    <button @click="$dispatch('open-modal', 'add-expense')" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fas fa-plus"></i> Tambah Pengeluaran
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ===== MODALS ===== --}}
@include('components.modals.add-income')
@include('components.modals.add-expense')

@push('scripts')
<script>
    function financeManager() {
        return {
            search: '',
            type: 'all',
            category: 'all',
            startDate: '',
            endDate: '',
            chartPeriod: 'week',
            chart: null,
            
            initFinance() {
                this.initChart();
                
                // Watch for filter changes
                this.$watch('search', () => this.debounceApplyFilters());
            },
            
            debounceApplyFilters() {
                clearTimeout(this._filterTimer);
                this._filterTimer = setTimeout(() => {
                    this.applyFilters();
                }, 400);
            },
            
            applyFilters() {
                const params = new URLSearchParams();
                if (this.search) params.append('search', this.search);
                if (this.type !== 'all') params.append('type', this.type);
                if (this.category !== 'all') params.append('category', this.category);
                if (this.startDate) params.append('start_date', this.startDate);
                if (this.endDate) params.append('end_date', this.endDate);
                
                window.location.href = '{{ route('finance.index') }}?' + params.toString();
            },
            
            resetFilters() {
                this.search = '';
                this.type = 'all';
                this.category = 'all';
                this.startDate = '';
                this.endDate = '';
                this.applyFilters();
            },
            
            initChart() {
                const ctx = document.getElementById('financeChart');
                if (!ctx) {
                    console.warn('Chart canvas not found');
                    return;
                }
                
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#9CA3AF' : '#6B7280';
                const gridColor = isDark ? '#374151' : '#E5E7EB';
                
                // Get chart data from server
                fetch('{{ route('finance.summary') }}')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const chartData = this.processChartData(data);
                        
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: chartData.labels,
                                datasets: [
                                    {
                                        label: 'Pemasukan',
                                        data: chartData.income,
                                        backgroundColor: 'rgba(34, 197, 94, 0.6)',
                                        borderColor: '#22C55E',
                                        borderWidth: 2,
                                        borderRadius: 4
                                    },
                                    {
                                        label: 'Pengeluaran',
                                        data: chartData.expense,
                                        backgroundColor: 'rgba(239, 68, 68, 0.6)',
                                        borderColor: '#EF4444',
                                        borderWidth: 2,
                                        borderRadius: 4
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        labels: {
                                            color: textColor,
                                            usePointStyle: true,
                                            pointStyle: 'circle',
                                            padding: 20
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: isDark ? '#1F2937' : '#FFFFFF',
                                        titleColor: isDark ? '#FFFFFF' : '#1F2937',
                                        bodyColor: isDark ? '#9CA3AF' : '#6B7280',
                                        borderColor: isDark ? '#374151' : '#E5E7EB',
                                        borderWidth: 1,
                                        padding: 12,
                                        cornerRadius: 8,
                                        callbacks: {
                                            label: function(context) {
                                                return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            color: textColor,
                                            callback: function(value) {
                                                return 'Rp ' + value.toLocaleString('id-ID');
                                            }
                                        },
                                        grid: {
                                            color: gridColor,
                                            drawBorder: false
                                        }
                                    },
                                    x: {
                                        grid: {
                                            color: gridColor,
                                            drawBorder: false,
                                            display: false
                                        },
                                        ticks: {
                                            color: textColor,
                                            maxRotation: 45,
                                            minRotation: 30
                                        }
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error loading chart:', error);
                        // Show fallback message
                        const canvas = ctx.getContext('2d');
                        canvas.fillStyle = isDark ? '#6B7280' : '#9CA3AF';
                        canvas.font = '14px sans-serif';
                        canvas.textAlign = 'center';
                        canvas.textBaseline = 'middle';
                        canvas.fillText('📊 Gagal memuat grafik', ctx.width / 2, ctx.height / 2);
                    });
            },
            
            processChartData(data) {
                if (!data || !Array.isArray(data)) {
                    return { labels: [], income: [], expense: [] };
                }
                const labels = data.map(d => d.date || '');
                const income = data.map(d => d.income || 0);
                const expense = data.map(d => d.expense || 0);
                return { labels, income, expense };
            },
            
            updateChart() {
                if (this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }
                this.initChart();
            }
        };
    }
</script>
@endpush

@push('styles')
<style>
    /* ===== CARD ===== */
    .card {
        transition: all 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .dark .card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }
    [x-cloak] { display: none !important; }
    
    /* ===== SCROLLBAR ===== */
    .scrollbar-hide::-webkit-scrollbar {
        width: 4px;
    }
    .scrollbar-hide::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-hide::-webkit-scrollbar-thumb {
        background: #D1D5DB;
        border-radius: 2px;
    }
    .dark .scrollbar-hide::-webkit-scrollbar-thumb {
        background: #4B5563;
    }
    .scrollbar-hide {
        scrollbar-width: thin;
    }
    
    /* ===== SALDO CARD - PERBAIKAN WARNA ===== */
    .saldo-card {
        background: linear-gradient(135deg, #4F46E5, #7C3AED);
    }
    
    .saldo-card .saldo-amount {
        color: #ffffff;
        text-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .saldo-card .saldo-income {
        color: #6EE7B7; /* emerald-300 */
        text-shadow: 0 1px 4px rgba(0,0,0,0.1);
    }
    
    .saldo-card .saldo-expense {
        color: #FCA5A5; /* rose-300 */
        text-shadow: 0 1px 4px rgba(0,0,0,0.1);
    }
    
    .saldo-card .saldo-label {
        color: rgba(255,255,255,0.6);
    }
</style>
@endpush
@endsection