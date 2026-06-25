@extends('layouts.app')

@section('title', 'Edit Item Belanja')
@section('breadcrumb', 'Edit Item')
@section('page-title', '✏️ Edit Item Belanja')
@section('page-description', 'Perbarui informasi item belanja')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <form action="{{ route('shopping.update', $shopping) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-tag text-indigo-500 mr-1"></i> Nama Item <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name', $shopping->name) }}" 
                       required
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="Contoh: Beras 5kg">
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Category -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-folder text-indigo-500 mr-1"></i> Kategori <span class="text-red-500">*</span>
                </label>
                <select name="category" 
                        id="category" 
                        required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="groceries" {{ old('category', $shopping->category) == 'groceries' ? 'selected' : '' }}>🛒 Sembako</option>
                    <option value="hygiene" {{ old('category', $shopping->category) == 'hygiene' ? 'selected' : '' }}>🧴 Kebersihan</option>
                    <option value="drinks" {{ old('category', $shopping->category) == 'drinks' ? 'selected' : '' }}>🥤 Minuman</option>
                    <option value="others" {{ old('category', $shopping->category) == 'others' ? 'selected' : '' }}>📦 Lainnya</option>
                </select>
                @error('category')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Stock -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-box text-indigo-500 mr-1"></i> Jumlah Stok
                    </label>
                    <input type="number" 
                           name="stock_quantity" 
                           id="stock_quantity" 
                           value="{{ old('stock_quantity', $shopping->stock_quantity) }}" 
                           min="0"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('stock_quantity')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="min_stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-exclamation-triangle text-indigo-500 mr-1"></i> Minimum Stok
                    </label>
                    <input type="number" 
                           name="min_stock" 
                           id="min_stock" 
                           value="{{ old('min_stock', $shopping->min_stock) }}" 
                           min="0"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('min_stock')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Estimated Price -->
            <div>
                <label for="estimated_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-tag text-indigo-500 mr-1"></i> Estimasi Harga
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span>
                    <input type="number" 
                           name="estimated_price" 
                           id="estimated_price" 
                           value="{{ old('estimated_price', $shopping->estimated_price) }}" 
                           step="0.01" 
                           min="0"
                           placeholder="0"
                           class="w-full pl-12 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                @error('estimated_price')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Is Checked -->
            <div class="flex items-center gap-2">
                <input type="checkbox" 
                       name="is_checked" 
                       id="is_checked"
                       value="1"
                       {{ old('is_checked', $shopping->is_checked) ? 'checked' : '' }}
                       class="rounded border-gray-300 dark:border-gray-600 text-indigo-500 focus:ring-indigo-500">
                <label for="is_checked" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                    <i class="fas fa-check-circle mr-1"></i> Sudah dibeli
                </label>
            </div>
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <i class="fas fa-save"></i> Update Item
                </button>
                <a href="{{ route('shopping.index') }}" 
                   class="flex-1 text-center px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection