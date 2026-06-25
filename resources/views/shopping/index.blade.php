@extends('layouts.app')

@section('title', 'Manajemen Belanja')
@section('breadcrumb', 'Belanja')
@section('page-title', '🛒 Manajemen Belanja')
@section('page-description', 'Kelola daftar belanja dan stok kebutuhan Anda')

@section('content')
<div x-data="shoppingManager()" x-init="initShopping()" class="space-y-6">
    
    {{-- ===== STATISTIK ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Total Items -->
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Item</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-box text-xl text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>
        
        <!-- Belum Dibeli -->
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum Dibeli</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['unchecked'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <i class="fas fa-clock text-xl text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
        </div>
        
        <!-- Sudah Dibeli -->
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sudah Dibeli</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['checked'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-emerald-600 dark:text-emerald-400"></i>
                </div>
            </div>
        </div>
        
        <!-- Stok Menipis -->
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Stok Menipis</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['low_stock'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-xl text-red-600 dark:text-red-400"></i>
                </div>
            </div>
        </div>
    </div>
    
    {{-- ===== FILTER & SEARCH ===== --}}
    <div class="card">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" 
                       x-model="search"
                       x-debounce="300"
                       placeholder="Cari item belanja..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
            </div>
            
            <!-- Filters -->
            <div class="flex flex-wrap gap-2">
                <select x-model="category" 
                        @change="applyFilters()"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-pointer">
                    <option value="all">📦 Semua Kategori</option>
                    <option value="groceries">🛒 Sembako</option>
                    <option value="hygiene">🧴 Kebersihan</option>
                    <option value="drinks">🥤 Minuman</option>
                    <option value="others">📦 Lainnya</option>
                </select>
                
                <select x-model="status" 
                        @change="applyFilters()"
                        class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-pointer">
                    <option value="all">📊 Semua Status</option>
                    <option value="unchecked">⏳ Belum Dibeli</option>
                    <option value="checked">✅ Sudah Dibeli</option>
                    <option value="low_stock">⚠️ Stok Menipis</option>
                </select>
                
                <a href="{{ route('shopping.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-xl hover:from-indigo-600 hover:to-purple-600 transition-all duration-200 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Tambah Item</span>
                </a>
            </div>
        </div>
        
        <!-- Active Filters -->
        <div x-show="category !== 'all' || status !== 'all' || search" 
             x-cloak
             class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <span class="text-xs text-gray-500 dark:text-gray-400">Filter aktif:</span>
            
            <span x-show="category !== 'all'" 
                  class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">
                <span x-text="category === 'groceries' ? '🛒 Sembako' : category === 'hygiene' ? '🧴 Kebersihan' : category === 'drinks' ? '🥤 Minuman' : '📦 Lainnya'"></span>
                <button @click="category = 'all'" class="hover:text-indigo-900 dark:hover:text-indigo-100">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </span>
            
            <span x-show="status !== 'all'" 
                  class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">
                <span x-text="status === 'unchecked' ? '⏳ Belum Dibeli' : status === 'checked' ? '✅ Sudah Dibeli' : '⚠️ Stok Menipis'"></span>
                <button @click="status = 'all'" class="hover:text-indigo-900 dark:hover:text-indigo-100">
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
    
{{-- ===== ITEMS LIST ===== --}}
<div class="card">
    @if(isset($items) && $items->count() > 0)
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($items as $item)
                <div class="py-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 px-2 rounded-lg transition-colors group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <!-- Checkbox -->
                            <button @click="toggleCheck({{ $item->id }})" 
                                    class="flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-200
                                           {{ $item->is_checked ? 'bg-emerald-500 border-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'border-gray-300 dark:border-gray-600 hover:border-indigo-500 hover:scale-110' }}">
                                <i class="fas fa-check text-xs transition-all duration-200" 
                                   :class="{'opacity-100 scale-100': {{ $item->is_checked ? 'true' : 'false' }}, 'opacity-0 scale-0': {{ $item->is_checked ? 'false' : 'true' }}}"></i>
                            </button>
                            
                            <!-- Item Info -->
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white
                                               {{ $item->is_checked ? 'line-through text-gray-400 dark:text-gray-500' : '' }}">
                                        {{ $item->name }}
                                    </h3>
                                    
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                        {{ $item->getCategoryLabel() }}
                                    </span>
                                    
                                    <!-- Stock Status -->
                                    @if($item->isLowStock())
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            Stok Menipis
                                        </span>
                                    @endif
                                    
                                    @if($item->is_checked)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Sudah Dibeli
                                        </span>
                                    @else
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300">
                                            <i class="fas fa-clock mr-1"></i>
                                            Belum Dibeli
                                        </span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    <span>
                                        <i class="fas fa-box mr-1"></i>
                                        Stok: {{ $item->stock_quantity }}
                                        @if($item->min_stock > 0)
                                            <span class="text-gray-400">(Min: {{ $item->min_stock }})</span>
                                        @endif
                                    </span>
                                    @if($item->estimated_price)
                                        <span>
                                            <i class="fas fa-tag mr-1"></i>
                                            ~Rp {{ number_format($item->estimated_price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center gap-1 flex-shrink-0 opacity-70 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('shopping.edit', $item) }}" 
                               class="p-2 rounded-lg text-gray-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all duration-200">
                                <i class="fas fa-pen text-sm"></i>
                            </a>
                            <button @click="confirmDelete({{ $item->id }}, '{{ addslashes($item->name) }}')" 
                                    class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-4">
            {{ $items->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shopping-cart text-3xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada item belanja</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mulai tambahkan kebutuhan belanja Anda</p>
            <a href="{{ route('shopping.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors mt-4">
                <i class="fas fa-plus"></i> Tambah Item
            </a>
        </div>
    @endif
</div>

{{-- ===== DELETE CONFIRMATION MODAL ===== --}}
<div x-data="{ show: false, itemId: null, itemName: '' }"
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
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Hapus Item?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                Apakah Anda yakin ingin menghapus item
                <br>
                <span class="font-medium text-gray-700 dark:text-gray-300" x-text="itemName"></span>?
                <br>
                <span class="text-red-500 text-xs">Tindakan ini tidak dapat dibatalkan!</span>
            </p>
            <div class="flex gap-3 mt-6">
                <button @click="show = false" 
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium">
                    Batal
                </button>
                <form :action="'{{ route('shopping.index') }}/' + itemId" method="POST" class="flex-1">
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
    function shoppingManager() {
        return {
            search: '',
            category: 'all',
            status: 'all',
            
            initShopping() {
                // Watch for filter changes
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
                if (this.status !== 'all') params.append('status', this.status);
                
                window.location.href = '{{ route('shopping.index') }}?' + params.toString();
            },
            
            resetFilters() {
                this.search = '';
                this.category = 'all';
                this.status = 'all';
                this.applyFilters();
            },
            
            toggleCheck(itemId) {
                fetch(`/shopping/${itemId}/toggle`, {
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
                    window.showToast('Gagal mengubah status item', 'error');
                });
            },
            
            confirmDelete(id, name) {
                this.itemId = id;
                this.itemName = name;
                this.show = true;
            }
        };
    }
</script>
@endpush

@push('styles')
<style>
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
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection