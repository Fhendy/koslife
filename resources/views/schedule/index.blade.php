@extends('layouts.app')

@section('title', 'Daftar Jadwal')
@section('breadcrumb', 'Daftar Jadwal')
@section('page-title', '📋 Daftar Jadwal')
@section('page-description', 'Kelola semua jadwal Anda')

@section('content')
<div x-data="scheduleManager()" x-init="initSchedule()" class="space-y-6">
    
    {{-- ===== FILTER ===== --}}
    <div class="card">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" 
                       x-model="search"
                       x-debounce="300"
                       placeholder="Cari jadwal..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            
            <div class="flex flex-wrap gap-2">
                <select x-model="category" 
                        @change="applyFilters()"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="all">📂 Semua Kategori</option>
                    <option value="school">📚 Sekolah</option>
                    <option value="pkl">💼 PKL</option>
                    <option value="organization">🤝 Organisasi</option>
                    <option value="meeting">📅 Meeting</option>
                    <option value="personal">👤 Pribadi</option>
                </select>
                
                <a href="{{ route('schedules.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-xl hover:from-indigo-600 hover:to-purple-600 transition-all duration-200 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Tambah Jadwal</span>
                </a>
            </div>
        </div>
    </div>
    
    {{-- ===== SCHEDULE LIST ===== --}}
    <div class="card">
        @if(isset($schedules) && $schedules->count() > 0)
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($schedules as $schedule)
                    <div class="py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 px-2 rounded-lg transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-start gap-4 min-w-0 flex-1">
                                <div class="w-1 h-full min-h-[50px] rounded-full flex-shrink-0" 
                                     style="background-color: {{ $schedule->color }}"></div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-medium text-gray-900 dark:text-white">
                                            {{ $schedule->title }}
                                        </h3>
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                            {{ $schedule->getCategoryLabel() }}
                                        </span>
                                        @if($schedule->isToday())
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                                                <i class="fas fa-calendar-day mr-1"></i>
                                                Hari Ini
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        <span>
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->translatedFormat('d M Y') }}
                                        </span>
                                        <span>
                                            <i class="far fa-clock mr-1"></i>
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        </span>
                                        @if($schedule->location)
                                            <span>
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $schedule->location }}
                                            </span>
                                        @endif
                                        <span>
                                            <i class="fas fa-hourglass-half mr-1"></i>
                                            {{ $schedule->getDurationFormatted() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-1 flex-shrink-0 opacity-70 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('schedules.show', $schedule) }}" 
                                   class="p-2 rounded-lg text-gray-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all duration-200">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('schedules.edit', $schedule) }}" 
                                   class="p-2 rounded-lg text-gray-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all duration-200">
                                    <i class="fas fa-pen text-sm"></i>
                                </a>
                                <form action="{{ route('schedules.destroy', $schedule) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Hapus jadwal ini?')"
                                            class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $schedules->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar text-3xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada jadwal</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mulai tambahkan jadwal Anda</p>
                <a href="{{ route('schedules.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors mt-4">
                    <i class="fas fa-plus"></i> Tambah Jadwal
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function scheduleManager() {
        return {
            search: '',
            category: 'all',
            
            initSchedule() {
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
                if (this.category !== 'all') params.append('category', this.category);
                
                window.location.href = '{{ route('schedules.index') }}?' + params.toString();
            }
        };
    }
</script>
@endpush
@endsection