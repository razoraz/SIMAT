<!-- ========================================================================= -->
<!-- LANGKAH 3: ALOKASI PENEMPATAN FLEKSIBEL (OPSI A) & SPESIFIKASI KIB         -->
<!-- ========================================================================= -->
<div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div class="border-b border-slate-800 pb-4">
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-300 border border-purple-500/20 text-xs font-bold mb-2">
            <span>Langkah 3 dari 3</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span>🏢 Alokasi Penempatan Inventaris &amp; Spesifikasi Teknis</span>
        </h2>
        <p class="text-xs text-slate-400 mt-0.5">
            Tentukan alur penempatan fisik barang (transit stok di Gudang Perbekalan atau langsung ke KIR Ruangan) serta lengkapi spesifikasi teknis KIB.
        </p>
    </div>

    <!-- ===================================================================== -->
    <!-- ALOKASI PENEMPATAN OPSI A (FLEKSIBEL)                                  -->
    <!-- ===================================================================== -->
    <div class="p-5 rounded-2xl bg-slate-950/80 border border-indigo-500/30 space-y-4 shadow-xl">
        <div class="flex items-center justify-between">
            <span class="text-xs font-extrabold text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>📍 ALUR PENEMPATAN AWAL BARANG BELANJA (OPSI A)</span>
                <span class="text-rose-400">*</span>
            </span>
            <span class="text-[10px] font-bold text-slate-400 bg-slate-900 px-2.5 py-0.5 rounded-full border border-slate-800">
                Pilih Jalur Distribusi Fisik
            </span>
        </div>

        <!-- Pilihan Mode Alokasi (Radio Cards) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- Card 1: Simpan di Gudang Perbekalan (Transit Stok) -->
            <div @click="setGlobalAlokasiMode('gudang')"
                class="p-4 rounded-2xl border cursor-pointer transition-all relative flex flex-col justify-between"
                :class="globalAlokasiMode === 'gudang' ? 'bg-indigo-600/15 border-indigo-500 shadow-lg shadow-indigo-500/10 ring-1 ring-indigo-500' : 'bg-slate-900/80 border-slate-800 hover:border-slate-700'">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">📦</span>
                        <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                            :class="globalAlokasiMode === 'gudang' ? 'border-indigo-400 bg-indigo-500' : 'border-slate-700'">
                            <div x-show="globalAlokasiMode === 'gudang'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                        </div>
                    </div>
                    <span class="text-xs font-extrabold text-white block">Simpan di Gudang Perbekalan</span>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Barang dicatat sebagai buffer stok di <strong class="text-indigo-300">Gudang Perbekalan</strong>. Register NIBAR berstatus <strong class="text-emerald-400">Tersedia</strong> dan siap didistribusikan bertahap ke ruangan menggunakan dokumen BAST Distribusi.
                    </p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex items-center justify-between text-[10px]">
                    <span class="text-slate-400">Status Register:</span>
                    <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold">Tersedia di Gudang</span>
                </div>
            </div>

            <!-- Card 2: Langsung Ditempatkan ke Ruangan Perawatan / Poli (KIR Langsung Aktif) -->
            <div @click="setGlobalAlokasiMode('ruangan')"
                class="p-4 rounded-2xl border cursor-pointer transition-all relative flex flex-col justify-between"
                :class="globalAlokasiMode === 'ruangan' ? 'bg-indigo-600/15 border-indigo-500 shadow-lg shadow-indigo-500/10 ring-1 ring-indigo-500' : 'bg-slate-900/80 border-slate-800 hover:border-slate-700'">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">🏢</span>
                        <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                            :class="globalAlokasiMode === 'ruangan' ? 'border-indigo-400 bg-indigo-500' : 'border-slate-700'">
                            <div x-show="globalAlokasiMode === 'ruangan'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                        </div>
                    </div>
                    <span class="text-xs font-extrabold text-white block">Langsung ke Ruangan Perawatan / Poli</span>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Untuk barang yang dibeli khusus unit tertentu (contoh: ICU, IGD, Poli Gigi). Register langsung berstatus <strong class="text-indigo-300">Tidak Tersedia</strong> dan <strong class="text-indigo-300">langsung aktif muncul di Kartu Inventaris Ruangan (KIR)</strong>.
                    </p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex items-center justify-between text-[10px]">
                    <span class="text-slate-400">Integrasi KIR:</span>
                    <span class="px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-bold">Langsung Masuk KIR</span>
                </div>
            </div>

        </div>

        <!-- Form Alokasi jika Mode = Ruangan (Global) -->
        <template x-if="globalAlokasiMode === 'ruangan'">
            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 space-y-4">
                <span class="text-xs font-bold text-indigo-300 flex items-center gap-1.5">
                    <span>🏢 Tentukan Unit / Ruangan Tujuan Seluruh Item</span>
                    <span class="text-rose-400">*</span>
                </span>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Pilih Unit / Ruangan / Poli
                        </label>
                        <select x-model.number="globalUnitId" @change="applyGlobalUnit()"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none">
                            <option value="">-- Pilih Unit / Ruangan Pemegang Aset --</option>
                            @foreach($dbUnits ?? [] as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->nama }} ({{ $unit->kode ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Kondisi Fisik Barang
                        </label>
                        <select x-model="globalKondisi" @change="applyGlobalKondisi()"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none">
                            <option value="Baik">Baik (Bagus / Baru)</option>
                            <option value="Kurang Baik">Kurang Baik (Dapat Berfungsi)</option>
                            <option value="Rusak Berat">Rusak Berat (Tidak Berfungsi)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-300 mb-1">
                        Detail Lokasi Fisik Spesifik
                    </label>
                    <input type="text" x-model="globalAlamatBarang" @input="applyGlobalAlamat()"
                        placeholder="Contoh: Gedung Rawat Inap Melati Lt. 2, Ruang Isolasi 204..."
                        class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none">
                </div>
            </div>
        </template>
    </div>

    <!-- ===================================================================== -->
    <!-- SPESIFIKASI TEKNIS KIB PER ITEM                                      -->
    <!-- ===================================================================== -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <span class="text-xs font-extrabold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                <span>📋 Rincian Spesifikasi KIB Per Item Barang</span>
            </span>
            <span class="text-[10px] text-slate-500">Opsional / Melengkapi Buku Inventaris</span>
        </div>

        <template x-for="(item, index) in formData.items" :key="'spec-' + (item._id || index)">
            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-indigo-500/20 text-indigo-400 text-[11px] font-mono font-bold flex items-center justify-center" x-text="index + 1"></span>
                        <span class="text-xs font-bold text-white" x-text="item.nama_barang || 'Barang #' + (index + 1)"></span>
                        <span class="text-[10px] text-slate-400" x-text="'(' + item.jumlah_volume + ' ' + item.satuan + ')'"></span>
                    </div>

                    <!-- Badge Alokasi Item Ini -->
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded"
                        :class="item.mode_penempatan === 'ruangan' ? 'bg-indigo-500/10 text-indigo-300 border border-indigo-500/20' : 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20'"
                        x-text="item.mode_penempatan === 'ruangan' ? '🏢 KIR Ruangan' : '📦 Gudang Perbekalan'"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-400 mb-1">Merk / Brand</label>
                        <input type="text" x-model="item.spesifikasi_json.merk" placeholder="Contoh: Omron, GE, Sharp..."
                            class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-1.5 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-400 mb-1">Tipe / Model</label>
                        <input type="text" x-model="item.spesifikasi_json.type" placeholder="Contoh: HEM-7120, FS868..."
                            class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-1.5 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-400 mb-1">Bahan / Material</label>
                        <input type="text" x-model="item.spesifikasi_json.bahan" placeholder="Contoh: Plastik ABS, Stainless..."
                            class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-1.5 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-400 mb-1">Nomor Pabrik / Seri (SN)</label>
                        <input type="text" x-model="item.spesifikasi_json.no_pabrik" placeholder="Contoh: SN-2026-X88..."
                            class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-1.5 text-white text-xs font-mono">
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- ===================================================================== -->
    <!-- RINGKASAN VERIFIKASI SEBELUM SUBMIT                                   -->
    <!-- ===================================================================== -->
    <div class="p-5 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
        <div class="space-y-1">
            <span class="text-indigo-400 font-extrabold flex items-center gap-1.5 text-sm">
                <span>✨ Siap Menerbitkan NIBAR &amp; QR Code Ekstrakomptabel</span>
            </span>
            <p class="text-slate-300 text-xs">
                Sistem akan membuat <strong class="text-white font-mono" x-text="totalVolumeFaktur"></strong> nomor register unik NIBAR 28 digit untuk seluruh unit fisik barang di faktur ini.
            </p>
        </div>

        <div class="text-right shrink-0">
            <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Faktur Toko</span>
            <span class="text-lg font-black font-mono text-emerald-400" x-text="formatRupiah(totalNilaiFaktur)"></span>
        </div>
    </div>

</div>
