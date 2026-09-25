                        <!-- ========================================================================= -->
                        <!-- PEMBUNGKUS BARANG EKSTRAKOMTABEL UNIVERSAL (SEMUA KIB)                   -->
                        <!-- ========================================================================= -->
                        <div class="space-y-4">
                            
                            <!-- Header Pembungkus Ekstrakomtabel -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-cyan-950/30 border border-cyan-500/40 shadow-md">
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="p-1.5 rounded-lg bg-cyan-500/20 text-cyan-400 text-sm">📦</span>
                                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                                            <span>RINCIAN BARANG EKSTRAKOMTABEL</span>
                                            (<span class="text-cyan-400" x-text="formData.mesin_items.length"></span> Barang / Unit Terdaftar)
                                        </h3>
                                    </div>
                                    <p class="text-[11px] text-slate-400">
                                        Setiap barang memiliki spesifikasi (Merk, Type, Ukuran, No. Pabrik/SN), Bahan, Kondisi, Volume, Nilai Satuan (Maks. Rp 300.000), dan Ruang/Pemegang penempatan masing-masing.
                                    </p>
                                </div>
                                <button type="button" @click="addMesinItem()" 
                                        class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-cyan-500/20 shrink-0 cursor-pointer">
                                    <span>➕ Tambah Barang / Unit Baru</span>
                                </button>
                            </div>

                            <!-- List Kartu Barang Ekstrakomtabel (Repeater) -->
                            <div class="space-y-5">
                                <template x-for="(item, idx) in formData.mesin_items" :key="idx">
                                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-cyan-500/30 hover:border-cyan-500/60 transition-all space-y-4 shadow-xl relative group">
                                        
                                        <!-- Header Kartu Tiap Barang -->
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="px-3 py-1 rounded-xl bg-cyan-500/20 text-cyan-300 font-mono font-extrabold text-xs border border-cyan-500/40 flex items-center space-x-1.5">
                                                    <span>📦 Barang / Unit #<span x-text="idx + 1"></span></span>
                                                </span>
                                                <span class="text-[11px] text-slate-300 font-semibold" x-show="item.mesin_merk || item.mesin_type">
                                                    • <span x-text="(item.mesin_merk || '') + ' ' + (item.mesin_type || '')"></span>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Qty: <strong class="text-cyan-300" x-text="(item.mesin_jumlah_barang || 1) + ' ' + (item.mesin_satuan || 'Unit')"></strong>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getMesinSubtotal(item))"></strong>
                                                </span>
                                                <span class="text-[10px] px-2 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/30">
                                                    Ekstrakomtabel (≤ 300rb)
                                                </span>
                                            </div>

                                            <!-- Tombol Hapus Barang (Muncul jika > 1 item) -->
                                            <button type="button" 
                                                    x-show="formData.mesin_items.length > 1" 
                                                    @click="removeMesinItem(idx)" 
                                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                                <span>🗑️ Hapus Barang Ini</span>
                                            </button>
                                        </div>

                                        <!-- Grid Form Pengisian Spesifikasi Barang Ekstrakomtabel -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                            <!-- 1. Spesifikasi Fisik (Merk, Type, Ukuran & Nama) -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>⚙️ Merk, Type & Ukuran:</span>
                                                    </span>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                                        <span>Nama Barang (PMDN 108)</span>
                                                        <span class="text-[9px] text-amber-400 font-bold flex items-center space-x-1">
                                                            <span>🔒</span>
                                                            <span>Otomatis dari Langkah 2</span>
                                                        </span>
                                                    </label>
                                                    <div class="relative">
                                                        <input type="text" 
                                                               :value="item.mesin_nama_barang || formData.mesin_nama_barang || formData.sub_rincian_nama || 'Barang Ekstrakomtabel'"
                                                               readonly
                                                               class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Merk Barang</label>
                                                    <input type="text" x-model="item.mesin_merk" placeholder="Contoh: Olympic / Lion / Krisbow / Kenko"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Type / Model</label>
                                                        <input type="text" x-model="item.mesin_type" placeholder="Contoh: Standard / Meja / Rak"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Ukuran / Kapasitas</label>
                                                        <input type="text" x-model="item.mesin_ukuran" placeholder="Contoh: 120x60 cm / Sedang"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-amber-500">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 2. Spesifikasi No Pabrik, Bahan & Kondisi -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>🏷️ No Pabrik, Bahan & Kondisi:</span>
                                                    </span>
                                                </div>

                                                <div class="grid grid-cols-2 gap-2.5">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">No Pabrik / SN</label>
                                                        <input type="text" x-model="item.mesin_no_pabrik" placeholder="SN-EXT-2026-001"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-cyan-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">Bahan Pembuatan</label>
                                                        <input type="text" x-model="item.mesin_bahan" placeholder="Kayu / Besi / Plastik"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500">
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi Barang</label>
                                                    <select x-model="item.mesin_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-cyan-500">
                                                        <option value="Baik">Baik (B)</option>
                                                        <option value="Kurang Baik">Kurang Baik (KB)</option>
                                                        <option value="Rusak Berat">Rusak Berat (RB)</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- 3. Volume & Nilai Satuan Barang -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30 space-y-2.5">
                                            <div class="flex items-center justify-between border-b border-emerald-500/20 pb-1.5">
                                                <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>💰 Volume & Nilai Satuan Barang (Rp):</span>
                                                </span>
                                                <div class="flex items-center space-x-1.5 bg-emerald-950/60 border border-emerald-500/30 px-2.5 py-0.5 rounded-lg">
                                                    <span class="text-[10px] text-slate-300 font-semibold">Sub Total Item #<span x-text="idx + 1"></span>:</span>
                                                    <span class="text-xs font-black text-emerald-400 font-mono" x-text="'Rp ' + Number(getMesinSubtotal(item)).toLocaleString('id-ID')"></span>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah (Volume)</label>
                                                    <input type="number" min="1" x-model.number="item.mesin_jumlah_barang" @input="syncMesinFieldsToMain(); syncRealisasiFromStep3();" placeholder="1"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:outline-none focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Satuan</label>
                                                    <input type="text" x-model="item.mesin_satuan" placeholder="Unit / Buah / Set"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                                        <span>Nilai Satuan (Rp)</span>
                                                        <span class="text-[9px] font-bold text-amber-400">Maks. Rp 300.000</span>
                                                    </label>
                                                    <input type="text" 
                                                           :value="item.mesin_nilai_satuan ? Number(item.mesin_nilai_satuan).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.mesin_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                               syncMesinFieldsToMain();
                                                               syncRealisasiFromStep3();
                                                           "
                                                           :class="Number(item.mesin_nilai_satuan || 0) > 300000 ? 'border-rose-500 text-rose-300 focus:border-rose-400 ring-1 ring-rose-500' : 'border-slate-700 text-cyan-300 focus:border-cyan-500'"
                                                           placeholder="Maks: 300.000"
                                                           class="w-full bg-slate-950 border rounded-xl px-3 py-2 text-xs font-mono font-bold focus:outline-none">
                                                    <span x-show="Number(item.mesin_nilai_satuan || 0) > 300000" class="text-[9px] font-bold text-rose-400 block mt-1">
                                                        ⚠️ Nilai satuan Extracom tidak boleh > Rp 300.000!
                                                    </span>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Admin Proyek (Rp)</label>
                                                    <input type="text" 
                                                           :value="item.mesin_administrasi_proyek ? Number(item.mesin_administrasi_proyek).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.mesin_administrasi_proyek = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                               syncMesinFieldsToMain();
                                                               syncRealisasiFromStep3();
                                                           "
                                                           placeholder="0"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-300 font-mono font-bold focus:outline-none focus:border-emerald-500">
                                                </div>
                                                <div class="col-span-2 sm:col-span-1">
                                                    <label class="block text-emerald-400 text-[10px] mb-1 font-bold">Sub Total (Rp)</label>
                                                    <div class="w-full bg-slate-950/90 border border-emerald-500/50 rounded-xl px-3 py-2 text-xs text-emerald-400 font-mono font-black flex items-center justify-between shadow-inner">
                                                        <span class="text-emerald-500 text-[10px]">Rp</span>
                                                        <span x-text="Number(getMesinSubtotal(item)).toLocaleString('id-ID')"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 4. Ruang / Pemegang Aset -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/40 space-y-2 relative" @click.away="item.isRuangOpen = false">
                                            <div class="flex items-center justify-between border-b border-amber-500/30 pb-1.5">
                                                <label class="block text-amber-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>📍 Ruang / Unit Pemegang (Penanggung Jawab & Lokasi):</span>
                                                </label>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold flex items-center space-x-1">
                                                        <span>🏥</span>
                                                        <span>Unit & Paviliun</span>
                                                    </span>
                                                    <button type="button" 
                                                            x-show="item.ruang_pemegang" 
                                                            @click="item.ruang_pemegang = ''; item.searchRuang = ''; item.isRuangOpen = true" 
                                                            class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition-colors">
                                                        ✕ Reset
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="relative">
                                                <input type="text" 
                                                       :value="!item.isRuangOpen ? item.ruang_pemegang : item.searchRuang"
                                                       @input="item.ruang_pemegang = $event.target.value; item.searchRuang = $event.target.value; item.isRuangOpen = true"
                                                       @focus="item.isRuangOpen = true"
                                                       placeholder="Ketik atau pilih nama Ruang / Unit / Paviliun dari master data RSUD..."
                                                       class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 focus:border-amber-500 rounded-xl px-3.5 py-2.5 pl-9 text-xs text-white font-semibold focus:outline-none transition-all">
                                                <svg class="w-3.5 h-3.5 text-amber-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            </div>

                                            <!-- Dropdown List Pilihan Unit & Paviliun -->
                                            <div x-show="item.isRuangOpen" x-transition x-cloak style="max-height: 180px;" class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-amber-500/50 rounded-2xl shadow-2xl overflow-y-auto divide-y divide-slate-800">
                                                <div class="px-2.5 py-1 bg-slate-950/80 rounded-lg text-[9.5px] font-bold text-amber-400 uppercase tracking-wider flex items-center justify-between">
                                                    <span>PILIH DARI DATA UNIT & PAVILIUN RSUD:</span>
                                                    <span class="text-slate-400 font-mono text-[9px]" x-text="filterUnitsForItem(item).length + ' Unit/Ruangan'"></span>
                                                </div>
                                                <template x-for="u in filterUnitsForItem(item)" :key="u.id">
                                                    <div @click="selectUnitForItem(item, u)" class="p-2 rounded-xl bg-slate-950/50 hover:bg-amber-500/15 border border-slate-800/60 hover:border-amber-500/40 cursor-pointer transition-all flex items-center justify-between group">
                                                        <div class="min-w-0 pr-2">
                                                            <div class="flex items-center space-x-2">
                                                                <span class="text-xs font-bold text-white group-hover:text-amber-300 truncate" x-text="u.nama"></span>
                                                                <span class="text-[9px] px-1.5 py-0.5 rounded font-mono font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="u.tipe || 'Unit'"></span>
                                                            </div>
                                                            <p class="text-[9.5px] text-slate-400 truncate mt-0.5" x-text="'Kepala/PJ: ' + (u.kepala || '-') + ' • Kode: ' + (u.kode || '-')"></p>
                                                        </div>
                                                        <span class="px-2 py-0.5 rounded-lg bg-slate-900 text-amber-300 border border-amber-500/30 text-[9.5px] font-bold shrink-0">Pilih →</span>
                                                    </div>
                                                </template>
                                                <template x-if="filterUnitsForItem(item).length === 0">
                                                    <div class="p-2.5 text-center text-xs text-slate-400">
                                                        <span>Tidak ada unit yang cocok. Ketikkan nama secara manual jika tidak ada di daftar.</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                    </div>
                                </template>
                            </div>

                            <!-- Tombol Tambah Barang Ekstrakomtabel Baru -->
                            <button type="button" @click="addMesinItem()" 
                                    class="w-full py-3.5 border-2 border-dashed border-cyan-500/50 hover:border-cyan-400 bg-cyan-950/20 hover:bg-cyan-950/40 text-cyan-300 hover:text-cyan-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Barang Ekstrakomtabel Lainnya</span>
                            </button>

                            <!-- Ringkasan Anggaran & Akumulasi Realisasi Ekstrakomtabel -->
                            <div class="p-4 rounded-2xl bg-slate-950/90 border border-cyan-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
                                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">💰 Jumlah Anggaran (Pagu):</span>
                                        <span class="text-sm font-black text-white font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_anggaran)"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-cyan-400 font-semibold block uppercase tracking-wider">📦 Total Volume / Unit:</span>
                                        <span class="text-sm font-black text-cyan-300 font-mono" x-text="totalVolumeMesin + ' Unit/Barang'"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">📈 Jumlah Realisasi (Akumulasi):</span>
                                        <span class="text-sm font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiMesin)"></span>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0">
                                    <span class="text-[10px] text-cyan-400 font-semibold block uppercase tracking-wider">Total Nilai Realisasi Ekstrakomtabel:</span>
                                    <span class="text-base font-extrabold text-cyan-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiMesin)"></span>
                                </div>
                            </div>

                        </div>

                        <!-- ========================================================================= -->
                        <!-- LIVE PREVIEW TABEL EKSTRAKOMTABEL (27 KOLOM)                              -->
                        <!-- ========================================================================= -->
                        <div class="space-y-2 pt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-cyan-300 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📄 Live Preview Tabel Rincian Belanja Ekstrakomtabel (Sesuai SPK/Invoice):</span>
                                </span>
                                <span class="text-[10px] text-cyan-400 font-mono" x-text="formData.mesin_items.length + ' Baris Barang Terdaftar (Extracom 27 Kolom)'">Format Excel Ekstrakomtabel RSUD (27 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-cyan-500/40 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1350px]">
                                    <!-- Header Utama Pastel Senada -->
                                    <thead>
                                        <tr class="bg-[#fde9d9] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="26" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#fde9d9]">
                                                RINCIAN BELANJA MODAL EKSTRAKOMTABEL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                RUANG /<br>PEMEGANG
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">Merk</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">Type</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-20 align-middle bg-[#fde9d9]">Ukuran</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">No Pabrik</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">BAHAN</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Tahun Perolehan</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Kondisi<br><span class="font-normal text-[9px]">(B,KB,RB)</span></th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">VOLUME</th>
                                            <th rowspan="3" class="px-2.5 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">ADMINISTRASI PROYEK (Rp)</th>
                                            <th rowspan="3" class="px-3 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Total Nilai Barang (Rp)</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">SP2D</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">BAST pada SPK/Surat Pesanan/Kwitansi/Invoice</th>
                                        </tr>
                                        <!-- Header Tingkat 2 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500 text-[9.5px]">
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">SPK</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Surat Pesanan</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Kwitansi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Invoice (Tanggal dan Nomor)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Jumlah Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nama Satuan Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Satuan Barang (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                        </tr>
                                        <!-- Header Tingkat 3 -->
                                        <tr class="bg-[#fde9d9] text-slate-900 font-semibold border-b border-slate-600 text-[9px]">
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <!-- Body Data Live Sesuai Input User -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <template x-for="(mItem, mIdx) in formData.mesin_items" :key="mIdx">
                                            <tr>
                                                <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="mItem.mesin_nama_barang || formData.mesin_nama_barang || formData.sub_rincian_nama"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="mItem.mesin_kode_barang || formData.mesin_kode_barang || formData.sub_rincian_kode"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="mItem.mesin_merk || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono" x-text="mItem.mesin_type || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400" x-text="mItem.mesin_ukuran || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono" x-text="mItem.mesin_no_pabrik || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400" x-text="mItem.mesin_bahan || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono" x-text="formData.tahun_perolehan || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.spk_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.surat_pesanan_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.kwitansi_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.faktur_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="mItem.mesin_kondisi || 'Baik'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="mItem.mesin_jumlah_barang || 1"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="mItem.mesin_satuan || 'Unit'"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(mItem.mesin_nilai_satuan)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(mItem.mesin_administrasi_proyek)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-cyan-800" x-text="formatRupiah(getMesinSubtotal(mItem))"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.sp2d_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.bast_dokumen_tanggal)"></td>
                                                <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="mItem.ruang_pemegang || '-'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
