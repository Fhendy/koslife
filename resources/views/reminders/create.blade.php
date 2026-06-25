@extends('layouts.app')

@section('title', 'Tambah Reminder')
@section('breadcrumb', 'Tambah Reminder')
@section('page-title', '🔔 Tambah Reminder Baru')
@section('page-description', 'Buat pengingat untuk kegiatan penting Anda')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <form action="{{ route('reminders.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-heading text-indigo-500 mr-1"></i> Judul Reminder <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       value="{{ old('title') }}" 
                       required
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="Contoh: Bayar Kos">
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
                          placeholder="Deskripsikan pengingat">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-tag text-indigo-500 mr-1"></i> Tipe <span class="text-red-500">*</span>
                </label>
                <select name="type" 
                        id="type" 
                        required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @foreach(\App\Models\Reminder::getTypes() as $key => $label)
                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Reminder Time -->
            <div>
                <label for="reminder_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-clock text-indigo-500 mr-1"></i> Waktu Pengingat <span class="text-red-500">*</span>
                </label>
                <input type="datetime-local" 
                       name="reminder_time" 
                       id="reminder_time" 
                       value="{{ old('reminder_time', date('Y-m-d\TH:i', strtotime('+1 hour'))) }}" 
                       required
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('reminder_time')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Quick Presets -->
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-bolt text-indigo-500 mr-1"></i> Preset Cepat
                </p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" 
                            @click="document.getElementById('reminder_time').value = '{{ date('Y-m-d\TH:i', strtotime('+15 minutes')) }}'"
                            class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        15 menit
                    </button>
                    <button type="button" 
                            @click="document.getElementById('reminder_time').value = '{{ date('Y-m-d\TH:i', strtotime('+30 minutes')) }}'"
                            class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        30 menit
                    </button>
                    <button type="button" 
                            @click="document.getElementById('reminder_time').value = '{{ date('Y-m-d\TH:i', strtotime('+1 hour')) }}'"
                            class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        1 jam
                    </button>
                    <button type="button" 
                            @click="document.getElementById('reminder_time').value = '{{ date('Y-m-d\TH:i', strtotime('+3 hours')) }}'"
                            class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        3 jam
                    </button>
                    <button type="button" 
                            @click="document.getElementById('reminder_time').value = '{{ date('Y-m-d\TH:i', strtotime('+1 day')) }}'"
                            class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        1 hari
                    </button>
                    <button type="button" 
                            @click="document.getElementById('reminder_time').value = '{{ date('Y-m-d\TH:i', strtotime('+1 week')) }}'"
                            class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        1 minggu
                    </button>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <i class="fas fa-save"></i> Simpan Reminder
                </button>
                <a href="{{ route('reminders.index') }}" 
                   class="flex-1 text-center px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection