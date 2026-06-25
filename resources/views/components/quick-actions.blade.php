{{-- Quick Actions Floating Button --}}
<div x-data="{ open: false }" class="fixed bottom-6 right-6 z-50">
    <!-- Menu Items -->
    <div x-show="open" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-75"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-75"
         class="absolute bottom-16 right-0 mb-2 space-y-2"
         @click.away="open = false">
        
        <!-- Add Task -->
        <a href="{{ route('tasks.create') }}" 
           class="flex items-center gap-3 bg-white dark:bg-gray-800 px-4 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 group w-48">
            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-plus text-blue-600 dark:text-blue-400"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Tambah Tugas</span>
        </a>
        
        <!-- Add Income -->
        <a href="#" 
           @click.prevent="$dispatch('open-modal', 'add-income')"
           class="flex items-center gap-3 bg-white dark:bg-gray-800 px-4 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 group w-48">
            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-arrow-up text-green-600 dark:text-green-400"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Tambah Pemasukan</span>
        </a>
        
        <!-- Add Expense -->
        <a href="#" 
           @click.prevent="$dispatch('open-modal', 'add-expense')"
           class="flex items-center gap-3 bg-white dark:bg-gray-800 px-4 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 group w-48">
            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-arrow-down text-red-600 dark:text-red-400"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Tambah Pengeluaran</span>
        </a>
        
        <!-- Add Note -->
        <a href="{{ route('notes.create') }}" 
           class="flex items-center gap-3 bg-white dark:bg-gray-800 px-4 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 group w-48">
            <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-sticky-note text-purple-600 dark:text-purple-400"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Tambah Catatan</span>
        </a>
        
        <!-- Start Focus -->
        <a href="{{ route('focus.index') }}" 
           class="flex items-center gap-3 bg-white dark:bg-gray-800 px-4 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 group w-48">
            <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-clock text-orange-600 dark:text-orange-400"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Mulai Focus Mode</span>
        </a>
        
        <!-- Add Schedule -->
        <a href="{{ route('schedules.create') }}" 
           class="flex items-center gap-3 bg-white dark:bg-gray-800 px-4 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 group w-48">
            <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-calendar-plus text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Tambah Jadwal</span>
        </a>
        
        <!-- Add Habit -->
        <a href="{{ route('habits.create') }}" 
           class="flex items-center gap-3 bg-white dark:bg-gray-800 px-4 py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 group w-48">
            <div class="w-10 h-10 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-check-circle text-pink-600 dark:text-pink-400"></i>
            </div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Tambah Habit</span>
        </a>
    </div>
    
    <!-- Main Button -->
    <button @click="open = !open"
            class="w-14 h-14 rounded-full bg-primary-500 text-white shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center hover:scale-110"
            :class="{ 'rotate-45': open }">
        <i class="fas fa-plus text-2xl transition-transform duration-200" 
           :class="{ 'rotate-45': open }"></i>
    </button>
</div>

{{-- Include Modals --}}
@include('components.modals.add-income')
@include('components.modals.add-expense')

<style>
    [x-cloak] { display: none !important; }
</style>