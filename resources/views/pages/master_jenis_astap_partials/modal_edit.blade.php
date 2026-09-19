<!-- ========================================================================= -->
<!-- MODAL EDIT KLASIFIKASI KODE 108 BMD                                       -->
<!-- ========================================================================= -->
<div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
    <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-3xl w-full p-6 shadow-2xl space-y-5 relative">
        <button type="button" @click="showEditModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="text-center pb-3 border-b border-slate-800">
            <h3 class="text-base font-extrabold text-white">Edit Klasifikasi Kode 108 BMD</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Perbarui Data Klasifikasi Kode 108</p>
        </div>

        <form :action="'/master-data/jenis-astap/' + editFormData.id" method="POST" class="space-y-4 text-xs" @submit="confirmEditForm($event)">
            @csrf
            @method('PUT')
            
            <div class="p-3.5 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 space-y-2">
                <span class="font-bold text-emerald-400 uppercase tracking-wider text-[10px] block">1. Jenis Utama</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">Kode Jenis <span class="text-rose-400">*</span></label>
                        <input type="text" 
                               name="jenis" 
                               x-model="editFormData.jenis" 
                               @input="onKodeJenisInputEdit()" 
                               required 
                               placeholder="Contoh: 1.3.2" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-emerald-400 font-mono font-bold focus:border-emerald-500">
                    </div>
                    <div class="col-span-2 relative" @click.away="showJenisDropdownEdit = false">
                        <label class="block text-slate-400 mb-1 flex items-center justify-between">
                            <span>Nama Jenis Utama <span class="text-rose-400">*</span></span>
                            <span class="text-[10px] text-emerald-400 font-normal">Ketik untuk filter / ubah</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="nama_jenis" 
                                   x-model="editFormData.nama_jenis" 
                                   @focus="showJenisDropdownEdit = true" 
                                   @input="showJenisDropdownEdit = true" 
                                   @keydown.escape="showJenisDropdownEdit = false" 
                                   required 
                                   autocomplete="off"
                                   placeholder="Ketik jenis utama..." 
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-emerald-500 pr-8">
                            <div class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>

                        <!-- Floating Dropdown Suggestions -->
                        <div x-show="showJenisDropdownEdit" 
                             x-transition.opacity.duration.150ms 
                             class="absolute left-0 right-0 z-50 mt-1 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto divide-y divide-slate-800" 
                             style="display: none;">
                            
                            <div class="px-3 py-1.5 bg-slate-950/90 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                <span>Pilih Master Jenis Utama</span>
                                <span class="text-emerald-400 font-mono text-[9px]" x-text="totalJenisCountEdit + ' tersedia'"></span>
                            </div>

                            <template x-for="item in filteredJenisListEdit" :key="item.kode + item.nama">
                                <div @click="selectJenisEdit(item)" 
                                     class="px-3 py-2 hover:bg-emerald-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-2"
                                     :class="{'bg-emerald-500/10': editFormData.jenis === item.kode}">
                                    <div class="flex items-center space-x-2 min-w-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0"></span>
                                        <span class="font-bold text-xs text-white group-hover:text-emerald-300 truncate" x-text="item.nama" :title="item.nama"></span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded bg-slate-950 border border-slate-700 text-emerald-400 font-mono text-[10px] font-bold shrink-0" x-text="item.kode"></span>
                                </div>
                            </template>

                            <template x-if="filteredJenisListEdit.length === 0">
                                <div class="p-3 text-center text-xs text-slate-400">
                                    <p class="text-emerald-400 font-semibold text-[11px]">✨ Jenis Utama Kustom</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Gunakan nama ini & sesuaikan Kode Jenis di samping</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-amber-950/20 border border-amber-500/30 space-y-2">
                <span class="font-bold text-amber-400 uppercase tracking-wider text-[10px] block">2. Sub Rincian Objek</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">Kode Sub Rincian <span class="text-rose-400">*</span></label>
                        <input type="text" 
                               name="sub_rincian_objek" 
                               x-model="editFormData.sub_rincian_objek" 
                               @input="onKodeSubRincianInputEdit()" 
                               required 
                               placeholder="Contoh: 1.3.2.02.01.01" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-400 font-mono font-bold focus:border-amber-500">
                    </div>
                    <div class="col-span-2 relative" @click.away="showSubRincianDropdownEdit = false">
                        <label class="block text-slate-400 mb-1 flex items-center justify-between">
                            <span>Uraian Sub Rincian <span class="text-rose-400">*</span></span>
                            <span class="text-[10px] text-amber-400 font-normal">Ketik untuk filter / ubah</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="uraian_sub_rincian" 
                                   x-model="editFormData.uraian_sub_rincian" 
                                   @focus="showSubRincianDropdownEdit = true" 
                                   @input="showSubRincianDropdownEdit = true; onUraianSubRincianInputEdit()" 
                                   @keydown.escape="showSubRincianDropdownEdit = false" 
                                   required 
                                   autocomplete="off" 
                                   placeholder="Contoh: ALAT KEDOKTERAN UMUM..." 
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-amber-500 pr-8">
                            <div class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>

                        <!-- Dropdown Suggestions Sub Rincian -->
                        <div x-show="showSubRincianDropdownEdit" 
                             x-transition.opacity.duration.150ms 
                             class="absolute left-0 right-0 z-50 mt-1 bg-slate-900 border border-slate-700 rounded-xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto divide-y divide-slate-800" 
                             style="display: none;">
                            <div class="px-3 py-1.5 bg-slate-950/90 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                <span>Pilih Sub Rincian Objek</span>
                                <span class="text-amber-400 font-mono text-[9px]" x-text="totalSubRincianCountEdit + ' tersedia'"></span>
                            </div>

                            <template x-for="item in filteredSubRincianListEdit" :key="item.sub_rincian_objek + item.uraian_sub_rincian">
                                <div @click="selectSubRincianEdit(item)" 
                                     class="px-3 py-2 hover:bg-amber-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-2"
                                     :class="{'bg-amber-500/10': editFormData.sub_rincian_objek === item.sub_rincian_objek}">
                                    <div class="flex items-center space-x-2 min-w-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                        <span class="font-bold text-xs text-white group-hover:text-amber-300 truncate" x-text="item.uraian_sub_rincian" :title="item.uraian_sub_rincian"></span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded bg-slate-950 border border-slate-700 text-amber-400 font-mono text-[10px] font-bold shrink-0" x-text="item.sub_rincian_objek"></span>
                                </div>
                            </template>

                            <template x-if="filteredSubRincianListEdit.length === 0">
                                <div class="p-3 text-center text-xs text-slate-400">
                                    <p class="text-amber-400 font-semibold text-[11px]">✨ Sub Rincian Kustom</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Gunakan uraian ini & sesuaikan Kode Sub Rincian di samping</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-purple-950/20 border border-purple-500/30 space-y-2">
                <span class="font-bold text-purple-400 uppercase tracking-wider text-[10px] block">3. Sub - Sub Rincian Objek (Spesifik)</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">Kode Sub-Sub Rincian <span class="text-rose-400">*</span></label>
                        <input type="text" 
                               name="sub_sub_rincian_objek" 
                               x-model="editFormData.sub_sub_rincian_objek" 
                               required 
                               placeholder="Contoh: 1.3.2.02.01.01.001" 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-purple-400 font-mono font-bold focus:border-purple-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-slate-400 mb-1">Uraian Sub-Sub Rincian (Nama Barang) <span class="text-rose-400">*</span></label>
                        <input type="text" 
                               name="uraian_sub_sub_rincian" 
                               x-model="editFormData.uraian_sub_sub_rincian" 
                               required 
                               autocomplete="off" 
                               placeholder="Contoh: Patient Monitor & Defibrillator..." 
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-purple-500">
                    </div>
                </div>
            </div>

            <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 text-slate-300 font-bold text-xs">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-cyan-500/20 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
