            <!-- BAGIAN 2: DAFTAR BARANG YANG DIDISTRIBUSIKAN (MULTI-BARANG DALAM 1 TRANSAKSI) -->
            <div class="space-y-4 pt-2">
                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-extrabold text-teal-300 uppercase tracking-wider flex items-center space-x-2">
                        <span>2. Rincian Barang Aset yang Didistribusikan</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pilih Jenis ASTAP untuk membatasi daftar nama barang — Satuan, kode 108, dan spesifikasi terisi otomatis</p>
                </div>

                <!-- Daftar Input Multi-Barang (Layout Card Terstruktur & Rapi) -->
                <div class="space-y-5">
                    <template x-for="(item, idx) in formData.items" :key="item.id">
                        <div class="bg-slate-950/90 border border-slate-800 hover:border-slate-700/80 rounded-3xl p-5 sm:p-6 transition-all shadow-lg space-y-5">

                            <!-- Card Header: Nomor Barang, Nama Terpilih Dinamis, Badge Kode 108 & Tombol Hapus Barang di Pojok Kanan -->
                            <div class="flex items-center justify-between pb-3 border-b border-slate-800 gap-3">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <span class="rounded-xl bg-teal-500/20 text-teal-300 font-extrabold text-xs flex items-center justify-center border border-teal-500/30 shrink-0"
                                          style="width: 28px; height: 28px; min-width: 28px; min-height: 28px;"
                                          x-text="idx + 1"></span>
                                    
                                    <!-- Judul Dinamis Mengikuti Barang yang Dipilih -->
                                    <div class="min-w-0 flex items-center space-x-2">
                                        <span class="text-sm font-extrabold text-white tracking-wide truncate" 
                                              x-text="item.nama_barang ? item.nama_barang : ('Rincian Barang #' + (idx + 1))"></span>
                                        <template x-if="item.jenis_astap_nama">
                                            <span class="px-2.5 py-0.5 rounded-md bg-teal-500/10 border border-teal-500/20 text-teal-300 text-[10px] font-bold hidden md:inline-block truncate max-w-[220px]" x-text="item.jenis_astap_nama"></span>
                                        </template>
                                    </div>
                                    
                                    <!-- Badge Otomatis Kode 108 -->
                                    <template x-if="item.kode_barang">
                                        <span class="px-2.5 py-0.5 rounded-lg bg-slate-900 border border-cyan-500/30 text-cyan-400 font-mono font-bold text-[10px] shrink-0 hidden sm:inline-block" x-text="'Kode: ' + item.kode_barang"></span>
                                    </template>
                                </div>

                                <!-- Tombol Hapus Barang — Masuk di Dalam Form Pojok Kanan Atas Header -->
                                <template x-if="formData.status !== 'Ditolak'">
                                    <button type="button" @click="removeItem(idx)"
                                            class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/25 text-rose-300 border border-rose-500/30 text-xs font-semibold flex items-center space-x-1.5 transition-all active:scale-95 shrink-0 cursor-pointer"
                                            title="Hapus baris barang ini">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        <span class="hidden sm:inline">Hapus</span>
                                    </button>
                                </template>
                            </div>

                            <!-- Grid Form Input Barang -->
                            <div class="space-y-4">
                                
                                <!-- Baris 0: Jenis ASTAP (Diatas Nama Barang, Format Sama Seperti Nama Barang) -->
                                <div class="relative" @click.away="if (activeJenisDropdownIndex === idx) activeJenisDropdownIndex = null">
                                    <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                                        <span class="flex items-center space-x-1.5">
                                            <span class="text-teal-400">🏷️</span>
                                            <span>Jenis ASTAP</span>
                                            <span class="text-slate-400 font-normal text-[11px] hidden sm:inline">(Pilih jenis untuk memfilter daftar barang)</span>
                                        </span>
                                        <span class="text-teal-400 font-mono text-[10px] hidden sm:inline">⚡ Pilih Jenis Aset</span>
                                    </label>
                                    <div class="relative flex items-center">
                                        <input type="text" 
                                               x-model="item.jenis_astap_nama" 
                                               :disabled="formData.status === 'Ditolak'"
                                               :readonly="formData.status === 'Ditolak'"
                                               @focus="if (formData.status !== 'Ditolak') activeJenisDropdownIndex = idx"
                                               @input="if (formData.status !== 'Ditolak') activeJenisDropdownIndex = idx"
                                               placeholder="Ketik atau pilih Jenis ASTAP (contoh: Peralatan dan Mesin, Gedung, Tanah)..." 
                                               :class="formData.status === 'Ditolak' ? 'bg-slate-950/80 text-slate-400 cursor-not-allowed border-slate-800' : 'bg-slate-900 text-white cursor-pointer border-slate-700/90 focus:border-teal-500 focus:ring-1 focus:ring-teal-500'"
                                               class="w-full h-11 border rounded-xl px-4 py-2.5 pl-10 pr-10 text-xs font-bold placeholder-slate-500 focus:outline-none transition-all">
                                        
                                        <svg class="w-4 h-4 text-teal-400 pointer-events-none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>

                                        <!-- Tombol Silang Bersihkan Jenis ASTAP di Pojok Kanan Dalam Input -->
                                        <template x-if="formData.status !== 'Ditolak' && item.jenis_astap_nama && item.jenis_astap_nama.trim() !== ''">
                                            <button type="button" 
                                                    @click.stop="clearJenisAstap(item, idx)" 
                                                    style="position: absolute; right: 12px; left: auto; top: 50%; transform: translateY(-50%); z-index: 20;"
                                                    class="w-6 h-6 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center transition-all cursor-pointer shadow-sm border border-slate-700/60 hover:border-rose-500/40"
                                                    title="Kosongkan jenis ASTAP">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </template>
                                    </div>

                                    <!-- Floating Dropdown Hasil Filter Jenis ASTAP (Hanya Menampilkan Nama Jenis) -->
                                    <div x-show="formData.status !== 'Ditolak' && activeJenisDropdownIndex === idx" 
                                         x-transition 
                                         class="absolute left-0 right-0 z-50 mt-1.5 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto divide-y divide-slate-800">
                                        
                                        <div class="px-4 py-2 bg-slate-950/90 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                            <span>Pilih Master Jenis ASTAP</span>
                                            <span class="text-teal-400 font-mono" x-text="getFilteredJenisAstap(item.jenis_astap_nama).length + ' jenis tersedia'"></span>
                                        </div>

                                        <template x-for="j in getFilteredJenisAstap(item.jenis_astap_nama)" :key="j.kode || j.nama">
                                            <div @click="selectJenisAstap(item, j)"
                                                 class="px-4 py-3 hover:bg-teal-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3"
                                                 :class="{'bg-teal-500/10': item.jenis_astap_nama === j.nama}">
                                                <div class="flex items-center space-x-2.5">
                                                    <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                                                    <p class="font-bold text-xs text-white group-hover:text-teal-300" x-text="j.nama"></p>
                                                </div>
                                                <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-700 text-teal-300 text-[10px] font-bold shrink-0">Pilih &rarr;</span>
                                            </div>
                                        </template>

                                        <template x-if="getFilteredJenisAstap(item.jenis_astap_nama).length === 0">
                                            <div class="p-4 text-center text-xs text-slate-400">
                                                <p class="text-amber-400 font-semibold">Tidak ditemukan Jenis ASTAP</p>
                                                <p class="text-[10px] text-slate-500 mt-0.5">Coba gunakan kata kunci pencarian yang lain</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Baris 1: Nama Barang & Kode Rekening 108 (Selalu Sejajar Berdampingan) -->
                                <div class="flex flex-row items-end gap-3 w-full">
                                    
                                    <!-- 1. Nama Barang / Aset (Autocomplete Search Langsung Berdasarkan Filter Jenis ASTAP) -->
                                    <div class="flex-1 min-w-0 relative" @click.away="if (activeDropdownIndex === idx) activeDropdownIndex = null">
                                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                                            <span class="flex items-center space-x-1.5">
                                                <span>Nama Barang / Aset ASTAP</span>
                                                <template x-if="item.jenis_astap_nama">
                                                    <span class="text-teal-400 text-[10px] bg-teal-500/10 px-2 py-0.5 rounded border border-teal-500/20 font-semibold" x-text="'Filter: ' + item.jenis_astap_nama"></span>
                                                </template>
                                            </span>
                                            <span class="text-teal-400 font-mono text-[10px] hidden sm:inline" x-text="item.jenis_astap_nama ? '⚡ Sesuai Jenis Terpilih' : '⚡ Ketik untuk filter'"></span>
                                        </label>
                                        <div class="relative flex items-center">
                                            <input type="text" 
                                                   x-model="item.nama_barang" 
                                                   :disabled="formData.status === 'Ditolak'"
                                                   :readonly="formData.status === 'Ditolak'"
                                                   @focus="if (formData.status !== 'Ditolak') activeDropdownIndex = idx"
                                                   @input="if (formData.status !== 'Ditolak') { activeDropdownIndex = idx; onNamaBarangInput(item); }"
                                                   :placeholder="item.jenis_astap_nama ? ('Ketik nama barang dari ' + item.jenis_astap_nama + '...') : 'Ketik nama barang aset (contoh: laptop, monitor, kasur)...'" 
                                                   :class="formData.status === 'Ditolak' ? 'bg-slate-950/80 text-slate-400 cursor-not-allowed border-slate-800' : 'bg-slate-900 text-white border-slate-700/90 focus:border-teal-500 focus:ring-1 focus:ring-teal-500'"
                                                   class="w-full h-11 border rounded-xl px-4 py-2.5 pl-10 pr-10 text-xs font-bold placeholder-slate-500 focus:outline-none transition-all">
                                            
                                            <svg class="w-4 h-4 text-teal-400 pointer-events-none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>

                                            <!-- Tombol Silang Bersihkan Nama Barang di Pojok Kanan Dalam Input -->
                                            <template x-if="formData.status !== 'Ditolak' && item.nama_barang && item.nama_barang.trim() !== ''">
                                                <button type="button" 
                                                        @click.stop="clearItemBarang(item, idx)" 
                                                        style="position: absolute; right: 12px; left: auto; top: 50%; transform: translateY(-50%); z-index: 20;"
                                                        class="w-6 h-6 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center transition-all cursor-pointer shadow-sm border border-slate-700/60 hover:border-rose-500/40"
                                                        title="Kosongkan nama barang">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </template>
                                        </div>

                                        <!-- Floating Dropdown Hasil Ketik Filter ASTAP -->
                                        <div x-show="formData.status !== 'Ditolak' && activeDropdownIndex === idx" 
                                             x-transition 
                                             class="absolute left-0 right-0 z-40 mt-1.5 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto divide-y divide-slate-800">
                                            
                                            <div class="px-4 py-2 bg-slate-950/90 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                                <span x-text="item.jenis_astap_nama ? ('Pilih Master ASTAP (' + item.jenis_astap_nama + ')') : 'Pilih Data Master ASTAP'"></span>
                                                <span class="text-teal-400 font-mono" x-text="getFilteredAstap(item, item.nama_barang).length + ' barang tersedia'"></span>
                                            </div>

                                            <template x-for="ast in getSlicedFilteredAstap(item, item.nama_barang, 10)" :key="ast.id">
                                                <div @click="selectAstapItem(item, ast)"
                                                     :class="isItemAlreadySelected(ast, item) ? 'opacity-40 cursor-not-allowed bg-slate-950/40' : 'hover:bg-teal-500/15 cursor-pointer'"
                                                     class="px-4 py-2.5 transition-colors group flex items-center justify-between gap-3">
                                                    <div class="space-y-0.5">
                                                        <div class="flex items-center gap-2">
                                                            <p class="font-bold text-xs text-white group-hover:text-teal-300" x-text="ast.nama"></p>
                                                            <template x-if="isItemAlreadySelected(ast, item)">
                                                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/40 font-bold">Sudah Dipilih</span>
                                                            </template>
                                                        </div>
                                                        <p class="text-[10px] text-slate-400" x-text="ast.kode + (ast.jenis_nama ? ' • ' + ast.jenis_nama : (ast.kategori ? ' • ' + ast.kategori : '')) + (ast.merk ? ' • ' + ast.merk : '')"></p>
                                                    </div>
                                                    <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-700 text-teal-300 font-mono text-[10px] font-bold shrink-0" x-text="ast.satuan || 'Unit'"></span>
                                                </div>
                                            </template>

                                            <template x-if="getFilteredAstap(item, item.nama_barang).length > 10">
                                                <div class="px-4 py-2 bg-slate-950/90 border-t border-slate-800 text-[10px] text-slate-400 flex items-center justify-between">
                                                    <span class="text-slate-400 italic">Menampilkan 10 hasil teratas</span>
                                                    <span class="text-teal-400 font-medium">Ketik nama barang untuk mempersempit pencarian</span>
                                                </div>
                                            </template>

                                            <template x-if="getFilteredAstap(item, item.nama_barang).length === 0">
                                                <div class="p-4 text-center text-xs text-slate-400">
                                                    <p class="text-amber-400 font-semibold" x-text="item.jenis_astap_nama ? ('Tidak ditemukan barang untuk ' + item.jenis_astap_nama) : 'Tidak ditemukan barang ASTAP'"></p>
                                                    <p class="text-[10px] text-slate-500 mt-0.5">Ketik nama lain atau isi nama barang secara manual</p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- 2. Kode Rekening 108 (Sejajar di Samping Nama Barang) -->
                                    <div class="w-48 sm:w-56 shrink-0">
                                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kode Rekening 108</label>
                                        <input type="text" x-model="item.kode_barang" placeholder="Terisi otomatis..." readonly
                                               :class="formData.status === 'Ditolak' ? 'bg-slate-950/80 text-slate-500 border-slate-800' : 'bg-slate-900 text-cyan-300 border-slate-700/90'"
                                               class="w-full h-11 border rounded-xl px-4 py-2.5 text-xs font-mono font-bold placeholder-slate-500 focus:outline-none transition-all cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- Baris 2: Stok Tersedia, Volume Pengajuan, Volume ACC (Admin) -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full">
                                    <!-- Kolom 1 (Baru): Stok NIBAR Tersedia (Readonly Info) -->
                                    <div>
                                        <label class="block font-semibold text-xs mb-1.5 flex items-center justify-between">
                                            <span class="flex items-center space-x-1.5">
                                                <span class="text-slate-300">Stok Tersedia</span>
                                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold transition-all"
                                                      :class="!item.nama_barang ? 'bg-slate-800 text-slate-400 border border-slate-700' : (getMatchingNibarCount(item) > 0 ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-300 border border-rose-500/30')"
                                                      x-text="!item.nama_barang ? '⚪ Belum Pilih' : (getMatchingNibarCount(item) > 0 ? '📦 Ada di Gudang' : '⚠️ Stok Habis')">
                                                </span>
                                            </span>
                                        </label>
                                        <div class="relative flex items-center">
                                            <input type="text"
                                                :value="!item.nama_barang ? 'Pilih nama barang...' : (getMatchingNibarCount(item) + ' ' + (item.satuan || 'Unit'))"
                                                readonly
                                                tabindex="-1"
                                                :title="item.nama_barang ? ('Total ' + getMatchingNibarCount(item) + ' ' + (item.satuan || 'Unit') + ' berstatus Tersedia dan kondisi Baik di gudang aset') : 'Pilih nama barang terlebih dahulu'"
                                                :class="!item.nama_barang ? 'bg-slate-950/80 text-slate-500 border-slate-800' : (getMatchingNibarCount(item) > 0 ? 'bg-slate-900 text-teal-300 border-teal-500/40 shadow-sm' : 'bg-slate-950/90 text-rose-400 border-rose-500/30')"
                                                class="w-full h-11 border rounded-xl px-4 py-2.5 pr-16 text-xs font-mono font-bold cursor-not-allowed select-none focus:outline-none transition-all">
                                            
                                            <!-- Suffix status icon di dalam kotak input -->
                                            <div style="position: absolute; right: 12px; left: auto; top: 50%; transform: translateY(-50%); pointer-events: none; z-index: 10;">
                                                <template x-if="item.nama_barang && getMatchingNibarCount(item) > 0">
                                                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-teal-500/15 text-teal-300 border border-teal-500/30 font-bold font-mono">✓ Siap</span>
                                                </template>
                                                <template x-if="item.nama_barang && getMatchingNibarCount(item) === 0">
                                                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-rose-500/15 text-rose-300 border border-rose-500/30 font-bold font-mono">Kosong</span>
                                                </template>
                                                <template x-if="!item.nama_barang">
                                                    <span class="text-xs text-slate-600 font-mono">—</span>
                                                </template>
                                            </div>
                                        </div>
                                        
                                        <!-- Helper note stok -->
                                        <p class="text-[10px] mt-1 flex items-center space-x-1">
                                            <template x-if="!item.nama_barang">
                                                <span class="text-slate-500">Pilih nama barang di atas</span>
                                            </template>
                                            <template x-if="item.nama_barang && getMatchingNibarCount(item) > 0">
                                                <span class="text-teal-400/90">ℹ️ Siap didistribusikan untuk unit</span>
                                            </template>
                                            <template x-if="item.nama_barang && getMatchingNibarCount(item) === 0">
                                                <span class="text-rose-400">⚠️ NIBAR belum ada / sedang terpakai</span>
                                            </template>
                                        </p>
                                    </div>

                                    <!-- Kolom 2: Volume Pengajuan (Qty) — Sub Admin & Admin bisa isi -->
                                    <div>
                                        <label class="block font-semibold text-xs mb-1.5 flex items-center justify-between">
                                            <span class="flex items-center space-x-1.5">
                                                <span class="text-slate-300">Volume Pengajuan</span>
                                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-teal-500/15 border border-teal-500/30 text-teal-300 font-bold" x-text="isSubAdmin ? '📋 Diisi Anda' : '📋 Qty Diajukan'"></span>
                                            </span>
                                        </label>
                                        <div class="relative flex items-center">
                                            <input type="text"
                                                :value="item.qty ? Number(item.qty).toLocaleString('id-ID') : ''"
                                                :disabled="formData.status === 'Ditolak'"
                                                :readonly="formData.status === 'Ditolak'"
                                                @input="
                                                    if (formData.status === 'Ditolak') return;
                                                    let raw = $event.target.value.replace(/\D/g, '');
                                                    item.qty = raw ? parseInt(raw, 10) : '';
                                                    $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                    item.qty_acc = (item.nibar_selected || []).length;
                                                "
                                                placeholder="1"
                                                :class="formData.status === 'Ditolak' ? 'bg-slate-950/80 text-slate-400 cursor-not-allowed border-slate-800' : 'bg-slate-900 text-white border-slate-700/90 focus:border-teal-500'"
                                                class="w-full h-11 border rounded-xl px-4 py-2.5 pr-20 text-xs font-mono font-bold focus:outline-none transition-all">
                                            <!-- Suffix Satuan otomatis -->
                                            <span style="position: absolute; right: 12px; left: auto; top: 50%; transform: translateY(-50%); pointer-events: none; z-index: 10;"
                                                  class="text-teal-300 font-bold text-xs px-2 py-0.5 rounded-lg bg-teal-500/10 border border-teal-500/20"
                                                  x-text="item.satuan || 'Unit'"></span>
                                        </div>
                                        <!-- Helper Note jika Pengajuan > Stok Tersedia -->
                                        <p class="text-[10px] mt-1 flex items-center space-x-1">
                                            <template x-if="item.nama_barang && item.qty && getMatchingNibarCount(item) > 0 && item.qty > getMatchingNibarCount(item)">
                                                <span class="text-amber-400 font-semibold">⚠️ Melebihi stok (<span x-text="getMatchingNibarCount(item) + ' ' + (item.satuan || 'Unit')"></span>)</span>
                                            </template>
                                            <template x-if="item.nama_barang && getMatchingNibarCount(item) === 0">
                                                <span class="text-rose-400">⚠️ Tidak dapat di-ACC karena stok kosong</span>
                                            </template>
                                            <template x-if="!item.nama_barang || (!item.qty || item.qty <= getMatchingNibarCount(item))">
                                                <span class="text-slate-400">Jumlah unit barang yang diajukan</span>
                                            </template>
                                        </p>
                                    </div>

                                    <!-- Vol 2: Volume ACC — Mengikuti NIBAR yang diinput & tidak dapat diedit -->
                                    <template x-if="!isSubAdmin">
                                        <div>
                                             <label class="block font-semibold text-xs mb-1.5 flex items-center justify-between">
                                                <span class="text-emerald-300">Volume Di-ACC</span>
                                                <template x-if="item.qty_acc && item.qty_acc > 0">
                                                    <span class="text-[10px] text-emerald-400 font-semibold" x-text="'✅ ACC: ' + item.qty_acc + ' ' + (item.satuan || 'Unit')"></span>
                                                </template>
                                            </label>
                                            <div class="relative flex items-center">
                                                <input type="text"
                                                    :value="((item.qty_acc !== null && item.qty_acc !== undefined && item.qty_acc !== '') ? item.qty_acc : (item.nibar_selected ? item.nibar_selected.length : 0)) + ' ' + (item.satuan || 'Unit')"
                                                    readonly
                                                    tabindex="-1"
                                                    title="Volume Di-ACC terisi otomatis mengikuti jumlah NIBAR yang diinput dan tidak dapat diedit manual"
                                                    :class="(!item.qty_acc || item.qty_acc <= 0) ? 'text-slate-400 border-slate-800' : 'text-emerald-400 border-emerald-500/30'"
                                                    class="w-full h-11 bg-slate-950/80 font-mono font-bold border rounded-xl px-4 py-2.5 pr-10 text-xs cursor-not-allowed select-none focus:outline-none shadow-inner">
                                                <div style="position: absolute; right: 12px; left: auto; top: 50%; transform: translateY(-50%); pointer-events: none; z-index: 10;"
                                                     class="flex items-center space-x-1" :class="(!item.qty_acc || item.qty_acc <= 0) ? 'text-slate-600' : 'text-emerald-400/80'">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                </div>
                                            </div>
                                            <p class="text-[10px] text-slate-400 mt-1 flex items-center space-x-1">
                                                <template x-if="!isNibarEmpty(item) && (item.nibar_selected || []).length > 0">
                                                    <span>ℹ️ Otomatis mengikuti total NIBAR terpilih (<strong class="text-emerald-300 font-mono" x-text="(item.nibar_selected || []).length"></strong> NIBAR)</span>
                                                </template>
                                                <template x-if="!isNibarEmpty(item) && (!item.nibar_selected || item.nibar_selected.length === 0)">
                                                    <span class="text-amber-400/90">ℹ️ Belum ada NIBAR dipilih (barang ini tidak di-ACC)</span>
                                                </template>
                                                <template x-if="isNibarEmpty(item)">
                                                    <span class="text-rose-400">⚠️ Stok kosong — barang ini tidak di-ACC (0 Unit)</span>
                                                </template>
                                            </p>
                                        </div>
                                    </template>

                                    <!-- Info Box Volume ACC untuk Sub Admin (readonly, tidak bisa isi) -->
                                    <template x-if="isSubAdmin">
                                        <div>
                                            <label class="block text-slate-400 font-semibold text-xs mb-1.5 flex items-center space-x-1.5">
                                                <span>Volume Di-ACC</span>
                                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-700/60 border border-slate-700 text-slate-400 font-bold">🔒 Admin Only</span>
                                            </label>
                                            <div class="w-full h-11 bg-slate-950/80 border border-slate-800 rounded-xl px-4 flex items-center text-xs cursor-not-allowed"
                                                 :class="(item.qty_acc !== null && item.qty_acc !== '') ? 'text-emerald-400 font-bold border-emerald-500/30' : 'text-slate-500'">
                                                <span x-text="(item.qty_acc !== null && item.qty_acc !== '') ? ('✅ ' + Number(item.qty_acc).toLocaleString('id-ID') + ' ' + (item.satuan || 'Unit')) : '⏳ Menunggu Keputusan Admin'"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Baris 1b: NIBAR Multi-Select (Admin & Master Admin bisa isi saat input baru maupun ubah) -->

                                <!-- Petunjuk: isi Volume Pengajuan dulu sebelum input NIBAR -->
                                <template x-if="!isSubAdmin && item.nama_barang && (!item.qty || item.qty <= 0)">
                                    <div class="flex items-center space-x-2 px-4 py-3 rounded-xl bg-amber-500/8 border border-amber-500/25 text-amber-300">
                                        <span class="text-base shrink-0">📋</span>
                                        <span class="text-xs font-semibold">Isi <strong>Volume Pengajuan</strong> terlebih dahulu sebelum memilih NIBAR.</span>
                                    </div>
                                </template>

                                <template x-if="!isSubAdmin && item.qty > 0">
                                    <div class="relative" @click.away="if(activeNibarDropdownIndex === idx) activeNibarDropdownIndex = null">
                                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                                            <span class="flex items-center space-x-1.5">
                                                <span class="text-amber-400">🔖</span>
                                                <span>NIBAR (Nomor Induk Barang)</span>
                                                <template x-if="!item.nama_barang && !item.kode_barang">
                                                    <span class="text-slate-400 font-normal text-[11px] hidden sm:inline">— pilih nama barang terlebih dahulu</span>
                                                </template>
                                                <template x-if="(item.nama_barang || item.kode_barang) && !isNibarEmpty(item)">
                                                    <span class="text-slate-400 font-normal text-[11px] hidden sm:inline">— pilih NIBAR yang didistribusikan</span>
                                                </template>
                                            </span>
                                            
                                            <!-- Status Badge -->
                                            <div>
                                                <template x-if="!item.nama_barang && !item.kode_barang">
                                                    <span class="text-slate-500 font-mono text-[10px]">Pilih barang dulu</span>
                                                </template>
                                                <template x-if="(item.nama_barang || item.kode_barang) && isNibarEmpty(item)">
                                                    <span class="px-2 py-0.5 rounded bg-rose-500/15 border border-rose-500/30 text-rose-300 text-[10px] font-bold">
                                                        ⚠️ Barang Kosong (0 NIBAR)
                                                    </span>
                                                </template>
                                                <template x-if="(item.nama_barang || item.kode_barang) && !isNibarEmpty(item)">
                                                    <span class="text-amber-400 font-mono text-[10px]" x-text="(item.nibar_selected || []).length + ' NIBAR dipilih'"></span>
                                                </template>
                                            </div>
                                        </label>

                                        <!-- List NIBAR yang Dipilih dengan Pengaturan Kondisi Fisik Per Unit -->
                                        <template x-if="(item.nibar_selected || []).length > 0">
                                            <div class="space-y-2 mb-3 p-3 rounded-2xl bg-slate-950/60 border border-slate-800/80">
                                                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between pb-1 border-b border-slate-800/60">
                                                    <span class="flex items-center space-x-1.5">
                                                        <span>📋</span>
                                                        <span>Kondisi Fisik Per Unit NIBAR:</span>
                                                    </span>
                                                    <span class="text-amber-400 font-mono text-[10.5px]" x-text="(item.nibar_selected || []).length + ' Unit NIBAR'"></span>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pt-1">
                                                    <template x-for="(n, nIdx) in item.nibar_selected" :key="n.nibar">
                                                        <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-900 border border-slate-700/80 hover:border-amber-500/40 transition-all shadow-sm">
                                                             <!-- Info Nomor NIBAR & Ruang -->
                                                            <div class="min-w-0 flex-1">
                                                                <div class="flex items-center space-x-1.5">
                                                                    <span class="w-4 h-4 rounded-full bg-amber-500/20 text-amber-300 flex items-center justify-center text-[9px] font-bold font-mono" x-text="nIdx + 1"></span>
                                                                    <span class="font-mono font-bold text-xs text-amber-300 truncate" x-text="n.nibar"></span>
                                                                </div>
                                                                <p class="text-[9.5px] text-slate-400 truncate mt-0.5 pl-5" x-text="'Ruang: ' + (n.ruang || 'Gudang Aset')"></p>
                                                            </div>

                                                            <!-- Badge Kondisi Per Unit NIBAR (Read-Only / Selalu Kondisi Baik) -->
                                                            <div class="shrink-0 flex items-center space-x-1.5">
                                                                <span class="text-[10px] font-bold rounded-lg border px-2 py-1 select-none flex items-center space-x-1 bg-emerald-500/15 text-emerald-300 border-emerald-500/40">
                                                                    <span>🟢 Baik</span>
                                                                </span>

                                                                <!-- Tombol Hapus NIBAR -->
                                                                <template x-if="formData.status !== 'Ditolak'">
                                                                    <button type="button" @click.stop="removeNibar(item, n.nibar)"
                                                                            class="w-6 h-6 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center transition-all border border-slate-700 hover:border-rose-500/40 cursor-pointer"
                                                                            title="Hapus NIBAR ini">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                    </button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- KONDISI 1: Belum Pilih Barang -->
                                        <template x-if="!item.nama_barang && !item.kode_barang">
                                            <div class="relative flex items-center">
                                                <input type="text" 
                                                       disabled
                                                       placeholder="Pilih nama barang di atas terlebih dahulu untuk memilih NIBAR..." 
                                                       class="w-full h-11 bg-slate-900/50 border border-slate-800 rounded-xl px-4 py-2.5 pl-10 pr-4 text-xs text-slate-500 placeholder-slate-600 cursor-not-allowed">
                                                <svg class="w-4 h-4 text-slate-600 pointer-events-none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                            </div>
                                        </template>

                                        <!-- KONDISI 2: Barang Terpilih Tapi Data NIBAR Kosong -->
                                        <template x-if="(item.nama_barang || item.kode_barang) && isNibarEmpty(item)">
                                            <div class="space-y-1.5">
                                                <div class="relative flex items-center">
                                                    <input type="text" 
                                                           disabled
                                                           value="⚠️ Stok Kosong — Data register NIBAR belum tersedia di sistem" 
                                                           class="w-full h-11 bg-rose-950/20 border border-rose-500/40 rounded-xl px-4 py-2.5 pl-10 pr-4 text-xs text-rose-300 font-semibold cursor-not-allowed">
                                                    <svg class="w-4 h-4 text-rose-400 pointer-events-none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                </div>
                                                <p class="text-[11px] text-rose-400/90 flex items-center space-x-1.5 pl-1">
                                                    <span>ℹ️ Register NIBAR aktif tidak ditemukan untuk <span class="font-mono font-bold text-white" x-text="getItemKode(item) || item.nama_barang"></span>. Volume Di-ACC tetap 0.</span>
                                                </p>
                                            </div>
                                        </template>

                                        <!-- KONDISI 3: Barang Terpilih & Ada NIBAR Tersedia -->
                                        <template x-if="(item.nama_barang || item.kode_barang) && !isNibarEmpty(item)">
                                            <div>
                                                <!-- Input Pencarian NIBAR -->
                                                <div class="relative flex items-center">
                                                    <input type="text" 
                                                           :value="nibarSearch[item.id] || ''"
                                                           :disabled="formData.status === 'Ditolak'"
                                                           :readonly="formData.status === 'Ditolak'"
                                                           @input="if (formData.status !== 'Ditolak') { nibarSearch = {...nibarSearch, [item.id]: $event.target.value}; activeNibarDropdownIndex = idx; }"
                                                           @focus="if (formData.status !== 'Ditolak') activeNibarDropdownIndex = idx"
                                                           :placeholder="formData.status === 'Ditolak' ? 'NIBAR terkunci' : 'Ketik atau klik untuk cari / pilih NIBAR...'" 
                                                           :class="formData.status === 'Ditolak' ? 'bg-slate-950/80 text-slate-500 cursor-not-allowed border-slate-800' : 'bg-slate-900 text-white border-amber-500/40 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/50'"
                                                           class="w-full h-11 border rounded-xl px-4 py-2.5 pl-10 pr-10 text-xs font-mono placeholder-slate-500 focus:outline-none transition-all">
                                                    <svg class="w-4 h-4 text-amber-400 pointer-events-none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>

                                                    <!-- Tombol Silang Reset Input Pencarian NIBAR di Pojok Kanan Dalam Input -->
                                                    <template x-if="formData.status !== 'Ditolak' && (nibarSearch[item.id] || '').trim() !== ''">
                                                        <button type="button" 
                                                                @click.stop="nibarSearch[item.id] = ''" 
                                                                style="position: absolute; right: 12px; left: auto; top: 50%; transform: translateY(-50%); z-index: 20;"
                                                                class="w-6 h-6 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center transition-all cursor-pointer shadow-sm border border-slate-700/60 hover:border-rose-500/40"
                                                                title="Bersihkan pencarian NIBAR">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </template>
                                                </div>

                                                <!-- Dropdown NIBAR -->
                                                <div x-show="formData.status !== 'Ditolak' && activeNibarDropdownIndex === idx"
                                                     x-transition
                                                     class="absolute left-0 right-0 z-40 mt-1.5 bg-slate-900 border border-amber-500/30 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto divide-y divide-slate-800">

                                                    <div class="px-4 py-2 bg-slate-950/90 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                                        <span x-text="'Pilih NIBAR untuk ' + (item.nama_barang || '-')"></span>
                                                        <span class="text-emerald-400 font-bold" x-text="getFilteredNibar(item, nibarSearch[item.id] || '').length + ' Tersedia'"></span>
                                                    </div>

                                                    <template x-for="n in getFilteredNibar(item, nibarSearch[item.id] || '')" :key="n.nibar">
                                                        <div @click="selectNibar(item, n)"
                                                             class="px-4 py-2.5 cursor-pointer transition-colors group flex items-center justify-between gap-3 hover:bg-amber-500/15">
                                                            <div class="space-y-0.5 min-w-0">
                                                                <div class="flex items-center space-x-2">
                                                                    <p class="font-mono font-bold text-xs text-white group-hover:text-amber-300 truncate" x-text="n.nibar"></p>
                                                                    <span class="text-[9px] px-2 py-0.5 rounded font-extrabold shrink-0 bg-emerald-500/15 border border-emerald-500/30 text-emerald-400">Tersedia</span>
                                                                </div>
                                                                <p class="text-[10px] text-slate-400 truncate" x-text="'Ruang: ' + n.ruang + ' • ' + n.kondisi"></p>
                                                            </div>
                                                            <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-amber-500/30 text-amber-300 text-[10px] font-bold shrink-0 shadow-sm">Pilih →</span>
                                                        </div>
                                                    </template>

                                                    <template x-if="getFilteredNibar(item, nibarSearch[item.id] || '').length === 0">
                                                        <div class="p-4 text-center text-xs text-slate-400">
                                                            <p class="text-amber-400 font-semibold">Tidak ada NIBAR yang cocok</p>
                                                            <p class="text-[10px] text-slate-500 mt-0.5">Semua NIBAR mungkin sudah dipilih</p>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Notice Box Khusus Akun Sub Admin Ruangan (NIBAR Dikelola Admin) -->
                                <template x-if="isSubAdmin">
                                    <div class="p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800 text-xs text-slate-400 flex items-start space-x-3 shadow-inner">
                                        <span class="text-teal-400 text-base shrink-0 mt-0.5">ℹ️</span>
                                        <div class="space-y-0.5">
                                            <p class="font-extrabold text-slate-200 text-xs flex items-center space-x-2">
                                                <span>Penomoran NIBAR 45 Karakter Dikelola Pengurus Barang</span>
                                                <span class="px-2 py-0.5 rounded bg-teal-500/20 text-teal-300 text-[10px] font-bold">Admin Only</span>
                                            </p>
                                            <p class="text-[11px] text-slate-400 leading-relaxed">
                                                Alokasi nomor register fisik NIBAR dan verifikasi kondisi unit akan diproses oleh <strong>Pengurus Barang / Admin</strong> saat permohonan disetujui. Cukup pilih nama barang dan volume (qty) yang diajukan.
                                            </p>
                                        </div>
                                    </div>
                                </template>

                            </div>

                        </div>
                    </template>
                </div>

                <!-- Ringkasan Akumulasi Volume Multi-Barang & Tombol Tambah Bawah -->
                <div class="mt-4 p-4 sm:p-5 bg-slate-950/90 rounded-2xl border border-slate-800 shadow-lg space-y-3">
                    
                    <!-- Baris Atas: Tombol Tambah Barang di Atas -->
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center space-x-1.5">
                            <span>📊</span>
                            <span>Ringkasan Rincian & Akumulasi Volume</span>
                        </span>

                        <template x-if="formData.status !== 'Ditolak'">
                            <button type="button" @click="addItem()" 
                                    class="px-4 py-2 rounded-xl bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/40 font-bold text-xs flex items-center space-x-2 transition-all active:scale-95 cursor-pointer shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Tambah Barang Lagi</span>
                            </button>
                        </template>
                    </div>

                    <!-- Baris Bawah: Grid 2 Kotak (Pengajuan & ACC Tetap Kanan-Kiri, Lebih Pendek & Proporsional) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        
                        <!-- Kotak 1: Ringkasan Pengajuan -->
                        <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-slate-900/90 border border-teal-500/30 shadow-sm hover:border-teal-500/50 transition-all">
                            <div class="flex items-center space-x-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-teal-500/15 border border-teal-500/30 flex items-center justify-center text-teal-300 text-xs shrink-0">
                                    📋
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-extrabold tracking-wider uppercase text-teal-400 block leading-tight">Pengajuan</span>
                                    <p class="text-xs font-semibold text-slate-300 truncate">
                                        <strong class="text-white font-extrabold" x-text="formData.items.length"></strong>
                                        <span class="text-slate-400 text-[11px] ml-1">Jenis Barang</span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right pl-3.5 border-l border-slate-800 shrink-0">
                                <span class="text-[9.5px] text-slate-400 font-semibold block leading-tight">Akumulasi Vol</span>
                                <span class="text-xs sm:text-sm font-mono font-black text-teal-300" x-text="getTotalItemVolume() + ' Unit'"></span>
                            </div>
                        </div>

                        <!-- Kotak 2: Ringkasan ACC (Ukuran & Layout Sama Persis) -->
                        <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-slate-900/90 border border-emerald-500/30 shadow-sm hover:border-emerald-500/50 transition-all">
                            <div class="flex items-center space-x-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-300 text-xs shrink-0">
                                    ✅
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-extrabold tracking-wider uppercase text-emerald-400 block leading-tight">ACC</span>
                                    <p class="text-xs font-semibold text-slate-300 truncate">
                                        <strong class="text-white font-extrabold" x-text="getTotalRincianAcc()"></strong>
                                        <span class="text-slate-400 text-[11px] ml-1">Jenis Barang</span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right pl-3.5 border-l border-slate-800 shrink-0">
                                <span class="text-[9.5px] text-slate-400 font-semibold block leading-tight">Akumulasi Vol</span>
                                <span class="text-xs sm:text-sm font-mono font-black text-emerald-400" x-text="getTotalItemVolumeAcc() + ' Unit'"></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- BAGIAN 3: CATATAN UMUM PENEMPATAN -->
            <div class="pt-4 border-t border-slate-800">
                <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center space-x-1">
                    <span>Catatan Umum / Keterangan Penempatan</span>
                    <span class="text-rose-400 font-bold" x-show="formData.status !== 'Ditolak'">*</span>
                </label>
                <textarea x-model="formData.keterangan" 
                          :disabled="formData.status === 'Ditolak'"
                          :readonly="formData.status === 'Ditolak'"
                          rows="2" 
                          placeholder="Contoh: Pengadaan DAK Kesehatan / BLUD untuk kelengkapan ruangan..." 
                          :class="formData.status === 'Ditolak' ? 'bg-slate-950/80 text-slate-400 cursor-not-allowed border-slate-800' : 'bg-slate-950 text-white border-slate-800 focus:border-teal-500'"
                          class="w-full border rounded-xl px-4 py-3 text-xs placeholder-slate-500 focus:outline-none"></textarea>
            </div>

            <!-- BAGIAN 4: ALASAN PENOLAKAN (Hanya muncul jika Status Ditolak) -->
            <div x-show="formData.status === 'Ditolak'" 
                 x-transition
                 class="pt-4 border-t border-rose-500/30 space-y-1.5">
                <label class="block text-rose-300 font-semibold text-xs flex items-center justify-between">
                    <span class="flex items-center space-x-1">
                        <span>Alasan Penolakan</span>
                        <span class="text-rose-400 font-bold">*</span>
                    </span>
                    <span class="text-[10px] text-rose-400 font-medium">Wajib diisi saat status Ditolak</span>
                </label>
                <textarea x-model="formData.alasan_penolakan" 
                          rows="2" 
                          placeholder="Tuliskan alasan penolakan pengajuan distribusi ini..." 
                          class="w-full bg-slate-950 border border-rose-500/40 rounded-xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500/40 transition-all"></textarea>
            </div>
