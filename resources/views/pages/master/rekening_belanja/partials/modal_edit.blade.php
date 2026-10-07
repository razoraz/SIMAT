<!-- ========================================================================= -->
<!-- MODAL UBAH REKENING BELANJA                                               -->
<!-- ========================================================================= -->
<div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
    <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-3xl w-full p-6 shadow-2xl space-y-4 relative">
        <!-- Tombol Close Corner -->
        <button type="button" @click="showEditModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <!-- Header Center -->
        <div class="text-center pb-3 border-b border-slate-800 mb-2">
            <h3 class="text-base font-extrabold text-white">Ubah Rekening Belanja SIPD</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Perbarui Kode Rekening & Nama Belanja Pengadaan SIPD</p>
        </div>

        <form method="POST" :action="editActionUrl" class="space-y-3.5 text-xs" @submit="confirmEditForm($event)">
            @csrf
            @method('PUT')

            <!-- Blok 1: Kelompok Belanja Modal -->
            <div class="p-3.5 rounded-2xl bg-blue-950/20 border border-blue-500/30 space-y-2">
                <span class="font-bold text-blue-400 uppercase tracking-wider text-[10px] block">1. Kelompok Belanja Modal</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">Kode Kelompok <span class="text-rose-400">*</span></label>
                        <input type="text" 
                               name="kelompok" 
                               x-model="editFormData.kelompok" 
                               @input="onKodeKelompokInputEdit()" 
                               placeholder="5.2.02" 
                               required 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-blue-400 font-mono font-bold focus:border-blue-500">
                    </div>
                    <div class="col-span-2 relative" @click.away="showKelompokDropdownEdit = false">
                        <label class="block text-slate-400 mb-1 flex items-center justify-between">
                            <span>Nama Kelompok Belanja <span class="text-rose-400">*</span></span>
                            <span class="text-[10px] text-blue-400 font-normal">Ketik untuk filter / ubah</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="nama_kelompok" 
                                   x-model="editFormData.nama_kelompok" 
                                   @focus="showKelompokDropdownEdit = true" 
                                   @input="showKelompokDropdownEdit = true" 
                                   @keydown.escape="showKelompokDropdownEdit = false" 
                                   required 
                                   autocomplete="off"
                                   placeholder="Contoh: Belanja Modal Peralatan dan Mesin..." 
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-blue-500 pr-8">
                            <div class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>

                        <!-- Dropdown Suggestions Kelompok -->
                        <div x-show="showKelompokDropdownEdit" 
                             x-transition.opacity.duration.150ms 
                             class="absolute left-0 right-0 z-50 mt-1 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto divide-y divide-slate-800" 
                             style="display: none;">
                            <div class="px-3 py-1.5 bg-slate-950/90 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                <span>Pilih Kelompok Belanja Modal</span>
                                <span class="text-blue-400 font-mono text-[9px]" x-text="totalKelompokCountEdit + ' tersedia'"></span>
                            </div>

                            <template x-for="item in filteredKelompokListEdit" :key="item.kelompok + item.nama_kelompok">
                                <div @click="selectKelompokEdit(item)" 
                                     class="px-3 py-2 hover:bg-blue-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-2"
                                     :class="{'bg-blue-500/10': editFormData.kelompok === item.kelompok}">
                                    <div class="flex items-center space-x-2 min-w-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 shrink-0"></span>
                                        <span class="font-bold text-xs text-white group-hover:text-blue-300 truncate" x-text="item.nama_kelompok" :title="item.nama_kelompok"></span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded bg-slate-950 border border-slate-700 text-blue-400 font-mono text-[10px] font-bold shrink-0" x-text="item.kelompok"></span>
                                </div>
                            </template>

                            <template x-if="filteredKelompokListEdit.length === 0">
                                <div class="p-3 text-center text-xs text-slate-400">
                                    <p class="text-blue-400 font-semibold text-[11px]">✨ Kelompok Belanja Kustom</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Gunakan nama ini & sesuaikan Kode Kelompok di samping</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blok 2: Rincian Rekening Belanja SIPD -->
            <div class="p-3.5 rounded-2xl bg-cyan-950/20 border border-cyan-500/30 space-y-2">
                <span class="font-bold text-cyan-400 uppercase tracking-wider text-[10px] block">2. Rekening Belanja Pengadaan SIPD</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">Kode Rek (8) <span class="text-rose-400">*</span></label>
                        <input type="text" 
                               name="kode_rek" 
                               x-model="editFormData.kode_rek" 
                               placeholder="Contoh: 5.2.02.05.02.0006" 
                               required 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-cyan-400 font-mono font-bold focus:border-cyan-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-slate-400 mb-1">Nama Belanja Pengadaan (9) <span class="text-rose-400">*</span></label>
                        <input type="text" 
                               name="nama_belanja" 
                               x-model="editFormData.nama_belanja" 
                               placeholder="Contoh: Belanja Modal Alat Rumah Tangga Lainnya..." 
                               required 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-cyan-500">
                    </div>
                </div>
            </div>

            <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-slate-700 hover:border-rose-500/30 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>Batal</span>
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
