@extends('layouts.app')

@section('title', 'Statistik Fokus')
@section('breadcrumb', 'Statistik Fokus')
@section('page-title', '📊 Statistik Fokus')
@section('page-description', 'Analisis produktivitas Anda')

@section('content')
<div x-data="statsManager()" x-init="initStats()" class="space-y-6">
    
    {{-- ===== OVERVIEW ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Sesi</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.total_sessions || 0"></p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <i class="fas fa-clock text-xl text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Jam</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.total_hours || 0"></p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-hourglass text-xl text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Rata-rata Harian</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.avg_daily || 0"></p>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <i class="fas fa-chart-line text-xl text-emerald-600 dark:text-emerald-400"></i>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Best Streak</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.best_streak || 0"></p>
                </div>
                <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <i class="fas fa-fire text-xl text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
        </div>
    </div>
    
    {{-- ===== CHART ===== --}}
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>
            Grafik Fokus Mingguan
        </h3>
        <div class="h-64">
            <canvas id="focusChart"></canvas>
        </div>
    </div>
    
    {{-- ===== BY TYPE ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-tags text-indigo-500 mr-2"></i>
                Sesi per Tipe
            </h3>
            <div class="space-y-3" x-html="byTypeHtml"></div>
        </div>
        
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-calendar text-indigo-500 mr-2"></i>
                Aktivitas Bulan Ini
            </h3>
            <div class="space-y-3" x-html="monthlyHtml"></div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function statsManager() {
        return {
            stats: {},
            chart: null,
            byTypeHtml: '',
            monthlyHtml: '',
            
            initStats() {
                this.loadStats();
            },
            
            loadStats() {
                fetch('{{ route('focus.stats') }}')
                    .then(response => response.json())
                    .then(data => {
                        this.stats = data;
                        this.renderByType(data);
                        this.renderMonthly(data);
                        this.initChart(data);
                    });
            },
            
            renderByType(data) {
                if (!data.by_type || data.by_type.length === 0) {
                    this.byTypeHtml = `
                        <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                            <p>Belum ada data</p>
                        </div>
                    `;
                    return;
                }
                
                let html = '';
                data.by_type.forEach(item => {
                    const colors = {
                        study: 'bg-blue-500',
                        pkl: 'bg-red-500',
                        deep_work: 'bg-purple-500',
                        custom: 'bg-gray-500'
                    };
                    const labels = {
                        study: '📚 Belajar',
                        pkl: '💼 PKL',
                        deep_work: '🧠 Deep Work',
                        custom: '⏱️ Custom'
                    };
                    
                    const total = data.by_type.reduce((sum, i) => sum + i.total_minutes, 0);
                    const percentage = total > 0 ? Math.round((item.total_minutes / total) * 100) : 0;
                    
                    html += `
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-700 dark:text-gray-300">${labels[item.session_type] || item.session_type}</span>
                                <span class="text-gray-500 dark:text-gray-400">
                                    ${Math.round(item.total_minutes / 60)} jam (${percentage}%)
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                                <div class="${colors[item.session_type] || 'bg-gray-500'} h-2 rounded-full transition-all duration-500" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                });
                
                this.byTypeHtml = html;
            },
            
            renderMonthly(data) {
                if (!data.monthly || data.monthly.length === 0) {
                    this.monthlyHtml = `
                        <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                            <p>Belum ada data</p>
                        </div>
                    `;
                    return;
                }
                
                let html = '';
                data.monthly.forEach(day => {
                    const color = day.minutes > 0 ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-700';
                    const height = day.minutes > 0 ? Math.min((day.minutes / 60) * 2, 100) : 5;
                    
                    html += `
                        <div class="flex flex-col items-center">
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-20 overflow-hidden relative">
                                <div class="absolute bottom-0 w-full ${color} rounded-full transition-all duration-500" style="height: ${height}%"></div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">${day.date}</span>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">${Math.round(day.minutes / 60)}j</span>
                        </div>
                    `;
                });
                
                this.monthlyHtml = html;
            },
            
            initChart(data) {
                const ctx = document.getElementById('focusChart');
                if (!ctx) return;
                
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#9CA3AF' : '#6B7280';
                const gridColor = isDark ? '#374151' : '#E5E7EB';
                
                this.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.weekly?.map(d => d.date) || [],
                        datasets: [{
                            label: 'Jam Fokus',
                            data: data.weekly?.map(d => d.minutes) || [],
                            backgroundColor: 'rgba(99, 102, 241, 0.6)',
                            borderColor: '#6366F1',
                            borderWidth: 2,
                            borderRadius: 4
                        }]
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
                                        const hours = context.parsed.y / 60;
                                        return hours.toFixed(1) + ' jam';
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
                                        return (value / 60).toFixed(1) + ' jam';
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
                                    color: textColor
                                }
                            }
                        }
                    }
                });
            }
        };
    }
</script>
@endpush
@endsection