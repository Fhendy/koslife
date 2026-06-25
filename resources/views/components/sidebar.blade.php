<aside x-data="{
           get collapsed() { return !$store.app.sidebarOpen },
           isMobile: window.innerWidth < 1024
       }"
       x-init="
           window.addEventListener('resize', () => {
               isMobile = window.innerWidth < 1024;
           });
       "
       class="fixed left-0 top-0 z-40 h-screen transition-all duration-300 ease-in-out"
       :class="{
           'w-64':           !collapsed && !isMobile,
           'w-20':            collapsed && !isMobile,
           'w-72':            isMobile,
           '-translate-x-full': isMobile && !$store.app.sidebarOpen,
           'translate-x-0':     isMobile &&  $store.app.sidebarOpen,
           'lg:translate-x-0':  !isMobile
       }"
       style="background: linear-gradient(180deg, #1e1b4b 0%, #0f172a 100%);"
       x-cloak>

    <div class="flex flex-col h-full relative">

        {{-- Decorative glow --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-20 -right-20 w-48 h-48 rounded-full"
                 style="background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 70%)"></div>
            <div class="absolute -bottom-20 -left-20 w-48 h-48 rounded-full"
                 style="background: radial-gradient(circle, rgba(139,92,246,0.12) 0%, transparent 70%)"></div>
        </div>

        {{-- ===== HEADER ===== --}}
        <div class="relative z-10 flex items-center px-4 py-3.5 border-b border-white/[0.07] flex-shrink-0"
             :class="collapsed && !isMobile ? 'justify-center' : 'justify-between'">

            <div class="flex items-center gap-3 overflow-hidden min-w-0"
                 :class="collapsed && !isMobile ? 'justify-center w-full' : ''">
                {{-- Logo --}}
                <div class="relative flex-shrink-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-base"
                         style="background: linear-gradient(135deg,#6366f1,#8b5cf6); box-shadow: inset 0 0 0 1px rgba(255,255,255,0.15);">
                        K
                    </div>
                    <div class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-green-400 rounded-full border-2 border-[#0f172a]"></div>
                </div>

                {{-- Name --}}
                <div x-show="!collapsed || isMobile"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-x-3"
                     x-transition:enter-end="opacity-100 translate-x-0">
                    <p class="text-white font-bold text-base leading-tight tracking-tight">KosLife</p>
                    <p class="text-white/25 text-[9px] leading-tight tracking-widest uppercase">v1.0</p>
                </div>
            </div>

            {{-- Desktop toggle --}}
            <button x-show="!isMobile"
                    @click="$store.app.sidebarOpen = !$store.app.sidebarOpen; localStorage.setItem('sidebarOpen', $store.app.sidebarOpen)"
                    class="flex items-center justify-center w-7 h-7 rounded-lg text-white/30 hover:text-white/70 hover:bg-white/10 transition-all duration-200 flex-shrink-0"
                    aria-label="Toggle sidebar">
                <i class="fas text-xs transition-transform duration-300"
                   :class="collapsed ? 'fa-chevron-right' : 'fa-chevron-left'"></i>
            </button>

            {{-- Mobile close --}}
            <button x-show="isMobile"
                    @click="$store.app.sidebarOpen = false"
                    class="flex items-center justify-center w-7 h-7 rounded-lg text-white/30 hover:text-white/70 hover:bg-white/10 transition-all duration-200"
                    aria-label="Tutup sidebar">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        {{-- ===== NAV ===== --}}
        <nav class="relative z-10 flex-1 overflow-y-auto overflow-x-hidden px-2.5 py-3 scrollbar-hide">

            @php
                $menuGroups = [
                    [
                        'label' => 'Utama',
                        'items' => [
                            ['icon' => 'fa-gauge-high',     'label' => 'Dashboard',    'route' => 'dashboard',         'badge' => false],
                            ['icon' => 'fa-list-check',     'label' => 'Tugas',        'route' => 'tasks.index',       'badge' => 'overdue'],
                            ['icon' => 'fa-wallet',         'label' => 'Keuangan',     'route' => 'finance.index',     'badge' => false],
                            ['icon' => 'fa-utensils',       'label' => 'Budget Makan', 'route' => 'meal-budget.index', 'badge' => false],
                            ['icon' => 'fa-cart-shopping',  'label' => 'Belanja',      'route' => 'shopping.index',    'badge' => false],
                        ],
                    ],
                    [
                        'label' => 'Produktivitas',
                        'items' => [
                            ['icon' => 'fa-calendar-days',       'label' => 'Kalender',      'route' => 'calendar.index',  'badge' => false],
                            ['icon' => 'fa-clock',               'label' => 'Focus Mode',    'route' => 'focus.index',     'badge' => false],
                            ['icon' => 'fa-arrow-rotate-right',  'label' => 'Habit Tracker', 'route' => 'habits.index',    'badge' => false],
                            ['icon' => 'fa-note-sticky',         'label' => 'Catatan',       'route' => 'notes.index',     'badge' => false],
                            ['icon' => 'fa-bell',                'label' => 'Reminder',      'route' => 'reminders.index', 'badge' => 'reminder'],
                        ],
                    ],
                ];
            @endphp

            @foreach($menuGroups as $gi => $group)

                {{-- Section label --}}
                <div x-show="!collapsed || isMobile"
                     class="{{ $gi > 0 ? 'mt-4' : '' }} mb-1 px-2">
                    <span class="text-[10px] font-semibold text-white/20 tracking-widest uppercase">
                        {{ $group['label'] }}
                    </span>
                </div>

                {{-- Collapsed divider between groups --}}
                @if($gi > 0)
                    <div x-show="collapsed && !isMobile" class="my-3 mx-2 border-t border-white/[0.07]"></div>
                @endif

                <div class="space-y-0.5">
                    @foreach($group['items'] as $menu)
                        @php
                            $isActive = request()->routeIs($menu['route']);
                            $badgeCount = 0;
                            if ($menu['badge'] === 'overdue') {
                                $badgeCount = \App\Models\Task::where('user_id', auth()->id())->overdue()->count();
                            } elseif ($menu['badge'] === 'reminder') {
                                $badgeCount = \App\Models\Reminder::where('user_id', auth()->id())
                                    ->where('is_notified', false)
                                    ->where('reminder_time', '>=', now())
                                    ->count();
                            }
                        @endphp

                        <a href="{{ route($menu['route']) }}"
                           class="relative flex items-center px-2.5 py-2 rounded-xl transition-all duration-200 group
                                  {{ $isActive ? 'text-white sidebar-item-active' : 'text-white/50 hover:text-white hover:bg-white/[0.05]' }}"
                           :class="collapsed && !isMobile ? 'justify-center' : ''"
                           {{ $isActive ? 'aria-current=page' : '' }}>

                            {{-- Active bar --}}
                            @if($isActive)
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-[3px] h-5 rounded-r-full menu-active-glow"
                                     style="background: linear-gradient(180deg,#818cf8,#6366f1);"></div>
                            @endif

                            {{-- Icon --}}
                            <div class="relative flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-200
                                        {{ $isActive ? 'text-indigo-300 sidebar-icon-active' : 'text-white/40 group-hover:text-white/80' }}">
                                <i class="fas {{ $menu['icon'] }} text-sm"></i>
                                @if($badgeCount > 0)
                                    <span x-show="collapsed && !isMobile"
                                          class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full border border-[#0f172a]"></span>
                                @endif
                            </div>

                            {{-- Label --}}
                            <span x-show="!collapsed || isMobile"
                                  x-transition:enter="transition ease-out duration-200"
                                  x-transition:enter-start="opacity-0 -translate-x-2"
                                  x-transition:enter-end="opacity-100 translate-x-0"
                                  class="ml-2.5 text-[13px] font-medium whitespace-nowrap flex-1">
                                {{ $menu['label'] }}
                            </span>

                            {{-- Badge --}}
                            @if($badgeCount > 0)
                                <span x-show="!collapsed || isMobile"
                                      class="ml-auto text-[10px] font-bold bg-red-500 text-white rounded-full px-1.5 py-0.5 leading-none">
                                    {{ $badgeCount > 9 ? '9+' : $badgeCount }}
                                </span>
                            @endif

                            {{-- Tooltip saat collapsed --}}
                            <div x-show="collapsed && !isMobile"
                                 x-cloak
                                 class="absolute left-full ml-3 px-2.5 py-1.5 rounded-lg text-white text-xs font-medium whitespace-nowrap
                                        border border-white/10 shadow-xl hidden group-hover:block z-50 pointer-events-none"
                                 style="background:#1e293b;">
                                {{ $menu['label'] }}
                                @if($badgeCount > 0)
                                    <span class="ml-1.5 px-1.5 py-0.5 text-[10px] font-bold bg-red-500 text-white rounded-full">
                                        {{ $badgeCount }}
                                    </span>
                                @endif
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 -ml-1.5 w-3 h-3 rotate-45 border-l border-t border-white/10"
                                     style="background:#1e293b;"></div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endforeach

            {{-- Divider --}}
            <div class="my-3 mx-2 border-t border-white/[0.07]"></div>

            {{-- Settings --}}
            <a href="{{ route('profile.edit') }}"
               class="relative flex items-center px-2.5 py-2 rounded-xl transition-all duration-200 group
                      text-white/35 hover:text-white hover:bg-white/[0.05]"
               :class="collapsed && !isMobile ? 'justify-center' : ''">
                <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center
                            text-white/35 group-hover:text-white/70 transition-all duration-200">
                    <i class="fas fa-cog text-sm transition-transform duration-300 group-hover:rotate-90"></i>
                </div>
                <span x-show="!collapsed || isMobile"
                      x-transition:enter="transition ease-out duration-200"
                      x-transition:enter-start="opacity-0 -translate-x-2"
                      x-transition:enter-end="opacity-100 translate-x-0"
                      class="ml-2.5 text-[13px] font-medium whitespace-nowrap">
                    Pengaturan
                </span>
                <div x-show="collapsed && !isMobile"
                     x-cloak
                     class="absolute left-full ml-3 px-2.5 py-1.5 rounded-lg text-white text-xs font-medium whitespace-nowrap
                            border border-white/10 shadow-xl hidden group-hover:block z-50 pointer-events-none"
                     style="background:#1e293b;">
                    Pengaturan
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 -ml-1.5 w-3 h-3 rotate-45 border-l border-t border-white/10"
                         style="background:#1e293b;"></div>
                </div>
            </a>
        </nav>

        {{-- ===== USER PROFILE ===== --}}
        <div class="relative z-10 px-2.5 pt-2.5 pb-3 border-t border-white/[0.07] flex-shrink-0">
            <a href="{{ route('profile.edit') }}"
               class="flex items-center p-2 rounded-xl hover:bg-white/[0.05] transition-all duration-200 group"
               :class="collapsed && !isMobile ? 'justify-center' : ''">

                <div class="relative flex-shrink-0">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=4F46E5&color=fff&size=64' }}"
                         class="w-9 h-9 rounded-full object-cover"
                         style="box-shadow: 0 0 0 2px rgba(99,102,241,0.4);"
                         alt="{{ auth()->user()->name }}">
                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 rounded-full border-2 border-[#0f172a]"></div>
                </div>

                <div x-show="!collapsed || isMobile"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-x-2"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="ml-2.5 overflow-hidden flex-1 min-w-0">
                    <p class="text-white text-[13px] font-medium leading-tight truncate">{{ auth()->user()->name }}</p>
                    <p class="text-white/30 text-[11px] leading-tight truncate mt-0.5">{{ auth()->user()->email }}</p>
                </div>

                <div x-show="!collapsed || isMobile"
                     class="text-white/20 group-hover:text-white/50 transition-colors duration-200 flex-shrink-0 ml-1">
                    <i class="fas fa-ellipsis-vertical text-xs"></i>
                </div>

                {{-- Profile tooltip saat collapsed --}}
                <div x-show="collapsed && !isMobile"
                     x-cloak
                     class="absolute left-full ml-3 px-3 py-2 rounded-lg text-white text-xs border border-white/10
                            shadow-xl hidden group-hover:block z-50 min-w-[150px] pointer-events-none"
                     style="background:#1e293b;">
                    <p class="font-medium truncate">{{ auth()->user()->name }}</p>
                    <p class="text-white/50 truncate mt-0.5">{{ auth()->user()->email }}</p>
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 -ml-1.5 w-3 h-3 rotate-45 border-l border-t border-white/10"
                         style="background:#1e293b;"></div>
                </div>
            </a>
        </div>

    </div>
</aside>

<style>
    .scrollbar-hide::-webkit-scrollbar { width: 0; height: 0; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    [x-cloak] { display: none !important; }
    .sidebar-item-active  { background: rgba(99,102,241,0.15); }
    .sidebar-icon-active  { background: rgba(99,102,241,0.20); }
    .menu-active-glow     { animation: glowPulse 3s ease-in-out infinite; }
    @keyframes glowPulse  { 0%,100%{opacity:1} 50%{opacity:.5} }
</style>