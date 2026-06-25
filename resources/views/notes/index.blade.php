@extends('layouts.app')

@section('title', 'Catatan Pribadi')
@section('breadcrumb', 'Catatan')
@section('page-title', '📝 Catatan Pribadi')
@section('page-description', 'Simpan ide, catatan, dan pemikiran Anda')

@section('content')
<div x-data="noteManager()" x-init="initNotes()" class="space-y-6">
    
    {{-- ===== STATISTIK ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Catatan</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <i class="fas fa-note-sticky text-xl text-indigo-600 dark:text-indigo-400"></i>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Disematkan</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pinned'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                    <i class="fas fa-thumbtack text-xl text-yellow-600 dark:text-yellow-400"></i>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kategori</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $categories->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <i class="fas fa-tags text-xl text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>
        
        {{-- ===== LAST UPDATED - FIX ===== --}}
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Terakhir Update</p>
                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300 truncate">
                        {{ $lastUpdated ?? '-' }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-clock text-xl text-blue-600 dark:text-blue-400"></i>
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
                       placeholder="Cari catatan..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            
            <div class="flex flex-wrap gap-2">
                <select x-model="category" 
                        @change="applyFilters()"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="all">📂 Semua Kategori</option>
                    @foreach(\App\Models\Note::getCategories() as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                
                <select x-model="sort" 
                        @change="applyFilters()"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="latest">🕐 Terbaru</option>
                    <option value="oldest">🕐 Terlama</option>
                    <option value="updated">📝 Terakhir Update</option>
                    <option value="title">🔤 Judul</option>
                </select>
                
                <a href="{{ route('notes.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-xl hover:from-indigo-600 hover:to-purple-600 transition-all duration-200 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Tambah Catatan</span>
                </a>
            </div>
        </div>
        
        <!-- Active Filters -->
        <div x-show="category !== 'all' || search" 
             x-cloak
             class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <span class="text-xs text-gray-500 dark:text-gray-400">Filter aktif:</span>
            
            <span x-show="category !== 'all'" 
                  class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">
                <span x-text="getCategoryLabel(category)"></span>
                <button @click="category = 'all'" class="hover:text-indigo-900 dark:hover:text-indigo-100">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </span>
            
            <span x-show="search" 
                  class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">
                <span>🔍 "<span x-text="search"></span>"</span>
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
    
    {{-- ===== NOTES GRID ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($notes as $note)
            <div class="card hover:shadow-xl transition-all duration-200 group relative"
                 style="{{ $note->color ? 'background-color: ' . $note->color . ';' : '' }}
                        {{ $note->color ? 'border: 2px solid ' . $note->color . ';' : '' }}">
                
                @if($note->is_pinned)
                    <div class="absolute top-2 right-2 text-yellow-500">
                        <i class="fas fa-thumbtack fa-rotate-45"></i>
                    </div>
                @endif
                
                <div class="flex items-start gap-3">
                    <span class="text-2xl flex-shrink-0">{{ $note->getCategoryIcon() }}</span>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 dark:text-white truncate">
                            {{ $note->title }}
                        </h3>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                {{ $note->getCategoryLabel() }}
                            </span>
                            @if($note->is_pinned)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">
                                    <i class="fas fa-thumbtack text-[10px] mr-1"></i>
                                    Disematkan
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3">
                        {{ $note->getExcerpt(120) }}
                    </p>
                </div>
                
                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="far fa-clock mr-1"></i>
                        {{ $note->updated_at->diffForHumans() }}
                    </span>
                    
                    <div class="flex items-center gap-1 opacity-70 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('notes.show', $note) }}" 
                           class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                            <i class="fas fa-eye text-sm"></i>
                        </a>
                        <a href="{{ route('notes.edit', $note) }}" 
                           class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                            <i class="fas fa-pen text-sm"></i>
                        </a>
                        <button @click="togglePin({{ $note->id }})" 
                                class="p-1.5 rounded-lg text-gray-400 hover:text-yellow-500 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-colors">
                            <i class="fas fa-thumbtack text-sm"></i>
                        </button>
                        <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Hapus catatan ini?')"
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-sticky-note text-3xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada catatan</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mulai tulis ide dan catatan Anda</p>
                <a href="{{ route('notes.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors mt-4">
                    <i class="fas fa-plus"></i> Tambah Catatan
                </a>
            </div>
        @endforelse
    </div>
    
    {{-- ===== PAGINATION ===== --}}
    @if(isset($notes) && $notes instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $notes->hasPages())
        <div class="card">
            {{ $notes->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
    function noteManager() {
        return {
            search: '',
            category: 'all',
            sort: 'latest',
            
            getCategoryLabel(category) {
                const categories = {
                    'school': '📚 Sekolah',
                    'pkl': '💼 PKL',
                    'finance': '💰 Keuangan',
                    'personal': '👤 Pribadi',
                    'idea': '💡 Ide'
                };
                return categories[category] || category;
            },
            
            initNotes() {
                this.$watch('search', () => this.debounceApplyFilters());
                this.$watch('category', () => this.applyFilters());
                this.$watch('sort', () => this.applyFilters());
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
                if (this.sort !== 'latest') params.append('sort', this.sort);
                
                window.location.href = '{{ route('notes.index') }}?' + params.toString();
            },
            
            resetFilters() {
                this.search = '';
                this.category = 'all';
                this.sort = 'latest';
                this.applyFilters();
            },
            
            togglePin(noteId) {
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
        transform: translateY(-4px);
    }
    .dark .card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection