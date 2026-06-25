@extends('layouts.app')

@section('title', 'Focus Mode')
@section('breadcrumb', 'Focus Mode')
@section('page-title', '⏱️ Focus Mode')
@section('page-description', 'Tingkatkan produktivitas dengan Pomodoro Timer')

@section('content')
<div x-data="focusManager()" x-init="initFocus()" class="space-y-6">

    {{-- ===== STATISTIK ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Fokus Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($todayMinutes / 60, 1) }} jam
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <i class="fas fa-clock text-xl text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sesi Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $todayCompleted }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Fokus Minggu Ini</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($weeklyMinutes / 60, 1) }} jam
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <i class="fas fa-calendar-week text-xl text-emerald-600 dark:text-emerald-400"></i>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Best Session</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $bestSession ? number_format($bestSession->duration / 60, 1) : 0 }} jam
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <i class="fas fa-trophy text-xl text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ALARM PERMISSION BANNER ===== --}}
    <div x-show="notifPermission === 'default'"
         x-cloak
         class="flex items-center justify-between gap-4 px-5 py-3.5 rounded-xl border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/20">
        <div class="flex items-center gap-3 text-sm text-indigo-700 dark:text-indigo-300">
            <i class="fas fa-bell text-base flex-shrink-0"></i>
            <span>Aktifkan notifikasi browser agar alarm tampil meski tab tidak aktif</span>
        </div>
        <button @click="requestNotifPermission()"
                class="flex-shrink-0 px-4 py-1.5 bg-indigo-500 text-white text-sm rounded-lg hover:bg-indigo-600 transition-colors font-medium">
            Izinkan
        </button>
    </div>

    {{-- ===== TIMER ===== --}}
    <div class="card">
        <div class="flex flex-col items-center">

            {{-- SVG Ring --}}
            <div class="relative" style="width:280px;height:280px;">
                <svg width="280" height="280" viewBox="0 0 280 280" style="transform:rotate(-90deg);">
                    <circle cx="140" cy="140" r="120"
                            fill="none" stroke="#E5E7EB" stroke-width="10"
                            class="dark:stroke-gray-700"/>
                    <circle cx="140" cy="140" r="120"
                            fill="none"
                            :stroke="isBreak ? '#10b981' : '#6366f1'"
                            stroke-width="10"
                            stroke-linecap="round"
                            :stroke-dasharray="circumference"
                            :stroke-dashoffset="progressOffset"
                            class="transition-all duration-1000"/>
                </svg>

                <div class="absolute inset-0 flex flex-col items-center justify-center gap-1">
                    {{-- Pulse ring saat running --}}
                    <div x-show="isRunning"
                         class="absolute inset-0 rounded-full animate-ping-slow opacity-20"
                         :style="isBreak ? 'box-shadow:0 0 0 8px #10b981' : 'box-shadow:0 0 0 8px #6366f1'"></div>

                    <span class="text-5xl font-bold text-gray-900 dark:text-white font-mono tracking-tight"
                          x-text="displayTime"></span>

                    <span class="text-xs font-semibold px-3 py-0.5 rounded-full"
                          :class="isBreak
                              ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                              : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400'"
                          x-text="sessionType"></span>

                    <span class="text-xs text-gray-400 dark:text-gray-500 max-w-[160px] truncate text-center px-2"
                          x-show="currentTask"
                          x-text="'📌 ' + currentTask"></span>

                    <span class="text-xs font-medium"
                          :class="progressPercent <= 10 ? 'text-red-500' : 'text-gray-400 dark:text-gray-500'"
                          x-text="progressPercent + '%'"></span>
                </div>
            </div>

            {{-- Controls --}}
            <div class="flex flex-wrap items-center justify-center gap-3 mt-6">
                <button @click="startTimer()"
                        x-show="!isRunning && !isPaused"
                        class="inline-flex items-center gap-2 px-8 py-3 text-white rounded-xl font-medium text-base shadow-lg transition-all duration-200 hover:scale-105 active:scale-95"
                        style="background:linear-gradient(135deg,#6366f1,#8b5cf6);box-shadow:0 4px 15px rgba(99,102,241,0.35);">
                    <i class="fas fa-play"></i> Mulai
                </button>

                <button @click="pauseTimer()"
                        x-show="isRunning"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-yellow-500 text-white rounded-xl hover:bg-yellow-600 transition-all duration-200 font-medium text-base hover:scale-105 active:scale-95">
                    <i class="fas fa-pause"></i> Jeda
                </button>

                <button @click="resumeTimer()"
                        x-show="isPaused"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 transition-all duration-200 font-medium text-base hover:scale-105 active:scale-95">
                    <i class="fas fa-play"></i> Lanjut
                </button>

                <button @click="skipBreak()"
                        x-show="isBreak && !isRunning"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition-all duration-200 font-medium text-base">
                    <i class="fas fa-forward"></i> Skip Break
                </button>

                <button @click="resetTimer()"
                        class="inline-flex items-center gap-2 px-6 py-3 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 font-medium text-base">
                    <i class="fas fa-redo text-sm"></i> Reset
                </button>

                {{-- Volume toggle --}}
                <button @click="alarmEnabled = !alarmEnabled"
                        :title="alarmEnabled ? 'Matikan alarm' : 'Aktifkan alarm'"
                        class="w-11 h-11 flex items-center justify-center rounded-xl border border-gray-200 dark:border-gray-600 transition-all duration-200"
                        :class="alarmEnabled
                            ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-800 text-indigo-500'
                            : 'text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'">
                    <i class="fas text-sm" :class="alarmEnabled ? 'fa-volume-up' : 'fa-volume-mute'"></i>
                </button>
            </div>

            {{-- Status --}}
            <div class="flex items-center gap-4 mt-4 text-sm flex-wrap justify-center">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full"
                          :class="isRunning ? 'bg-emerald-500 animate-pulse' : isPaused ? 'bg-yellow-500' : 'bg-gray-400'"></span>
                    <span class="text-gray-500 dark:text-gray-400" x-text="statusText"></span>
                </span>
                <span class="text-gray-300 dark:text-gray-600">|</span>
                <span class="text-gray-500 dark:text-gray-400">
                    Sesi ke: <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="sessionCount"></span>
                </span>
                <span class="text-gray-300 dark:text-gray-600">|</span>
                <span class="text-gray-500 dark:text-gray-400">
                    <span x-text="focusTime"></span>m fokus · <span x-text="breakTime"></span>m istirahat
                </span>
                <span class="text-gray-300 dark:text-gray-600">|</span>
                <span class="flex items-center gap-1 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-bell text-xs"></i>
                    <span x-text="alarmEnabled ? 'Alarm ON' : 'Alarm OFF'"
                          :class="alarmEnabled ? 'text-indigo-500' : 'text-gray-400'"></span>
                </span>
            </div>
        </div>
    </div>

    {{-- ===== PRESET ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card cursor-pointer select-none transition-all duration-200"
             @click="setPreset('study')"
             :class="currentPreset==='study' ? 'ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/10' : 'hover:shadow-md'">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-xl flex-shrink-0">📚</div>
                <div class="min-w-0">
                    <h4 class="font-semibold text-gray-900 dark:text-white">Study</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $presets['study']['focus'] }}m · {{ $presets['study']['break'] }}m break</p>
                </div>
            </div>
        </div>
        <div class="card cursor-pointer select-none transition-all duration-200"
             @click="setPreset('pkl')"
             :class="currentPreset==='pkl' ? 'ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/10' : 'hover:shadow-md'">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-xl flex-shrink-0">💼</div>
                <div class="min-w-0">
                    <h4 class="font-semibold text-gray-900 dark:text-white">PKL</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $presets['pkl']['focus'] }}m · {{ $presets['pkl']['break'] }}m break</p>
                </div>
            </div>
        </div>
        <div class="card cursor-pointer select-none transition-all duration-200"
             @click="setPreset('deep_work')"
             :class="currentPreset==='deep_work' ? 'ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/10' : 'hover:shadow-md'">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-xl flex-shrink-0">🧠</div>
                <div class="min-w-0">
                    <h4 class="font-semibold text-gray-900 dark:text-white">Deep Work</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $presets['deep_work']['focus'] }}m · {{ $presets['deep_work']['break'] }}m break</p>
                </div>
            </div>
        </div>
        <div class="card cursor-pointer select-none transition-all duration-200"
             @click="showCustomModal = true"
             :class="currentPreset==='custom' ? 'ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/10' : 'hover:shadow-md'">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xl flex-shrink-0">⚙️</div>
                <div class="min-w-0">
                    <h4 class="font-semibold text-gray-900 dark:text-white">Custom</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        <span x-text="currentPreset==='custom' ? focusTime : customFocusTime">25</span>m ·
                        <span x-text="currentPreset==='custom' ? breakTime : customBreakTime">5</span>m break
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TASK INPUT ===== --}}
    <div class="card">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-tasks absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text"
                       x-model="currentTask"
                       @keydown.enter="startTimer()"
                       placeholder="Apa yang akan kamu kerjakan? (Tekan Enter untuk mulai)"
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
            </div>
            <button @click="startTimer()"
                    class="px-6 py-2.5 bg-indigo-500 text-white rounded-xl hover:bg-indigo-600 transition-colors whitespace-nowrap font-medium text-sm">
                <i class="fas fa-play mr-2"></i>Mulai Fokus
            </button>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
            <i class="fas fa-info-circle mr-1"></i>
            Masukkan tugas, lalu klik Mulai Fokus atau tekan Enter
        </p>
    </div>

    {{-- ===== HISTORY ===== --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-history text-indigo-500 mr-2"></i>Riwayat Sesi
            </h3>
            <button @click="loadHistory()"
                    class="text-sm text-indigo-500 hover:text-indigo-600 transition-colors flex items-center gap-1.5">
                <i class="fas fa-sync-alt" :class="loadingHistory ? 'animate-spin' : ''"></i> Refresh
            </button>
        </div>
        <div x-show="!loadingHistory" x-html="historyHtml" class="space-y-1"></div>
        <div x-show="loadingHistory" class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
            <p class="text-sm text-gray-400 mt-2">Memuat riwayat...</p>
        </div>
    </div>

    {{-- ===== CUSTOM MODAL ===== --}}
    <div x-show="showCustomModal"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
         @click.self="showCustomModal = false"
         @keydown.escape.window="showCustomModal = false">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm mx-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xl">⚙️</div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Custom Timer</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Atur durasi sesuai kebutuhanmu</p>
                    </div>
                </div>
                <button @click="showCustomModal = false"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="p-5 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-brain text-indigo-500 mr-1.5"></i>Durasi Fokus
                    </label>
                    <div class="flex items-center gap-2">
                        <button @click="customFocusTime = Math.max(1, parseInt(customFocusTime) - 5)"
                                class="w-9 h-9 rounded-lg border border-gray-200 dark:border-gray-600 flex items-center justify-center text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-bold text-lg">−</button>
                        <input type="number" x-model="customFocusTime" min="1" max="180"
                               class="flex-1 text-center py-2 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-semibold">
                        <button @click="customFocusTime = Math.min(180, parseInt(customFocusTime) + 5)"
                                class="w-9 h-9 rounded-lg border border-gray-200 dark:border-gray-600 flex items-center justify-center text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-bold text-lg">+</button>
                        <span class="text-sm text-gray-500 w-10">menit</span>
                    </div>
                    <div class="flex gap-1.5 mt-2">
                        @foreach([15, 25, 30, 45, 60, 90] as $m)
                            <button @click="customFocusTime = {{ $m }}"
                                    :class="customFocusTime == {{ $m }} ? 'bg-indigo-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                    class="flex-1 py-1 text-xs rounded-lg transition-colors font-medium">{{ $m }}m</button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-coffee text-emerald-500 mr-1.5"></i>Durasi Istirahat
                    </label>
                    <div class="flex items-center gap-2">
                        <button @click="customBreakTime = Math.max(1, parseInt(customBreakTime) - 1)"
                                class="w-9 h-9 rounded-lg border border-gray-200 dark:border-gray-600 flex items-center justify-center text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-bold text-lg">−</button>
                        <input type="number" x-model="customBreakTime" min="1" max="60"
                               class="flex-1 text-center py-2 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-semibold">
                        <button @click="customBreakTime = Math.min(60, parseInt(customBreakTime) + 1)"
                                class="w-9 h-9 rounded-lg border border-gray-200 dark:border-gray-600 flex items-center justify-center text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-bold text-lg">+</button>
                        <span class="text-sm text-gray-500 w-10">menit</span>
                    </div>
                    <div class="flex gap-1.5 mt-2">
                        @foreach([5, 10, 15, 20] as $m)
                            <button @click="customBreakTime = {{ $m }}"
                                    :class="customBreakTime == {{ $m }} ? 'bg-emerald-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                    class="flex-1 py-1 text-xs rounded-lg transition-colors font-medium">{{ $m }}m</button>
                        @endforeach
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-center text-sm text-gray-600 dark:text-gray-300">
                    <i class="fas fa-eye text-gray-400 mr-1.5"></i>
                    <span x-text="customFocusTime"></span>m fokus → <span x-text="customBreakTime"></span>m istirahat
                    <span class="text-xs text-gray-400 block mt-0.5">
                        Total 1 siklus: <span x-text="parseInt(customFocusTime || 0) + parseInt(customBreakTime || 0)"></span> menit
                    </span>
                </div>
            </div>

            <div class="flex gap-3 px-5 pb-5">
                <button @click="applyCustomPreset()"
                        class="flex-1 py-2.5 text-white rounded-xl font-medium text-sm transition-all hover:opacity-90"
                        style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                    <i class="fas fa-check mr-2"></i>Terapkan
                </button>
                <button @click="showCustomModal = false"
                        class="flex-1 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function focusManager() {
    return {
        // ─── state ───────────────────────────────────────────────
        currentPreset:   'study',
        presets:         @json($presets),
        focusTime:       25,
        breakTime:       5,
        totalSeconds:    25 * 60,
        currentTime:     25 * 60,
        isRunning:       false,
        isPaused:        false,
        isBreak:         false,
        sessionCount:    0,
        currentTask:     '',
        sessionId:       null,

        showCustomModal: false,
        customFocusTime: 25,
        customBreakTime: 5,

        displayTime:     '25:00',
        sessionType:     'Fokus',
        statusText:      'Siap',
        circumference:   2 * Math.PI * 120,
        progressOffset:  0,
        progressPercent: 100,

        historyHtml:     '',
        loadingHistory:  false,

        // ─── ALARM ───────────────────────────────────────────────
        alarmEnabled:    true,
        notifPermission: 'default',
        _audioCtx:       null,
        _alarmRepeat:    0,

        // ─── init ────────────────────────────────────────────────
        initFocus() {
            this.circumference   = 2 * Math.PI * 120;
            this.notifPermission = ('Notification' in window) ? Notification.permission : 'denied';
            this.setPreset('study');
            this.loadHistory();

            setInterval(() => {
                if (!this.isRunning) return;
                if (this.currentTime > 0) {
                    this.currentTime--;
                    this.updateDisplay();
                    // Alarm 10 detik sebelum selesai
                    if (this.currentTime === 10) this.playWarningBeep();
                } else {
                    this.timerComplete();
                }
            }, 1000);
        },

        // ─── alarm helpers ───────────────────────────────────────
        getAudioCtx() {
            if (!this._audioCtx || this._audioCtx.state === 'closed') {
                this._audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            // Resume jika suspended (browser autoplay policy)
            if (this._audioCtx.state === 'suspended') this._audioCtx.resume();
            return this._audioCtx;
        },

        playTone(freq, startTime, duration, volume = 0.4, type = 'sine') {
            try {
                const ctx  = this.getAudioCtx();
                const osc  = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type            = type;
                osc.frequency.value = freq;
                gain.gain.setValueAtTime(0, startTime);
                gain.gain.linearRampToValueAtTime(volume, startTime + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.001, startTime + duration);
                osc.start(startTime);
                osc.stop(startTime + duration);
            } catch(e) { console.warn('Audio error:', e); }
        },

        playWarningBeep() {
            if (!this.alarmEnabled) return;
            // 3 beep pendek sebagai peringatan
            try {
                const ctx = this.getAudioCtx();
                const t   = ctx.currentTime;
                [0, 0.3, 0.6].forEach(offset => this.playTone(880, t + offset, 0.2, 0.2));
            } catch(e) {}
        },

        playAlarm(repeat = 0) {
            if (!this.alarmEnabled) return;

            // Melodi berbeda: fokus selesai vs break selesai
            const melody = this.isBreak
                ? [[523, 0.3], [659, 0.3], [784, 0.3], [1047, 0.5]]   // C-E-G-C (naik, semangat!)
                : [[784, 0.3], [659, 0.3], [523, 0.3], [392, 0.5]];   // G-E-C-G (turun, rileks)

            try {
                const ctx = this.getAudioCtx();
                let   t   = ctx.currentTime;

                melody.forEach(([freq, dur]) => {
                    this.playTone(freq, t, dur, 0.4, 'sine');
                    t += dur + 0.05;
                });

                // Ulangi alarm 3x dengan jeda 1.5 detik
                if (repeat < 2) {
                    const totalDur = melody.reduce((s,[,d]) => s + d + 0.05, 0) * 1000;
                    setTimeout(() => this.playAlarm(repeat + 1), totalDur + 800);
                }
            } catch(e) { console.warn('Alarm error:', e); }
        },

        async requestNotifPermission() {
            if (!('Notification' in window)) return;
            const result = await Notification.requestPermission();
            this.notifPermission = result;
            if (result === 'granted') window.showToast('Notifikasi diaktifkan! 🔔', 'success');
        },

        sendNotification() {
            if (!('Notification' in window) || Notification.permission !== 'granted') return;
            const isBreakEnd = this.isBreak; // state sebelum di-toggle di timerComplete
            const title = isBreakEnd ? '☕ Istirahat Selesai!' : '🎉 Sesi Fokus Selesai!';
            const body  = isBreakEnd
                ? 'Waktunya fokus lagi. Semangat! 💪'
                : `"${this.currentTask}" selesai! Waktunya istirahat ☕`;
            try {
                new Notification(title, {
                    body,
                    icon:     '/images/icon-192.png',
                    tag:      'focus-alarm',
                    renotify: true,
                });
            } catch(e) {}
        },

        // ─── preset ──────────────────────────────────────────────
        setPreset(preset) {
            if (this.isRunning && !confirm('Timer sedang berjalan. Ganti preset dan reset?')) return;
            if (this.isRunning) this.stopCurrentSession();
            this.currentPreset = preset;
            const p = this.presets[preset];
            if (p) { this.focusTime = p.focus; this.breakTime = p.break; }
            this.resetState();
        },

        applyCustomPreset() {
            const focus = Math.max(1, Math.min(180, parseInt(this.customFocusTime) || 25));
            const brk   = Math.max(1, Math.min(60,  parseInt(this.customBreakTime) || 5));
            this.presets.custom = { focus, break: brk };
            this.currentPreset  = 'custom';
            this.focusTime      = focus;
            this.breakTime      = brk;
            this.showCustomModal = false;
            this.resetState();
            window.showToast(`Custom: ${focus}m fokus · ${brk}m istirahat ⚙️`, 'success');
        },

        resetState() {
            this.isRunning    = false;
            this.isPaused     = false;
            this.isBreak      = false;
            this.sessionId    = null;
            this.statusText   = 'Siap';
            this.sessionType  = 'Fokus';
            this.currentTime  = this.focusTime * 60;
            this.totalSeconds = this.focusTime * 60;
            this.updateDisplay();
        },

        // ─── timer controls ──────────────────────────────────────
        startTimer() {
            if (!this.currentTask.trim()) {
                window.showToast('Masukkan tugas yang akan dikerjakan! 📝', 'warning');
                return;
            }
            if (this.isRunning) return;
            if (this.isPaused && this.sessionId) { this.resumeTimer(); return; }

            // Unlock AudioContext di sini (user gesture)
            try { this.getAudioCtx(); } catch(e) {}

            this.isRunning   = true;
            this.isPaused    = false;
            this.statusText  = 'Berjalan';
            this.sessionType = this.isBreak ? 'Istirahat' : 'Fokus';

            fetch('{{ route('focus.start') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                },
                body: JSON.stringify({
                    task:         this.currentTask,
                    duration:     this.isBreak ? this.breakTime : this.focusTime,
                    session_type: this.currentPreset,
                }),
            })
            .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
            .then(data => {
                if (data.success) {
                    this.sessionId = data.session.id;
                    this.sessionCount++;
                    window.showToast(data.message || '🎯 Sesi dimulai!', 'success');
                } else throw new Error(data.message || 'Gagal');
            })
            .catch(err => {
                this.isRunning  = false;
                this.statusText = 'Siap';
                window.showToast(`Gagal memulai sesi: ${err.message}`, 'error');
            });
        },

        pauseTimer() {
            if (!this.isRunning) return;
            this.isRunning  = false;
            this.isPaused   = true;
            this.statusText = 'Dijeda';
            if (!this.sessionId) return;
            fetch(`/focus/${this.sessionId}/pause`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            }).then(r => r.json()).then(d => { if (d.success) window.showToast(d.message, 'info'); }).catch(() => {});
        },

        resumeTimer() {
            if (!this.isPaused) return;
            this.isRunning  = true;
            this.isPaused   = false;
            this.statusText = 'Berjalan';
            if (!this.sessionId) return;
            fetch(`/focus/${this.sessionId}/resume`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            }).then(r => r.json()).then(d => { if (d.success) window.showToast(d.message, 'info'); }).catch(() => {});
        },

        resetTimer() {
            if ((this.isRunning || this.isPaused) && this.sessionId) this.stopCurrentSession();
            this.resetState();
            window.showToast('Timer direset ↺', 'info');
        },

        skipBreak() {
            this.isBreak      = false;
            this.sessionType  = 'Fokus';
            this.currentTime  = this.focusTime * 60;
            this.totalSeconds = this.focusTime * 60;
            this.updateDisplay();
            window.showToast('Break dilewati. Siap fokus! 🎯', 'info');
        },

        stopCurrentSession() {
            if (!this.sessionId) return;
            fetch(`/focus/${this.sessionId}/stop`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            }).catch(() => {});
            this.sessionId = null;
        },

        timerComplete() {
            this.isRunning  = false;
            this.statusText = 'Selesai!';

            // ══ ALARM ══
            this.playAlarm();
            this.sendNotification();

            if (this.sessionId) {
                fetch(`/focus/${this.sessionId}/complete`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                })
                .then(r => r.json())
                .then(d => { if (d.success) { window.showToast(d.message, 'success'); this.loadHistory(); } })
                .catch(() => {});
                this.sessionId = null;
            }

            if (this.isBreak) {
                this.isBreak      = false;
                this.sessionType  = 'Fokus';
                this.currentTime  = this.focusTime * 60;
                this.totalSeconds = this.focusTime * 60;
                window.showToast('🎯 Istirahat selesai! Saatnya fokus lagi!', 'success');
            } else {
                this.isBreak      = true;
                this.sessionType  = 'Istirahat';
                this.currentTime  = this.breakTime * 60;
                this.totalSeconds = this.breakTime * 60;
                window.showToast('🎉 Sesi fokus selesai! Waktunya istirahat ☕', 'success');
            }

            this.statusText = 'Siap';
            this.updateDisplay();
        },

        // ─── display ─────────────────────────────────────────────
        updateDisplay() {
            const m = Math.floor(this.currentTime / 60);
            const s = this.currentTime % 60;
            this.displayTime     = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            const ratio          = this.totalSeconds > 0 ? this.currentTime / this.totalSeconds : 1;
            this.progressPercent = Math.round(ratio * 100);
            this.progressOffset  = this.circumference * (1 - ratio);

            // Update tab title
            document.title = this.isRunning
                ? `${this.displayTime} — ${this.sessionType} | KosLife`
                : 'Focus Mode | KosLife';
        },

        // ─── history ─────────────────────────────────────────────
        loadHistory() {
            this.loadingHistory = true;
            fetch('{{ route('focus.stats') }}', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(d => { this.historyHtml = this.renderHistory(d); this.loadingHistory = false; })
                .catch(() => {
                    this.loadingHistory = false;
                    this.historyHtml = `<div class="text-center py-8 text-gray-500 dark:text-gray-400"><i class="fas fa-exclamation-circle text-2xl mb-2 block text-red-400"></i><p class="text-sm">Gagal memuat riwayat</p></div>`;
                });
        },

        renderHistory(data) {
            if (!data?.sessions?.length) {
                return `<div class="text-center py-8 text-gray-500 dark:text-gray-400"><i class="fas fa-clock text-3xl mb-2 block text-gray-300 dark:text-gray-600"></i><p class="font-medium">Belum ada sesi fokus</p><p class="text-xs mt-1">Mulai sesi pertamamu!</p></div>`;
            }
            const colors = { completed:'emerald', interrupted:'yellow', stopped:'red' };
            const icons  = { completed:'fa-check-circle', interrupted:'fa-pause-circle', stopped:'fa-times-circle' };
            return data.sessions.map(s => {
                const c = colors[s.status] || 'gray';
                const i = icons[s.status]  || 'fa-circle';
                return `
                <div class="flex items-center justify-between py-2.5 px-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="text-lg flex-shrink-0">${s.icon || '⏱️'}</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${s.task}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">${s.date}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0 ml-3">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">${s.duration}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-${c}-100 dark:bg-${c}-900/30 text-${c}-700 dark:text-${c}-300 whitespace-nowrap">
                            <i class="fas ${i} mr-1"></i>${s.status_label}
                        </span>
                    </div>
                </div>`;
            }).join('');
        },
    };
}
</script>
@endpush

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .card { @apply bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5; transition: box-shadow 0.2s ease; }
    .card:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
    .dark .card:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.3); }
    @keyframes ping-slow { 0%,100%{transform:scale(1);opacity:.2} 50%{transform:scale(1.05);opacity:.1} }
    .animate-ping-slow { animation: ping-slow 2s ease-in-out infinite; }
</style>
@endpush