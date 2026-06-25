@extends('layouts.app')

@section('title', 'Detail Habit')
@section('breadcrumb', 'Detail Habit')
@section('page-title', '📊 Detail Habit')
@section('page-description', 'Informasi lengkap kebiasaan Anda')

@section('content')
<div class="max-w-3xl mx-auto" x-data="habitDetail()" x-init="initDetail()">
    
    {{-- ===== HEADER ===== --}}
    <div class="card">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <span class="text-5xl">{{ $habit->icon ?? '✅' }}</span>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $habit->name }}</h1>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                            {{ ucfirst($habit->target_frequency) }}
                        </span>
                        @if($habit->is_active)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                <i class="fas fa-circle text-[6px] mr-1"></i> Aktif
                            </span>
                        @else
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                <i class="fas fa-circle text-[6px] mr-1"></i> Tidak Aktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('habits.edit', $habit) }}" 
                   class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                    <i class="fas fa-pen"></i>
                </a>
                <form action="{{ route('habits.destroy', $habit) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Hapus habit ini?')"
                            class="p-2 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    {{-- ===== STATS ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
        <div class="card text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Streak</p>
            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $habit->streak }}</p>
        </div>
        <div class="card text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Best Streak</p>
            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $habit->best_streak }}</p>
        </div>
        <div class="card text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Completion Rate</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $habit->getCompletionRate() }}%</p>
        </div>
        <div class="card text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Logs</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $habit->logs()->count() }}</p>
        </div>
    </div>
    
    {{-- ===== CALENDAR HEATMAP ===== --}}
    <div class="card mt-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-calendar-alt text-indigo-500 mr-2"></i>
            Aktivitas 30 Hari Terakhir
        </h3>
        <div class="grid grid-cols-7 gap-1" x-html="calendarHtml"></div>
    </div>
    
    {{-- ===== LOGS ===== --}}
    <div class="card mt-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-list text-indigo-500 mr-2"></i>
                Riwayat Log
            </h3>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500 dark:text-gray-400">Total: {{ $habit->logs()->count() }}</span>
            </div>
        </div>
        
        <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-64 overflow-y-auto">
            @forelse($habit->logs()->orderBy('log_date', 'desc')->limit(30)->get() as $log)
                <div class="py-2 flex items-center justify-between">
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        {{ \Carbon\Carbon::parse($log->log_date)->translatedFormat('l, d M Y') }}
                    </span>
                    <span class="text-sm {{ $log->is_completed ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                        <i class="fas {{ $log->is_completed ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                        {{ $log->is_completed ? 'Selesai ✅' : 'Terlewat ❌' }}
                    </span>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>Belum ada log untuk habit ini</p>
                </div>
            @endforelse
        </div>
    </div>
    
    {{-- ===== ACTIONS ===== --}}
    <div class="flex flex-wrap gap-3 mt-6">
        <button @click="toggleToday()" 
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors font-medium">
            <i class="fas fa-check"></i> Tandai Hari Ini
        </button>
        <a href="{{ route('habits.index') }}" 
           class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

@push('scripts')
<script>
    function habitDetail() {
        return {
            calendarHtml: '',
            
            initDetail() {
                this.generateCalendar();
            },
            
            generateCalendar() {
                const logs = @json($habit->logs()->pluck('is_completed', 'log_date')->toArray());
                const today = new Date();
                let html = '';
                
                // Header hari
                const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                days.forEach(day => {
                    html += `<div class="text-center text-xs text-gray-500 dark:text-gray-400 font-medium py-1">${day}</div>`;
                });
                
                // 30 hari terakhir
                for (let i = 29; i >= 0; i--) {
                    const date = new Date(today);
                    date.setDate(date.getDate() - i);
                    const dateStr = date.toISOString().split('T')[0];
                    const isCompleted = logs[dateStr] || false;
                    const isToday = i === 0;
                    
                    html += `
                        <div class="aspect-square rounded-lg flex items-center justify-center text-xs font-medium
                                    ${isCompleted ? 'bg-emerald-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-400'}
                                    ${isToday ? 'ring-2 ring-indigo-500' : ''}
                                    transition-colors duration-200">
                            ${date.getDate()}
                        </div>
                    `;
                }
                
                this.calendarHtml = html;
            },
            
            toggleToday() {
                fetch(`/habits/{{ $habit->id }}/log`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        is_completed: true
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.showToast(data.message, 'success');
                        setTimeout(() => location.reload(), 500);
                    }
                })
                .catch(() => {
                    window.showToast('Gagal mengubah status habit', 'error');
                });
            }
        };
    }
</script>
@endpush
@endsection