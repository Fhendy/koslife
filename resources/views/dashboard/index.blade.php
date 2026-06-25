@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('page-title')
    👋 Halo, {{ auth()->user()->name }}!
@endsection

@section('page-description', 'Ringkasan aktivitas dan keuangan Anda hari ini')

@section('content')
<div x-data="dashboard()" 
     x-init="initDashboard()"
     class="space-y-6">
    
    {{-- ===== GREETING & TIME ===== --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                <span x-text="greeting"></span>, {{ auth()->user()->name }}! 
                <span class="text-2xl" x-text="emoji"></span>
            </h2>
            <p class="text-gray-500 dark:text-gray-400 flex items-center gap-2 mt-1">
                <i class="far fa-calendar-alt"></i>
                <span x-text="formattedDate"></span>
                <span class="mx-2">•</span>
                <i class="far fa-clock"></i>
                <span x-text="currentTime" class="font-mono"></span>
            </p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Online</span>
            </div>
            <button @click="refreshData" 
                    class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-sync" :class="{'animate-spin': refreshing}"></i>
                <span>Refresh</span>
            </button>
        </div>
    </div>
    
    {{-- ===== STATS GRID ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Balance Card -->
        <div class="card hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Saldo</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        Rp {{ number_format($financial['balance'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/20 flex items-center justify-center">
                    <i class="fas fa-wallet text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-3 text-sm">
                <span class="text-green-600 dark:text-green-400">
                    <i class="fas fa-arrow-up mr-1"></i>
                    +Rp {{ number_format($financial['income'], 0, ',', '.') }}
                </span>
                <span class="text-gray-300 dark:text-gray-600">|</span>
                <span class="text-red-600 dark:text-red-400">
                    <i class="fas fa-arrow-down mr-1"></i>
                    -Rp {{ number_format($financial['expense'], 0, ',', '.') }}
                </span>
            </div>
        </div>
        
        <!-- Tasks Card -->
        <div class="card hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tugas Aktif</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ $taskSummary['active'] }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center">
                    <i class="fas fa-tasks text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-3 text-sm">
                <span class="text-green-600 dark:text-green-400">
                    <i class="fas fa-check mr-1"></i>
                    {{ $taskSummary['completed'] }} selesai
                </span>
                @if($taskSummary['overdue'] > 0)
                    <span class="text-red-600 dark:text-red-400">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $taskSummary['overdue'] }} terlambat
                    </span>
                @endif
            </div>
        </div>
        
        <!-- Focus Card -->
        <div class="card hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Fokus Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ number_format($productivity['focus_today'], 1) }} jam
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/20 flex items-center justify-center">
                    <i class="fas fa-clock text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Target {{ $productivity['daily_target'] }} jam</span>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">
                        {{ min(round(($productivity['focus_today'] / $productivity['daily_target']) * 100), 100) }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                    <div class="bg-purple-500 h-2 rounded-full transition-all duration-500"
                         style="width: {{ min(round(($productivity['focus_today'] / $productivity['daily_target']) * 100), 100) }}%">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Streak Card -->
        <div class="card hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Habit Streak</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ $productivity['streak'] }} hari
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/20 flex items-center justify-center">
                    <i class="fas fa-fire text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @if($productivity['streak'] >= 30)
                        <i class="fas fa-trophy text-yellow-500 mr-1"></i> Luar biasa! 🏆
                    @elseif($productivity['streak'] >= 14)
                        <i class="fas fa-star text-yellow-500 mr-1"></i> Sangat konsisten! 💪
                    @elseif($productivity['streak'] >= 7)
                        <i class="fas fa-check-circle text-green-500 mr-1"></i> Mulai konsisten! 🔥
                    @elseif($productivity['streak'] > 0)
                        <i class="fas fa-check-circle text-blue-500 mr-1"></i> Pertahankan! 💪
                    @else
                        <i class="fas fa-plus-circle text-gray-400 mr-1"></i> Mulai habit hari ini!
                    @endif
                </p>
            </div>
        </div>
    </div>
    
    {{-- ===== CHARTS & SCHEDULE ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-chart-line text-indigo-500 mr-2"></i>
                    Grafik Keuangan Mingguan
                </h3>
                <select class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                    <option value="year">Tahun Ini</option>
                </select>
            </div>
            <div class="h-64 relative">
                <canvas id="financeChart"></canvas>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-calendar-day text-indigo-500 mr-2"></i>
                    Jadwal Hari Ini
                </h3>
                <a href="{{ route('calendar.index') }}" 
                   class="text-sm text-indigo-500 hover:text-indigo-600 transition-colors">
                    Lihat semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            @if($todaySchedules->count() > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto pr-2 scrollbar-hide">
                    @foreach($todaySchedules as $schedule)
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div class="w-1 min-h-[50px] rounded-full flex-shrink-0" style="background-color: {{ $schedule->color }}"></div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <p class="font-medium text-gray-900 dark:text-white truncate">{{ $schedule->title }}</p>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 whitespace-nowrap ml-2">
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                    <i class="fas fa-map-marker-alt mr-1 text-xs"></i>
                                    {{ $schedule->location ?? 'Tidak ada lokasi' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-calendar-day text-4xl mb-3 block text-gray-300 dark:text-gray-600"></i>
                    <p class="font-medium">Tidak ada jadwal hari ini</p>
                    <p class="text-sm mt-1">Santai dulu, tidak ada kegiatan! 🎉</p>
                    <a href="{{ route('schedules.create') }}" class="inline-block mt-4 text-sm text-indigo-500 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-plus mr-1"></i> Tambah Jadwal
                    </a>
                </div>
            @endif
        </div>
    </div>
    
    {{-- ===== MEAL BUDGET & TASKS ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Meal Budget -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-utensils text-indigo-500 mr-2"></i>
                    Budget Makan Hari Ini
                </h3>
                <a href="{{ route('meal-budget.index') }}" class="text-sm text-indigo-500 hover:text-indigo-600 transition-colors">
                    Kelola <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Budget Harian</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            Rp {{ number_format($mealBudget['budget'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sisa</p>
                        <p class="text-2xl font-bold {{ $mealBudget['remaining'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            Rp {{ number_format($mealBudget['remaining'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-500 dark:text-gray-400">Terpakai</span>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">
                            Rp {{ number_format($mealBudget['spent'], 0, ',', '.') }} ({{ $mealBudget['percentage'] }}%)
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div class="h-3 rounded-full transition-all duration-500"
                             style="width: {{ $mealBudget['percentage'] }}%; background: linear-gradient(90deg, #6366f1, #8b5cf6);">
                        </div>
                    </div>
                    @if($mealBudget['remaining'] < 0)
                        <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-triangle mr-1"></i> Budget habis!</p>
                    @elseif($mealBudget['percentage'] > 80)
                        <p class="text-xs text-yellow-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i> Budget hampir habis, hemat ya!</p>
                    @endif
                </div>
                <form action="{{ route('meal-budget.store') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="number" name="amount" placeholder="Jumlah..." 
                           class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <select name="meal_type" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="breakfast">Sarapan</option>
                        <option value="lunch">Makan Siang</option>
                        <option value="dinner">Makan Malam</option>
                        <option value="snack">Camilan</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors text-sm">
                        <i class="fas fa-plus"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Upcoming Tasks -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-clock text-indigo-500 mr-2"></i>
                    Deadline Terdekat
                </h3>
                <a href="{{ route('tasks.index') }}" class="text-sm text-indigo-500 hover:text-indigo-600 transition-colors">
                    Lihat semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            @if($upcomingTasks->count() > 0)
                <div class="space-y-2 max-h-64 overflow-y-auto pr-2 scrollbar-hide">
                    @foreach($upcomingTasks as $task)
                        @php $daysLeft = \Carbon\Carbon::parse($task->deadline)->diffInDays(); @endphp
                        <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="w-2 h-2 rounded-full flex-shrink-0 
                                            {{ $task->priority === 'high' ? 'bg-red-500' : ($task->priority === 'medium' ? 'bg-yellow-500' : 'bg-blue-500') }}">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm text-gray-900 dark:text-white truncate">{{ $task->title }}</p>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        <i class="far fa-calendar-alt mr-1"></i>
                                        {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                            <span class="text-xs font-medium whitespace-nowrap ml-2
                                        {{ $daysLeft <= 1 ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}">
                                @if($daysLeft == 0) Hari ini!
                                @elseif($daysLeft == 1) Besok
                                @else {{ $daysLeft }} hari
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-check-circle text-4xl mb-3 block text-green-500"></i>
                    <p class="font-medium">Semua tugas selesai! 🎉</p>
                    <a href="{{ route('tasks.create') }}" class="inline-block mt-4 text-sm text-indigo-500 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-plus mr-1"></i> Tambah Tugas Baru
                    </a>
                </div>
            @endif
        </div>
    </div>
    
    {{-- ===== RECENT TRANSACTIONS ===== --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-receipt text-indigo-500 mr-2"></i>
                Transaksi Terbaru
            </h3>
            <a href="{{ route('finance.index') }}" class="text-sm text-indigo-500 hover:text-indigo-600 transition-colors">
                Lihat semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        @if($recentTransactions->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($recentTransactions as $transaction)
                    <div class="flex items-center justify-between py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 px-2 rounded-lg transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                        {{ $transaction->type === 'income' ? 'bg-green-100 dark:bg-green-900/20' : 'bg-red-100 dark:bg-red-900/20' }}">
                                <i class="fas {{ $transaction->type === 'income' ? 'fa-arrow-up text-green-600 dark:text-green-400' : 'fa-arrow-down text-red-600 dark:text-red-400' }}"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $transaction->description }}</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $transaction->getCategoryLabel() }} • {{ \Carbon\Carbon::parse($transaction->transaction_date)->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </div>
                        <p class="font-semibold {{ $transaction->type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $transaction->type === 'income' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="fas fa-receipt text-4xl mb-3 block text-gray-300 dark:text-gray-600"></i>
                <p class="font-medium">Belum ada transaksi</p>
                <p class="text-sm mt-1">Mulai catat pemasukan dan pengeluaran Anda.</p>
                <div class="flex items-center justify-center gap-2 mt-4">
                    <button @click="$dispatch('open-modal', 'add-income')" 
                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm">
                        <i class="fas fa-plus mr-1"></i> Pemasukan
                    </button>
                    <button @click="$dispatch('open-modal', 'add-expense')" 
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm">
                        <i class="fas fa-plus mr-1"></i> Pengeluaran
                    </button>
                </div>
            </div>
        @endif
    </div>
    
    {{-- ===== QUICK FOCUS & HABITS ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Quick Focus -->
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-clock text-indigo-500 mr-2"></i>
                Quick Focus Mode
            </h3>
            <div class="space-y-4">
                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('focus.index') }}" 
                       class="px-4 py-3 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-900/50 transition-colors text-sm font-medium text-center">
                        <i class="fas fa-book mr-1"></i> 25m
                    </a>
                    <a href="{{ route('focus.index') }}" 
                       class="px-4 py-3 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-900/50 transition-colors text-sm font-medium text-center">
                        <i class="fas fa-briefcase mr-1"></i> 50m
                    </a>
                    <a href="{{ route('focus.index') }}" 
                       class="px-4 py-3 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-900/50 transition-colors text-sm font-medium text-center">
                        <i class="fas fa-brain mr-1"></i> 90m
                    </a>
                </div>
                <div class="flex gap-2">
                    <input type="text" id="quick_task" placeholder="Apa yang akan kamu kerjakan?" 
                           class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <a href="{{ route('focus.index') }}" 
                       class="px-6 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors whitespace-nowrap flex items-center gap-2">
                        <i class="fas fa-play"></i> Mulai
                    </a>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i>
                    Gunakan Focus Mode untuk meningkatkan produktivitas
                </p>
            </div>
        </div>
        
        <!-- Quick Habits -->
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-check-circle text-indigo-500 mr-2"></i>
                Habit Hari Ini
            </h3>
            @if($habits->count() > 0)
                <div class="space-y-2">
                    @foreach($habits->take(5) as $habit)
                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">{{ $habit->icon ?? '✅' }}</span>
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $habit->name }}</span>
                            </div>
                            <form action="{{ route('habits.log', $habit) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="w-8 h-8 rounded-full border-2 transition-all duration-200
                                               {{ $habit->isLoggedToday() ? 'bg-green-500 border-green-500 text-white' : 'border-gray-300 dark:border-gray-600 hover:border-indigo-500' }}">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                    @if($habits->count() > 5)
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-2">+{{ $habits->count() - 5 }} habit lainnya</p>
                    @endif
                </div>
            @else
                <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-plus-circle text-3xl mb-2 block text-gray-300 dark:text-gray-600"></i>
                    <p class="text-sm">Belum ada habit</p>
                    <a href="{{ route('habits.create') }}" class="inline-block mt-2 text-sm text-indigo-500 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-plus mr-1"></i> Tambah Habit
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ===== MODALS ===== --}}
@include('components.modals.add-income')
@include('components.modals.add-expense')

@push('scripts')
<script>
function dashboard() {
    return {
        greeting: '',
        emoji: '',
        currentTime: '',
        formattedDate: '',
        refreshing: false,
        
        initDashboard() {
            this.updateTime();
            this.setGreeting();
            setInterval(() => this.updateTime(), 1000);
            this.initChart();
        },
        
        updateTime() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            this.formattedDate = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        },
        
        setGreeting() {
            const hour = new Date().getHours();
            if (hour < 12) { this.greeting = 'Selamat Pagi'; this.emoji = '🌅'; }
            else if (hour < 15) { this.greeting = 'Selamat Siang'; this.emoji = '☀️'; }
            else if (hour < 18) { this.greeting = 'Selamat Sore'; this.emoji = '🌤️'; }
            else { this.greeting = 'Selamat Malam'; this.emoji = '🌙'; }
        },
        
        refreshData() {
            this.refreshing = true;
            setTimeout(() => location.reload(), 500);
        },
        
        initChart() {
            const ctx = document.getElementById('financeChart');
            if (!ctx) return;
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#9CA3AF' : '#6B7280';
            const gridColor = isDark ? '#374151' : '#E5E7EB';
            
            new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [
                        {
                            label: 'Pengeluaran',
                            data: @json($chartData['expense']),
                            borderColor: '#EF4444',
                            backgroundColor: 'rgba(239,68,68,0.1)',
                            fill: true, tension: 0.4, pointRadius: 3,
                            pointBackgroundColor: '#EF4444', borderWidth: 2
                        },
                        {
                            label: 'Pemasukan',
                            data: @json($chartData['income']),
                            borderColor: '#22C55E',
                            backgroundColor: 'rgba(34,197,94,0.1)',
                            fill: true, tension: 0.4, pointRadius: 3,
                            pointBackgroundColor: '#22C55E', borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { labels: { color: textColor, usePointStyle: true, pointStyle: 'circle', padding: 20 } },
                        tooltip: {
                            backgroundColor: isDark ? '#1F2937' : '#FFFFFF',
                            titleColor: isDark ? '#FFFFFF' : '#1F2937',
                            bodyColor: textColor,
                            borderColor: gridColor, borderWidth: 1,
                            padding: 12, cornerRadius: 8,
                            callbacks: {
                                label: ctx => ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: textColor, callback: v => 'Rp ' + v.toLocaleString('id-ID'), maxTicksLimit: 6 },
                            grid: { color: gridColor, drawBorder: false }
                        },
                        x: {
                            grid: { color: gridColor, drawBorder: false, display: false },
                            ticks: { color: textColor, maxTicksLimit: 7 }
                        }
                    }
                }
            });
        }
    };
}
</script>
@endpush

@push('styles')
<style>
    .card {
        @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 hover:shadow-md transition-all duration-200;
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endpush
@endsection