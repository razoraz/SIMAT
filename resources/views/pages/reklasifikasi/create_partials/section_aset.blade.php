<!-- SEKSI 1: PEMILIHAN ASET BMD (ASTAP) -->
<div class="p-6 sm:p-7 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-5" x-data="{ searchAsetKeyword: '' }">
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
                    Pilih aset dari Buku Inventaris ASTAP RSUD yang akan direklasifikasi atau disesuaikan bukuannya.
                </p>
            </div>
        </div>
        <span class="text-[11px] font-semibold text-slate-400 bg-slate-950 px-3 py-1 rounded-full border border-slate-800 self-start sm:self-auto">
            Tersedia <strong class="text-white">{{ count($kandidatAstaps) }}</strong> Aset Kandidat
        </span>
    </div>

    <!-- Quick Search Bar & Selector -->
    <div class="space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <!-- Search Filter Bar -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Cari Nama / KIB:</span>
                </label>
                <div class="relative">
                    <input type="text" x-model="searchAsetKeyword" placeholder="Ketik kata kunci untuk menyaring opsi..."
                           class="w-full bg-slate-950 border border-slate-800 hover:border-cyan-500/50 focus:border-cyan-500 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-sm">
                    <button type="button" x-show="searchAsetKeyword" @click="searchAsetKeyword = ''"
                            class="absolute right-3 top-2.5 text-slate-500 hover:text-white text-xs font-bold transition-colors">&times;</button>
                </div>
            </div>

            <!-- Dropdown Aset -->
            <div class="md:col-span-2">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>Pilih Barang Dari Inventaris <span class="text-rose-400">*</span></span>
                    </span>
                    <span class="text-[10px] text-slate-500 lowercase font-normal">(wajib dipilih)</span>
                </label>
                <div class="relative">
                    <select x-model="formData.astap_id" @change="onSelectAstap($event.target.value)" required
                        style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                        class="w-full bg-slate-950 border border-slate-800 hover:border-indigo-500/50 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 pr-8 text-xs font-bold text-white focus:outline-none transition-all cursor-pointer shadow-sm">
                        <option value="">-- Pilih Barang dari Buku Inventaris ASTAP --</option>
                        @foreach ($kandidatAstaps as $kand)
                            @php
                                $kibLabel = $kand->category ?: ($kand->jenisAstap ? substr($kand->jenisAstap->sub_rincian_objek ?? '', 0, 5) : 'ASTAP');
                                $searchText = strtolower($kand->nama_barang . ' ' . $kibLabel . ' ' . ($kand->merk_type ?? ''));
                            @endphp
                            <option value="{{ $kand->id }}"
                                x-show="!searchAsetKeyword || '{{ addslashes($searchText) }}'.includes(searchAsetKeyword.toLowerCase().trim())"
                                data-nilai="{{ $kand->total_realisasi }}"
                                data-nama="{{ $kand->nama_barang }}"
                                data-kib="{{ $kibLabel }}"
                                data-prefix="{{ $kand->jenisAstap ? substr($kand->jenisAstap->sub_rincian_objek ?? '', 0, 8) : '' }}"
                                data-sub="{{ $kand->jenisAstap->uraian_sub_rincian ?? '' }}"
                                class="bg-slate-900 text-white py-1">
                                {{ $kand->nama_barang }} (Rp {{ number_format($kand->total_realisasi, 0, ',', '.') }}) [{{ $kibLabel }}] {{ $kand->is_reklas ? '• (Pernah Reklas)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rich Snapshot Card Aset Terpilih -->
        <template x-if="selectedAstap">
            <div class="mt-4 p-5 rounded-2xl bg-slate-950/90 border border-slate-800/90 space-y-4 shadow-inner"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0">
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800/80 pb-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 uppercase"
                                  x-text="selectedAstap.category || 'KIB'"></span>
                            <span class="text-xs font-mono text-slate-400"
                                  x-text="selectedAstap.jenis_astap?.sub_sub_rincian_objek || '-'"></span>
                            <template x-if="selectedAstap.is_reklas">
                                <span class="px-2 py-0.5 rounded text-[9.5px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    Pernah Direklasifikasi
                                </span>
                            </template>
                        </div>
                        <h3 class="text-base font-black text-white" x-text="selectedAstap.nama_barang"></h3>
                        <p class="text-xs text-slate-400" x-text="selectedAstap.jenis_astap?.uraian_sub_rincian || selectedAstap.jenis_astap?.uraian_sub_sub_rincian || '-'"></p>
                    </div>

                    <div class="bg-slate-900/90 border border-slate-800 px-4 py-2.5 rounded-xl text-left md:text-right shrink-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Nilai Realisasi Buku Saat Ini</span>
                        <div class="text-lg font-black font-mono text-white mt-0.5"
                             x-text="'Rp ' + Number(selectedAstap.total_realisasi || 0).toLocaleString('id-ID')"></div>
                        <div class="text-[11px] text-slate-400 mt-0.5"
                             x-text="(selectedAstap.jumlah_volume || 1) + ' ' + (selectedAstap.satuan || 'Unit')"></div>
                    </div>
                </div>

                <!-- Detail Grid Tambahan -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Tahun Perolehan</span>
                        <span class="font-bold text-white font-mono" x-text="selectedAstap.tahun_perolehan || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Merk / Tipe</span>
                        <span class="font-medium text-slate-300 truncate block" x-text="selectedAstap.merk_type || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Status Akuntansi</span>
                        <span class="font-bold" :class="selectedAstap.is_extracomtable ? 'text-amber-400' : 'text-emerald-400'"
                              x-text="selectedAstap.is_extracomtable ? 'Ekstrakomptabel' : 'Intrakomptabel (Aset Tetap)'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Lokasi / Alamat</span>
                        <span class="font-medium text-slate-300 truncate block" x-text="selectedAstap.alamat_barang || 'RSUD Dr. H. Koesnandi'"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

