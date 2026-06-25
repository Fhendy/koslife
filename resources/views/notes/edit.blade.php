@extends('layouts.app')

@section('title', 'Edit Catatan')
@section('breadcrumb', 'Edit Catatan')
@section('page-title', '✏️ Edit Catatan')
@section('page-description', 'Perbarui catatan Anda')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <form action="{{ route('notes.update', $note) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-heading text-indigo-500 mr-1"></i> Judul <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title', $note->title) }}" 
                       required
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="Masukkan judul catatan">
                @error('title')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-align-left text-indigo-500 mr-1"></i> Isi Catatan <span class="text-red-500">*</span>
                </label>
                <textarea name="content" 
                          id="content" 
                          rows="8"
                          required
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                          placeholder="Tulis catatan Anda di sini...">{{ old('content', $note->content) }}</textarea>
                @error('content')
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
                    @foreach(\App\Models\Note::getCategories() as $key => $label)
                        <option value="{{ $key }}" {{ old('category', $note->category) == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('category')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Color -->
            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-palette text-indigo-500 mr-1"></i> Warna Background
                </label>
                <div class="flex flex-wrap gap-2">
                    @foreach(\App\Models\Note::getColors() as $color => $label)
                        <button type="button" 
                                @click="document.getElementById('color_input').value = '{{ $color }}'"
                                class="w-10 h-10 rounded-lg border-2 transition-all hover:scale-110"
                                style="background-color: {{ $color }}; border-color: {{ old('color', $note->color ?? '#FFFFFF') == $color ? '#4F46E5' : '#E5E7EB' }}">
                        </button>
                    @endforeach
                    <input type="hidden" name="color" id="color_input" value="{{ old('color', $note->color ?? '#FFFFFF') }}">
                </div>
                @error('color')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Is Pinned -->
            <div class="flex items-center gap-2">
                <input type="checkbox" 
                       name="is_pinned" 
                       id="is_pinned"
                       value="1"
                       {{ old('is_pinned', $note->is_pinned) ? 'checked' : '' }}
                       class="rounded border-gray-300 dark:border-gray-600 text-indigo-500 focus:ring-indigo-500">
                <label for="is_pinned" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                    <i class="fas fa-thumbtack mr-1"></i> Sematkan catatan ini
                </label>
            </div>
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <i class="fas fa-save"></i> Update Catatan
                </button>
                <a href="{{ route('notes.index') }}" 
                   class="flex-1 text-center px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection