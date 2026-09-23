<!-- ===================================================================== -->
<!-- KONDISI B: RINCIAN PERALATAN DAN MESIN (KIB B) UNTUK HIBAH            -->
<!-- ===================================================================== -->
<template x-if="isMesin">
    <div class="space-y-6">

        <!-- INFO BADGE: Mode Extracom (diatur dari Langkah 2) -->
        <div class="flex items-center justify-between px-4 py-3 rounded-2xl border transition-all"
             :class="formData.is_extracomtable
                 ? 'bg-cyan-950/30 border-cyan-500/40'
                 : 'bg-purple-950/30 border-purple-500/40'">
            <div class="flex items-center space-x-2.5">
                <span class="text-lg" x-text="formData.is_extracomtable ? '📦' : '⚙️'"></span>
                <div>
                    <p class="text-xs font-black"
                       :class="formData.is_extracomtable ? 'text-cyan-300' : 'text-purple-300'"
                       x-text="formData.is_extracomtable ? 'Mode: Barang Ekstrakomtabel (Extracom)' : 'Mode: Peralatan & Mesin (KIB B Reguler)'">
                    </p>
                    <p class="text-[10px] text-slate-400 mt-0.5"
                       x-text="formData.is_extracomtable
                           ? 'Harga satuan maks. Rp 300.000 · Legalitas kendaraan ditiadakan'
                           : 'Harga satuan bebas · Mendukung spesifikasi lengkap termasuk legalitas kendaraan'">
                    </p>
                </div>
            </div>
            <a @click.prevent="currentStep = 2"
               href="#"
               class="shrink-0 text-[10px] font-bold px-3 py-1.5 rounded-xl transition-all border cursor-pointer"
               :class="formData.is_extracomtable
                   ? 'text-cyan-300 border-cyan-500/40 hover:bg-cyan-500/10'
                   : 'text-purple-300 border-purple-500/40 hover:bg-purple-500/10'">
                ← Ubah di Langkah 2
            </a>
        </div>

        <!-- ========================================================================= -->
        <!-- PEMBUNGKUS BARANG PERALATAN DAN MESIN / EXTRACOM MULTI-ITEM               -->
        <!-- ========================================================================= -->
        <div class="space-y-4">
            
            <!-- Header Pembungkus Peralatan dan Mesin / Extracom -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-purple-950/30 border border-purple-500/40 shadow-md">
                <div class="space-y-0.5">
                    <div class="flex items-center space-x-2">
                        <span class="p-1.5 rounded-lg bg-purple-500/20 text-purple-400 text-sm" x-text="formData.is_extracomtable ? '📦' : '⚙️'"></span>
                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                            <span x-text="formData.is_extracomtable ? 'RINCIAN BARANG EKSTRAKOMTABEL' : 'RINCIAN PERALATAN DAN MESIN'"></span>
                            (<span class="text-purple-400" x-text="formData.mesin_items.length"></span> Barang / Unit Terdaftar)
                        </h3>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        <span x-show="!formData.is_extracomtable">Setiap barang memiliki spesifikasi (Merk, Type, Ukuran, No. Pabrik/SN), Legalitas Kendaraan, Volume, Nilai Satuan, dan Ruang/Pemegang penempatan masing-masing.</span>
                        <span x-show="formData.is_extracomtable">Setiap barang memiliki spesifikasi (Merk, Type, Ukuran, No. Pabrik/SN), Bahan, Kondisi, Volume, Nilai Satuan (Maks. Rp 300.000), dan Ruang/Pemegang penempatan masing-masing.</span>
                    </p>
                </div>
                <button type="button" @click="addMesinItem()" 
                        class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-purple-500/20 shrink-0 cursor-pointer">
                    <span>➕ Tambah Barang / Unit Baru</span>
                </button>
            </div>

            <!-- List Kartu Barang Peralatan & Mesin (Repeater) -->
            <div class="space-y-5">
                <template x-for="(item, idx) in formData.mesin_items" :key="idx">
                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-purple-500/30 hover:border-purple-500/60 transition-all space-y-4 shadow-xl relative group">
                        
                        <!-- Header Kartu Tiap Barang -->
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 rounded-xl bg-purple-500/20 text-purple-300 font-mono font-extrabold text-xs border border-purple-500/40 flex items-center space-x-1.5">
                                    <span>⚙️ Barang / Unit #<span x-text="idx + 1"></span></span>
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
                                <span x-show="formData.is_extracomtable" class="text-[10px] px-2 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/30">
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

                        <!-- Grid Form Pengisian Spesifikasi Peralatan dan Mesin -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Spesifikasi Fisik (Merk, Type, Ukuran & Nama) -->
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
                                               :value="item.mesin_nama_barang || formData.nama_barang || formData.sub_rincian_nama || 'Peralatan dan Mesin'"
                                               readonly
                                               class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Merk Barang</label>
                                    <input type="text" x-model="item.mesin_merk" @input="syncMesinFieldsToMain()" placeholder="Contoh: Mindray / GE / Siemens / Daikin"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Type / Model</label>
                                        <input type="text" x-model="item.mesin_type" @input="syncMesinFieldsToMain()" placeholder="Contoh: DC-30 Color Doppler"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Ukuran / Kapasitas</label>
                                        <input type="text" x-model="item.mesin_ukuran" @input="syncMesinFieldsToMain()" placeholder="Contoh: 120 x 80 cm / 500 Watt"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-amber-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Spesifikasi No Pabrik, Kendaraan, Bahan & Kondisi -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>🏷️ No Pabrik, Kendaraan, Bahan & Kondisi:</span>
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">No Pabrik / SN</label>
                                        <input type="text" x-model="item.mesin_no_pabrik" @input="syncMesinFieldsToMain()" placeholder="SN-12345678"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-cyan-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">Bahan Pembuatan</label>
                                        <input type="text" x-model="item.mesin_bahan" @input="syncMesinFieldsToMain()" placeholder="Logam & Elektronik"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500">
                                    </div>
                                </div>

                                <!-- Detail Kendaraan (Hanya tampil jika BUKAN Extracom) -->
                                <div x-show="!formData.is_extracomtable" class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800 space-y-1.5 transition-all">
                                    <span class="text-[9.5px] font-bold text-slate-400 block uppercase tracking-wider">🚗 Legality Kendaraan (Jika Ada):</span>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-slate-500 text-[9px] mb-0.5">No Rangka</label>
                                            <input type="text" x-model="item.mesin_no_rangka" @input="syncMesinFieldsToMain()" placeholder="MH1JM..."
                                                   class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px] mb-0.5">No Mesin</label>
                                            <input type="text" x-model="item.mesin_no_mesin" @input="syncMesinFieldsToMain()" placeholder="JM51E..."
                                                   class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px] mb-0.5">No BPKB</label>
                                            <input type="text" x-model="item.mesin_no_bpkb" @input="syncMesinFieldsToMain()" placeholder="BPKB-88..."
                                                   class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px] mb-0.5">No POLISI / Plat</label>
                                            <input type="text" x-model="item.mesin_no_polisi" @input="syncMesinFieldsToMain()" placeholder="P 1234 WB"
                                                   class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1.5 text-xs text-amber-300 font-mono font-bold">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi Fisik Barang</label>
                                    <select x-model="item.mesin_kondisi" @change="syncMesinFieldsToMain()" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-cyan-500">
                                        <option value="Baik">Baik (B / Berfungsi Normal)</option>
                                        <option value="Kurang Baik">Kurang Baik (KB)</option>
                                        <option value="Rusak Berat">Rusak Berat (RB)</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <!-- Volume & Nilai Taksiran Barang Hibah -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30 space-y-2.5">
                            <div class="flex items-center justify-between border-b border-emerald-500/20 pb-1.5">
                                <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>💰 Volume & Nilai Taksiran Barang (Rp):</span>
                                </span>
                                <div class="flex items-center space-x-1.5 bg-emerald-950/60 border border-emerald-500/30 px-2.5 py-0.5 rounded-lg">
                                    <span class="text-[10px] text-slate-300 font-semibold">Sub Total Item #<span x-text="idx + 1"></span>:</span>
                                    <span class="text-xs font-black text-emerald-400 font-mono" x-text="'Rp ' + Number(getMesinSubtotal(item)).toLocaleString('id-ID')"></span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah (Volume)</label>
                                    <input type="number" min="1" x-model.number="item.mesin_jumlah_barang" @input="syncMesinFieldsToMain()" placeholder="1"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:outline-none focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Satuan</label>
                                    <input type="text" x-model="item.mesin_satuan" @input="syncMesinFieldsToMain()" placeholder="Unit / Set"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                        <span>Nilai Satuan (Rp)</span>
                                        <span x-show="!formData.is_extracomtable" class="text-[9px] font-bold text-emerald-400">> Rp 300rb</span>
                                        <span x-show="formData.is_extracomtable" class="text-[9px] font-bold text-amber-400">≤ Rp 300rb</span>
                                    </label>
                                    <input type="text" 
                                           :value="item.mesin_nilai_satuan ? Number(item.mesin_nilai_satuan).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.mesin_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                               syncMesinFieldsToMain();
                                           "
                                           :placeholder="formData.is_extracomtable ? 'Maks: 300.000' : '25.000.000'"
                                           class="w-full bg-slate-950 border border-slate-700 text-emerald-300 rounded-xl px-3 py-2 text-xs font-mono font-bold focus:outline-none focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Ongkos / Biaya Lain (Rp)</label>
                                    <input type="text" 
                                           :value="item.mesin_administrasi_proyek ? Number(item.mesin_administrasi_proyek).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.mesin_administrasi_proyek = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                               syncMesinFieldsToMain();
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

                        <!-- Ruang / Pemegang Aset -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/40 space-y-2 relative" @click.away="item.isRuangOpen = false">
                            <div class="flex items-center justify-between border-b border-amber-500/30 pb-1.5">
                                <label class="block text-amber-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📍 Ruang / Unit Pemegang (Penanggung Jawab & Lokasi):</span>
                                </label>
                                <div class="flex items-center space-x-2">
                                    <button type="button" 
                                            x-show="item.ruang_pemegang" 
                                            @click="item.ruang_pemegang = ''; item.searchRuang = ''; item.isRuangOpen = true; syncMesinFieldsToMain()" 
                                            class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition-colors">
                                        ✕ Reset
                                    </button>
                                </div>
                            </div>
                            
                            <div class="relative">
                                <input type="text" 
                                       :value="!item.isRuangOpen ? item.ruang_pemegang : item.searchRuang"
                                       @input="item.ruang_pemegang = $event.target.value; item.searchRuang = $event.target.value; item.isRuangOpen = true; syncMesinFieldsToMain()"
                                       @focus="item.isRuangOpen = true"
                                       placeholder="Ketik atau pilih nama Ruang / Unit / Paviliun dari master data RSUD..."
                                       class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 focus:border-amber-500 rounded-xl px-3.5 py-2.5 pl-9 text-xs text-white font-semibold focus:outline-none transition-all">
                                <svg class="w-3.5 h-3.5 text-amber-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>

                            <!-- Dropdown List Ruangan -->
                            <div x-show="item.isRuangOpen" x-transition x-cloak style="max-height: 180px !important; overflow-y: auto !important;"
                                 class="absolute z-30 left-4 right-4 mt-1 bg-slate-900 border border-amber-500/50 rounded-2xl shadow-2xl p-2 divide-y divide-slate-800">
                                <template x-for="u in filterUnitsForItem(item).slice(0, 20)" :key="u.id || u.nama">
                                    <div @click="selectUnitForItem(item, u)"
                                         class="p-2 hover:bg-amber-500/10 rounded-xl cursor-pointer flex items-center justify-between group transition-colors">
                                        <div>
                                            <p class="text-xs font-bold text-white group-hover:text-amber-300" x-text="u.nama"></p>
                                            <p class="text-[10px] text-slate-400" x-text="'Kode: ' + (u.kode || u.kode_unit || '-')"></p>
                                        </div>
                                        <span class="text-[10px] text-amber-400 font-bold">Pilih →</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </template>
            </div>

            <!-- Tombol Tambah Barang Baru -->
            <button type="button" @click="addMesinItem()" 
                    class="w-full py-3.5 border-2 border-dashed border-purple-500/50 hover:border-purple-400 bg-purple-950/20 hover:bg-purple-950/40 text-purple-300 hover:text-purple-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Barang / Unit Peralatan & Mesin Lainnya</span>
            </button>

            <!-- Ringkasan Akumulasi Peralatan & Mesin -->
            <div class="p-4 rounded-2xl bg-slate-950/90 border border-purple-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                    <div>
                        <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">📦 Total Volume Barang:</span>
                        <span class="text-sm font-black text-white font-mono" x-text="totalVolumeMesin + ' Unit / Item'"></span>
                    </div>
                </div>
                <div class="text-left sm:text-right border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0">
                    <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">Total Taksiran Nilai Hibah:</span>
                    <span class="text-base font-extrabold text-emerald-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiMesin)"></span>
                </div>
            </div>

        </div>

    </div>
</template>
