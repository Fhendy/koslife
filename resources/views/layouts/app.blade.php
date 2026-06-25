<!DOCTYPE html>
<html lang="id" 
      x-data="{ theme: $store.app.theme }" 
      :class="theme">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#4F46E5" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#1e1b4b" media="(prefers-color-scheme: dark)">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>@yield('title', 'KosLife') - Dashboard Anak Kos</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ===== RESET ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow-x: hidden; }
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f9fafb;
            transition: background-color 0.3s ease;
        }
        .dark body { background-color: #111827; }
        [x-cloak] { display: none !important; }

        /* ===== SCROLLBAR ===== */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #6366f1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #4f46e5; }
        .dark ::-webkit-scrollbar-thumb { background: #818cf8; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #6366f1; }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp { 
            from { opacity: 0; transform: translateY(20px); } 
            to { opacity: 1; transform: translateY(0); } 
        }
        @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: .5; } }
        @keyframes skeleton { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
        @keyframes loadingProgress { 0% { width: 0%; } 50% { width: 70%; } 100% { width: 100%; } }
        @keyframes slideInRight { 
            from { opacity: 0; transform: translateX(100%); } 
            to { opacity: 1; transform: translateX(0); } 
        }

        .page-transition { animation: fadeInUp 0.4s ease-out; }
        .animate-pulse { animation: pulse 2s cubic-bezier(.4,0,.6,1) infinite; }
        .loading-bar { animation: loadingProgress 1.5s ease-in-out infinite; }

        /* ===== SKELETON ===== */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton 1.5s ease-in-out infinite;
        }
        .dark .skeleton {
            background: linear-gradient(90deg, #1f2937 25%, #374151 50%, #1f2937 75%);
            background-size: 200% 100%;
        }

        /* ===== TRANSITIONS ===== */
        .transition-sidebar { transition: padding-left 0.3s cubic-bezier(0.4,0,0.2,1); }
        .transition-theme { transition: background-color .3s ease, color .3s ease, border-color .3s ease; }

        /* ===== CARD ===== */
        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            transition: all 0.2s ease;
        }
        .dark .card {
            background: #1f2937;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }
        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        .dark .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }

        /* ===== BUTTONS ===== */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }
        .btn-primary:active { transform: scale(0.98); }

        /* ===== UTILITY ===== */
        .text-balance { text-wrap: balance; }
        .glow-primary { box-shadow: 0 0 30px rgba(99, 102, 241, 0.3); }
        .glow-primary:hover { box-shadow: 0 0 50px rgba(99, 102, 241, 0.5); }
    </style>

    {{-- ===== ALPINE STORE ===== --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('app', {
                sidebarOpen: window.innerWidth >= 1024 
                    ? (localStorage.getItem('sidebarOpen') !== 'false') 
                    : false,
                theme: localStorage.getItem('theme') || 'light',

                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                    localStorage.setItem('sidebarOpen', this.sidebarOpen);
                },
                toggleTheme() {
                    this.theme = this.theme === 'dark' ? 'light' : 'dark';
                    localStorage.setItem('theme', this.theme);
                    document.documentElement.className = this.theme;
                },
            });
        });
    </script>

    @stack('styles')
</head>

