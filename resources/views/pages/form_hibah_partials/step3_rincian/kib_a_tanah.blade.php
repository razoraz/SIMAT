<!-- ===================================================================== -->
<!-- KONDISI A: RINCIAN BIDANG TANAH (KIB A) UNTUK HIBAH                   -->
<!-- ===================================================================== -->
<template x-if="isTanah">
    <div class="space-y-6">

        <!-- ========================================================================= -->
        <!-- PEMBUNGKUS BIDANG TANAH MULTI-ITEM (BISA TAMBAH BIDANG TANAH JAMAK)       -->
        <!-- ========================================================================= -->
        <div class="space-y-4">
            
            <!-- Header Pembungkus Bidang Tanah -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-emerald-950/30 border border-emerald-500/40 shadow-md">
                <div class="space-y-0.5">
                    <div class="flex items-center space-x-2">
                        <span class="p-1.5 rounded-lg bg-emerald-500/20 text-emerald-400 text-sm">🌾</span>
                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                            RINCIAN BIDANG TANAH (<span class="text-emerald-400" x-text="formData.tanah_items.length"></span> Bidang Terdaftar)
                        </h3>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        Setiap bidang tanah memiliki rincian sertifikat, luas, kondisi, nilai perolehan hibah, dan alamat lokasi fisik masing-masing.
                    </p>
                </div>
                <button type="button" @click="addTanahItem()" 
                        class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-emerald-500/20 shrink-0 cursor-pointer">
                    <span>➕ Tambah Bidang Tanah</span>
                </button>
            </div>

            <!-- List Kartu Bidang Tanah (Repeater) -->
            <div class="space-y-5">
                <template x-for="(item, idx) in formData.tanah_items" :key="idx">
                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-emerald-500/30 hover:border-emerald-500/60 transition-all space-y-4 shadow-xl relative group">
                        
                        <!-- Header Kartu Tiap Bidang Tanah -->
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 font-mono font-extrabold text-xs border border-emerald-500/40 flex items-center space-x-1.5">
                                    <span>🌾 Bidang Tanah #<span x-text="idx + 1"></span></span>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Luas: <strong class="text-cyan-300" x-text="(item.tanah_luas_m2 || 0).toLocaleString('id-ID') + ' m²'"></strong>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getTanahSubtotal(item))"></strong>
                                </span>
                            </div>

                            <!-- Tombol Hapus Bidang (Muncul jika > 1 item) -->
                            <button type="button" 
                                    x-show="formData.tanah_items.length > 1" 
                                    @click="removeTanahItem(idx)" 
                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                <span>🗑️ Hapus Bidang Ini</span>
                            </button>
                        </div>

                        <!-- Grid Status Sertifikat & Kondisi/Luas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Status Tanah & Sertifikat -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>📜 Status Tanah & Sertifikat:</span>
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1 font-semibold">Hak Tanah</label>
                                    <select x-model="item.tanah_hak" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                        <option value="Hak Pakai">Hak Pakai</option>
                                        <option value="Hak Pengelolaan">Hak Pengelolaan</option>
                                        <option value="Hak Milik">Hak Milik</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Sertifikat Nomor</label>
                                        <input type="text" x-model="item.tanah_sertifikat_no" placeholder="HP-108/1984"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Sertifikat Tanggal</label>
                                        <input type="date" x-model="item.tanah_sertifikat_tgl"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-amber-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Kondisi, Penggunaan & Volume -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>📐 Kondisi, Penggunaan & Volume:</span>
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Kondisi (B/KB/RB)</label>
                                        <select x-model="item.tanah_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-cyan-500">
                                            <option value="Baik">Baik (B)</option>
                                            <option value="Kurang Baik">Kurang Baik (KB)</option>
                                            <option value="Rusak Berat">Rusak Berat (RB)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Jumlah Bidang</label>
                                        <input type="number" min="1" x-model.number="item.tanah_jumlah_bidang" 
                                               @input="if (item.tanah_jumlah_bidang < 1) item.tanah_jumlah_bidang = 1; syncTanahFieldsToMain()"
                                               placeholder="1"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-cyan-500">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Luas Tanah (m²)</label>
                                        <input type="number" min="0" step="any" x-model.number="item.tanah_luas_m2" 
                                               @input="if (item.tanah_luas_m2 < 0) item.tanah_luas_m2 = 0; syncTanahFieldsToMain()"
                                               placeholder="35400"
                                               class="w-full bg-slate-950 border border-cyan-500/40 rounded-xl px-2.5 py-2 text-xs text-cyan-300 font-mono font-bold focus:border-cyan-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Penggunaan Lahan</label>
                                        <input type="text" x-model="item.tanah_penggunaan" placeholder="Bangunan Rumah Sakit & Fasilitas"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-cyan-500">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Nilai Barang Hibah (Rp) -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>💰 Nilai Barang (Rp):</span>
                                </span>
                                <span class="text-[10px] text-slate-400">Rincian Komponen Nilai Taksiran Tanah</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Nilai Perencanaan (Rp)</label>
                                    <input type="number" x-model.number="item.tanah_nilai_perencanaan" placeholder="0"
                                           @input="syncTanahFieldsToMain()"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Nilai Fisik (Rp)</label>
                                    <input type="number" x-model.number="item.tanah_nilai_fisik" placeholder="0"
                                           @input="syncTanahFieldsToMain()"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Nilai Pengawasan (Rp)</label>
                                    <input type="number" x-model.number="item.tanah_nilai_pengawasan" placeholder="0"
                                           @input="syncTanahFieldsToMain()"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                </div>
                            </div>
                            <!-- Subtotal Kartu Bidang Tanah Ini -->
                            <div :class="getTanahSubtotal(item) <= 0 ? 'border-amber-500/40 bg-amber-950/10' : 'border-emerald-500/30 bg-slate-950'" 
                                 class="p-2.5 rounded-xl border flex flex-col gap-1 text-xs transition-colors shadow-inner">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400 font-medium text-[11px]">Subtotal Nilai Bidang Tanah #<span x-text="idx + 1"></span>:</span>
                                    <span :class="getTanahSubtotal(item) <= 0 ? 'text-amber-400' : 'text-emerald-400'" 
                                          class="font-extrabold font-mono text-sm" x-text="'Rp ' + formatRupiah(getTanahSubtotal(item))"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Letak / Alamat Barang -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/30 space-y-2">
                            <div class="flex items-center justify-between border-b border-amber-500/20 pb-1.5">
                                <label class="block text-amber-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📍 Letak / Alamat Tanah & Lokasi Fisik:</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Lokasi Fisik Bidang #<span x-text="idx + 1"></span></span>
                            </div>
                            <input type="text" x-model="item.tanah_alamat" @input="syncTanahFieldsToMain()"
                                   placeholder="Contoh: Jl. Piere Tendean No. 3, Kel. Badean, Kec. Bondowoso (Area Paviliun RSUD Dr. H. Koesnandi)"
                                   class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 rounded-xl px-3.5 py-2.5 text-xs text-white font-medium focus:outline-none focus:border-amber-500 transition-all">
                        </div>

                    </div>
                </template>
            </div>

            <!-- Tombol Tambah Bidang Tanah Baru (Besar & Jelas) -->
            <button type="button" @click="addTanahItem()" 
                    class="w-full py-3.5 border-2 border-dashed border-emerald-500/50 hover:border-emerald-400 bg-emerald-950/20 hover:bg-emerald-950/40 text-emerald-300 hover:text-emerald-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Bidang Tanah Lainnya</span>
            </button>

            <!-- Ringkasan Akumulasi Keseluruhan Tanah Hibah -->
            <div class="p-4 rounded-2xl bg-slate-950/90 border border-emerald-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                    <div>
                        <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">🌾 Total Bidang Tanah:</span>
                        <span class="text-sm font-black text-white font-mono" x-text="formData.tanah_items.length + ' Bidang'"></span>
                    </div>
                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                    <div>
                        <span class="text-[10px] text-cyan-400 font-semibold block uppercase tracking-wider">📐 Total Luas Tanah:</span>
                        <span class="text-sm font-black text-cyan-300 font-mono" x-text="totalLuasTanah.toLocaleString('id-ID') + ' m²'"></span>
                    </div>
                </div>
                <div class="text-left sm:text-right border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0">
                    <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">Total Taksiran Nilai Hibah Tanah:</span>
                    <span class="text-base font-extrabold text-emerald-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiTanah)"></span>
                </div>
            </div>

        </div>

    </div>
</template>
