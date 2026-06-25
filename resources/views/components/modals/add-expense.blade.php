<div x-data="{ open: false }" 
     x-show="open" 
     x-on:open-modal.window="if ($event.detail === 'add-expense') open = true"
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
            
            <form action="{{ route('finance.expense.store') }}" method="POST">
                @csrf
                <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                            <i class="fas fa-arrow-down text-xl text-red-600 dark:text-red-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Tambah Pengeluaran
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Catat pengeluaran Anda
                            </p>
                        </div>
                        <button type="button" @click="open = false" class="ml-auto text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Kategori -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                <i class="fas fa-tag mr-1"></i> Kategori
                            </label>
                            <select name="category" required 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="makan">🍽️ Makan</option>
                                <option value="transportasi">🚗 Transportasi</option>
                                <option value="jajan">🍿 Jajan</option>
                                <option value="belanja">🛍️ Belanja</option>
                                <option value="kos">🏠 Kos</option>
                                <option value="internet">🌐 Internet</option>
                                <option value="pendidikan">📚 Pendidikan</option>
                                <option value="hiburan">🎮 Hiburan</option>
                                <option value="lainnya">📦 Lainnya</option>
                            </select>
                        </div>
                        
                        <!-- Jumlah -->
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
                        
                        <!-- Deskripsi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                <i class="fas fa-pencil mr-1"></i> Deskripsi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="description" 
                                   required
                                   placeholder="Contoh: Makan siang di kantin"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        
                        <!-- Tanggal -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                <i class="fas fa-calendar mr-1"></i> Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="transaction_date" 
                                   value="{{ date('Y-m-d') }}" 
                                   required
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>
                        
                        <!-- Hutang -->
                        <div class="flex items-center gap-2">
                            <input type="checkbox" 
                                   name="is_debt" 
                                   id="is_debt_expense"
                                   value="1"
                                   class="rounded border-gray-300 dark:border-gray-600 text-indigo-500 focus:ring-indigo-500">
                            <label for="is_debt_expense" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                <i class="fas fa-handshake mr-1"></i> Ini adalah hutang
                            </label>
                        </div>
                        
                        <!-- Nama Hutang -->
                        <div x-data="{ showDebtor: false }">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" 
                                       x-model="showDebtor"
                                       id="has_debtor_expense"
                                       class="rounded border-gray-300 dark:border-gray-600 text-indigo-500 focus:ring-indigo-500">
                                <label for="has_debtor_expense" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                    <i class="fas fa-user mr-1"></i> Ada nama penerima
                                </label>
                            </div>
                            <div x-show="showDebtor" x-cloak class="mt-2">
                                <input type="text" 
                                       name="debtor_name" 
                                       id="debtor_name_expense"
                                       placeholder="Nama penerima hutang"
                                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="submit" 
                            class="w-full sm:w-auto px-6 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors font-medium">
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