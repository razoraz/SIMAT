<!-- SEKSI 1: PEMILIHAN ASET BMD (ASTAP) -->
<div class="p-6 sm:p-7 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-5"
     x-data="{
         searchQuery: '',
         isOpen: false,
         openPicker() {
             this.isOpen = true;
             this.$nextTick(() => {
                 this.$refs.searchInput?.focus();
             });
         },
         closePicker() {
             this.isOpen = false;
             this.searchQuery = '';
         },
         get filteredAstapList() {
             const list = window.kandidatAstaps || [];
             if (!this.searchQuery || !this.searchQuery.trim()) {
                 return list;
             }
             const q = this.searchQuery.toLowerCase().trim();
             return list.filter(item => {
                 if (!item) return false;
                 const nama = (item.nama_barang || '').toLowerCase();
                 const kib = (item.category || item.asal_kib || '').toLowerCase();
                 const merk = (item.merk_type || '').toLowerCase();
                 const kode = (item.jenis_astap?.sub_sub_rincian_objek || item.jenis_astap?.sub_rincian_objek || '').toLowerCase();
                 const uraian = (item.jenis_astap?.uraian_sub_rincian || item.jenis_astap?.uraian_sub_sub_rincian || '').toLowerCase();
                 return nama.includes(q) || kib.includes(q) || merk.includes(q) || kode.includes(q) || uraian.includes(q);
             });
         },
         selectAstapItem(item) {
             if (!item) return;
             formData.astap_id = String(item.id);
             onSelectAstap(item.id);
             this.closePicker();
         },
         clearSelection() {
             formData.astap_id = '';
             onSelectAstap('');
             this.closePicker();
         }
     }">

    <!-- Hidden Input Form Submission -->
    <input type="hidden" name="astap_id" :value="formData.astap_id" required>

    <!-- Header Section 01 -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-4">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center font-black text-xs font-mono">
                01
            </span>
            <div>
                <h2 class="text-sm sm:text-base font-bold text-white tracking-wide">
                    Identitas Barang Milik Daerah (ASTAP)
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Pilih aset dari Buku Inventaris ASTAP RSUD yang memenuhi syarat untuk direklasifikasi.
                </p>
            </div>
        </div>
        <span class="text-[11px] font-semibold text-slate-400 bg-slate-950 px-3 py-1 rounded-full border border-slate-800 self-start sm:self-auto flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
            <span>Tersedia <strong class="text-white">{{ count($kandidatAstaps) }}</strong> Aset Layak Reklas</span>
        </span>
    </div>

    <!-- Dropdown Selector Trigger & Floating Popover (Wajib Memilih dari Daftar) -->
    <div class="space-y-3 relative" @click.outside="closePicker()">
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-300 mb-1 flex items-center justify-between">
            <span class="flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span>Pilih Barang Dari Inventaris <span class="text-rose-400">*</span></span>
            </span>
            <span class="text-[10px] text-slate-500 font-normal lowercase">(wajib memilih dari filter daftar)</span>
        </label>

        <!-- KONDISI 1: Belum Memilih Aset (Trigger Button Read-Only) -->
        <template x-if="!selectedAstap">
            <button type="button"
                    @click="openPicker()"
                    class="w-full bg-slate-950/90 border border-slate-800 hover:border-cyan-500/50 hover:bg-slate-900/50 rounded-2xl p-3.5 sm:p-4 text-left transition-all duration-200 cursor-pointer shadow-sm group flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 group-hover:text-cyan-400 group-hover:border-cyan-500/30 flex items-center justify-center shrink-0 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-xs sm:text-sm font-semibold text-slate-300 group-hover:text-white transition-colors block truncate">
                            -- Klik untuk Memilih Barang dari Buku Inventaris ASTAP --
                        </span>
                        <span class="text-[11px] text-slate-500 block mt-0.5">
                            Wajib memilih salah satu aset yang memenuhi kriteria reklasifikasi
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-slate-900 border border-slate-800 text-slate-400"
                          x-text="(window.kandidatAstaps?.length || {{ count($kandidatAstaps) }}) + ' Aset'"></span>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-white transition-transform duration-200"
                         :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </button>
        </template>

        <!-- KONDISI 2: Aset Telah Dipilih (Selected Chip Banner) -->
        <template x-if="selectedAstap">
            <div class="w-full bg-slate-950/95 border border-cyan-500/40 rounded-2xl p-3.5 sm:p-4 text-left transition-all duration-200 shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-start sm:items-center gap-3 min-w-0">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shrink-0 mt-1 sm:mt-0"></span>
                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold tracking-wider uppercase border shrink-0"
                          :class="{
                              'bg-cyan-500/15 text-cyan-300 border-cyan-500/30': ((selectedAstap.category || '').includes('B')),
                              'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': ((selectedAstap.category || '').includes('C')),
                              'bg-amber-500/15 text-amber-300 border-amber-500/30': ((selectedAstap.category || '').includes('A')),
                              'bg-purple-500/15 text-purple-300 border-purple-500/30': ((selectedAstap.category || '').includes('D')),
                              'bg-indigo-500/15 text-indigo-300 border-indigo-500/30': ((selectedAstap.category || '').includes('F')),
                              'bg-rose-500/15 text-rose-300 border-rose-500/30': (selectedAstap.category === 'EXTRACOM' || selectedAstap.is_extracomtable),
                              'bg-slate-800 text-slate-300 border-slate-700': !selectedAstap.category
                          }"
                          x-text="selectedAstap.category || 'ASTAP'"></span>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs sm:text-sm font-bold text-white truncate" x-text="selectedAstap.nama_barang"></span>
                            <template x-if="selectedAstap.is_reklas">
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    ⚡ Pernah Reklas
                                </span>
                            </template>
                        </div>
                        <span class="text-[11px] text-slate-400 font-mono block mt-0.5 truncate"
                              x-text="(selectedAstap.jenis_astap?.sub_sub_rincian_objek || '-') + ' • ' + (selectedAstap.merk_type || selectedAstap.jenis_astap?.uraian_sub_rincian || '-')"></span>
                    </div>
                </div>

                <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 border-t sm:border-t-0 border-slate-800/60 pt-2 sm:pt-0">
                    <span class="text-xs sm:text-sm font-black font-mono text-emerald-400"
                          x-text="'Rp ' + Number(selectedAstap.total_realisasi || 0).toLocaleString('id-ID')"></span>
                    
                    <button type="button"
                            @click="openPicker()"
                            class="text-[11px] font-semibold text-cyan-400 hover:text-cyan-300 hover:bg-cyan-500/10 px-3 py-1.5 rounded-xl border border-cyan-500/20 transition-all flex items-center gap-1.5"
                            title="Buka daftar untuk mengganti aset">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        <span>Ganti Aset</span>
                    </button>
                    
                    <button type="button"
                            @click="clearSelection()"
                            class="text-slate-400 hover:text-rose-400 p-1.5 rounded-lg hover:bg-rose-500/10 transition-colors"
                            title="Batal pilih aset">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </template>

        <!-- FLOATING POPOVER DROPDOWN: KOTAK PENCARIAN & DAFTAR ASET SAH -->
        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-2 scale-98"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-98"
             class="absolute z-50 left-0 right-0 top-full mt-2 bg-slate-950/98 backdrop-blur-2xl border border-slate-800 rounded-2xl shadow-2xl overflow-hidden">
            
            <!-- Dedicated Search Filter Header Inside Popover -->
            <div class="p-3 border-b border-slate-800 bg-slate-900/70">
                <div class="relative">
                    <input type="text"
                           x-ref="searchInput"
                           x-model="searchQuery"
                           @keydown.escape="closePicker()"
                           placeholder="Ketik untuk menyaring opsi (nama barang, KIB, merk, kode 108)..."
                           class="w-full bg-slate-950 border border-slate-800 hover:border-cyan-500/50 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/30 rounded-xl pl-9 pr-24 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                    
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-cyan-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center gap-1.5">
                        <span class="text-[9.5px] font-mono px-1.5 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-400"
                              x-text="filteredAstapList.length + ' Sesuai'"></span>
                        
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''; $refs.searchInput.focus()"
                                class="text-slate-400 hover:text-white p-1 rounded-md hover:bg-slate-800 transition-colors"
                                title="Bersihkan kata kunci">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- List of Eligible Assets (Scrollable) -->
            <div class="max-h-72 overflow-y-auto divide-y divide-slate-800/60 custom-scrollbar">
                <template x-for="item in filteredAstapList" :key="item.id">
                    <div @click="selectAstapItem(item)"
                         class="p-3.5 hover:bg-slate-800/70 cursor-pointer transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3 group"
                         :class="String(formData.astap_id) === String(item.id) ? 'bg-indigo-950/40 border-l-4 border-indigo-500' : ''">
                        
                        <div class="flex items-start gap-3 min-w-0">
                            <!-- KIB Category Badge -->
                            <span class="shrink-0 px-2.5 py-1 rounded-lg text-[10px] font-bold tracking-wider uppercase border mt-0.5"
                                  :class="{
                                      'bg-cyan-500/15 text-cyan-300 border-cyan-500/30': ((item.category || '').includes('B')),
                                      'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': ((item.category || '').includes('C')),
                                      'bg-amber-500/15 text-amber-300 border-amber-500/30': ((item.category || '').includes('A')),
                                      'bg-purple-500/15 text-purple-300 border-purple-500/30': ((item.category || '').includes('D')),
                                      'bg-indigo-500/15 text-indigo-300 border-indigo-500/30': ((item.category || '').includes('F')),
                                      'bg-rose-500/15 text-rose-300 border-rose-500/30': (item.category === 'EXTRACOM' || item.is_extracomtable),
                                      'bg-slate-800 text-slate-300 border-slate-700': !item.category
                                  }"
                                  x-text="item.category || 'ASTAP'"></span>
                            
                            <div class="space-y-0.5 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-xs sm:text-sm font-bold text-white group-hover:text-cyan-300 transition-colors truncate"
                                        x-text="item.nama_barang"></h4>
                                    <template x-if="item.is_reklas">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 shrink-0">
                                            ⚡ Pernah Reklas
                                        </span>
                                    </template>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] text-slate-400 truncate">
                                    <span class="font-mono text-slate-400" x-text="item.jenis_astap?.sub_sub_rincian_objek || item.jenis_astap?.sub_rincian_objek || '-'"></span>
                                    <span>•</span>
                                    <span class="truncate" x-text="item.merk_type || item.jenis_astap?.uraian_sub_rincian || '-'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="sm:text-right shrink-0 flex sm:flex-col items-center sm:items-end justify-between gap-1 border-t sm:border-t-0 border-slate-800/40 pt-1.5 sm:pt-0">
                            <span class="text-xs sm:text-sm font-black font-mono text-emerald-400"
                                  x-text="'Rp ' + Number(item.total_realisasi || 0).toLocaleString('id-ID')"></span>
                            <span class="text-[10px] text-slate-400"
                                  x-text="(item.jumlah_volume || 1) + ' ' + (item.satuan || 'Unit')"></span>
                        </div>
                    </div>
                </template>

                <!-- Empty State (Jika Kata Kunci Tidak Ditemukan) -->
                <div x-show="filteredAstapList.length === 0" class="p-8 text-center space-y-2">
                    <div class="w-10 h-10 rounded-full bg-slate-900 border border-slate-800 text-slate-500 flex items-center justify-center mx-auto">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-slate-300">Tidak ada aset kandidat yang sesuai</p>
                    <p class="text-[11px] text-slate-500 max-w-sm mx-auto">
                        Kata kunci tidak cocok dengan daftar inventaris yang memenuhi syarat reklasifikasi.
                    </p>
                </div>
            </div>

            <!-- Footer Popover Info -->
            <div class="p-2.5 bg-slate-950 border-t border-slate-800/80 text-[10px] text-slate-500 flex items-center justify-between">
                <span>Klik salah satu aset untuk memilih</span>
                <button type="button" @click="closePicker()" class="text-slate-400 hover:text-white font-medium">
                    Tutup [Esc]
                </button>
            </div>
        </div>

        <!-- Rich Snapshot Card Aset Terpilih -->
        <template x-if="selectedAstap">
            <div class="mt-4 p-5 sm:p-6 rounded-2xl bg-slate-950/90 border border-slate-800/90 space-y-4 shadow-inner"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0">
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800/80 pb-4">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 uppercase tracking-wider"
                                  x-text="selectedAstap.category || 'KIB'"></span>
                            <span class="text-xs font-mono text-slate-400"
                                  x-text="selectedAstap.jenis_astap?.sub_sub_rincian_objek || selectedAstap.jenis_astap?.sub_rincian_objek || '-'"></span>
                            <template x-if="selectedAstap.is_reklas">
                                <span class="px-2 py-0.5 rounded text-[9.5px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    ⚡ Pernah Direklasifikasi
                                </span>
                            </template>
                        </div>
                        <h3 class="text-base sm:text-lg font-black text-white" x-text="selectedAstap.nama_barang"></h3>
                        <p class="text-xs text-slate-400" x-text="selectedAstap.jenis_astap?.uraian_sub_rincian || selectedAstap.jenis_astap?.uraian_sub_sub_rincian || '-'"></p>
                    </div>

                    <div class="bg-slate-900/90 border border-slate-800 px-4 py-3 rounded-2xl text-left md:text-right shrink-0 shadow-sm">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Nilai Realisasi Buku Saat Ini</span>
                        <div class="text-lg sm:text-xl font-black font-mono text-emerald-400 mt-0.5"
                             x-text="'Rp ' + Number(selectedAstap.total_realisasi || 0).toLocaleString('id-ID')"></div>
                        <div class="text-[11px] text-slate-400 mt-0.5"
                             x-text="(selectedAstap.jumlah_volume || 1) + ' ' + (selectedAstap.satuan || 'Unit')"></div>
                    </div>
                </div>

                <!-- Detail Grid Tambahan -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs pt-1">
                    <div class="p-2.5 rounded-xl bg-slate-900/40 border border-slate-800/60">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Tahun Perolehan</span>
                        <span class="font-bold text-white font-mono mt-0.5 block" x-text="selectedAstap.tahun_perolehan || '-'"></span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-900/40 border border-slate-800/60">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Merk / Tipe</span>
                        <span class="font-medium text-slate-300 truncate mt-0.5 block" x-text="selectedAstap.merk_type || '-'"></span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-900/40 border border-slate-800/60">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Status Akuntansi</span>
                        <span class="font-bold mt-0.5 block" :class="selectedAstap.is_extracomtable ? 'text-amber-400' : 'text-emerald-400'"
                              x-text="selectedAstap.is_extracomtable ? 'Ekstrakomptabel' : 'Intrakomptabel (Aset Tetap)'"></span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-900/40 border border-slate-800/60">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Lokasi / Alamat</span>
                        <span class="font-medium text-slate-300 truncate mt-0.5 block" x-text="selectedAstap.alamat_barang || 'RSUD Dr. H. Koesnandi'"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
