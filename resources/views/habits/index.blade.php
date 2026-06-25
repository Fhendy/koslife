@extends('layouts.app')

@section('title', 'Habit Tracker')
@section('breadcrumb', 'Habit Tracker')
@section('page-title', '✅ Habit Tracker')
@section('page-description', 'Bangun kebiasaan baik setiap hari')

@section('content')
<div x-data="habitManager()" x-init="initHabits()" class="space-y-6">
    
    {{-- ===== STATISTIK ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Habit</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <i class="fas fa-list-check text-xl text-indigo-600 dark:text-indigo-400"></i>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Selesai Hari Ini</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['completed_today'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-emerald-600 dark:text-emerald-400"></i>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Streak</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['streak'] ?? 0 }} hari</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <i class="fas fa-fire text-xl text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Completion Rate</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['completion_rate'] ?? 0 }}%</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <i class="fas fa-chart-simple text-xl text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>
    </div>
    
    {{-- ===== FILTER & SEARCH ===== --}}
    <div class="card">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" 
                       x-model="search"
                       x-debounce="300"
                       placeholder="Cari habit..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            
            <div class="flex flex-wrap gap-2">
                <select x-model="filterStatus" 
                        @change="applyFilters()"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="all">📊 Semua</option>
                    <option value="active">✅ Aktif</option>
                    <option value="inactive">⏸️ Tidak Aktif</option>
                </select>
                
                <a href="{{ route('habits.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-xl hover:from-indigo-600 hover:to-purple-600 transition-all duration-200 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Tambah Habit</span>
                </a>
            </div>
        </div>
    </div>
    
    {{-- ===== HABIT GRID ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($habits as $habit)
            <div class="card hover:shadow-lg transition-all duration-200 group" 
                 x-data="{ expanded: false }">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <span class="text-3xl flex-shrink-0">{{ $habit->icon ?? '✅' }}</span>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-gray-900 dark:text-white truncate">
                                {{ $habit->name }}
                            </h3>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                    {{ ucfirst($habit->target_frequency) }}
                                </span>
                                @if($habit->is_active)
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                        <i class="fas fa-circle text-[6px] mr-1"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-circle text-[6px] mr-1"></i>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button @click="toggleHabit({{ $habit->id }})" 
                                class="p-2 rounded-lg transition-all duration-200
                                       {{ $habit->isLoggedToday() ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 hover:text-emerald-600 dark:hover:text-emerald-400' }}">
                            <i class="fas fa-check text-sm"></i>
                        </button>
                        
                        <div class="relative" x-data="{ showDropdown: false }" @click.away="showDropdown = false">
                            <button @click="showDropdown = !showDropdown" 
                                    class="p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <i class="fas fa-ellipsis-vertical text-sm"></i>
                            </button>
                            
                            <div x-show="showDropdown" 
                                 x-transition
                                 class="absolute right-0 mt-1 w-40 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden z-10">
                                <a href="{{ route('habits.edit', $habit) }}" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-pen text-xs"></i> Edit
                                </a>
                                <a href="{{ route('habits.show', $habit) }}" 
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-eye text-xs"></i> Detail
                                </a>
                                <form action="{{ route('habits.toggle', $habit) }}" method="POST" class="block">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" 
                                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left">
                                        <i class="fas {{ $habit->is_active ? 'fa-pause' : 'fa-play' }} text-xs"></i>
                                        {{ $habit->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form action="{{ route('habits.destroy', $habit) }}" method="POST" class="block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Hapus habit ini?')"
                                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-left">
                                        <i class="fas fa-trash text-xs"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Streak & Stats -->
                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-4">
                            <span class="text-gray-500 dark:text-gray-400">
                                🔥 Streak: <strong class="text-orange-600 dark:text-orange-400">{{ $habit->streak }}</strong>
                            </span>
                            <span class="text-gray-500 dark:text-gray-400">
                                🏆 Best: <strong class="text-purple-600 dark:text-purple-400">{{ $habit->best_streak }}</strong>
                            </span>
                        </div>
                        <button @click="expanded = !expanded" 
                                class="text-xs text-indigo-500 hover:text-indigo-600 transition-colors">
                            <span x-text="expanded ? 'Sembunyikan' : 'Lihat Detail'"></span>
                        </button>
                    </div>
                    
                    <!-- Expanded Detail -->
                    <div x-show="expanded" 
                         x-collapse
                         class="mt-3 space-y-3">
                        <!-- Progress Bar -->
                        <div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                <span>Progress Bulan Ini</span>
                                <span>{{ $habit->getCompletionRate() }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-gradient-to-r from-indigo-400 to-purple-500 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $habit->getCompletionRate() }}%">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Log Status -->
                        <div class="flex items-center gap-2 text-xs">
                            <span class="text-gray-500 dark:text-gray-400">Hari ini:</span>
                            @if($habit->isLoggedToday())
                                <span class="text-emerald-600 dark:text-emerald-400 font-medium">
                                    <i class="fas fa-check-circle mr-1"></i> Sudah dilakukan ✅
                                </span>
                            @else
                                <span class="text-gray-400 dark:text-gray-500">
                                    <i class="fas fa-clock mr-1"></i> Belum dilakukan
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-3xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada habit</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mulai bangun kebiasaan baik Anda</p>
                <a href="{{ route('habits.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors mt-4">
                    <i class="fas fa-plus"></i> Tambah Habit
                </a>
            </div>
        @endforelse
    </div>
    
{{-- ===== PAGINATION ===== --}}
@if(isset($habits) && method_exists($habits, 'hasPages') && $habits->hasPages())
    <div class="card">
        {{ $habits->links() }}
    </div>
@endif
</div>

@push('scripts')
<script>
    function habitManager() {
        return {
            search: '',
            filterStatus: 'all',
            
            initHabits() {
                this.$watch('search', () => this.debounceApplyFilters());
                this.$watch('filterStatus', () => this.applyFilters());
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
                if (this.filterStatus !== 'all') params.append('status', this.filterStatus);
                
                window.location.href = '{{ route('habits.index') }}?' + params.toString();
            },
            
            toggleHabit(habitId) {
                // Toggle habit via AJAX
                fetch(`/habits/${habitId}/log`, {
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
                .catch(error => {
                    window.showToast('Gagal mengubah status habit', 'error');
                });
            }
        };
    }
</script>
@endpush

@push('styles')
<style>
    [x-cloak] { display: none !important; }
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
    /* Alpine collapse transition */
    .x-collapse {
        overflow: hidden;
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endpush
@endsection