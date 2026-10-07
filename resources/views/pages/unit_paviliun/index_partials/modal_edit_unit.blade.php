        <!-- MODAL UBAH UNIT -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl space-y-4 relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-amber-500 to-orange-500"></div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Ubah Data Unit / Paviliun</h3>
                            <p class="text-[11px] text-slate-400">Perbarui identitas ruangan atau penanggung jawab</p>
                        </div>
                    </div>
                    <button type="button" @click="showEditModal = false" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-all">&times;</button>
                </div>
                <form @submit.prevent="showEditModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1.5">Nama Unit / Paviliun</label>
                        <input type="text" x-model="selectedUnit ? selectedUnit.nama : ''" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 focus:outline-none rounded-xl px-3.5 py-2.5 text-white transition-all">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1.5">Nama Kepala Ruangan / Penanggung Jawab</label>
                        <input type="text" x-model="selectedUnit ? selectedUnit.kepala : ''" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 focus:outline-none rounded-xl px-3.5 py-2.5 text-white transition-all">
                    </div>
                    <div class="pt-4 border-t border-slate-800 flex items-center justify-end space-x-2.5">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700 transition-all active:scale-95 cursor-pointer">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-amber-500/25 border border-amber-400/40 transition-all active:scale-95 cursor-pointer flex items-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