<body class="bg-gray-50 dark:bg-gray-900 transition-theme font-sans antialiased">

    {{-- ===== LOADING OVERLAY ===== --}}
    <div x-data="{ show: true }"
         x-init="setTimeout(() => show = false, 500)"
         x-show="show"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[999] flex items-center justify-center bg-white dark:bg-gray-900"
         style="display:flex;">
        <div class="text-center">
            <div class="w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg animate-pulse"
                 style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                <span class="text-white font-bold text-2xl">K</span>
            </div>
            <div class="w-48 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden mx-auto">
                <div class="h-full rounded-full loading-bar" style="background:linear-gradient(90deg,#6366f1,#8b5cf6);"></div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-3 font-medium">Memuat KosLife...</p>
        </div>
    </div>

    {{-- ===== APP SHELL ===== --}}
    <div x-data="{
        isMobile: window.innerWidth < 1024
    }"
    x-init="
        window.addEventListener('resize', () => {
            isMobile = window.innerWidth < 1024;
            if (!isMobile) {
                $store.app.sidebarOpen = localStorage.getItem('sidebarOpen') !== 'false';
                document.body.style.overflow = '';
            }
        });
        $watch('$store.app.sidebarOpen', v => {
            if (isMobile) document.body.style.overflow = v ? 'hidden' : '';
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && isMobile) $store.app.sidebarOpen = false;
        });
    "
    class="min-h-screen flex">

        {{-- ===== SIDEBAR ===== --}}
        <x-sidebar />

        {{-- ===== MOBILE OVERLAY ===== --}}
        <div x-show="isMobile && $store.app.sidebarOpen"
             x-cloak
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-30 bg-black/60 backdrop-blur-sm lg:hidden"
             @click="$store.app.sidebarOpen = false">
        </div>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="flex-1 min-h-screen w-full transition-sidebar"
             :class="{
                 'lg:pl-64': $store.app.sidebarOpen && !isMobile,
                 'lg:pl-20': !$store.app.sidebarOpen && !isMobile,
                 'pl-0': isMobile
             }">

            {{-- ===== TOP NAV ===== --}}
            <x-top-nav />

            {{-- ===== PAGE CONTENT ===== --}}
            <main class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 md:py-6 page-transition">

                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" class="text-indigo-500 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-home"></i>
                    </a>
                    <span class="text-gray-300 dark:text-gray-600">/</span>
                    <span class="font-medium text-gray-700 dark:text-gray-300">
                        @yield('breadcrumb', 'Dashboard')
                    </span>
                </nav>

                {{-- Page Header --}}
                <div class="mb-6">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                        @yield('page-title', 'Dashboard')
                    </h1>
                    @hasSection('page-description')
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @yield('page-description')
                        </p>
                    @endif
                </div>

                {{-- Content --}}
                @yield('content')

            </main>

            {{-- ===== FOOTER ===== --}}
            <footer class="border-t border-gray-200 dark:border-gray-700 mt-8 transition-theme">
                <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-2">
                            <span>© {{ date('Y') }}</span>
                            <span class="font-semibold text-indigo-500">KosLife</span>
                            <span>•</span>
                            <span>v1.0</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                <span>All systems operational</span>
                            </span>
                            <div class="flex items-center gap-3">
                                <a href="#" class="text-gray-400 hover:text-indigo-500 transition-colors">
                                    <i class="fab fa-github"></i>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-indigo-500 transition-colors">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-indigo-500 transition-colors">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    {{-- ===== QUICK ACTIONS ===== --}}
    <x-quick-actions />

    {{-- ===== TOAST CONTAINER ===== --}}
    <div id="toast-container"
         class="fixed bottom-24 right-4 z-50 space-y-2 max-w-xs sm:max-w-sm w-full pointer-events-none">
    </div>

    {{-- ===== SCROLL TO TOP ===== --}}
    <button x-data="{ show: false }"
            x-init="window.addEventListener('scroll', () => { show = window.scrollY > 500 })"
            x-show="show"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-90"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-90"
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="fixed bottom-24 right-4 z-40 p-3 bg-indigo-500 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-110 active:scale-95"
            aria-label="Scroll to top">
        <i class="fas fa-arrow-up text-sm"></i>
    </button>

    {{-- ===== SCRIPTS ===== --}}
    @stack('scripts')

    <script>
        // ===== TOAST NOTIFICATION =====
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const colors = {
                success: 'bg-green-50 dark:bg-green-900/30 border-green-500 text-green-800 dark:text-green-200',
                error: 'bg-red-50 dark:bg-red-900/30 border-red-500 text-red-800 dark:text-red-200',
                warning: 'bg-yellow-50 dark:bg-yellow-900/30 border-yellow-500 text-yellow-800 dark:text-yellow-200',
                info: 'bg-blue-50 dark:bg-blue-900/30 border-blue-500 text-blue-800 dark:text-blue-200'
            };
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };

            const toast = document.createElement('div');
            toast.className = `flex items-start gap-3 p-4 rounded-xl border-l-4 shadow-lg ${colors[type]} pointer-events-auto transition-all duration-300`;
            toast.style.animation = 'slideInRight 0.3s ease-out';
            toast.innerHTML = `
                <i class="fas ${icons[type]} text-lg mt-0.5 flex-shrink-0"></i>
                <span class="flex-1 text-sm font-medium">${message}</span>
                <button onclick="this.closest('div').remove()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 flex-shrink-0 -mt-1">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        };

        // ===== FLASH MESSAGES =====
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                setTimeout(() => showToast('{{ session('success') }}', 'success'), 300);
            @endif
            @if(session('error'))
                setTimeout(() => showToast('{{ session('error') }}', 'error'), 300);
            @endif
            @if(session('warning'))
                setTimeout(() => showToast('{{ session('warning') }}', 'warning'), 300);
            @endif
            @if(session('info'))
                setTimeout(() => showToast('{{ session('info') }}', 'info'), 300);
            @endif

            // ===== LOADING OVERLAY HIDE =====
            setTimeout(() => {
                const loading = document.querySelector('[x-data="{ show: true }"]');
                if (loading && loading.__x) {
                    loading.__x.$data.show = false;
                }
            }, 600);
        });
    </script>

</body>
</html>