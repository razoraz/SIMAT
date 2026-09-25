<!-- ===================================================================== -->
<!-- KONDISI F: RINCIAN KONSTRUKSI DALAM PENGERJAAN (KDP) UNTUK HIBAH     -->
<!-- ===================================================================== -->
<template x-if="isKDP">
    <div class="space-y-6">

        <!-- ============================================================= -->
        <!-- MULTI-ITEM REPEATER KHUSUS KIB F (KDP / KONSTRUKSI)             -->
        <!-- ============================================================= -->
        <div class="space-y-4">
            
            <!-- Header Pembungkus KDP -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-rose-950/30 border border-rose-500/40 shadow-md">
                <div class="space-y-0.5">
                    <div class="flex items-center space-x-2">
                        <span class="p-1.5 rounded-lg bg-rose-500/20 text-rose-400 text-sm">🏗️</span>
                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                            RINCIAN KONSTRUKSI DALAM PENGERJAAN (<span class="text-rose-400" x-text="formData.kdp_items.length"></span> Proyek KDP Terdaftar)
                        </h3>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        Setiap proyek konstruksi hibah memiliki Luas Rencana, Progres Fisik (%), Kondisi, Status Tanah, Volume, Komponen Nilai Taksiran Hibah, dan Alamat Lokasi Fisik masing-masing.
                    </p>
                </div>
                <button type="button" @click="addKdpItem()" 
                        class="px-4 py-2 rounded-xl bg-rose-500 hover:bg-rose-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-rose-500/20 shrink-0 cursor-pointer">
                    <span>➕ Tambah Proyek KDP Baru</span>
                </button>
            </div>

            <!-- List Kartu KDP (Repeater) -->
            <div class="space-y-5">
                <template x-for="(item, idx) in formData.kdp_items" :key="idx">
                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-rose-500/30 hover:border-rose-500/60 transition-all space-y-4 shadow-xl relative group">
                        
                        <!-- Header Kartu Tiap KDP -->
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 rounded-xl bg-rose-500/20 text-rose-300 font-mono font-extrabold text-xs border border-rose-500/40 flex items-center space-x-1.5">
                                    <span>🏗️ Proyek KDP #<span x-text="idx + 1"></span></span>
                                </span>
                                <span class="text-[11px] text-slate-300 font-semibold" x-show="item.kdp_nama_barang">
                                    • <span x-text="item.kdp_nama_barang"></span>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Luas: <strong class="text-cyan-300" x-text="(item.kdp_luas_m2 || 0) + ' M²'"></strong>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Progres: <strong class="text-rose-400 font-bold" x-text="(item.kdp_progres_persen || 0) + '%'"></strong>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Qty: <strong class="text-amber-300" x-text="(item.kdp_jumlah_bangunan || 1) + ' ' + (item.kdp_satuan || 'Gedung')"></strong>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getKdpSubtotal(item))"></strong>
                                </span>
                            </div>

                            <!-- Tombol Hapus KDP (Muncul jika > 1 item) -->
                            <button type="button" 
                                    x-show="formData.kdp_items.length > 1" 
                                    @click="removeKdpItem(idx)" 
                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                <span>🗑️ Hapus KDP Ini</span>
                            </button>
                        </div>

                        <!-- Grid Form Pengisian Spesifikasi KDP -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Kondisi & Spesifikasi Bangunan KDP -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>🏗️ Kondisi, Spesifikasi & Progres Fisik:</span>
                                    </span>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-slate-400 text-[10px] font-semibold">Nama Bangunan (PMDN 108)</label>
                                        <span class="text-[9px] text-amber-400/80 flex items-center gap-1 font-medium bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20">
                                            <span>🔒 Terkunci dari Langkah 2</span>
                                        </span>
                                    </div>
                                    <input type="text" :value="item.kdp_nama_barang || formData.kdp_nama_barang || formData.sub_rincian_nama || 'Konstruksi Dalam Pengerjaan'" readonly
                                           class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Luas Rencana (M2/Lt)</label>
                                        <input type="number" min="0" step="any" x-model.number="item.kdp_luas_m2" 
                                               @input="if (item.kdp_luas_m2 < 0) item.kdp_luas_m2 = 0;"
                                               placeholder="850"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-amber-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi (B/KB/RB)</label>
                                        <select x-model="item.kdp_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-amber-500">
                                            <option value="B">B (Baik)</option>
                                            <option value="KB">KB (Kurang Baik)</option>
                                            <option value="RB">RB (Rusak Berat)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bertingkat / Tidak</label>
                                        <select x-model="item.kdp_bertingkat" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                            <option value="Bertingkat">Bertingkat</option>
                                            <option value="Tidak">Tidak Bertingkat</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Beton / Tidak</label>
                                        <select x-model="item.kdp_beton" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                            <option value="Beton">Beton</option>
                                            <option value="Tidak">Bukan Beton</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Field Khusus KIB F: Progres Fisik (%) -->
                                <div class="p-3 rounded-xl bg-slate-950 border border-rose-500/30 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-rose-300 text-[10px] font-bold uppercase tracking-wider flex items-center space-x-1">
                                            <span>📊 Progres Fisik Pengerjaan (%):</span>
                                        </label>
                                        <span class="text-rose-400 font-mono font-black text-xs" x-text="(item.kdp_progres_persen || 0) + '%'"></span>
                                    </div>
                                    <div class="relative">
                                        <input type="number" min="0" max="100" x-model.number="item.kdp_progres_persen" 
                                               @input="if (item.kdp_progres_persen < 0) item.kdp_progres_persen = 0; if (item.kdp_progres_persen > 100) item.kdp_progres_persen = 100;"
                                               placeholder="65"
                                               class="w-full bg-slate-900 border border-rose-500/50 rounded-xl px-3 py-2 pr-7 text-xs text-rose-300 font-mono font-black focus:outline-none focus:border-rose-400">
                                        <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none;" class="text-rose-400 text-xs font-bold">%</span>
                                    </div>
                                    <div class="w-full bg-slate-900 rounded-full h-2 overflow-hidden border border-slate-800">
                                        <div class="bg-gradient-to-r from-rose-500 via-amber-400 to-emerald-400 h-2 rounded-full transition-all duration-300" :style="'width: ' + (item.kdp_progres_persen || 0) + '%'"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Jenis Bangunan & Status Tanah (KIB A) -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>📜 Status Tanah & Kapitalisasi:</span>
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Status Tanah</label>
                                        <input type="text" x-model="item.kdp_status_tanah" placeholder="Hak Pakai RSUD"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-cyan-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kode Aset Tanah</label>
                                        <input type="text" x-model="item.kdp_kode_aset_tanah" placeholder="1.3.1.01.01.02.013"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-emerald-400 font-mono focus:border-cyan-500">
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-1.5">
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Bangunan Baru</label>
                                        <select x-model="item.kdp_is_baru" 
                                                @change="if (item.kdp_is_baru === 'Baru') { item.kdp_kapitalisasi_tahun_induk = ''; item.kdp_kapitalisasi_nilai_induk = 0; }"
                                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-cyan-500">
                                            <option value="Baru">Baru</option>
                                            <option value="Lama">Lama</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Tahun Induk</label>
                                        <input type="text" x-model="item.kdp_kapitalisasi_tahun_induk" 
                                               :disabled="item.kdp_is_baru === 'Baru'"
                                               :class="item.kdp_is_baru === 'Baru' ? 'opacity-40 cursor-not-allowed bg-slate-900/60 border-slate-800 text-slate-500' : 'bg-slate-950 border-slate-700 focus:border-cyan-500 text-white'"
                                               placeholder="2020"
                                               class="w-full border rounded-xl px-2 py-2 text-xs font-mono transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Induk</label>
                                        <input type="text" 
                                               :disabled="item.kdp_is_baru === 'Baru'"
                                               :class="item.kdp_is_baru === 'Baru' ? 'opacity-40 cursor-not-allowed bg-slate-900/60 border-slate-800 text-slate-500' : 'bg-slate-950 border-slate-700 focus:border-cyan-500 text-amber-300'"
                                               :value="item.kdp_is_baru === 'Baru' ? '' : (item.kdp_kapitalisasi_nilai_induk ? Number(item.kdp_kapitalisasi_nilai_induk).toLocaleString('id-ID') : '')"
                                               @input="
                                                   let raw = $event.target.value.replace(/\D/g, '');
                                                   item.kdp_kapitalisasi_nilai_induk = raw ? parseInt(raw, 10) : 0;
                                                   $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                               "
                                               placeholder="3.500.000.000"
                                               class="w-full border rounded-xl px-1.5 py-2 text-[10px] font-mono transition-all">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Volume & Komponen Nilai Taksiran Hibah KDP (Rp) -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30 space-y-2.5">
                            <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider">💰 Volume & Komponen Nilai Taksiran Hibah KDP (Rp):</span>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah Bangunan</label>
                                    <input type="number" min="1" x-model.number="item.kdp_jumlah_bangunan" 
                                           @input="if (item.kdp_jumlah_bangunan < 1) item.kdp_jumlah_bangunan = 1;"
                                           placeholder="1"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Satuan Barang</label>
                                    <input type="text" x-model="item.kdp_satuan" placeholder="Gedung / Unit / Paket / M²"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-emerald-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Perencanaan (Rp)</label>
                                    <input type="text" 
                                           :value="item.kdp_nilai_perencanaan ? Number(item.kdp_nilai_perencanaan).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.kdp_nilai_perencanaan = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                           "
                                           placeholder="0"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Fisik (Rp)</label>
                                    <input type="text" 
                                           :value="item.kdp_nilai_fisik ? Number(item.kdp_nilai_fisik).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.kdp_nilai_fisik = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                           "
                                           placeholder="1.850.000.000"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Pengawasan (Rp)</label>
                                    <input type="text" 
                                           :value="item.kdp_nilai_pengawasan ? Number(item.kdp_nilai_pengawasan).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.kdp_nilai_pengawasan = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                           "
                                           placeholder="0"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai AP/Lainnya (Rp)</label>
                                    <input type="text" 
                                           :value="(item.kdp_nilai_ap || item.kdp_nilai_pip) ? Number(item.kdp_nilai_ap || item.kdp_nilai_pip).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.kdp_nilai_ap = raw ? parseInt(raw, 10) : 0;
                                               item.kdp_nilai_pip = item.kdp_nilai_ap;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                           "
                                           placeholder="0"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                </div>
                            </div>

                            <div :class="getKdpSubtotal(item) <= 0 ? 'border-amber-500/40 bg-amber-950/10' : 'border-slate-800 bg-slate-950/60'" 
                                 class="pt-2 p-2.5 rounded-xl border flex flex-col gap-1 transition-colors shadow-inner">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] text-slate-400 font-semibold uppercase">Subtotal Nilai KDP Ini:</span>
                                    <span :class="getKdpSubtotal(item) <= 0 ? 'text-amber-400' : 'text-emerald-400'" 
                                          class="text-xs font-black font-mono" x-text="'Rp ' + formatRupiah(getKdpSubtotal(item))"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Letak / Alamat Lokasi Fisik Proyek KDP -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-rose-500/40 space-y-1.5">
                            <div class="flex items-center justify-between border-b border-rose-500/30 pb-1.5">
                                <label class="block text-rose-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📍 Letak / Alamat Lokasi Fisik Proyek KDP:</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold">Lokasi Fisik KDP</span>
                            </div>
                            <input type="text" x-model="item.kdp_alamat" placeholder="Contoh: Kompleks Paviliun Melati & Gedung Rawat Inap Baru RSUD Dr. H. Koesnandi"
                                   class="w-full bg-slate-950 border border-slate-700 hover:border-rose-500 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-rose-500 transition-all">
                        </div>

                    </div>
                </template>
            </div>

            <!-- Tombol Tambah Proyek KDP Baru -->
            <button type="button" @click="addKdpItem()" 
                    class="w-full py-3.5 border-2 border-dashed border-rose-500/50 hover:border-rose-400 bg-rose-950/20 hover:bg-rose-950/40 text-rose-300 hover:text-rose-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Proyek KDP Lainnya</span>
            </button>

            <!-- Ringkasan Akumulasi KIB F Hibah -->
            <div class="p-4 rounded-2xl bg-slate-950/90 border border-rose-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                    <div>
                        <span class="text-[10px] text-rose-400 font-semibold block uppercase tracking-wider">🏗️ Total Proyek KDP:</span>
                        <span class="text-sm font-black text-rose-300 font-mono" x-text="formData.kdp_items.length + ' Proyek Konstruksi'"></span>
                    </div>
                </div>
                <div class="text-left sm:text-right border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0">
                    <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">Total Taksiran Nilai Hibah:</span>
                    <span class="text-base font-extrabold text-emerald-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiKdp)"></span>
                </div>
            </div>

        </div>

    </div>
</template>
