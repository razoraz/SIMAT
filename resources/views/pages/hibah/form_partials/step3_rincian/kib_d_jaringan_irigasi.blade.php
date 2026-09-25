<!-- ===================================================================== -->
<!-- KONDISI D: RINCIAN JALAN, IRIGASI & JARINGAN (KIB D) UNTUK HIBAH      -->
<!-- ===================================================================== -->
<template x-if="isJaringan">
    <div class="space-y-6">

        <!-- ========================================================================= -->
        <!-- PEMBUNGKUS JALAN, IRIGASI DAN JARINGAN MULTI-ITEM (KIB D)                 -->
        <!-- ========================================================================= -->
        <div class="space-y-4">
            
            <!-- Header Pembungkus Jalan, Irigasi & Jaringan -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-teal-950/30 border border-teal-500/40 shadow-md">
                <div class="space-y-0.5">
                    <div class="flex items-center space-x-2">
                        <span class="p-1.5 rounded-lg bg-teal-500/20 text-teal-400 text-sm">🛣️</span>
                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                            RINCIAN JALAN, IRIGASI DAN JARINGAN (<span class="text-teal-400" x-text="formData.jaringan_items.length"></span> Jaringan / Ruas Terdaftar)
                        </h3>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        Setiap jaringan/ruas memiliki Konstruksi, Dimensi (Panjang, Lebar, Luas P × L), Kondisi, Komponen Nilai Taksiran Hibah, dan Alamat Lokasi Fisik masing-masing.
                    </p>
                </div>
                <button type="button" @click="addJaringanItem()" 
                        class="px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-teal-500/20 shrink-0 cursor-pointer">
                    <span>➕ Tambah Jaringan / Ruas Baru</span>
                </button>
            </div>

            <!-- List Kartu Jaringan & Ruas (Repeater) -->
            <div class="space-y-5">
                <template x-for="(item, idx) in formData.jaringan_items" :key="idx">
                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-teal-500/30 hover:border-teal-500/60 transition-all space-y-4 shadow-xl relative group">
                        
                        <!-- Header Kartu Tiap Jaringan / Ruas -->
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 rounded-xl bg-teal-500/20 text-teal-300 font-mono font-extrabold text-xs border border-teal-500/40 flex items-center space-x-1.5">
                                    <span>🛣️ Jaringan / Ruas #<span x-text="idx + 1"></span></span>
                                </span>
                                <span class="text-[11px] text-slate-300 font-semibold" x-show="item.jaringan_nama_barang">
                                    • <span x-text="item.jaringan_nama_barang"></span>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Dimensi: <strong class="text-cyan-300" x-text="(item.jaringan_panjang_m || 0) + ' M × ' + (item.jaringan_lebar_m || 0) + ' M (' + (item.jaringan_luas_m2 || 0) + ' M²)'"></strong>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Qty: <strong class="text-amber-300" x-text="(item.jaringan_jumlah || 1) + ' ' + (item.jaringan_satuan || 'Paket')"></strong>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getJaringanSubtotal(item))"></strong>
                                </span>
                            </div>

                            <!-- Tombol Hapus Jaringan (Muncul jika > 1 item) -->
                            <button type="button" 
                                    x-show="formData.jaringan_items.length > 1" 
                                    @click="removeJaringanItem(idx)" 
                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                <span>🗑️ Hapus Jaringan Ini</span>
                            </button>
                        </div>

                        <!-- Grid Form Pengisian Spesifikasi Jalan & Jaringan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Kondisi & Spesifikasi Jaringan (Panjang, Lebar, Luas, Kondisi, dll.) -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>🏗️ Kondisi & Spesifikasi:</span>
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Jaringan / Ruas</label>
                                    <input type="text" x-model="item.jaringan_nama_barang" @input="syncJaringanFieldsToMain()" placeholder="Contoh: Jaringan Pipa Oksigen Sentral / Jalan Lingkungan RSUD"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Panjang (M)</label>
                                        <input type="number" min="0" step="any" x-model.number="item.jaringan_panjang_m" 
                                               @input="
                                                   if (item.jaringan_panjang_m < 0) item.jaringan_panjang_m = 0;
                                                   if (item.jaringan_panjang_m && item.jaringan_lebar_m) { 
                                                       item.jaringan_luas_m2 = parseFloat(((parseFloat(item.jaringan_panjang_m) || 0) * (parseFloat(item.jaringan_lebar_m) || 0)).toFixed(2)); 
                                                   }
                                                   syncJaringanFieldsToMain();
                                               "
                                               placeholder="100"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono font-bold focus:border-amber-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Lebar (M)</label>
                                        <input type="number" min="0" step="any" x-model.number="item.jaringan_lebar_m" 
                                               @input="
                                                   if (item.jaringan_lebar_m < 0) item.jaringan_lebar_m = 0;
                                                   if (item.jaringan_panjang_m && item.jaringan_lebar_m) { 
                                                       item.jaringan_luas_m2 = parseFloat(((parseFloat(item.jaringan_panjang_m) || 0) * (parseFloat(item.jaringan_lebar_m) || 0)).toFixed(2)); 
                                                   }
                                                   syncJaringanFieldsToMain();
                                               "
                                               placeholder="4"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-amber-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Luas (M²)</label>
                                        <input type="number" min="0" step="any" x-model.number="item.jaringan_luas_m2" 
                                               @input="if (item.jaringan_luas_m2 < 0) item.jaringan_luas_m2 = 0; syncJaringanFieldsToMain()"
                                               placeholder="400"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-cyan-300 font-mono font-bold focus:border-amber-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi</label>
                                        <select x-model="item.jaringan_kondisi" @change="syncJaringanFieldsToMain()" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-bold focus:border-amber-500">
                                            <option value="B">B (Baik)</option>
                                            <option value="KB">KB (Kurang Baik)</option>
                                            <option value="RB">RB (Rusak Berat)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Konstruksi Bertingkat</label>
                                        <select x-model="item.jaringan_bertingkat" @change="syncJaringanFieldsToMain()" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                            <option value="Tidak">Tidak Bertingkat</option>
                                            <option value="Bertingkat">Bertingkat</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Konstruksi Beton</label>
                                        <select x-model="item.jaringan_beton" @change="syncJaringanFieldsToMain()" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                            <option value="Beton">Beton</option>
                                            <option value="Tidak">Bukan Beton</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Tanah & Asal Jaringan -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>📜 Status Tanah & Asal Aset:</span>
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Status Tanah</label>
                                        <input type="text" x-model="item.jaringan_status_tanah" @input="syncJaringanFieldsToMain()" placeholder="Hak Pakai RSUD"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-cyan-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kode Aset Tanah</label>
                                        <input type="text" x-model="item.jaringan_kode_aset_tanah" @input="syncJaringanFieldsToMain()" placeholder="1.3.1.01.01.02.013"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-emerald-400 font-mono focus:border-cyan-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Status Jaringan</label>
                                    <select x-model="item.jaringan_is_baru" @change="syncJaringanFieldsToMain()"
                                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500">
                                        <option value="Baru">Jaringan / Ruas Baru</option>
                                        <option value="Lama">Jaringan Lama (Peningkatan / Renovasi)</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <!-- Volume & Nilai Taksiran Jaringan Hibah (Rp) -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-teal-500/30 space-y-2.5">
                            <span class="text-xs font-bold text-teal-400 block uppercase tracking-wider">💰 Volume & Rincian Nilai Jaringan (Rp):</span>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah Jaringan / Ruas</label>
                                    <input type="number" min="1" x-model.number="item.jaringan_jumlah" 
                                           @input="if (item.jaringan_jumlah < 1) item.jaringan_jumlah = 1; syncJaringanFieldsToMain()"
                                           placeholder="1"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Satuan Barang</label>
                                    <input type="text" x-model="item.jaringan_satuan" @input="syncJaringanFieldsToMain()" placeholder="Paket / Ruas / Meter"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-teal-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Perencanaan (Rp)</label>
                                    <input type="text" 
                                           :value="item.jaringan_nilai_perencanaan ? Number(item.jaringan_nilai_perencanaan).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.jaringan_nilai_perencanaan = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                               syncJaringanFieldsToMain();
                                           "
                                           placeholder="0"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Fisik (Rp)</label>
                                    <input type="text" 
                                           :value="item.jaringan_nilai_fisik ? Number(item.jaringan_nilai_fisik).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.jaringan_nilai_fisik = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                               syncJaringanFieldsToMain();
                                           "
                                           placeholder="50.000.000"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Pengawasan (Rp)</label>
                                    <input type="text" 
                                           :value="item.jaringan_nilai_pengawasan ? Number(item.jaringan_nilai_pengawasan).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.jaringan_nilai_pengawasan = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                               syncJaringanFieldsToMain();
                                           "
                                           placeholder="0"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Biaya Lainnya (Rp)</label>
                                    <input type="text" 
                                           :value="item.jaringan_nilai_ap ? Number(item.jaringan_nilai_ap).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.jaringan_nilai_ap = raw ? parseInt(raw, 10) : 0;
                                               item.jaringan_nilai_pip = item.jaringan_nilai_ap;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                               syncJaringanFieldsToMain();
                                           "
                                           placeholder="0"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-teal-500">
                                </div>
                            </div>

                            <div :class="getJaringanSubtotal(item) <= 0 ? 'border-amber-500/40 bg-amber-950/10' : 'border-slate-800 bg-slate-950/60'" 
                                 class="pt-2 p-2.5 rounded-xl border flex flex-col gap-1 transition-colors shadow-inner">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] text-slate-400 font-semibold uppercase">Subtotal Nilai Jaringan Ini:</span>
                                    <span :class="getJaringanSubtotal(item) <= 0 ? 'text-amber-400' : 'text-teal-400'" 
                                          class="text-xs font-black font-mono" x-text="'Rp ' + formatRupiah(getJaringanSubtotal(item))"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Letak / Alamat Lokasi Fisik Jaringan -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-teal-500/40 space-y-1.5">
                            <div class="flex items-center justify-between border-b border-teal-500/30 pb-1.5">
                                <label class="block text-teal-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📍 Letak / Alamat Lokasi Fisik Jaringan:</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 font-bold">Lokasi Fisik Jaringan</span>
                            </div>
                            <input type="text" x-model="item.jaringan_alamat" @input="syncJaringanFieldsToMain()" placeholder="Contoh: Jalur Utilitas Gedung Bedah Sentral & Paviliun Teratai RSUD Dr. H. Koesnandi"
                                   class="w-full bg-slate-950 border border-slate-700 hover:border-teal-500 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-teal-500 transition-all">
                        </div>

                    </div>
                </template>
            </div>

            <!-- Tombol Tambah Jaringan / Ruas Baru -->
            <button type="button" @click="addJaringanItem()" 
                    class="w-full py-3.5 border-2 border-dashed border-teal-500/50 hover:border-teal-400 bg-teal-950/20 hover:bg-teal-950/40 text-teal-300 hover:text-teal-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Jaringan / Ruas Lainnya</span>
            </button>

            <!-- Ringkasan Akumulasi KIB D -->
            <div class="p-4 rounded-2xl bg-slate-950/90 border border-teal-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                    <div>
                        <span class="text-[10px] text-teal-400 font-semibold block uppercase tracking-wider">🛣️ Total Volume Jaringan:</span>
                        <span class="text-sm font-black text-teal-300 font-mono" x-text="totalVolumeJaringan + ' Paket / Ruas'"></span>
                    </div>
                </div>
                <div class="text-left sm:text-right border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0">
                    <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">Total Taksiran Nilai Hibah:</span>
                    <span class="text-base font-extrabold text-emerald-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiJaringan)"></span>
                </div>
            </div>

        </div>

    </div>
</template>
