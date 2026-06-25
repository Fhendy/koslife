@extends('layouts.app')

@section('title', 'Edit Habit')
@section('breadcrumb', 'Edit Habit')
@section('page-title', '✏️ Edit Habit')
@section('page-description', 'Perbarui kebiasaan Anda')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <form action="{{ route('habits.update', $habit) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Icon -->
            <div>
                <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-smile text-indigo-500 mr-1"></i> Icon
                </label>
                <div class="flex items-center gap-2">
                    <input type="text" 
                           name="icon" 
                           id="icon" 
                           value="{{ old('icon', $habit->icon) }}" 
                           maxlength="5"
                           class="w-20 px-4 py-2.5 text-center text-2xl rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="✅">
                    <div class="flex flex-wrap gap-1">
                        @foreach(['✅', '📚', '💻', '🏋️', '📖', '💧', '😴', '🧘', '🎯', '🏃', '🧠', '💪'] as $emoji)
                            <button type="button" 
                                    @click="document.getElementById('icon').value = '{{ $emoji }}'"
                                    class="w-10 h-10 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-xl">
                                {{ $emoji }}
                            </button>
                        @endforeach
                    </div>
                </div>
                @error('icon')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-heading text-indigo-500 mr-1"></i> Nama Habit <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name', $habit->name) }}" 
                       required
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="Contoh: Bangun Pagi">
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Target Frequency -->
            <div>
                <label for="target_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-clock text-indigo-500 mr-1"></i> Frekuensi Target <span class="text-red-500">*</span>
                </label>
                <select name="target_frequency" 
                        id="target_frequency" 
                        required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="daily" {{ old('target_frequency', $habit->target_frequency) == 'daily' ? 'selected' : '' }}>📅 Harian</option>
                    <option value="weekly" {{ old('target_frequency', $habit->target_frequency) == 'weekly' ? 'selected' : '' }}>📆 Mingguan</option>
                    <option value="monthly" {{ old('target_frequency', $habit->target_frequency) == 'monthly' ? 'selected' : '' }}>📋 Bulanan</option>
                </select>
                @error('target_frequency')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Is Active -->
            <div class="flex items-center gap-2">
                <input type="checkbox" 
                       name="is_active" 
                       id="is_active"
                       value="1"
                       {{ old('is_active', $habit->is_active) ? 'checked' : '' }}
                       class="rounded border-gray-300 dark:border-gray-600 text-indigo-500 focus:ring-indigo-500">
                <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                    <i class="fas fa-power-off mr-1"></i> Aktifkan habit ini
                </label>
            </div>
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <i class="fas fa-save"></i> Update Habit
                </button>
                <a href="{{ route('habits.index') }}" 
                   class="flex-1 text-center px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection