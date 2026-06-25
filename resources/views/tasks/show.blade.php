@extends('layouts.app')

@section('title', 'Detail Tugas')
@section('breadcrumb', 'Detail Tugas')
@section('page-title', '📄 Detail Tugas')
@section('page-description', 'Informasi lengkap tugas')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card">
        <!-- Header -->
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center
                            {{ $task->status === 'completed' ? 'bg-green-100 dark:bg-green-900/30' : 
                               ($task->status === 'in_progress' ? 'bg-blue-100 dark:bg-blue-900/30' : 
                               'bg-gray-100 dark:bg-gray-700') }}">
                    <i class="fas text-xl
                              {{ $task->status === 'completed' ? 'fa-check-circle text-green-600 dark:text-green-400' : 
                                 ($task->status === 'in_progress' ? 'fa-spinner text-blue-600 dark:text-blue-400' : 
                                 'fa-circle text-gray-600 dark:text-gray-400') }}"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $task->title }}</h1>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        <span class="text-xs px-2 py-0.5 rounded-full
                                    {{ $task->priority === 'high' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 
                                       ($task->priority === 'medium' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300' : 
                                       'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300') }}">
                            {{ ucfirst($task->priority) }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full
                                    {{ $task->status === 'completed' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 
                                       ($task->status === 'in_progress' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 
                                       ($task->status === 'overdue' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 
                                       'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300')) }}">
                            {{ $task->status === 'not_started' ? 'Belum Mulai' : 
                               ($task->status === 'in_progress' ? 'Sedang Dikerjakan' : 
                               ($task->status === 'completed' ? 'Selesai' : 'Terlambat')) }}
                        </span>
                        @if($task->category)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                                {{ ucfirst($task->category) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('tasks.edit', $task) }}" 
                   class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                    <i class="fas fa-pen"></i>
                </a>
                <button onclick="confirmDelete()" 
                        class="p-2 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        
        <!-- Details -->
        <div class="space-y-4">
            @if($task->description)
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-align-left text-indigo-500 mr-1"></i> Deskripsi
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $task->description }}</p>
                </div>
            @endif
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-calendar-alt text-indigo-500 mr-1"></i> Deadline
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $task->deadline->format('l, d F Y') }}
                        @php
                            $daysLeft = $task->getDaysUntilDeadline();
                        @endphp
                        @if($task->status !== 'completed')
                            <span class="ml-2 text-xs {{ $daysLeft <= 1 ? 'text-red-500' : 'text-gray-500' }}">
                                ({{ $daysLeft <= 0 ? 'Hari ini!' : $daysLeft . ' hari lagi' }})
                            </span>
                        @endif
                    </p>
                </div>
                
                @if($task->completed_at)
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-check-circle text-green-500 mr-1"></i> Selesai
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $task->completed_at->format('l, d F Y H:i') }}
                        </p>
                    </div>
                @endif
            </div>
            
            @if($task->notes)
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-sticky-note text-indigo-500 mr-1"></i> Catatan
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $task->notes }}</p>
                </div>
            @endif
            
            @if($task->attachment)
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-paperclip text-indigo-500 mr-1"></i> Lampiran
                    </h3>
                    <a href="{{ asset('storage/' . $task->attachment) }}" 
                       target="_blank"
                       class="inline-flex items-center gap-2 text-sm text-indigo-500 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-file"></i>
                        {{ basename($task->attachment) }}
                    </a>
                </div>
            @endif
            
            <div class="text-xs text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700 pt-4">
                <p>Dibuat: {{ $task->created_at->format('d M Y H:i') }}</p>
                <p>Terakhir diupdate: {{ $task->updated_at->format('d M Y H:i') }}</p>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
            @if($task->status !== 'completed')
                <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-check"></i> Tandai Selesai
                    </button>
                </form>
            @else
                <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        <i class="fas fa-undo"></i> Buka Kembali
                    </button>
                </form>
            @endif
            
            <a href="{{ route('tasks.index') }}" 
               class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div x-data="{ show: false }"
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
                Apakah Anda yakin ingin menghapus tugas ini?<br>
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex gap-3 mt-6">
                <button @click="show = false" 
                        class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Batal
                </button>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full px-4 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition-colors">
                        <i class="fas fa-trash mr-2"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete() {
        const modal = document.querySelector('[x-data="{ show: false }"]');
        if (modal && modal.__x) {
            modal.__x.$data.show = true;
        }
    }
</script>
@endpush
@endsection