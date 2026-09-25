<!-- ===================================================================== -->
<!-- KONDISI ATB: RINCIAN ASET TIDAK BERWUJUD UNTUK HIBAH                 -->
<!-- ===================================================================== -->
<template x-if="isATB">
    <div class="space-y-6">

        <!-- ============================================================= -->
        <!-- MULTI-ITEM REPEATER KHUSUS ATB (ASET TIDAK BERWUJUD)           -->
        <!-- ============================================================= -->
        <div class="space-y-4">

            <!-- Header Pembungkus ATB -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-violet-950/30 border border-violet-500/40 shadow-md">
                <div class="space-y-0.5">
                    <div class="flex items-center space-x-2">
                        <span class="p-1.5 rounded-lg bg-violet-500/20 text-violet-400 text-sm">💻</span>
                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                            RINCIAN ASET TIDAK BERWUJUD (<span class="text-violet-400" x-text="formData.atb_items.length"></span> Item ATB Terdaftar)
                        </h3>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        Setiap item software, aplikasi, lisensi, atau hak cipta hibah memiliki judul, pencipta/vendor, spesifikasi teknis, kondisi, nilai taksiran, dan unit penanggung jawab masing-masing.
                    </p>
                </div>
                <button type="button" @click="addAtbItem()"
                        class="px-4 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-xs font-bold transition-all flex items-center justify-center space-x-2 shadow-lg shadow-violet-600/30 border border-violet-400/40 shrink-0 cursor-pointer active:scale-95">
                    <span>➕</span>
                    <span>Tambah Item ATB Baru</span>
                </button>
            </div>

            <!-- List Kartu ATB (Repeater) -->
            <div class="space-y-5">
                <template x-for="(item, idx) in formData.atb_items" :key="idx">
                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-violet-500/30 hover:border-violet-500/60 transition-all space-y-4 shadow-xl relative group">
                        
                        <!-- Header Kartu Tiap ATB -->
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 rounded-xl bg-violet-500/20 text-violet-300 font-mono font-extrabold text-xs border border-violet-500/40 flex items-center space-x-1.5">
                                    <span>💻 Item ATB #<span x-text="idx + 1"></span></span>
                                </span>
                                <span class="text-[11px] text-slate-300 font-semibold" x-show="item.atb_nama_barang">
                                    • <span x-text="item.atb_nama_barang"></span>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono" x-show="item.atb_judul_nama">
                                    • <span x-text="item.atb_judul_nama"></span>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Qty: <strong class="text-cyan-300" x-text="(item.atb_jumlah || 1) + ' ' + (item.atb_satuan || 'Lisensi')"></strong>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getAtbSubtotal(item))"></strong>
                                </span>
                            </div>

                            <!-- Tombol Hapus ATB (Muncul jika > 1 item) -->
                            <button type="button" 
                                    x-show="formData.atb_items.length > 1" 
                                    @click="removeAtbItem(idx)" 
                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                <span>🗑️ Hapus Item Ini</span>
                            </button>
                        </div>

                        <!-- Grid Form Pengisian Spesifikasi ATB -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Kolom Kiri: Identitas ATB -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>💻 Identitas &amp; Spesifikasi ATB:</span>
                                    </span>
                                </div>

                                <!-- Nama Barang (Terkunci) -->
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-slate-400 text-[10px] font-semibold">Nama Barang (PMDN 108)</label>
                                        <span class="text-[9px] text-amber-400/80 flex items-center gap-1 font-medium bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20">
                                            <span>🔒 Terkunci dari Langkah 2</span>
                                        </span>
                                    </div>
                                    <input type="text" :value="item.atb_nama_barang || formData.atb_nama_barang || formData.sub_rincian_nama || 'Aset Tidak Berwujud'" readonly
                                           class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                </div>

                                <!-- Judul / Nama Software -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Judul / Nama Software &amp; Lisensi</label>
                                    <input type="text" x-model="item.atb_judul_nama" placeholder="Aplikasi SIMAT-RK / Sistem Informasi Klinis..."
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                                </div>

                                <!-- Pencipta / Vendor -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pencipta / Vendor / Pengembang</label>
                                    <input type="text" x-model="item.atb_pencipta" placeholder="Penyedia / Pemberi Hibah Sistem"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                                </div>

                                <!-- Spesifikasi -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Spesifikasi Software / Hak Cipta</label>
                                    <textarea rows="2" x-model="item.atb_spesifikasi" placeholder="Web-Based, Multi-Role Access, Integrasi Rekam Medis Elektronik..."
                                              class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-cyan-300 focus:outline-none focus:border-cyan-500 resize-none"></textarea>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Volume & Nilai -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-violet-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>📦 Volume &amp; Taksiran Nilai Hibah:</span>
                                    </span>
                                </div>

                                <!-- Jumlah, Satuan, Kondisi -->
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah</label>
                                        <input type="number" min="1" x-model.number="item.atb_jumlah" placeholder="1"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-violet-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Satuan</label>
                                        <input type="text" x-model="item.atb_satuan" placeholder="Lisensi"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-violet-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi</label>
                                        <select x-model="item.atb_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-violet-500">
                                            <option value="Baik">Baik (B)</option>
                                            <option value="Kurang Baik">Kurang Baik (KB)</option>
                                            <option value="Rusak Berat">Rusak Berat (RB)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Nilai Satuan -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nilai Taksiran Hibah Satuan (Rp)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-slate-500 text-xs font-bold">Rp</span>
                                        <input type="number" x-model.number="item.atb_nilai_satuan" placeholder="10000000"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs text-violet-300 font-mono font-bold focus:border-violet-500">
                                    </div>
                                </div>

                                <!-- Administrasi Proyek / Biaya Tambahan -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Biaya Tambahan / Admin (Rp)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-slate-500 text-xs font-bold">Rp</span>
                                        <input type="number" x-model.number="item.atb_administrasi_proyek" placeholder="0"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs text-amber-300 font-mono font-bold focus:border-violet-500">
                                    </div>
                                </div>

                                <!-- Subtotal Item -->
                                <div class="p-3 rounded-xl bg-violet-950/40 border border-violet-500/30 flex items-center justify-between">
                                    <span class="text-[10px] text-violet-400 font-semibold uppercase tracking-wider">Subtotal Item Ini:</span>
                                    <span class="text-sm font-black text-violet-300 font-mono" x-text="'Rp ' + formatRupiah(getAtbSubtotal(item))"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Ruang / Pemegang — Full Width -->
                        <div class="p-4 rounded-2xl bg-slate-950/80 border border-violet-500/40 space-y-2" @click.away="item.isRuangOpen = false">
                            <div class="flex items-center justify-between border-b border-violet-500/20 pb-2">
                                <label class="block text-violet-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-2">
                                    <span>📍 RUANG / PEMEGANG (PENANGGUNG JAWAB & LOKASI ITEM INI):</span>
                                </label>
                                <div class="flex items-center space-x-2">
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold flex items-center space-x-1">
                                        <span>🏥</span>
                                        <span>Unit & Paviliun</span>
                                    </span>
                                    <button type="button"
                                            x-show="item.atb_ruang_pemegang"
                                            @click="item.atb_ruang_pemegang = ''; item.searchRuang = ''; item.isRuangOpen = true"
                                            class="text-[10.5px] font-bold text-rose-400 hover:text-rose-300 transition-colors">
                                        ✕ Reset
                                    </button>
                                </div>
                            </div>
                            <div class="relative">
                                <input type="text"
                                       :value="!item.isRuangOpen ? item.atb_ruang_pemegang : item.searchRuang"
                                       @input="item.atb_ruang_pemegang = $event.target.value; item.searchRuang = $event.target.value; item.isRuangOpen = true"
                                       @focus="item.isRuangOpen = true"
                                       placeholder="Ketik atau pilih nama Ruang / Unit / Paviliun dari master data RSUD..."
                                       class="w-full bg-slate-900 border border-slate-700 hover:border-violet-500 focus:border-violet-500 rounded-xl px-4 py-3 pl-10 text-xs text-white font-semibold focus:outline-none transition-all">
                                <svg class="w-4 h-4 text-violet-400 absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <!-- Dropdown List Pilihan Unit & Paviliun -->
                            <div x-show="item.isRuangOpen" x-transition x-cloak style="max-height: 210px;"
                                 class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-violet-500/50 rounded-2xl shadow-2xl overflow-y-auto divide-y divide-slate-800">
                                <div class="px-3 py-1.5 bg-slate-950/80 rounded-xl text-[10px] font-bold text-violet-400 uppercase tracking-wider flex items-center justify-between">
                                    <span>PILIH DARI DATA UNIT & PAVILIUN RSUD:</span>
                                    <span class="text-slate-400 font-mono text-[9.5px]" x-text="(masterUnits || []).filter(u => !item.searchRuang || u.nama.toLowerCase().includes(item.searchRuang.toLowerCase())).length + ' Unit/Ruangan'"></span>
                                </div>
                                <template x-for="u in (masterUnits || []).filter(u => !item.searchRuang || u.nama.toLowerCase().includes(item.searchRuang.toLowerCase()))" :key="u.id">
                                    <div @click="item.atb_ruang_pemegang = u.nama; item.isRuangOpen = false; item.searchRuang = ''"
                                         class="p-2.5 rounded-xl bg-slate-950/50 hover:bg-violet-500/15 border border-slate-800/60 hover:border-violet-500/40 cursor-pointer transition-all flex items-center justify-between group">
                                        <div class="min-w-0 pr-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs font-bold text-white group-hover:text-violet-300 truncate" x-text="u.nama"></span>
                                                <span class="text-[9px] px-1.5 py-0.5 rounded font-mono font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="u.tipe || 'Unit'"></span>
                                            </div>
                                            <p class="text-[10px] text-slate-400 truncate mt-0.5" x-text="'Kepala/PJ: ' + (u.kepala || '-') + ' • Kode: ' + (u.kode || '-')"></p>
                                        </div>
                                        <span class="px-2 py-1 rounded-lg bg-slate-900 text-violet-300 border border-violet-500/30 text-[10px] font-bold shrink-0">Pilih →</span>
                                    </div>
                                </template>
                                <template x-if="(masterUnits || []).filter(u => !item.searchRuang || u.nama.toLowerCase().includes(item.searchRuang.toLowerCase())).length === 0">
                                    <div class="p-3 text-center text-xs text-slate-400">
                                        <span>Tidak ada unit yang cocok. Ketikkan nama secara manual jika tidak ada di daftar.</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </template>
            </div>

            <!-- Tombol Tambah ATB Lainnya -->
            <button type="button" @click="addAtbItem()"
                    class="w-full py-3.5 border-2 border-dashed border-violet-500/50 hover:border-violet-400 bg-violet-950/20 hover:bg-violet-950/40 text-violet-300 hover:text-violet-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Aset Tidak Berwujud Lainnya</span>
            </button>

            <!-- Ringkasan Total ATB Hibah -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-violet-500/30 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-4">
                    <div>
                        <span class="text-[10px] text-violet-400 font-semibold block uppercase tracking-wider">📦 Total Volume ATB:</span>
                        <span class="text-sm font-black text-violet-300 font-mono" x-text="totalVolumeAtb + ' Lisensi / Item'"></span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">Total Taksiran Nilai Hibah:</span>
                    <span class="text-base font-extrabold text-emerald-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiAtb)"></span>
                </div>
            </div>
        </div>

    </div>
</template>
