<header x-data="{ 
    open: false,
    showNotifications: false,
    showProfile: false,
    searchOpen: false,
    searchQuery: '',
    notifications: [],
    unreadCount: 0
}" 
        x-init="
            // Load notifications
            fetchNotifications();
            // Close dropdowns on escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    showNotifications = false;
                    showProfile = false;
                    searchOpen = false;
                }
            });
        "
        class="sticky top-0 z-30 bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg border-b border-gray-200/50 dark:border-gray-700/50 transition-all duration-200"
        :class="{'shadow-sm': scrollY > 10}"
        @scroll.window="scrollY = window.scrollY">
    
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14 lg:h-16">
            
            {{-- ===== LEFT SECTION ===== --}}
            <div class="flex items-center gap-2 lg:gap-4">
                <!-- Mobile Menu Toggle -->
                <button @click="$store.app.sidebarOpen = !$store.app.sidebarOpen; $store.app.showMobileSidebar = !$store.app.showMobileSidebar"
                        class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 text-gray-600 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-400"
                        aria-label="Toggle sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                
                <!-- Brand Logo (Mobile) -->
                <a href="{{ route('dashboard') }}" class="lg:hidden flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-primary-500/30">
                        K
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white text-lg">KosLife</span>
                </a>
                
                <!-- Breadcrumb -->
                <nav class="hidden sm:flex items-center gap-2 text-sm" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" 
                       class="text-gray-400 hover:text-primary-500 dark:text-gray-500 dark:hover:text-primary-400 transition-colors duration-200">
                        <i class="fas fa-home text-xs"></i>
                    </a>
                    <span class="text-gray-300 dark:text-gray-600 select-none">/</span>
                    <span class="text-gray-600 dark:text-gray-300 font-medium truncate max-w-[150px] sm:max-w-[200px] md:max-w-[300px]">
                        @yield('breadcrumb', 'Dashboard')
                    </span>
                </nav>
            </div>
            
            {{-- ===== RIGHT SECTION ===== --}}
            <div class="flex items-center gap-1 sm:gap-2">
                
                <!-- Search -->
                <div class="relative hidden lg:block">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-xs"></i>
                        <input type="text" 
                               x-model="searchQuery"
                               placeholder="Cari cepat..."
                               class="w-40 focus:w-64 transition-all duration-300 pl-8 pr-4 py-1.5 text-sm bg-gray-100 dark:bg-gray-700/50 border-0 rounded-lg focus:ring-2 focus:ring-primary-500/50 dark:focus:ring-primary-400/50 placeholder-gray-400 dark:placeholder-gray-500 text-gray-700 dark:text-gray-300"
                               @focus="searchOpen = true"
                               @blur="setTimeout(() => searchOpen = false, 200)">
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div x-show="searchOpen && searchQuery.length > 0"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
                         class="absolute top-full left-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden"
                         style="display: none;">
                        <div class="p-2 text-xs text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                            Hasil pencarian untuk "<span x-text="searchQuery" class="font-medium text-gray-700 dark:text-gray-300"></span>"
                        </div>
                        <div class="max-h-64 overflow-y-auto p-2 space-y-1">
                            <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-search text-2xl mb-2 block text-gray-300 dark:text-gray-600"></i>
                                <p>Ketik untuk mencari...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile Search Toggle -->
                <button @click="searchOpen = !searchOpen" 
                        class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 text-gray-600 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-400"
                        aria-label="Search">
                    <i class="fas fa-search text-sm"></i>
                </button>
                
                <!-- Theme Toggle -->
                <button @click="$store.app.toggleTheme()"
                        class="relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 text-gray-600 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-400 group"
                        aria-label="Toggle theme">
                    <i class="fas text-sm transition-transform duration-300 group-hover:rotate-12" 
                       :class="theme === 'dark' ? 'fa-sun text-yellow-400' : 'fa-moon'"
                       :style="{ color: theme === 'dark' ? '#FBBF24' : '#4B5563' }"></i>
                    <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-4 h-4 bg-primary-500 rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300 blur-sm"></span>
                </button>
                
                <!-- Notifications -->
                <div class="relative" @click.away="showNotifications = false">
                    <button @click="showNotifications = !showNotifications"
                            class="relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 text-gray-600 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-400 group"
                            aria-label="Notifications">
                        <i class="fas fa-bell text-sm"></i>
                        <span x-show="unreadCount > 0"
                              x-transition
                              class="absolute -top-0.5 -right-0.5 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full shadow-lg shadow-red-500/30">
                            <span x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                        </span>
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-4 h-4 bg-primary-500 rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300 blur-sm"></span>
                    </button>
                    
                    <!-- Notifications Dropdown -->
                    <div x-show="showNotifications"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
                         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden"
                         style="display: none;">
                        
                        <!-- Header -->
                        <div class="flex items-center justify-between p-3 border-b border-gray-100 dark:border-gray-700">
                            <span class="font-semibold text-gray-900 dark:text-white text-sm">
                                <i class="fas fa-bell text-primary-500 mr-2"></i>
                                Notifikasi
                            </span>
                            <button class="text-xs text-primary-500 hover:text-primary-600 dark:text-primary-400 dark:hover:text-primary-300 transition-colors font-medium">
                                Tandai semua
                            </button>
                        </div>
                        
                        <!-- Notifications List -->
                        <div class="max-h-80 overflow-y-auto p-2 space-y-1">
                            <!-- Empty State -->
                            <div class="text-center py-8">
                                <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-bell-slash text-xl text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Tidak ada notifikasi</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Semua sudah aman 👌</p>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="p-2 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('reminders.index') }}" 
                               class="block text-center text-xs text-primary-500 hover:text-primary-600 dark:text-primary-400 dark:hover:text-primary-300 transition-colors font-medium">
                                Lihat semua notifikasi
                                <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Divider -->
                <div class="hidden sm:block w-px h-6 bg-gray-200 dark:bg-gray-700"></div>
                
                <!-- User Profile -->
                <div class="relative" @click.away="showProfile = false">
                    <button @click="showProfile = !showProfile"
                            class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 group"
                            aria-label="Profile">
                        <!-- Avatar -->
                        <div class="relative flex-shrink-0">
                            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=4F46E5&color=fff&size=64' }}"
                                 class="w-8 h-8 lg:w-9 lg:h-9 rounded-full ring-2 ring-transparent group-hover:ring-primary-400 transition-all duration-300"
                                 alt="{{ auth()->user()->name }}">
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"></span>
                        </div>
                        
                        <!-- User Info (Desktop) -->
                        <div class="hidden lg:block text-left">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-200 leading-none">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 leading-none mt-0.5">
                                {{ auth()->user()->email }}
                            </p>
                        </div>
                        
                        <!-- Chevron -->
                        <i class="fas fa-chevron-down text-[10px] text-gray-400 dark:text-gray-500 transition-transform duration-200 hidden lg:block"
                           :class="{'rotate-180': showProfile}"></i>
                    </button>
                    
                    <!-- Profile Dropdown -->
                    <div x-show="showProfile"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
                         class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden"
                         style="display: none;">
                        
                        <!-- User Info -->
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=4F46E5&color=fff&size=64' }}"
                                 class="w-12 h-12 rounded-full ring-2 ring-primary-500/30"
                                 alt="{{ auth()->user()->name }}">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ auth()->user()->name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ auth()->user()->email }}
                                </p>
                                <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full">
                                    <i class="fas fa-circle text-[6px] mr-1 text-green-500"></i>
                                    Online
                                </span>
                            </div>
                        </div>
                        
                        <!-- Menu Items -->
                        <div class="p-1">
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-700 dark:text-gray-300">
                                <i class="fas fa-user-circle w-4 text-center text-gray-400 dark:text-gray-500"></i>
                                <span class="text-sm">Profil Saya</span>
                            </a>
                            <a href="{{ route('dashboard') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-700 dark:text-gray-300">
                                <i class="fas fa-gauge-high w-4 text-center text-gray-400 dark:text-gray-500"></i>
                                <span class="text-sm">Dashboard</span>
                            </a>
                            <a href="{{ route('focus.index') }}" 
                               class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-700 dark:text-gray-300">
                                <i class="fas fa-clock w-4 text-center text-gray-400 dark:text-gray-500"></i>
                                <span class="text-sm">Focus Mode</span>
                            </a>
                            <a href="#" 
                               class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-700 dark:text-gray-300">
                                <i class="fas fa-question-circle w-4 text-center text-gray-400 dark:text-gray-500"></i>
                                <span class="text-sm">Bantuan</span>
                            </a>
                            
                            <hr class="my-1 border-gray-100 dark:border-gray-700">
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-red-600 dark:text-red-400">
                                    <i class="fas fa-sign-out-alt w-4 text-center"></i>
                                    <span class="text-sm font-medium">Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- ===== MOBILE SEARCH BAR ===== --}}
    <div x-show="searchOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="lg:hidden px-3 pb-3 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800"
         style="display: none;">
        <div class="flex items-center bg-gray-100 dark:bg-gray-700/50 rounded-xl px-3 py-2 mt-2">
            <i class="fas fa-search text-gray-400 dark:text-gray-500 text-sm"></i>
            <input type="text" 
                   x-model="searchQuery"
                   placeholder="Cari tugas, catatan, keuangan..." 
                   class="bg-transparent border-none focus:ring-0 text-sm text-gray-700 dark:text-gray-300 w-full ml-2 placeholder-gray-400 dark:placeholder-gray-500"
                   @focus="searchOpen = true">
            <button @click="searchOpen = false" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>
</header>

{{-- ===== STYLES ===== --}}
<style>
    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }
    
    /* Custom scrollbar for dropdowns */
    .max-h-80::-webkit-scrollbar {
        width: 4px;
    }
    .max-h-80::-webkit-scrollbar-track {
        background: transparent;
    }
    .max-h-80::-webkit-scrollbar-thumb {
        background: #D1D5DB;
        border-radius: 2px;
    }
    .dark .max-h-80::-webkit-scrollbar-thumb {
        background: #4B5563;
    }
    
    /* Backdrop blur support */
    @supports (backdrop-filter: blur(20px)) {
        .backdrop-blur-lg {
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
    }
    
    /* Hover glow effect */
    .hover\\:glow:hover {
        box-shadow: 0 0 20px rgba(79, 70, 229, 0.15);
    }
</style>