@extends('layouts.app')

@section('title', 'Tambah Tugas')
@section('breadcrumb', 'Tambah Tugas')
@section('page-title', '📝 Tambah Tugas Baru')
@section('page-description', 'Buat tugas baru untuk dikerjakan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-heading text-indigo-500 mr-1"></i> Judul Tugas <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title') }}" 
                       required
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="Masukkan judul tugas">
                @error('title')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-align-left text-indigo-500 mr-1"></i> Deskripsi
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="3"
                          class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                          placeholder="Deskripsikan tugas Anda">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Priority & Status Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-flag text-indigo-500 mr-1"></i> Prioritas <span class="text-red-500">*</span>
                    </label>
                    <select name="priority" 
                            id="priority" 
                            required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-circle text-indigo-500 mr-1"></i> Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" 
                            id="status" 
                            required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="not_started" {{ old('status') == 'not_started' ? 'selected' : '' }}>Belum Mulai</option>
                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Deadline & Category Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-calendar-alt text-indigo-500 mr-1"></i> Deadline <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="deadline" 
                           id="deadline" 
                           value="{{ old('deadline', date('Y-m-d', strtotime('+7 days'))) }}" 
                           required
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('deadline')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-tag text-indigo-500 mr-1"></i> Kategori
                    </label>
                    <select name="category" 
                            id="category" 
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Pilih Kategori</option>
                        <option value="school" {{ old('category') == 'school' ? 'selected' : '' }}>Sekolah</option>
                        <option value="pkl" {{ old('category') == 'pkl' ? 'selected' : '' }}>PKL</option>
                        <option value="personal" {{ old('category') == 'personal' ? 'selected' : '' }}>Pribadi</option>
                        <option value="organization" {{ old('category') == 'organization' ? 'selected' : '' }}>Organisasi</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-sticky-note text-indigo-500 mr-1"></i> Catatan Tambahan
                </label>
                <textarea name="notes" 
                          id="notes" 
                          rows="2"
                          class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                          placeholder="Catatan tambahan untuk tugas ini">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Attachment -->
            <div>
                <label for="attachment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-paperclip text-indigo-500 mr-1"></i> Lampiran
                </label>
                <div class="relative">
                    <input type="file" 
                           name="attachment" 
                           id="attachment"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400">
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maksimal 10MB (PDF, DOC, DOCX, JPG, PNG)</p>
                @error('attachment')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <i class="fas fa-save"></i> Simpan Tugas
                </button>
                <a href="{{ route('tasks.index') }}" 
                   class="flex-1 text-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection