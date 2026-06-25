@extends('layouts.app')

@section('title', 'Budget Makan')
@section('breadcrumb', 'Budget Makan')
@section('page-title', '🍜 Budget Makan')
@section('page-description', 'Kelola budget dan pengeluaran makan Anda')

@section('content')
<div x-data="mealBudgetManager()" x-init="initMealBudget()" class="space-y-6">
    
    {{-- ===== BUDGET OVERVIEW ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Budget Harian -->
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Budget Harian</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        Rp {{ number_format($user->daily_meal_budget ?? 50000, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                    <i class="fas fa-wallet text-xl text-indigo-600 dark:text-indigo-400"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center gap-2 text-xs">
                <span class="text-gray-500 dark:text-gray-400">Hari ini:</span>
                <span class="font-medium text-gray-700 dark:text-gray-300">
                    Rp {{ number_format($todaySpent ?? 0, 0, ',', '.') }}
                </span>
                <span class="text-gray-400 dark:text-gray-500">•</span>
                <span class="text-emerald-600 dark:text-emerald-400">
                    Sisa Rp {{ number_format(($user->daily_meal_budget ?? 50000) - ($todaySpent ?? 0), 0, ',', '.') }}
                </span>
            </div>
        </div>
        
        <!-- Total Bulan Ini -->
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        Rp {{ number_format($monthlySpent ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-xl text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                {{ now()->translatedFormat('F Y') }}
            </p>
        </div>
        
        <!-- Rata-rata Harian -->
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Rata-rata Harian</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        Rp {{ number_format($avgDaily ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <i class="fas fa-chart-line text-xl text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                {{ now()->daysInMonth }} hari di bulan ini
            </p>
        </div>
        
        <!-- Total Makanan -->
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Makanan</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $statsByType->sum('count') ?? 0 }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <i class="fas fa-utensils text-xl text-orange-600 dark:text-orange-400"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                <span class="text-emerald-600 dark:text-emerald-400">Hari ini: {{ $todayMeals->count() }}</span>
            </p>
        </div>
    </div>
    
    {{-- ===== BUDGET PROGRESS ===== --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-chart-simple text-indigo-500 mr-2"></i>
                Progress Budget Harian
            </h3>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $todaySpent ?? 0 }} / {{ $user->daily_meal_budget ?? 50000 }}
                </span>
                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">
                    {{ $budgetPercentage ?? 0 }}%
                </span>
            </div>
        </div>
        
        <div class="relative">
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
                <div class="h-4 rounded-full transition-all duration-500 
                            {{ ($budgetPercentage ?? 0) > 100 ? 'bg-red-500' : 'bg-gradient-to-r from-emerald-400 to-indigo-500' }}"
                     style="width: {{ min($budgetPercentage ?? 0, 100) }}%">
                </div>
            </div>
            
            <!-- Warning indicators -->
            <div class="flex justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
                <span>💰 {{ ($user->daily_meal_budget ?? 50000) > 0 ? 'Rp ' . number_format(($user->daily_meal_budget ?? 50000) * 0.5, 0, ',', '.') : '' }}</span>
                <span>⚠️ {{ ($user->daily_meal_budget ?? 50000) > 0 ? 'Rp ' . number_format(($user->daily_meal_budget ?? 50000) * 0.8, 0, ',', '.') : '' }}</span>
                <span class="{{ ($budgetPercentage ?? 0) > 100 ? 'text-red-500 font-bold' : '' }}">
                    🎯 Rp {{ number_format($user->daily_meal_budget ?? 50000, 0, ',', '.') }}
                </span>
            </div>
        </div>
        
        @if(($budgetPercentage ?? 0) > 80)
            <div class="mt-3 p-3 rounded-lg {{ ($budgetPercentage ?? 0) > 100 ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300' }}">
                <p class="text-sm font-medium">
                    <i class="fas {{ ($budgetPercentage ?? 0) > 100 ? 'fa-exclamation-triangle' : 'fa-exclamation-circle' }} mr-2"></i>
                    @if(($budgetPercentage ?? 0) > 100)
                        Budget Anda telah melewati batas! Hemat ya! 😅
                    @elseif(($budgetPercentage ?? 0) > 80)
                        Budget hampir habis, atur pengeluaran Anda! 💡
                    @endif
                </p>
            </div>
        @endif
    </div>
    
    {{-- ===== MEAL TYPE STATS ===== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $mealTypes = [
                'breakfast' => ['label' => 'Sarapan', 'icon' => '🌅', 'color' => 'bg-yellow-500'],
                'lunch' => ['label' => 'Makan Siang', 'icon' => '☀️', 'color' => 'bg-orange-500'],
                'dinner' => ['label' => 'Makan Malam', 'icon' => '🌙', 'color' => 'bg-indigo-500'],
                'snack' => ['label' => 'Camilan', 'icon' => '🍿', 'color' => 'bg-pink-500']
            ];
        @endphp
        
        @foreach($mealTypes as $key => $type)
            @php
                $stat = $statsByType->where('meal_type', $key)->first();
                $count = $stat->count ?? 0;
                $total = $stat->total ?? 0;
            @endphp
            <div class="card text-center hover:scale-105 transition-transform duration-200">
                <div class="text-3xl mb-2">{{ $type['icon'] }}</div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $type['label'] }}</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $count }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </p>
                <div class="mt-2 h-1.5 w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="{{ $type['color'] }} h-1.5 rounded-full transition-all duration-500" 
                         style="width: {{ $monthlySpent > 0 ? min(($total / $monthlySpent) * 100, 100) : 0 }}%">
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    {{-- ===== TODAY'S MEALS ===== --}}
    <div class="card">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <i class="fas fa-list text-indigo-500 mr-2"></i>
                Makanan Hari Ini
            </h3>
            
            <div class="flex flex-wrap gap-2">
                <button @click="$dispatch('open-modal', 'add-meal')" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors text-sm">
                    <i class="fas fa-plus"></i>
                    <span class="hidden sm:inline">Tambah Makanan</span>
                </button>
                
                <div class="flex items-center gap-2">
                    <input type="date" 
                           x-model="selectedDate"
                           @change="loadMeals()"
                           class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>
        </div>
        
        <!-- Today's Meals List -->
        @if(isset($todayMeals) && $todayMeals->count() > 0)
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($todayMeals as $meal)
                    <div class="py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/30 px-2 rounded-lg transition-colors group">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">
                                {{ $meal->getMealTypeIcon() }}
                            </span>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $meal->getMealTypeLabel() }}
                                </p>
                                @if($meal->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $meal->description }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="font-semibold text-gray-900 dark:text-white">
                                Rp {{ number_format($meal->amount, 0, ',', '.') }}
                            </p>
                            <form action="{{ route('meal-budget.destroy', $meal) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Hapus data makanan ini?')"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors opacity-0 group-hover:opacity-100">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Today's Total -->
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Hari Ini</span>
                <span class="text-lg font-bold {{ ($user->daily_meal_budget ?? 50000) - ($todaySpent ?? 0) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                    Rp {{ number_format($todaySpent ?? 0, 0, ',', '.') }}
                </span>
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-utensils text-3xl text-gray-400 dark:text-gray-500"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada makanan hari ini</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mulai catat makanan Anda</p>
                <button @click="$dispatch('open-modal', 'add-meal')" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors mt-4">
                    <i class="fas fa-plus"></i> Tambah Makanan
                </button>
            </div>
        @endif
    </div>
    
    {{-- ===== RECENT MEALS (Bulan Ini) ===== --}}
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-history text-indigo-500 mr-2"></i>
            Riwayat Makanan Bulan Ini
        </h3>
        
        @if(isset($monthlyMeals) && $monthlyMeals->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                            <th class="pb-2 font-medium">Tanggal</th>
                            <th class="pb-2 font-medium">Makanan</th>
                            <th class="pb-2 font-medium">Deskripsi</th>
                            <th class="pb-2 font-medium text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($monthlyMeals->take(10) as $meal)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-2 text-gray-600 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($meal->meal_date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-2">
                                    <span class="text-lg">{{ $meal->getMealTypeIcon() }}</span>
                                    <span class="ml-1 text-gray-700 dark:text-gray-300">{{ $meal->getMealTypeLabel() }}</span>
                                </td>
                                <td class="py-2 text-gray-500 dark:text-gray-400">
                                    {{ $meal->description ?? '-' }}
                                </td>
                                <td class="py-2 text-right font-medium text-gray-900 dark:text-white">
                                    Rp {{ number_format($meal->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    @if($monthlyMeals->count() > 10)
                        <tfoot>
                            <tr>
                                <td colspan="4" class="pt-2 text-center text-xs text-gray-500 dark:text-gray-400">
                                    + {{ $monthlyMeals->count() - 10 }} data lainnya
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="fas fa-inbox text-3xl mb-2 block text-gray-300 dark:text-gray-600"></i>
                <p>Belum ada data makanan bulan ini</p>
            </div>
        @endif
    </div>
</div>

{{-- ===== MODAL TAMBAH MAKANAN ===== --}}
<div x-data="{ open: false }" 
     x-show="open" 
     x-on:open-modal.window="if ($event.detail === 'add-meal') open = true"
     x-on:keydown.escape.window="open = false"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75 transition-opacity"
             @click="open = false">
        </div>

        <!-- Modal panel -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            
            <form action="{{ route('meal-budget.store') }}" method="POST">
                @csrf
                <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            <i class="fas fa-utensils text-xl text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Tambah Makanan
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Catat makanan Anda hari ini
                            </p>
                        </div>
                        <button type="button" @click="open = false" class="ml-auto text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Meal Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                <i class="fas fa-utensils mr-1"></i> Jenis Makanan <span class="text-red-500">*</span>
                            </label>
                            <select name="meal_type" required 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="breakfast">🌅 Sarapan</option>
                                <option value="lunch">☀️ Makan Siang</option>
                                <option value="dinner">🌙 Makan Malam</option>
                                <option value="snack">🍿 Camilan</option>
                            </select>
                        </div>
                        
                        <!-- Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                <i class="fas fa-coins mr-1"></i> Jumlah <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span>
                                <input type="number" 
                                       name="amount" 
                                       required 
                                       step="0.01" 
                                       min="0"
                                       placeholder="0"
                                       class="w-full pl-12 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                <i class="fas fa-pencil mr-1"></i> Deskripsi
                            </label>
                            <input type="text" 
                                   name="description" 
                                   placeholder="Contoh: Nasi goreng + es teh"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        
                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                <i class="fas fa-calendar mr-1"></i> Tanggal
                            </label>
                            <input type="date" 
                                   name="meal_date" 
                                   value="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="submit" 
                            class="w-full sm:w-auto px-6 py-2.5 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors font-medium">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                    <button type="button" 
                            @click="open = false"
                            class="w-full sm:w-auto px-6 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors font-medium mt-2 sm:mt-0">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function mealBudgetManager() {
        return {
            selectedDate: '{{ date('Y-m-d') }}',
            
            initMealBudget() {
                // Auto refresh when date changes
                this.$watch('selectedDate', () => this.loadMeals());
            },
            
            loadMeals() {
                const url = '{{ route('meal-budget.stats', '') }}/' + this.selectedDate;
                window.location.href = url;
            }
        };
    }
</script>
@endpush

@push('styles')
<style>
    .card {
        transition: all 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .dark .card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection