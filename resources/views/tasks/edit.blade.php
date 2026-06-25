@extends('layouts.app')

@section('title', 'Edit Tugas')
@section('breadcrumb', 'Edit Tugas')
@section('page-title', '✏️ Edit Tugas')
@section('page-description', 'Perbarui informasi tugas')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-heading text-indigo-500 mr-1"></i> Judul Tugas <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title', $task->title) }}" 
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
                          placeholder="Deskripsikan tugas Anda">{{ old('description', $task->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Priority & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-flag text-indigo-500 mr-1"></i> Prioritas <span class="text-red-500">*</span>
                    </label>
                    <select name="priority" 
                            id="priority" 
                            required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>Tinggi</option>
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
                        <option value="not_started" {{ old('status', $task->status) == 'not_started' ? 'selected' : '' }}>Belum Mulai</option>
                        <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                        <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Deadline & Category -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-calendar-alt text-indigo-500 mr-1"></i> Deadline <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="deadline" 
                           id="deadline" 
                           value="{{ old('deadline', $task->deadline->format('Y-m-d')) }}" 
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
                        <option value="school" {{ old('category', $task->category) == 'school' ? 'selected' : '' }}>Sekolah</option>
                        <option value="pkl" {{ old('category', $task->category) == 'pkl' ? 'selected' : '' }}>PKL</option>
                        <option value="personal" {{ old('category', $task->category) == 'personal' ? 'selected' : '' }}>Pribadi</option>
                        <option value="organization" {{ old('category', $task->category) == 'organization' ? 'selected' : '' }}>Organisasi</option>
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
                          placeholder="Catatan tambahan untuk tugas ini">{{ old('notes', $task->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Attachment -->
            <div>
                <label for="attachment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-paperclip text-indigo-500 mr-1"></i> Lampiran
                </label>
                @if($task->attachment)
                    <div class="mb-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-file"></i> {{ basename($task->attachment) }}
                        </span>
                        <a href="{{ asset('storage/' . $task->attachment) }}" 
                           target="_blank"
                           class="text-sm text-indigo-500 hover:text-indigo-600 transition-colors">
                            <i class="fas fa-eye"></i> Lihat
                        </a>
                    </div>
                @endif
                <input type="file" 
                       name="attachment" 
                       id="attachment"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan jika tidak ingin mengganti lampiran</p>
                @error('attachment')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <i class="fas fa-save"></i> Update Tugas
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