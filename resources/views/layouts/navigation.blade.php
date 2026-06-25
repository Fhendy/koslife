<header x-data="{ 
    open: false,
    notifications: [], 
    showNotifications: false,
    searchOpen: false,
    searchQuery: ''
}" 
        class="sticky top-0 z-30 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 transition-colors duration-200">
    
    <div class="px-4 h-16 flex items-center justify-between">
        
        <!-- ===== LEFT SECTION ===== -->
        <div class="flex items-center gap-3">
            <!-- Mobile Menu Toggle -->
            <button @click="$store.app.sidebarOpen = !$store.app.sidebarOpen; $store.app.showMobileSidebar = !$store.app.showMobileSidebar"
                    class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 text-gray-600 dark:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            
            <!-- Breadcrumb -->
            <nav class="hidden sm:flex items-center gap-2 text-sm">
                <a href="{{ route('dashboard') }}" 
                   class="text-gray-400 hover:text-primary-500 dark:text-gray-500 dark:hover:text-primary-400 transition-colors">
                    <i class="fas fa-home"></i>
                </a>
                <span class="text-gray-300 dark:text-gray-600">/</span>
                <span class="text-gray-700 dark:text-gray-300 font-medium truncate max-w-[200px]">
                    @yield('breadcrumb', 'Dashboard')
                </span>
            </nav>
        </div>
        
        <!-- ===== RIGHT SECTION ===== -->
        <div class="flex items-center gap-2">
            
            <!-- Search Button (Mobile) -->
            <button @click="searchOpen = !searchOpen" 
                    class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-600 dark:text-gray-300">
                <i class="fas fa-search"></i>
            </button>
            
            <!-- Search Bar (Desktop) -->
            <div x-show="!searchOpen" 
                 class="hidden lg:flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg px-3 py-2 transition-all duration-200">
                <i class="fas fa-search text-gray-400 dark:text-gray-500 text-sm"></i>
                <input type="text" 
                       placeholder="Cari..." 
                       class="bg-transparent border-none focus:ring-0 text-sm text-gray-700 dark:text-gray-300 w-40 focus:w-56 transition-all duration-300 placeholder-gray-400 dark:placeholder-gray-500">
            </div>
            
            <!-- Search Bar (Mobile Expanded) -->
            <div x-show="searchOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute top-full left-0 right-0 p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-lg lg:hidden">
                <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg px-3 py-2">
                    <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
                    <input type="text" 
                           placeholder="Cari tugas, catatan, dll..." 
                           class="bg-transparent border-none focus:ring-0 text-sm text-gray-700 dark:text-gray-300 w-full ml-2 placeholder-gray-400 dark:placeholder-gray-500">
                </div>
            </div>
            
            <!-- Theme Toggle -->
            <button @click="$store.app.toggleTheme()"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 text-gray-600 dark:text-gray-300 relative group"
                    aria-label="Toggle theme">
                <i class="fas text-sm" 
                   :class="theme === 'dark' ? 'fa-sun' : 'fa-moon'"
                   :style="{ color: theme === 'dark' ? '#FBBF24' : '#4B5563' }"></i>
                <span class="absolute inset-0 rounded-lg group-hover:scale-110 transition-transform"></span>
            </button>
            
            <!-- Notifications -->
            <div class="relative" 
                 x-data="{ showDropdown: false }"
                 @click.away="showDropdown = false">
                
                <button @click="showDropdown = !showDropdown"
                        class="relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 text-gray-600 dark:text-gray-300">
                    <i class="fas fa-bell text-sm"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                </button>
                
                <!-- Notification Dropdown -->
                <div x-show="showDropdown"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                     x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
                     class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
                     style="display: none;">
                    
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-900 dark:text-white">Notifikasi</span>
                            <button class="text-xs text-primary-500 hover:text-primary-600 transition-colors">
                                Tandai semua
                            </button>
                        </div>
                    </div>
                    
                    <div class="max-h-64 overflow-y-auto">
                        <!-- Empty State -->
                        <div class="p-6 text-center">
                            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-bell-slash text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Semua sudah aman 👌</p>
                        </div>
                    </div>
                    
                    <div class="p-2 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('reminders.index') }}" 
                           class="block text-center text-xs text-primary-500 hover:text-primary-600 transition-colors">
                            Lihat semua notifikasi
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- User Profile -->
            <div class="relative ml-1" 
                 x-data="{ showDropdown: false }"
                 @click.away="showDropdown = false">
                
                <button @click="showDropdown = !showDropdown"
                        class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=4F46E5&color=fff&size=64' }}"
                         class="w-8 h-8 rounded-full ring-2 ring-transparent hover:ring-primary-500 transition-all duration-200"
                         alt="{{ auth()->user()->name }}">
                    <div class="hidden lg:block text-left">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200 leading-none">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-none mt-0.5">
                            {{ auth()->user()->email }}
                        </p>
                    </div>
                    <i class="fas fa-chevron-down text-xs text-gray-400 dark:text-gray-500 hidden lg:block"></i>
                </button>
                
                <!-- Profile Dropdown -->
                <div x-show="showDropdown"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                     x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
                     class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
                     style="display: none;">
                    
                    <!-- User Info -->
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                        <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=4F46E5&color=fff&size=64' }}"
                             class="w-10 h-10 rounded-full"
                             alt="{{ auth()->user()->name }}">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ auth()->user()->email }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Menu Items -->
                    <div class="p-1">
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-700 dark:text-gray-300">
                            <i class="fas fa-user-circle text-gray-400 dark:text-gray-500 w-4"></i>
                            <span class="text-sm">Profil</span>
                        </a>
                        
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-700 dark:text-gray-300">
                            <i class="fas fa-gauge-high text-gray-400 dark:text-gray-500 w-4"></i>
                            <span class="text-sm">Dashboard</span>
                        </a>
                        
                        <a href="{{ route('focus.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-700 dark:text-gray-300">
                            <i class="fas fa-clock text-gray-400 dark:text-gray-500 w-4"></i>
                            <span class="text-sm">Focus Mode</span>
                        </a>
                        
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-red-600 dark:text-red-400">
                                <i class="fas fa-sign-out-alt w-4"></i>
                                <span class="text-sm">Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Quick Add (Desktop) -->
            <div class="hidden lg:flex items-center">
                <button @click="$dispatch('open-modal', 'quick-add')"
                        class="p-2 rounded-lg bg-primary-500 text-white hover:bg-primary-600 transition-colors duration-200 shadow-lg shadow-primary-500/20">
                    <i class="fas fa-plus text-sm"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- ===== MOBILE MENU ===== -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="lg:hidden border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800"
         style="display: none;">
        
        <div class="px-4 py-3 space-y-2">
            <!-- Search -->
            <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg px-3 py-2">
                <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
                <input type="text" 
                       placeholder="Cari..." 
                       class="bg-transparent border-none focus:ring-0 text-sm text-gray-700 dark:text-gray-300 w-full ml-2 placeholder-gray-400 dark:placeholder-gray-500">
            </div>
            
            <!-- Quick Links -->
            <a href="{{ route('tasks.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-tasks text-gray-400 dark:text-gray-500"></i>
                <span class="text-sm text-gray-700 dark:text-gray-300">Tugas</span>
            </a>
            
            <a href="{{ route('finance.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-wallet text-gray-400 dark:text-gray-500"></i>
                <span class="text-sm text-gray-700 dark:text-gray-300">Keuangan</span>
            </a>
            
            <a href="{{ route('calendar.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-calendar-days text-gray-400 dark:text-gray-500"></i>
                <span class="text-sm text-gray-700 dark:text-gray-300">Kalender</span>
            </a>
            
            <hr class="border-gray-200 dark:border-gray-700">
            
            <!-- User -->
            <div class="flex items-center gap-3 px-3 py-2">
                <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=4F46E5&color=fff&size=64' }}"
                     class="w-8 h-8 rounded-full"
                     alt="{{ auth()->user()->name }}">
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ auth()->user()->email }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- ===== STYLES ===== --}}
<style>
    /* Search input focus */
    input[type="text"]:focus {
        outline: none;
        box-shadow: none;
    }
    
    /* Notification badge pulse */
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    /* Dark mode transition */
    .dark {
        color-scheme: dark;
    }
</style>