<!-- ========================================================================= -->
<!-- MODAL UBAH JENIS PENGADAAN SIPD                                           -->
<!-- ========================================================================= -->
<div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
    <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-3xl w-full p-6 shadow-2xl space-y-4 relative">
        <!-- Tombol Close Corner -->
        <button type="button" @click="showEditModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <!-- Header Center -->
        <div class="text-center pb-3 border-b border-slate-800 mb-2">
            <h3 class="text-base font-extrabold text-white">Ubah Data Pengadaan SIPD</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Perbarui struktur 3 tingkatan (Program, Kegiatan, Sub Kegiatan)</p>
        </div>

        <form method="POST" :action="editActionUrl" class="space-y-3.5 text-xs" @submit="confirmEditForm($event)">
            @csrf
            @method('PUT')

            <!-- Blok 1: Program -->
            <div class="p-3.5 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 space-y-2">
                <span class="font-bold text-emerald-400 uppercase tracking-wider text-[10px] block">1. Program Pengadaan SIPD</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">Kode Program <span class="text-rose-400">*</span></label>
                        <input type="text" 
                               name="program_kode" 
                               x-model="editFormData.program_kode" 
                               @input="onKodeProgramInputEdit()" 
                               placeholder="0.00.01" 
                               required 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-emerald-400 font-mono font-bold focus:border-emerald-500">
                    </div>
                    <div class="col-span-2 relative" @click.away="showProgramDropdownEdit = false">
                        <label class="block text-slate-400 mb-1 flex items-center justify-between">
                            <span>Nama Program <span class="text-rose-400">*</span></span>
                            <span class="text-[10px] text-emerald-400 font-normal">Ketik untuk filter / ubah</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="program_nama" 
                                   x-model="editFormData.program_nama" 
                                   @focus="showProgramDropdownEdit = true" 
                                   @input="showProgramDropdownEdit = true" 
                                   @keydown.escape="showProgramDropdownEdit = false" 
                                   required 
                                   autocomplete="off"
                                   placeholder="Contoh: Program Penunjang Urusan Pemda..." 
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-emerald-500 pr-8">
                            <div class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>

                        <!-- Dropdown Suggestions Program -->
                        <div x-show="showProgramDropdownEdit" 
                             x-transition.opacity.duration.150ms 
                             class="absolute left-0 right-0 z-50 mt-1 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto divide-y divide-slate-800" 
                             style="display: none;">
                            <div class="px-3 py-1.5 bg-slate-950/90 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                <span>Pilih Program SIPD</span>
                                <span class="text-emerald-400 font-mono text-[9px]" x-text="totalProgramCountEdit + ' tersedia'"></span>
                            </div>

                            <template x-for="item in filteredProgramListEdit" :key="item.program_kode + item.program_nama">
                                <div @click="selectProgramEdit(item)" 
                                     class="px-3 py-2 hover:bg-emerald-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-2"
                                     :class="{'bg-emerald-500/10': editFormData.program_kode === item.program_kode}">
                                    <div class="flex items-center space-x-2 min-w-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0"></span>
                                        <span class="font-bold text-xs text-white group-hover:text-emerald-300 truncate" x-text="item.program_nama" :title="item.program_nama"></span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded bg-slate-950 border border-slate-700 text-emerald-400 font-mono text-[10px] font-bold shrink-0" x-text="item.program_kode"></span>
                                </div>
                            </template>

                            <template x-if="filteredProgramListEdit.length === 0">
                                <div class="p-3 text-center text-xs text-slate-400">
                                    <p class="text-emerald-400 font-semibold text-[11px]">✨ Program Pengadaan Kustom</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Gunakan nama ini & sesuaikan Kode Program di samping</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blok 2: Kegiatan -->
            <div class="p-3.5 rounded-2xl bg-amber-950/20 border border-amber-500/30 space-y-2">
                <span class="font-bold text-amber-400 uppercase tracking-wider text-[10px] block">2. Kegiatan Pengadaan SIPD</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">Kode Kegiatan <span class="text-rose-400">*</span></label>
                        <input type="text" 
                               name="kegiatan_kode" 
                               x-model="editFormData.kegiatan_kode" 
                               @input="onKodeKegiatanInputEdit()" 
                               placeholder="0.00.01.2.10" 
                               required 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-400 font-mono font-bold focus:border-amber-500">
                    </div>
                    <div class="col-span-2 relative" @click.away="showKegiatanDropdownEdit = false">
                        <label class="block text-slate-400 mb-1 flex items-center justify-between">
                            <span>Nama Kegiatan Pengadaan <span class="text-rose-400">*</span></span>
                            <span class="text-[10px] text-amber-400 font-normal">Ketik untuk filter / ubah</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="kegiatan_nama" 
                                   x-model="editFormData.kegiatan_nama" 
                                   @focus="showKegiatanDropdownEdit = true" 
                                   @input="showKegiatanDropdownEdit = true" 
                                   @keydown.escape="showKegiatanDropdownEdit = false" 
                                   required 
                                   autocomplete="off"
                                   placeholder="Contoh: Peningkatan Pelayanan BLUD..." 
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-amber-500 pr-8">
                            <div class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>

                        <!-- Dropdown Suggestions Kegiatan -->
                        <div x-show="showKegiatanDropdownEdit" 
                             x-transition.opacity.duration.150ms 
                             class="absolute left-0 right-0 z-50 mt-1 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto divide-y divide-slate-800" 
                             style="display: none;">
                            <div class="px-3 py-1.5 bg-slate-950/90 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                <span>Pilih Kegiatan Pengadaan</span>
                                <span class="text-amber-400 font-mono text-[9px]" x-text="totalKegiatanCountEdit + ' tersedia'"></span>
                            </div>

                            <template x-for="item in filteredKegiatanListEdit" :key="item.kegiatan_kode + item.kegiatan_nama">
                                <div @click="selectKegiatanEdit(item)" 
                                     class="px-3 py-2 hover:bg-amber-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-2"
                                     :class="{'bg-amber-500/10': editFormData.kegiatan_kode === item.kegiatan_kode}">
                                    <div class="flex items-center space-x-2 min-w-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                        <span class="font-bold text-xs text-white group-hover:text-amber-300 truncate" x-text="item.kegiatan_nama" :title="item.kegiatan_nama"></span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded bg-slate-950 border border-slate-700 text-amber-400 font-mono text-[10px] font-bold shrink-0" x-text="item.kegiatan_kode"></span>
                                </div>
                            </template>

                            <template x-if="filteredKegiatanListEdit.length === 0">
                                <div class="p-3 text-center text-xs text-slate-400">
                                    <p class="text-amber-400 font-semibold text-[11px]">✨ Kegiatan Kustom</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Gunakan nama ini & sesuaikan Kode Kegiatan di samping</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blok 3: Sub Kegiatan -->
            <div class="p-3.5 rounded-2xl bg-purple-950/20 border border-purple-500/30 space-y-2">
                <span class="font-bold text-purple-400 uppercase tracking-wider text-[10px] block">3. Sub Kegiatan Pengadaan SIPD</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">Kode Sub Kegiatan <span class="text-rose-400">*</span></label>
                        <input type="text" 
                               name="sub_kegiatan_kode" 
                               x-model="editFormData.sub_kegiatan_kode" 
                               placeholder="0.00.01.2.10.0001" 
                               required 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-purple-400 font-mono font-bold focus:border-purple-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-slate-400 mb-1">Nama Sub Kegiatan Pengadaan <span class="text-rose-400">*</span></label>
                        <input type="text" 
                               name="sub_kegiatan_nama" 
                               x-model="editFormData.sub_kegiatan_nama" 
                               placeholder="Contoh: Pelayanan dan Penunjang Pelayanan..." 
                               required 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-purple-500">
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
