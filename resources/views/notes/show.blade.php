@extends('layouts.app')

@section('title', 'Detail Catatan')
@section('breadcrumb', 'Detail Catatan')
@section('page-title', '📄 Detail Catatan')
@section('page-description', 'Lihat catatan lengkap')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card" style="{{ $note->color ? 'background-color: ' . $note->color . ';' : '' }}
                            {{ $note->color ? 'border: 2px solid ' . $note->color . ';' : '' }}">
        
        <!-- Header -->
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <span class="text-3xl">{{ $note->getCategoryIcon() }}</span>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $note->title }}</h1>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                            {{ $note->getCategoryLabel() }}
                        </span>
                        @if($note->is_pinned)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">
                                <i class="fas fa-thumbtack text-[10px] mr-1"></i> Disematkan
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('notes.edit', $note) }}" 
                   class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                    <i class="fas fa-pen"></i>
                </a>
                <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Hapus catatan ini?')"
                            class="p-2 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Content -->
        <div class="prose prose-sm dark:prose-invert max-w-none">
            {!! nl2br(e($note->content)) !!}
        </div>
        
        <!-- Footer -->
        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 text-xs text-gray-500 dark:text-gray-400">
            <div class="flex flex-wrap gap-4">
                <span>
                    <i class="far fa-clock mr-1"></i>
                    Dibuat: {{ $note->created_at->translatedFormat('d M Y H:i') }}
                </span>
                <span>
                    <i class="fas fa-pen mr-1"></i>
                    Terakhir Update: {{ $note->updated_at->diffForHumans() }}
                </span>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('notes.index') }}" 
               class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button @click="togglePin({{ $note->id }})" 
                    class="px-4 py-2 rounded-lg {{ $note->is_pinned ? 'bg-yellow-500 text-white hover:bg-yellow-600' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }} transition-colors">
                <i class="fas fa-thumbtack mr-1"></i>
                {{ $note->is_pinned ? 'Batal Sematkan' : 'Sematkan' }}
            </button>
            <a href="{{ route('notes.edit', $note) }}" 
               class="px-4 py-2 rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition-colors">
                <i class="fas fa-pen mr-1"></i> Edit
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePin(noteId) {
        fetch(`/notes/${noteId}/pin`, {
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
            window.showToast('Gagal mengubah status pin', 'error');
        });
    }
</script>
@endpush
@endsection