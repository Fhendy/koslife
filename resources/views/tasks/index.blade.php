@extends('layouts.app')

@section('title', 'Manajemen Tugas')
@section('breadcrumb', 'Tugas')
@section('page-title', '📋 Manajemen Tugas')
@section('page-description', 'Kelola semua tugas sekolah, PKL, dan aktivitas Anda')

@section('content')
<div x-data="taskManager()" x-init="initTasks()" class="space-y-6">
    
    {{-- ===== STATISTIK ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total -->
        <div class="card group hover:scale-[1.02] transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Tugas</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-tasks text-xl text-indigo-600 dark:text-indigo-400"></i>
                </div>
            </div>
            <div class="mt-2 h-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-indigo-500 rounded-full" style="width: 100%"></div>
            </div>
        </div>
        
        <!-- Active -->
        <div class="card group hover:scale-[1.02] transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aktif</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['active'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-spinner text-xl text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
            <div class="mt-2 h-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 rounded-full transition-all duration-500" 
                     style="width: {{ $stats['total'] > 0 ? ($stats['active'] / $stats['total']) * 100 : 0 }}%"></div>
            </div>
        </div>
        
        <!-- Completed -->
        <div class="card group hover:scale-[1.02] transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Selesai</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['completed'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-check-circle text-xl text-green-600 dark:text-green-400"></i>
                </div>
            </div>
            <div class="mt-2 h-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 rounded-full transition-all duration-500" 
                     style="width: {{ $stats['total'] > 0 ? ($stats['completed'] / $stats['total']) * 100 : 0 }}%"></div>
            </div>
        </div>
        
        <!-- Overdue -->
        <div class="card group hover:scale-[1.02] transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Terlambat</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['overdue'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-exclamation-triangle text-xl text-red-600 dark:text-red-400"></i>
                </div>
            </div>
            <div class="mt-2 h-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-red-500 rounded-full transition-all duration-500" 
                     style="width: {{ $stats['total'] > 0 ? ($stats['overdue'] / $stats['total']) * 100 : 0 }}%"></div>
            </div>
        </div>
    </div>
    
    {{-- ===== FILTER & SEARCH ===== --}}
    <div class="card">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" 
                       x-model="search"
                       x-debounce="300"
                       placeholder="Cari tugas..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
            </div>
            
            <!-- Filters -->
            <div class="flex flex-wrap gap-2">
                <select x-model="status" 
                        class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 cursor-pointer">
                    <option value="all">📊 Semua Status</option>
                    <option value="not_started">⏳ Belum Mulai</option>
                    <option value="in_progress">🔄 Sedang Dikerjakan</option>
                    <option value="completed">✅ Selesai</option>
                    <option value="overdue">⚠️ Terlambat</option>
                </select>
                
                <select x-model="priority" 
                        class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 cursor-pointer">
                    <option value="all">🏷️ Semua Prioritas</option>
                    <option value="high">🔴 Tinggi</option>
                    <option value="medium">🟡 Sedang</option>
                    <option value="low">🟢 Rendah</option>
                </select>
                
                <a href="{{ route('tasks.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-xl hover:from-indigo-600 hover:to-purple-600 transition-all duration-200 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Tambah Tugas</span>
                </a>
            </div>
        </div>
        
        <!-- Active Filters -->
        <div x-show="status !== 'all' || priority !== 'all' || search" 
             x-cloak
             class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <span class="text-xs text-gray-500 dark:text-gray-400">Filter aktif:</span>
            
            <span x-show="status !== 'all'" 
                  class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">
                <span x-text="status === 'not_started' ? '⏳ Belum Mulai' : status === 'in_progress' ? '🔄 Sedang Dikerjakan' : status === 'completed' ? '✅ Selesai' : '⚠️ Terlambat'"></span>
                <button @click="status = 'all'" class="hover:text-indigo-900 dark:hover:text-indigo-100">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </span>
            
            <span x-show="priority !== 'all'" 
                  class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">
                <span x-text="priority === 'high' ? '🔴 Tinggi' : priority === 'medium' ? '🟡 Sedang' : '🟢 Rendah'"></span>
                <button @click="priority = 'all'" class="hover:text-indigo-900 dark:hover:text-indigo-100">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </span>
            
            <span x-show="search" 
                  class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">
                <span>🔍 "{{ $search }}"</span>
                <button @click="search = ''" class="hover:text-indigo-900 dark:hover:text-indigo-100">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </span>
            
            <button @click="resetFilters()" 
                    class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                Reset semua
            </button>
        </div>
    </div>
    
    {{-- ===== TASK LIST ===== --}}
    <div class="card">
        @if($tasks->count() > 0)
            <!-- List Header -->
            <div class="hidden md:grid grid-cols-12 gap-3 px-2 pb-3 mb-2 border-b border-gray-200 dark:border-gray-700 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                <div class="col-span-1"></div>
                <div class="col-span-6">Tugas</div>
                <div class="col-span-2">Deadline</div>
                <div class="col-span-1 text-center">Status</div>
                <div class="col-span-2 text-right">Aksi</div>
            </div>
            
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($tasks as $task)
                    <div x-data="{ expanded: false }" 
                         class="py-3 px-2 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all duration-200 group">
                        
                        <div class="flex items-start gap-3">
                            <!-- Checkbox -->
                            <button @click="toggleComplete({{ $task->id }})" 
                                    class="mt-1 flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-200
                                           {{ $task->status === 'completed' ? 'bg-green-500 border-green-500 text-white shadow-lg shadow-green-500/30' : 'border-gray-300 dark:border-gray-600 hover:border-indigo-500 hover:scale-110' }}">
                                <i class="fas fa-check text-xs transition-all duration-200" 
                                   :class="{'opacity-100 scale-100': {{ $task->status === 'completed' ? 'true' : 'false' }}, 'opacity-0 scale-0': {{ $task->status === 'completed' ? 'false' : 'true' }}}"></i>
                            </button>
                            
                            <!-- Task Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-start gap-2">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white
                                               {{ $task->status === 'completed' ? 'line-through text-gray-400 dark:text-gray-500' : '' }}">
                                        {{ $task->title }}
                                    </h3>
                                    
                                    <!-- Priority Badge -->
                                    <span class="inline-flex items-center gap-1 text-xs px-2.5 py-0.5 rounded-full font-medium
                                                {{ $task->priority === 'high' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 
                                                   ($task->priority === 'medium' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300' : 
                                                   'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300') }}">
                                        <i class="fas {{ $task->priority === 'high' ? 'fa-arrow-up' : ($task->priority === 'medium' ? 'fa-minus' : 'fa-arrow-down') }} text-[10px]"></i>
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                    
                                    <!-- Status Badge -->
                                    <span class="inline-flex items-center gap-1 text-xs px-2.5 py-0.5 rounded-full font-medium
                                                {{ $task->status === 'completed' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 
                                                   ($task->status === 'in_progress' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 
                                                   ($task->status === 'overdue' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 
                                                   'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300')) }}">
                                        <i class="fas 
                                            {{ $task->status === 'completed' ? 'fa-check-circle' : 
                                               ($task->status === 'in_progress' ? 'fa-spinner fa-spin' : 
                                               ($task->status === 'overdue' ? 'fa-exclamation-circle' : 
                                               'fa-circle')) }} text-[10px]"></i>
                                        {{ $task->status === 'not_started' ? 'Belum Mulai' : 
                                           ($task->status === 'in_progress' ? 'Sedang Dikerjakan' : 
                                           ($task->status === 'completed' ? 'Selesai' : 'Terlambat')) }}
                                    </span>
                                </div>
                                
                                <!-- Meta Info -->
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center gap-1.5">
                                        <i class="far fa-calendar-alt"></i>
                                        {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}
                                        @php
                                            $daysLeft = \Carbon\Carbon::parse($task->deadline)->diffInDays();
                                        @endphp
                                        @if($task->status !== 'completed')
                                            <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] font-medium
                                                {{ $daysLeft <= 0 ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 
                                                   ($daysLeft <= 3 ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300' : 
                                                   'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400') }}">
                                                {{ $daysLeft <= 0 ? 'Hari ini!' : $daysLeft . ' hari' }}
                                            </span>
                                        @endif
                                    </span>
                                    
                                    @if($task->category)
                                        <span class="flex items-center gap-1.5">
                                            <i class="fas fa-tag"></i>
                                            <span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700">
                                                {{ ucfirst($task->category) }}
                                            </span>
                                        </span>
                                    @endif
                                    
                                    @if($task->attachment)
                                        <span class="flex items-center gap-1.5 text-indigo-500">
                                            <i class="fas fa-paperclip"></i>
                                            Lampiran
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center gap-0.5 flex-shrink-0 opacity-70 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('tasks.show', $task) }}" 
                                   class="p-2 rounded-lg text-gray-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all duration-200">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('tasks.edit', $task) }}" 
                                   class="p-2 rounded-lg text-gray-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all duration-200">
                                    <i class="fas fa-pen text-sm"></i>
                                </a>
                                <button @click="confirmDelete({{ $task->id }}, '{{ addslashes($task->title) }}')" 
                                        class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                                <button @click="expanded = !expanded" 
                                        class="p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-all duration-200">
                                    <i class="fas fa-chevron-down text-sm transition-transform duration-300" 
                                       :class="{'rotate-180': expanded}"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Expanded Details -->
                        <div x-show="expanded" 
                             x-collapse.duration.300ms
                             class="mt-3 ml-9 pl-4 border-l-2 border-indigo-200 dark:border-indigo-800 space-y-2">
                            
                            @if($task->description)
                                <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">📝 Deskripsi</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $task->description }}</p>
                                </div>
                            @endif
                            
                            @if($task->notes)
                                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/10 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                    <p class="text-xs font-medium text-yellow-700 dark:text-yellow-400 mb-1">📌 Catatan</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $task->notes }}</p>
                                </div>
                            @endif
                            
                            @if($task->attachment)
                                <a href="{{ asset('storage/' . $task->attachment) }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-2 px-3 py-1.5 text-sm text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors">
                                    <i class="fas fa-file-pdf"></i>
                                    Lihat Lampiran
                                </a>
                            @endif
                            
                            <div class="flex items-center gap-4 text-xs text-gray-400 dark:text-gray-500 pt-1">
                                <span>🕐 Dibuat: {{ $task->created_at->format('d M Y H:i') }}</span>
                                @if($task->completed_at)
                                    <span>✅ Selesai: {{ $task->completed_at->format('d M Y H:i') }}</span>
                                @endif
                                <span>🔄 Diupdate: {{ $task->updated_at->format('d M Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                {{ $tasks->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="relative inline-block">
                    <div class="w-24 h-24 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto">
                        <i class="fas fa-tasks text-4xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <div class="absolute -top-1 -right-1 w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <i class="fas fa-plus text-indigo-500 text-sm"></i>
                    </div>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Belum ada tugas</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mulai tambahkan tugas pertama Anda sekarang!</p>
                <div class="flex flex-wrap items-center justify-center gap-3 mt-6">
                    <a href="{{ route('tasks.create') }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Tambah Tugas
                    </a>
                    <button @click="status = 'all'; priority = 'all'; search = ''" 
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-sync-alt"></i> Reset Filter
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- ===== DELETE CONFIRMATION MODAL ===== --}}
<div x-data="{ show: false, taskId: null, taskTitle: '' }"
     x-show="show"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
     @click.away="show = false">
    
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100">
        
        <div class="text-center">
            <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600 dark:text-red-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Hapus Tugas?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                Apakah Anda yakin ingin menghapus tugas
                <br>
                <span class="font-medium text-gray-700 dark:text-gray-300" x-text="taskTitle"></span>?
                <br>
                <span class="text-red-500 text-xs">Tindakan ini tidak dapat dibatalkan!</span>
            </p>
            <div class="flex gap-3 mt-6">
                <button @click="show = false" 
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                    Batal
                </button>
                <form :action="'{{ route('tasks.index') }}/' + taskId" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full px-4 py-2.5 rounded-xl bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 transition-all duration-200 font-medium shadow-lg shadow-red-500/25">
                        <i class="fas fa-trash mr-2"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function taskManager() {
        return {
            search: '',
            status: 'all',
            priority: 'all',
            showDeleteModal: false,
            taskId: null,
            taskTitle: '',
            
            initTasks() {
                // Auto refresh when filters change
                this.$watch('search', (value) => {
                    this.debounceApplyFilters();
                });
                this.$watch('status', () => this.applyFilters());
                this.$watch('priority', () => this.applyFilters());
            },
            
            debounceApplyFilters() {
                clearTimeout(this._debounceTimer);
                this._debounceTimer = setTimeout(() => {
                    this.applyFilters();
                }, 400);
            },
            
            applyFilters() {
                const params = new URLSearchParams();
                if (this.search) params.append('search', this.search);
                if (this.status !== 'all') params.append('status', this.status);
                if (this.priority !== 'all') params.append('priority', this.priority);
                
                const url = '{{ route('tasks.index') }}?' + params.toString();
                window.location.href = url;
            },
            
            resetFilters() {
                this.search = '';
                this.status = 'all';
                this.priority = 'all';
                this.applyFilters();
            },
            
            toggleComplete(taskId) {
                fetch(`/tasks/${taskId}/toggle`, {
                    method: 'PATCH',
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
                        setTimeout(() => location.reload(), 400);
                    }
                })
                .catch(error => {
                    window.showToast('Gagal mengubah status tugas', 'error');
                });
            },
            
            confirmDelete(id, title) {
                this.taskId = id;
                this.taskTitle = title;
                this.show = true;
            }
        };
    }
</script>
@endpush

@push('styles')
<style>
    /* Alpine collapse transition */
    .x-collapse {
        overflow: hidden;
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Card hover effect */
    .card {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Smooth checkbox transition */
    .checkbox-transition {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endpush
@endsection