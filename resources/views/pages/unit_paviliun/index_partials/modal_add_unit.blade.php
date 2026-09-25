        <!-- MODAL TAMBAH UNIT -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl space-y-4 relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-blue-500 to-cyan-500"></div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-500/15 border border-blue-500/30 flex items-center justify-center text-blue-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Tambah Unit / Paviliun Baru</h3>
                            <p class="text-[11px] text-slate-400">Daftarkan lokasi ruangan atau paviliun baru ke katalog</p>
                        </div>
                    </div>
                    <button type="button" @click="showAddModal = false" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-all">&times;</button>
                </div>
                <form @submit.prevent="showAddModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1.5">Nama Unit / Paviliun</label>
                        <input type="text" placeholder="Contoh: Paviliun Melati, IGD, Farmasi..." class="w-full bg-slate-950 border border-slate-800 focus:border-blue-500 focus:outline-none rounded-xl px-3.5 py-2.5 text-white placeholder-slate-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1.5">Nama Kepala Ruangan / Penanggung Jawab</label>
                        <input type="text" placeholder="Contoh: dr. Budi Santoso, Sp.A..." class="w-full bg-slate-950 border border-slate-800 focus:border-blue-500 focus:outline-none rounded-xl px-3.5 py-2.5 text-white placeholder-slate-500 transition-all">
                    </div>
                    <div class="pt-4 border-t border-slate-800 flex items-center justify-end space-x-2.5">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700 transition-all active:scale-95 cursor-pointer">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-500/25 border border-blue-400/30 transition-all active:scale-95 cursor-pointer flex items-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Unit</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
