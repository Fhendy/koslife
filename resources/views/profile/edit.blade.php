@extends('layouts.app')

@section('title', 'Pengaturan')
@section('breadcrumb', 'Pengaturan')
@section('page-title', '⚙️ Pengaturan')
@section('page-description', 'Kelola profil dan preferensi Anda')

@section('content')
<div x-data="settingsManager()" x-init="initSettings()" class="space-y-6">
    
    {{-- ===== TAB NAVIGATION ===== --}}
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="flex flex-wrap gap-2 -mb-px">
            <button @click="activeTab = 'profile'" 
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200
                           {{-- PERBAIKAN: Gunakan x-bind:class atau conditional class --}}
                           :class="activeTab === 'profile' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                <i class="fas fa-user mr-2"></i> Profil
            </button>
            <button @click="activeTab = 'preferences'" 
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200
                           :class="activeTab === 'preferences' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                <i class="fas fa-sliders-h mr-2"></i> Preferensi
            </button>
            <button @click="activeTab = 'security'" 
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200
                           :class="activeTab === 'security' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                <i class="fas fa-lock mr-2"></i> Keamanan
            </button>
            <button @click="activeTab = 'account'" 
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors duration-200
                           :class="activeTab === 'account' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                <i class="fas fa-cog mr-2"></i> Akun
            </button>
        </nav>
    </div>
    
    {{-- ===== TAB CONTENT ===== --}}
    <div class="mt-6">
        <!-- Profile Tab -->
        <div x-show="activeTab === 'profile'" x-cloak>
            <div class="card max-w-2xl">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-user-circle text-indigo-500 mr-2"></i>
                        Informasi Profil
                    </h3>
                    
                    <!-- Avatar -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Foto Profil
                        </label>
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=4F46E5&color=fff&size=128' }}"
                                     class="w-24 h-24 rounded-full object-cover ring-4 ring-gray-200 dark:ring-gray-700">
                                <div class="absolute -bottom-1 -right-1 bg-green-500 rounded-full p-1.5 ring-2 ring-white dark:ring-gray-800">
                                    <i class="fas fa-camera text-white text-xs"></i>
                                </div>
                            </div>
                            <div>
                                <input type="file" 
                                       name="avatar" 
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 dark:text-gray-400
                                              file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                              file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-600
                                              hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">PNG, JPG maksimal 2MB</p>
                            </div>
                        </div>
                        @error('avatar')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-user text-indigo-500 mr-1"></i> Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', auth()->user()->name) }}" 
                               required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-envelope text-indigo-500 mr-1"></i> Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', auth()->user()->email) }}" 
                               required
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Preferences Tab -->
        <div x-show="activeTab === 'preferences'" x-cloak>
            <div class="card max-w-2xl">
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-sliders-h text-indigo-500 mr-2"></i>
                        Preferensi
                    </h3>
                    
                    <!-- Theme -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-palette text-indigo-500 mr-1"></i> Tema
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" 
                                    @click="selectedTheme = 'light'"
                                    class="p-4 rounded-lg border-2 transition-all duration-200 text-center
                                           :class="selectedTheme === 'light' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300'">
                                <i class="fas fa-sun text-2xl text-yellow-500"></i>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-2">Terang</p>
                            </button>
                            <button type="button" 
                                    @click="selectedTheme = 'dark'"
                                    class="p-4 rounded-lg border-2 transition-all duration-200 text-center
                                           :class="selectedTheme === 'dark' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300'">
                                <i class="fas fa-moon text-2xl text-indigo-500"></i>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-2">Gelap</p>
                            </button>
                            <button type="button" 
                                    @click="selectedTheme = 'system'"
                                    class="p-4 rounded-lg border-2 transition-all duration-200 text-center
                                           :class="selectedTheme === 'system' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300'">
                                <i class="fas fa-desktop text-2xl text-gray-500"></i>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-2">Sistem</p>
                            </button>
                        </div>
                        <input type="hidden" name="theme" x-model="selectedTheme">
                    </div>
                    
                    <!-- Daily Meal Budget -->
                    <div>
                        <label for="daily_meal_budget" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-utensils text-indigo-500 mr-1"></i> Budget Makan Harian
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span>
                            <input type="number" 
                                   name="daily_meal_budget" 
                                   id="daily_meal_budget" 
                                   value="{{ old('daily_meal_budget', $user->daily_meal_budget ?? 50000) }}" 
                                   step="1000"
                                   min="0"
                                   class="w-full pl-12 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        @error('daily_meal_budget')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Savings Goal -->
                    <div>
                        <label for="savings_goal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-piggy-bank text-indigo-500 mr-1"></i> Target Tabungan
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span>
                            <input type="number" 
                                   name="savings_goal" 
                                   id="savings_goal" 
                                   value="{{ old('savings_goal', $user->savings_goal ?? 0) }}" 
                                   step="10000"
                                   min="0"
                                   class="w-full pl-12 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        @error('savings_goal')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-money-bill-wave text-indigo-500 mr-1"></i> Mata Uang
                        </label>
                        <select name="currency" 
                                id="currency" 
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="IDR" {{ ($user->currency ?? 'IDR') == 'IDR' ? 'selected' : '' }}>🇮🇩 IDR - Rupiah</option>
                            <option value="USD" {{ ($user->currency ?? 'IDR') == 'USD' ? 'selected' : '' }}>🇺🇸 USD - Dollar</option>
                            <option value="SGD" {{ ($user->currency ?? 'IDR') == 'SGD' ? 'selected' : '' }}>🇸🇬 SGD - Dollar Singapura</option>
                        </select>
                        @error('currency')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save mr-2"></i> Simpan Preferensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Security Tab -->
        <div x-show="activeTab === 'security'" x-cloak>
            <div class="card max-w-2xl">
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-lock text-indigo-500 mr-2"></i>
                        Keamanan
                    </h3>
                    
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-key text-indigo-500 mr-1"></i> Password Saat Ini
                        </label>
                        <input type="password" 
                               name="current_password" 
                               id="current_password" 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="Masukkan password saat ini">
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-lock text-indigo-500 mr-1"></i> Password Baru
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <i class="fas fa-check-circle text-indigo-500 mr-1"></i> Konfirmasi Password Baru
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="Ketik ulang password baru">
                    </div>
                    
                    <!-- Security Tips -->
                    <div class="p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-300">
                            <i class="fas fa-shield-alt mr-2"></i> Tips Keamanan:
                        </p>
                        <ul class="mt-2 text-sm text-blue-700 dark:text-blue-400 list-disc list-inside space-y-1">
                            <li>Gunakan minimal 8 karakter</li>
                            <li>Kombinasikan huruf besar, kecil, angka, dan simbol</li>
                            <li>Jangan gunakan password yang sama dengan akun lain</li>
                        </ul>
                    </div>
                    
                    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save mr-2"></i> Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Account Tab -->
        <div x-show="activeTab === 'account'" x-cloak>
            <div class="card max-w-2xl">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-cog text-indigo-500 mr-2"></i>
                    Manajemen Akun
                </h3>
                
                <!-- Account Info -->
                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Akun Dibuat</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ auth()->user()->created_at->translatedFormat('d M Y H:i') }}
                            </p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                            <i class="fas fa-check-circle mr-1"></i> Aktif
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Email Terverifikasi</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ auth()->user()->email_verified_at ? 'Sudah diverifikasi' : 'Belum diverifikasi' }}
                            </p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ auth()->user()->email_verified_at ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300' }}">
                            <i class="fas {{ auth()->user()->email_verified_at ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                            {{ auth()->user()->email_verified_at ? 'Terverifikasi' : 'Belum' }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Data</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $stats['total_tasks'] ?? 0 }} tugas, 
                                {{ $stats['total_notes'] ?? 0 }} catatan, 
                                {{ $stats['total_habits'] ?? 0 }} habit
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Danger Zone -->
                <div class="mt-6 p-4 rounded-lg border-2 border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20">
                    <h4 class="text-sm font-semibold text-red-800 dark:text-red-300">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Zona Berbahaya
                    </h4>
                    <p class="mt-1 text-sm text-red-700 dark:text-red-400">
                        Tindakan ini tidak dapat dibatalkan. Hapus semua data Anda secara permanen.
                    </p>
                    
                    <div class="mt-4 flex flex-wrap gap-3">
                        <form action="{{ route('profile.destroy') }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus akun? Semua data akan hilang!')"
                                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium">
                                <i class="fas fa-trash mr-2"></i> Hapus Akun
                            </button>
                        </form>
                        
                        <form action="{{ route('profile.clear-data') }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Hapus semua data? Tindakan ini tidak dapat dibatalkan!')"
                                    class="px-4 py-2 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-sm font-medium">
                                <i class="fas fa-eraser mr-2"></i> Hapus Semua Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function settingsManager() {
        return {
            activeTab: 'profile',
            selectedTheme: '{{ $user->theme ?? "light" }}',
            
            initSettings() {
                // Load from URL hash if exists
                const hash = window.location.hash.replace('#', '');
                if (['profile', 'preferences', 'security', 'account'].includes(hash)) {
                    this.activeTab = hash;
                }
                
                // Update URL hash on tab change
                this.$watch('activeTab', (value) => {
                    window.location.hash = value;
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
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .dark .card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }
</style>
@endpush
@endsection