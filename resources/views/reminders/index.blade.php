@extends('layouts.app')

@section('title', 'Reminder')
@section('breadcrumb', 'Reminder')
@section('page-title', '🔔 Reminder')
@section('page-description', 'Kelola pengingat penting Anda')

@section('content')
<div x-data="reminderManager()" x-init="initReminders()" class="space-y-6">
    
    {{-- ===== STATISTIK ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Reminder</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <i class="fas fa-bell text-xl text-indigo-600 dark:text-indigo-400"></i>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Akan Datang</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['upcoming'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <i class="fas fa-clock text-xl text-emerald-600 dark:text-emerald-400"></i>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Terlewat</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['overdue'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-xl text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sudah Diberitahu</p>
                    <p class="text-2xl font-bold text-gray-500 dark:text-gray-400">{{ $stats['notified'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-gray-500 dark:text-gray-400"></i>
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
                       placeholder="Cari reminder..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            
            <div class="flex flex-wrap gap-2">
                <select x-model="type" 
                        @change="applyFilters()"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="all">📋 Semua Tipe</option>
                    @foreach(\App\Models\Reminder::getTypes() as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                
                <select x-model="status" 
                        @change="applyFilters()"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="all">📊 Semua Status</option>
                    <option value="upcoming">🕐 Akan Datang</option>
                    <option value="overdue">⚠️ Terlewat</option>
                    <option value="notified">✅ Sudah Diberitahu</option>
                </select>
                
                <a href="{{ route('reminders.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-xl hover:from-indigo-600 hover:to-purple-600 transition-all duration-200 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Tambah Reminder</span>
                </a>
            </div>
        </div>
    </div>
    
    {{-- ===== REMINDER LIST ===== --}}
    <div class="card">
        @if(isset($reminders) && $reminders->count() > 0)
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($reminders as $reminder)
                    <div class="py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 px-2 rounded-lg transition-colors group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <span class="text-2xl flex-shrink-0">{{ $reminder->getTypeIcon() }}</span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-medium text-gray-900 dark:text-white truncate">
                                            {{ $reminder->title }}
                                        </h3>
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                            {{ $reminder->getTypeLabel() }}
                                        </span>
                                        <span class="text-xs px-2 py-0.5 rounded-full
                                                    {{ $reminder->getStatus() == 'upcoming' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 
                                                       ($reminder->getStatus() == 'overdue' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 
                                                       'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400') }}">
                                            <i class="fas {{ $reminder->getStatus() == 'upcoming' ? 'fa-clock' : ($reminder->getStatus() == 'overdue' ? 'fa-exclamation-circle' : 'fa-check-circle') }} mr-1 text-[10px]"></i>
                                            {{ $reminder->getStatus() == 'upcoming' ? 'Akan Datang' : ($reminder->getStatus() == 'overdue' ? 'Terlewat' : 'Sudah Diberitahu') }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        <span>
                                            <i class="far fa-clock mr-1"></i>
                                            {{ \Carbon\Carbon::parse($reminder->reminder_time)->translatedFormat('d M Y H:i') }}
                                        </span>
                                        <span>
                                            <i class="fas fa-hourglass-half mr-1"></i>
                                            {{ $reminder->getTimeRemaining() }}
                                        </span>
                                        @if($reminder->description)
                                            <span class="truncate max-w-[150px]">
                                                {{ $reminder->description }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-1 flex-shrink-0 opacity-70 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('reminders.edit', $reminder) }}" 
                                   class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                    <i class="fas fa-pen text-sm"></i>
                                </a>
                                @if($reminder->getStatus() != 'notified')
                                    <button @click="markNotified({{ $reminder->id }})" 
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors">
                                        <i class="fas fa-check text-sm"></i>
                                    </button>
                                @endif
                                <form action="{{ route('reminders.destroy', $reminder) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Hapus reminder ini?')"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if(isset($reminders) && $reminders instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $reminders->hasPages())
                <div class="mt-4">
                    {{ $reminders->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bell-slash text-3xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada reminder</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Buat pengingat untuk tugas penting Anda</p>
                <a href="{{ route('reminders.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors mt-4">
                    <i class="fas fa-plus"></i> Tambah Reminder
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function reminderManager() {
        return {
            search: '',
            type: 'all',
            status: 'all',
            
            initReminders() {
                this.$watch('search', () => this.debounceApplyFilters());
                this.$watch('type', () => this.applyFilters());
                this.$watch('status', () => this.applyFilters());
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
                if (this.status !== 'all') params.append('status', this.status);
                
                window.location.href = '{{ route('reminders.index') }}?' + params.toString();
            },
            
            resetFilters() {
                this.search = '';
                this.type = 'all';
                this.status = 'all';
                this.applyFilters();
            },
            
            markNotified(reminderId) {
                fetch(`/reminders/${reminderId}/notify`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.showToast(data.message, 'success');
                        setTimeout(() => location.reload(), 500);
                    }
                })
                .catch(() => {
                    window.showToast('Gagal menandai reminder', 'error');
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
</style>
@endpush
@endsection