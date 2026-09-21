        <!-- GLOBAL CUSTOM CONFIRMATION DIALOG MODAL (Sleek Dark Theme) -->
        <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
            <div @click.away="showConfirmModal = false"
                 x-show="showConfirmModal"
                 x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-slate-900 border rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl space-y-4 relative overflow-hidden"
                 :class="{
                     'border-rose-500/40': confirmData.type === 'danger',
                     'border-amber-500/40': confirmData.type === 'warning',
                     'border-emerald-500/40': confirmData.type === 'success',
                     'border-cyan-500/40': confirmData.type === 'info'
                 }">
                
                <!-- Subtle Top Accent Strip -->
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r"
                     :class="confirmData.isBlocked ? 'from-amber-500 via-rose-500 to-rose-600' : (confirmData.type === 'danger' ? 'from-rose-500 to-red-600' : (confirmData.type === 'warning' ? 'from-amber-500 to-orange-500' : 'from-blue-500 to-cyan-500'))">
                </div>

                <!-- Header Icon & Title -->
                <div class="flex items-start space-x-3.5 pt-1">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 border shadow-inner"
                         :class="{
                             'bg-rose-500/20 text-rose-400 border-rose-500/30': confirmData.type === 'danger',
                             'bg-amber-500/20 text-amber-300 border-amber-500/30': confirmData.type === 'warning',
                             'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': confirmData.type === 'success',
                             'bg-cyan-500/20 text-cyan-300 border-cyan-500/30': confirmData.type === 'info'
                         }">
                        <!-- Blocked Shield / Warning Icon -->
                        <template x-if="confirmData.isBlocked">
                            <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </template>
                        <!-- Delete Trash Icon -->
                        <template x-if="!confirmData.isBlocked && confirmData.type === 'danger'">
                            <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </template>
                        <!-- Warning Icon -->
                        <template x-if="!confirmData.isBlocked && confirmData.type === 'warning'">
                            <svg class="w-6 h-6 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </template>
                        <!-- Success Check Icon -->
                        <template x-if="!confirmData.isBlocked && confirmData.type === 'success'">
                            <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </template>
                        <!-- Info Icon -->
                        <template x-if="!confirmData.isBlocked && confirmData.type === 'info'">
                            <svg class="w-6 h-6 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </template>
                    </div>
                    <div class="space-y-1 min-w-0 flex-1">
                        <h3 class="text-base sm:text-lg font-black text-white leading-snug tracking-tight" x-text="confirmData.title"></h3>
                        <p class="text-slate-300 text-xs sm:text-sm leading-relaxed" x-text="confirmData.message"></p>
                    </div>
                </div>

                <!-- Item Target Preview Card -->
                <template x-if="confirmData.itemName">
                    <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800/90 space-y-1.5">
                        <div class="flex items-center justify-between text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                            <span>Item Target:</span>
                            <template x-if="confirmData.itemDetails && confirmData.itemDetails.totalAset > 0">
                                <span class="px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[9px] font-bold" x-text="confirmData.itemDetails.totalAset + ' Aset Aktif'"></span>
                            </template>
                        </div>
                        <p class="text-xs sm:text-sm font-bold text-cyan-300 break-words font-mono" x-text="confirmData.itemName"></p>
                    </div>
                </template>

                <!-- Warning Card Khusus: Unit Masih Memiliki Aset -->
                <template x-if="confirmData.assetWarning">
                    <div class="p-4 bg-gradient-to-br from-rose-950/40 via-slate-950 to-slate-950 border border-rose-500/30 rounded-2xl space-y-2.5 shadow-inner">
                        <div class="flex items-center space-x-2 text-rose-300 font-extrabold text-xs">
                            <span class="p-1 rounded-md bg-rose-500/20 text-rose-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </span>
                            <span class="tracking-wide uppercase text-[11px]">Peringatan Akuntabilitas Aset RSUD</span>
                        </div>
                        <p class="text-xs text-rose-200/90 leading-relaxed" x-text="confirmData.assetWarning"></p>
                        <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800 flex items-center space-x-2 text-[11px] text-slate-300">
                            <span class="text-amber-400">💡</span>
                            <span>Silakan kosongkan aset ruangan ini dengan memindahkan (mutasi) seluruh aset ke ruangan tujuan lain.</span>
                        </div>
                    </div>
                </template>

                <!-- Footer Action Buttons -->
                <div class="pt-3 border-t border-slate-800/90 flex items-center justify-end space-x-2.5 flex-wrap sm:flex-nowrap gap-y-2">
                    <button type="button" @click="showConfirmModal = false"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-xs sm:text-sm border border-slate-700 transition-all active:scale-95 cursor-pointer text-center">
                        <span x-text="confirmData.isBlocked ? 'Tutup' : 'Batal'"></span>
                    </button>

                    <!-- Link / Tombol Ajukan Mutasi Barang (Jika Diblokir karena Memiliki Aset) -->
                    <template x-if="confirmData.actionUrl">
                        <a :href="confirmData.actionUrl"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-blue-600/30 border border-blue-400/30 transition-all active:scale-95 flex items-center justify-center space-x-2 cursor-pointer">
                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            <span x-text="confirmData.actionText || 'Ajukan Mutasi Aset'"></span>
                        </a>
                    </template>

                    <!-- Tombol Eksekusi Normal (Hanya tampil jika TIDAK diblokir) -->
                    <template x-if="!confirmData.isBlocked && confirmData.btnText">
                        <button type="button" @click="executeConfirmedAction()"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm shadow-lg transition-all active:scale-95 cursor-pointer flex items-center justify-center space-x-2 text-center"
                            :class="{
                                'bg-rose-600 hover:bg-rose-500 text-white shadow-rose-600/30 border border-rose-500/30': confirmData.type === 'danger',
                                'bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-amber-500/30 border border-amber-400/30': confirmData.type === 'warning',
                                'bg-emerald-600 hover:bg-emerald-500 text-white shadow-emerald-600/30 border border-emerald-500/30': confirmData.type === 'success',
                                'bg-cyan-600 hover:bg-cyan-500 text-white shadow-cyan-600/30 border border-cyan-500/30': confirmData.type === 'info'
                            }">
                            <template x-if="confirmData.type === 'danger'">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </template>
                            <span x-text="confirmData.btnText"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
