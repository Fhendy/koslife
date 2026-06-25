@extends('layouts.app')

@section('title', 'Kalender')
@section('breadcrumb', 'Kalender')
@section('page-title', '📅 Kalender')
@section('page-description', 'Lihat dan kelola semua jadwal Anda')

@section('content')
<div x-data="calendarManager()" x-init="initCalendar()" class="space-y-6">
    
    {{-- ===== STATISTIK ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Jadwal</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-calendar text-xl text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Hari Ini</p>
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['today'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <i class="fas fa-calendar-day text-xl text-indigo-600 dark:text-indigo-400"></i>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Akan Datang</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['upcoming'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <i class="fas fa-arrow-right text-xl text-emerald-600 dark:text-emerald-400"></i>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Selesai</p>
                    <p class="text-2xl font-bold text-gray-500 dark:text-gray-400">{{ $stats['past'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-gray-500 dark:text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>
    
    {{-- ===== CALENDAR ===== --}}
    <div class="card">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-calendar-alt text-indigo-500 mr-2"></i>
                Kalender Jadwal
            </h3>
            
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('schedules.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-xl hover:from-indigo-600 hover:to-purple-600 transition-all duration-200 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Tambah Jadwal</span>
                </a>
                
                <button @click="calendar.refetchEvents()" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-sync-alt"></i>
                    <span class="hidden sm:inline">Refresh</span>
                </button>
            </div>
        </div>
        
        <!-- Calendar Container -->
        <div id="calendar-container" class="min-h-[500px]">
            <div id="calendar"></div>
        </div>
    </div>
    
    {{-- ===== UPCOMING EVENTS ===== --}}
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-clock text-indigo-500 mr-2"></i>
            Jadwal Mendatang
        </h3>
        
        @if(isset($upcomingEvents) && $upcomingEvents->count() > 0)
            <div class="space-y-3">
                @foreach($upcomingEvents->take(5) as $event)
                    <div class="flex items-start gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                        <div class="w-1 h-full min-h-[50px] rounded-full flex-shrink-0" 
                             style="background-color: {{ $event->color }}"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $event->title }}</p>
                                    <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        <span>
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            {{ \Carbon\Carbon::parse($event->start_time)->translatedFormat('d M Y') }}
                                        </span>
                                        <span>
                                            <i class="far fa-clock mr-1"></i>
                                            {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}
                                        </span>
                                        @if($event->location)
                                            <span>
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $event->location }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                    {{ ucfirst($event->category) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($upcomingEvents->count() > 5)
                <p class="text-xs text-center text-gray-500 dark:text-gray-400 mt-4">
                    + {{ $upcomingEvents->count() - 5 }} jadwal lainnya
                </p>
            @endif
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="fas fa-calendar-check text-3xl mb-2 block text-gray-300 dark:text-gray-600"></i>
                <p>Tidak ada jadwal mendatang</p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet">
<style>
    .fc {
        font-family: inherit;
    }
    .fc .fc-toolbar-title {
        font-size: 1.2rem;
        font-weight: 600;
    }
    .fc .fc-button {
        background: #4F46E5;
        border: none;
        border-radius: 0.5rem;
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: capitalize;
    }
    .fc .fc-button:hover {
        background: #4338ca;
    }
    .fc .fc-button-primary:not(:disabled):active,
    .fc .fc-button-primary:not(:disabled).fc-button-active {
        background: #3730a3;
    }
    .fc .fc-daygrid-day-number {
        color: #374151;
        font-weight: 500;
        padding: 0.25rem;
    }
    .dark .fc .fc-daygrid-day-number {
        color: #e5e7eb;
    }
    .fc .fc-daygrid-day.fc-day-today {
        background: rgba(79, 70, 229, 0.08);
    }
    .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
        background: #4F46E5;
        color: white;
        border-radius: 50%;
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    .fc .fc-col-header-cell-cushion {
        color: #6B7280;
        font-weight: 600;
        padding: 0.5rem 0;
    }
    .dark .fc .fc-col-header-cell-cushion {
        color: #9CA3AF;
    }
    .fc .fc-event {
        border: none;
        padding: 0.15rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        cursor: pointer;
    }
    .fc .fc-event:hover {
        opacity: 0.8;
    }
    .fc .fc-event-title {
        font-weight: 500;
    }
    .fc .fc-daygrid-event-dot {
        display: none;
    }
    .fc .fc-daygrid-event {
        background: transparent;
    }
    .fc .fc-daygrid-dot-event .fc-event-title {
        font-weight: 500;
    }
    .fc .fc-daygrid-dot-event:hover {
        background: rgba(0,0,0,0.05);
    }
    .dark .fc .fc-daygrid-dot-event:hover {
        background: rgba(255,255,255,0.05);
    }
    .fc .fc-daygrid-day-events {
        min-height: 20px;
    }
    .fc .fc-toolbar-chunk .fc-button-group .fc-button {
        background: #e5e7eb;
        color: #374151;
    }
    .fc .fc-toolbar-chunk .fc-button-group .fc-button:hover {
        background: #d1d5db;
    }
    .fc .fc-toolbar-chunk .fc-button-group .fc-button.fc-button-active {
        background: #4F46E5;
        color: white;
    }
    .dark .fc .fc-toolbar-chunk .fc-button-group .fc-button {
        background: #374151;
        color: #e5e7eb;
    }
    .dark .fc .fc-toolbar-chunk .fc-button-group .fc-button:hover {
        background: #4B5563;
    }
    .dark .fc .fc-toolbar-chunk .fc-button-group .fc-button.fc-button-active {
        background: #4F46E5;
        color: white;
    }
    .fc .fc-popover {
        border-radius: 0.75rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border: none;
    }
    .dark .fc .fc-popover {
        background: #1F2937;
        color: #e5e7eb;
    }
    .fc .fc-popover-header {
        background: #4F46E5;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.75rem 0.75rem 0 0;
    }
    .fc .fc-popover-body {
        padding: 0.5rem;
    }
    .fc .fc-popover .fc-daygrid-event {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        margin: 0.25rem 0;
    }
    @media (max-width: 640px) {
        .fc .fc-toolbar {
            flex-direction: column;
            gap: 0.5rem;
        }
        .fc .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
            width: 100%;
        }
        .fc .fc-toolbar-title {
            font-size: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js"></script>
<script>
    function calendarManager() {
        return {
            calendar: null,
            
            initCalendar() {
                const calendarEl = document.getElementById('calendar');
                if (!calendarEl) return;
                
                const isDark = document.documentElement.classList.contains('dark');
                
                this.calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: {
                        url: '{{ route('schedules.events') }}',
                        method: 'GET',
                        failure: function() {
                            window.showToast('Gagal memuat jadwal', 'error');
                        }
                    },
                    eventClick: function(info) {
                        const event = info.event;
                        const id = event.id;
                        const url = '/schedules/' + id;
                        window.location.href = url;
                    },
                    eventDidMount: function(info) {
                        // Add tooltip
                        const tooltip = info.el.querySelector('.fc-event-title');
                        if (tooltip) {
                            const start = info.event.start?.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
                            const end = info.event.end?.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
                            const time = start && end ? start + ' - ' + end : '';
                            tooltip.title = info.event.title + (time ? ' (' + time + ')' : '');
                        }
                    },
                    loading: function(isLoading) {
                        if (isLoading) {
                            document.getElementById('calendar-container').style.opacity = '0.5';
                        } else {
                            document.getElementById('calendar-container').style.opacity = '1';
                        }
                    },
                    locale: 'id',
                    firstDay: 1,
                    height: 600,
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        meridiem: false
                    },
                });
                
                this.calendar.render();
            },
            
            refreshCalendar() {
                if (this.calendar) {
                    this.calendar.refetchEvents();
                    window.showToast('Kalender diperbarui', 'info');
                }
            }
        };
    }
</script>
@endpush
@endsection