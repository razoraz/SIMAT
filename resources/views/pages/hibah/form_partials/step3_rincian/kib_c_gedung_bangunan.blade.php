<!-- ===================================================================== -->
<!-- KONDISI C: RINCIAN GEDUNG DAN BANGUNAN (KIB C) UNTUK HIBAH            -->
<!-- ===================================================================== -->
<template x-if="isGedung">
    <div class="space-y-6">

        <!-- ========================================================================= -->
        <!-- PEMBUNGKUS GEDUNG DAN BANGUNAN MULTI-ITEM (KIB C)                         -->
        <!-- ========================================================================= -->
        <div class="space-y-4">
            
            <!-- Header Pembungkus Gedung dan Bangunan -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-purple-950/30 border border-purple-500/40 shadow-md">
                <div class="space-y-0.5">
                    <div class="flex items-center space-x-2">
                        <span class="p-1.5 rounded-lg bg-purple-500/20 text-purple-400 text-sm">🏢</span>
                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                            RINCIAN GEDUNG DAN BANGUNAN (<span class="text-purple-400" x-text="formData.gedung_items.length"></span> Gedung / Bangunan Terdaftar)
                        </h3>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        Setiap gedung/bangunan memiliki Luas, Kondisi, Status Tanah, Volume, Komponen Nilai Taksiran Hibah, dan Alamat Lokasi Fisik masing-masing.
                    </p>
                </div>
                <button type="button" @click="addGedungItem()" 
                        class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-purple-500/20 shrink-0 cursor-pointer">
                    <span>➕ Tambah Gedung / Bangunan Baru</span>
                </button>
            </div>

            <!-- List Kartu Gedung & Bangunan (Repeater) -->
            <div class="space-y-5">
                <template x-for="(item, idx) in formData.gedung_items" :key="idx">
                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-purple-500/30 hover:border-purple-500/60 transition-all space-y-4 shadow-xl relative group">
                        
                        <!-- Header Kartu Tiap Gedung -->
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 rounded-xl bg-purple-500/20 text-purple-300 font-mono font-extrabold text-xs border border-purple-500/40 flex items-center space-x-1.5">
                                    <span>🏢 Gedung / Bangunan #<span x-text="idx + 1"></span></span>
                                </span>
                                <span class="text-[11px] text-slate-300 font-semibold" x-show="item.gedung_nama_barang">
                                    • <span x-text="item.gedung_nama_barang"></span>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Luas: <strong class="text-cyan-300" x-text="(item.gedung_luas_m2 || 0) + ' M²'"></strong>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Qty: <strong class="text-amber-300" x-text="(item.gedung_jumlah_bangunan || 1) + ' ' + (item.gedung_satuan || 'Gedung')"></strong>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getGedungSubtotal(item))"></strong>
                                </span>
                            </div>

                            <!-- Tombol Hapus Gedung (Muncul jika > 1 item) -->
                            <button type="button" 
                                    x-show="formData.gedung_items.length > 1" 
                                    @click="removeGedungItem(idx)" 
                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                <span>🗑️ Hapus Gedung Ini</span>
                            </button>
                        </div>

                        <!-- Grid Form Pengisian Spesifikasi Gedung dan Bangunan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Kondisi & Spesifikasi Bangunan (Luas, Kondisi, Bertingkat, Beton) -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>🏗️ Kondisi & Spesifikasi:</span>
                                    </span>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-slate-400 text-[10px] font-semibold">Nama Bangunan (PMDN 108)</label>
                                        <span class="text-[9px] text-amber-400/80 flex items-center gap-1 font-medium bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20">
                                            🔒 Terkunci (Mengikuti Langkah 2)
                                        </span>
                                    </div>
                                    <input type="text" :value="item.gedung_nama_barang || formData.nama_barang || formData.sub_rincian_nama || 'Bangunan Gedung'" readonly
                                           class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Luas (M2/Lt)</label>
                                        <input type="number" min="0" step="any" x-model.number="item.gedung_luas_m2" 
                                               @input="if (item.gedung_luas_m2 < 0) item.gedung_luas_m2 = 0; syncGedungFieldsToMain()"
                                               placeholder="850"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-amber-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi (B/KB/RB)</label>
                                        <select x-model="item.gedung_kondisi" @change="syncGedungFieldsToMain()" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-amber-500">
                                            <option value="B">B (Baik)</option>
                                            <option value="KB">KB (Kurang Baik)</option>
                                            <option value="RB">RB (Rusak Berat)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bertingkat / Tidak</label>
                                        <select x-model="item.gedung_bertingkat" @change="syncGedungFieldsToMain()" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                            <option value="Bertingkat">Bertingkat</option>
                                            <option value="Tidak">Tidak Bertingkat</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Beton / Tidak</label>
                                        <select x-model="item.gedung_beton" @change="syncGedungFieldsToMain()" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                            <option value="Beton">Beton</option>
                                            <option value="Tidak">Bukan Beton</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Jenis Bangunan & Status Tanah (KIB A) -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>📜 Status Tanah & Asal Aset:</span>
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Status Tanah</label>
                                        <input type="text" x-model="item.gedung_status_tanah" @input="syncGedungFieldsToMain()" placeholder="Hak Pakai RSUD"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-cyan-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kode Aset Tanah</label>
                                        <input type="text" x-model="item.gedung_kode_aset_tanah" @input="syncGedungFieldsToMain()" placeholder="1.3.1.01.01.02.013"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-emerald-400 font-mono focus:border-cyan-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Status Bangunan</label>
                                    <select x-model="item.gedung_is_baru" @change="syncGedungFieldsToMain()"
                                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500">
                                        <option value="Baru">Bangunan Baru</option>
                                        <option value="Lama">Bangunan Lama (Renovasi / Tambahan)</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <!-- Volume & Nilai Taksiran Bangunan Hibah (Rp) -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30 space-y-2.5">
                            <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider">💰 Volume & Rincian Nilai Bangunan (Rp):</span>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah Bangunan</label>
                                    <input type="number" min="1" x-model.number="item.gedung_jumlah_bangunan" 
                                           @input="if (item.gedung_jumlah_bangunan < 1) item.gedung_jumlah_bangunan = 1; syncGedungFieldsToMain()"
                                           placeholder="1"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Satuan Barang</label>
                                    <input type="text" x-model="item.gedung_satuan" @input="syncGedungFieldsToMain()" placeholder="Gedung / Unit / Paket"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-emerald-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Perencanaan (Rp)</label>
                                    <input type="text" 
                                           :value="item.gedung_nilai_perencanaan ? Number(item.gedung_nilai_perencanaan).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.gedung_nilai_perencanaan = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                               syncGedungFieldsToMain();
                                           "
                                           placeholder="0"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Fisik (Rp)</label>
                                    <input type="text" 
                                           :value="item.gedung_nilai_fisik ? Number(item.gedung_nilai_fisik).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.gedung_nilai_fisik = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                               syncGedungFieldsToMain();
                                           "
                                           placeholder="150.000.000"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Pengawasan (Rp)</label>
                                    <input type="text" 
                                           :value="item.gedung_nilai_pengawasan ? Number(item.gedung_nilai_pengawasan).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.gedung_nilai_pengawasan = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                               syncGedungFieldsToMain();
                                           "
                                           placeholder="0"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Biaya Lainnya (Rp)</label>
                                    <input type="text" 
                                           :value="item.gedung_nilai_ap ? Number(item.gedung_nilai_ap).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.gedung_nilai_ap = raw ? parseInt(raw, 10) : 0;
                                               item.gedung_nilai_pip = item.gedung_nilai_ap;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                               syncGedungFieldsToMain();
                                           "
                                           placeholder="0"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                </div>
                            </div>

                            <div :class="getGedungSubtotal(item) <= 0 ? 'border-amber-500/40 bg-amber-950/10' : 'border-slate-800 bg-slate-950/60'" 
                                 class="pt-2 p-2.5 rounded-xl border flex flex-col gap-1 transition-colors shadow-inner">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] text-slate-400 font-semibold uppercase">Subtotal Nilai Gedung Ini:</span>
                                    <span :class="getGedungSubtotal(item) <= 0 ? 'text-amber-400' : 'text-emerald-400'" 
                                          class="text-xs font-black font-mono" x-text="'Rp ' + formatRupiah(getGedungSubtotal(item))"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Letak / Alamat Lokasi Fisik Gedung -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/40 space-y-1.5">
                            <div class="flex items-center justify-between border-b border-emerald-500/30 pb-1.5">
                                <label class="block text-emerald-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📍 Letak / Alamat Lokasi Fisik Bangunan:</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Lokasi Fisik</span>
                            </div>
                            <input type="text" x-model="item.gedung_alamat" @input="syncGedungFieldsToMain()" placeholder="Contoh: Jl. Piere Tendean No. 3 Bondowoso (Kompleks RSUD Dr. H. Koesnandi - Blok Paviliun Melati)"
                                   class="w-full bg-slate-950 border border-slate-700 hover:border-emerald-500 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-500 transition-all">
                        </div>

                    </div>
                </template>
            </div>

            <!-- Tombol Tambah Gedung / Bangunan Baru -->
            <button type="button" @click="addGedungItem()" 
                    class="w-full py-3.5 border-2 border-dashed border-purple-500/50 hover:border-purple-400 bg-purple-950/20 hover:bg-purple-950/40 text-purple-300 hover:text-purple-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Gedung & Bangunan Lainnya</span>
            </button>

            <!-- Ringkasan Akumulasi KIB C -->
            <div class="p-4 rounded-2xl bg-slate-950/90 border border-purple-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                    <div>
                        <span class="text-[10px] text-purple-400 font-semibold block uppercase tracking-wider">🏢 Total Bangunan:</span>
                        <span class="text-sm font-black text-purple-300 font-mono" x-text="totalVolumeGedung + ' Bangunan'"></span>
                    </div>
                </div>
                <div class="text-left sm:text-right border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0">
                    <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">Total Taksiran Nilai Hibah:</span>
                    <span class="text-base font-extrabold text-emerald-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiGedung)"></span>
                </div>
            </div>

        </div>

    </div>
</template>
