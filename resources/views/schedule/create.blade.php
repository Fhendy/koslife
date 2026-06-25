@extends('layouts.app')

@section('title', 'Tambah Jadwal')
@section('breadcrumb', 'Tambah Jadwal')
@section('page-title', '📅 Tambah Jadwal Baru')
@section('page-description', 'Buat jadwal baru untuk kegiatan Anda')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <form action="{{ route('schedules.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-heading text-indigo-500 mr-1"></i> Judul Jadwal <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title') }}" 
                       required
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="Contoh: Rapat PKL">
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
                          class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                          placeholder="Deskripsikan kegiatan">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Category & Location -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-folder text-indigo-500 mr-1"></i> Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="category" 
                            id="category" 
                            required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="school" {{ old('category') == 'school' ? 'selected' : '' }}>📚 Sekolah</option>
                        <option value="pkl" {{ old('category') == 'pkl' ? 'selected' : '' }}>💼 PKL</option>
                        <option value="organization" {{ old('category') == 'organization' ? 'selected' : '' }}>🤝 Organisasi</option>
                        <option value="meeting" {{ old('category') == 'meeting' ? 'selected' : '' }}>📅 Meeting</option>
                        <option value="personal" {{ old('category') == 'personal' ? 'selected' : '' }}>👤 Pribadi</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-map-marker-alt text-indigo-500 mr-1"></i> Lokasi
                    </label>
                    <input type="text" 
                           name="location" 
                           id="location" 
                           value="{{ old('location') }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Contoh: Ruang Meeting A">
                    @error('location')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Start & End Time -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-clock text-indigo-500 mr-1"></i> Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           name="start_time" 
                           id="start_time" 
                           value="{{ old('start_time', date('Y-m-d\TH:i')) }}" 
                           required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-clock text-indigo-500 mr-1"></i> Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" 
                           name="end_time" 
                           id="end_time" 
                           value="{{ old('end_time', date('Y-m-d\TH:i', strtotime('+1 hour'))) }}" 
                           required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Reminder -->
            <div>
                <label for="reminder_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-bell text-indigo-500 mr-1"></i> Pengingat
                </label>
                <select name="reminder_minutes" 
                        id="reminder_minutes"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="0">Tidak ada pengingat</option>
                    <option value="5" {{ old('reminder_minutes') == 5 ? 'selected' : '' }}>5 menit sebelumnya</option>
                    <option value="10" {{ old('reminder_minutes') == 10 ? 'selected' : '' }}>10 menit sebelumnya</option>
                    <option value="15" {{ old('reminder_minutes') == 15 ? 'selected' : '' }}>15 menit sebelumnya</option>
                    <option value="30" {{ old('reminder_minutes') == 30 ? 'selected' : '' }}>30 menit sebelumnya</option>
                    <option value="60" {{ old('reminder_minutes') == 60 ? 'selected' : '' }}>1 jam sebelumnya</option>
                    <option value="120" {{ old('reminder_minutes') == 120 ? 'selected' : '' }}>2 jam sebelumnya</option>
                    <option value="1440" {{ old('reminder_minutes') == 1440 ? 'selected' : '' }}>1 hari sebelumnya</option>
                </select>
                @error('reminder_minutes')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Is All Day -->
            <div class="flex items-center gap-2">
                <input type="checkbox" 
                       name="is_all_day" 
                       id="is_all_day"
                       value="1"
                       {{ old('is_all_day') ? 'checked' : '' }}
                       class="rounded border-gray-300 dark:border-gray-600 text-indigo-500 focus:ring-indigo-500">
                <label for="is_all_day" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                    <i class="fas fa-calendar-day mr-1"></i> Acara seharian
                </label>
            </div>
            
            <!-- Color -->
            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-palette text-indigo-500 mr-1"></i> Warna
                </label>
                <input type="color" 
                       name="color" 
                       id="color" 
                       value="{{ old('color', '#4F46E5') }}"
                       class="w-16 h-10 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer">
                @error('color')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <i class="fas fa-save"></i> Simpan Jadwal
                </button>
                <a href="{{ route('calendar.index') }}" 
                   class="flex-1 text-center px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection