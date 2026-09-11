        <!-- ========================================================================= -->
        <!-- KONTEN TAB 1: BAST PENAMBAHAN DATA ASTAP BERDASARKAN TRIWULAN             -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'triwulan'" class="space-y-6" x-cloak>
            
            <!-- Filter Triwulan & Tahun Toolbar -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    
                    <!-- Pilihan Periode Filter Dropdown -->
                    <div class="flex flex-wrap items-center gap-2">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Pilih Periode:</label>
                        <select x-model="selectedTriwulanKey" class="bg-slate-950 border border-purple-500/40 hover:border-purple-400 rounded-xl px-3.5 py-2 text-xs font-bold text-purple-300 focus:outline-none focus:border-purple-500 shadow-md shadow-purple-500/10 transition-all cursor-pointer">
                            <option value="TW1">Triwulan I (Jan - Mar)</option>
                            <option value="TW2">Triwulan II (Apr - Jun)</option>
                            <option value="TW3">Triwulan III (Jul - Sep)</option>
                            <option value="TW4">Triwulan IV (Okt - Des)</option>
                        </select>
                    </div>

                    <!-- Tahun Dropdown & Tombol Aksi TTD / Cetak -->
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <select x-model="selectedTahun" 
                                @change="window.location.href = '/berita-acara?tab=triwulan&tahun=' + $event.target.value"
                                class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-purple-500 cursor-pointer">
                            @foreach($availableYears ?? [2027, 2026, 2025, 2024, 2023, 2022, 2021, 2020] as $yr)
                                <option value="{{ $yr }}" {{ (string)$yr === (string)($tahun ?? '2026') ? 'selected' : '' }}>Tahun Anggaran {{ $yr }}</option>
                            @endforeach
                        </select>

                        <!-- Button Toggle TTD BSrE / Batalkan TTD -->
                        <button type="button" @click="toggleSignTriwulan(selectedTriwulanKey)"
                            :class="currentTriwulanDoc.pihak2_signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-lg shadow-emerald-500/20'"
                            class="px-3.5 py-2 rounded-xl font-extrabold text-xs transition-all active:scale-95 flex items-center space-x-1">
                            <span x-text="currentTriwulanDoc.pihak2_signed ? '↩️ Batalkan TTD BSrE' : '✍️ TTD BSrE'"></span>
                        </button>

                        <!-- Button Cetak BAST Triwulan -->
                        <button type="button" @click="openPrintTriwulan(selectedTriwulanKey)"
                            class="px-3.5 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-1 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>🖨️ Cetak / Edit</span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Card Ringkasan Triwulan Aktif -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-4">
                    <div>
                        <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-[10px] font-bold mb-1">
                            <span x-text="'DOKUMEN BAST RESMI: ' + currentTriwulanDoc.nomor_surat"></span>
                        </div>
                        <h2 class="text-lg font-extrabold text-white" x-text="'Berita Acara Serah Terima Barang - ' + currentTriwulanDoc.triwulan_nama"></h2>
                        <p class="text-xs text-slate-400 mt-0.5" x-text="'Hari & Tanggal Pelaksanaan: ' + currentTriwulanDoc.hari_tanggal"></p>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Realisasi Triwulan Ini</span>
                            <span class="text-base sm:text-lg font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(currentTriwulanTotalNilai)"></span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border shadow-sm"
                              :class="currentTriwulanDoc.pihak2_signed ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/15 text-amber-300 border-amber-500/30'">
                            <template x-if="currentTriwulanDoc.pihak2_signed">
                                <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <template x-if="!currentTriwulanDoc.pihak2_signed">
                                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </template>
                            <span x-text="currentTriwulanDoc.pihak2_signed ? 'Sudah TTD BSrE' : 'Belum TTD'"></span>
                        </span>
                    </div>
                </div>

                <!-- Bagian 1: TABEL REKAPITULASI 8 KELOMPOK ASET (FORMAT ASLI STANDAR BAST) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-200 uppercase tracking-wider flex items-center space-x-2">
                            <span>📊 1. REKAPITULASI 8 KELOMPOK ASET TETAP TRIWULAN INI:</span>
                        </span>
                        <span class="text-[11px] text-purple-400 font-mono font-bold" x-text="'Total: ' + formatNumber(currentTriwulanTotalQty) + ' Barang / Aset'"></span>
                    </div>
                    
                    <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-xl">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-950 text-slate-300 font-bold uppercase tracking-wider border-b border-slate-700">
                                <tr>
                                    <th class="px-3 py-2.5 text-center w-12 border-r border-slate-800">No</th>
                                    <th class="px-4 py-2.5 border-r border-slate-800">Nama Kelompok / Kategori Aset</th>
                                    <th class="px-4 py-2.5 text-center border-r border-slate-800 w-32">Kuantitas</th>
                                    <th class="px-4 py-2.5 text-right w-48">Nilai Realisasi Perolehan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 bg-slate-900/60 font-medium">
                                <template x-for="item in currentTriwulanDoc.rekapItems" :key="item.no">
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="px-3 py-2.5 text-center text-slate-400 font-mono border-r border-slate-800" x-text="item.no"></td>
                                        <td class="px-4 py-2.5 font-semibold text-slate-200 border-r border-slate-800" x-text="item.nama"></td>
                                        <td class="px-4 py-2.5 text-center font-mono font-bold text-cyan-400 border-r border-slate-800" x-text="formatNumber(item.qty) + ' Barang'"></td>
                                        <td class="px-4 py-2.5 text-right font-mono font-bold" :class="item.nilai > 0 ? 'text-amber-300' : 'text-slate-500'" x-text="'Rp ' + formatRupiah(item.nilai)"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-slate-950 text-white font-extrabold border-t-2 border-slate-700">
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-center uppercase tracking-wider text-purple-300 border-r border-slate-800">Total Pengadaan Triwulan Terpilih:</td>
                                    <td class="px-4 py-3 text-center font-mono text-cyan-300 text-sm border-r border-slate-800" x-text="formatNumber(currentTriwulanTotalQty) + ' Barang'"></td>
                                    <td class="px-4 py-3 text-right font-mono text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(currentTriwulanTotalNilai)"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Bagian 2: TABEL RINCIAN DETAIL BARANG YANG DIADAKAN PADA TRIWULAN INI -->
                <div class="pt-4 border-t border-slate-800 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider flex items-center space-x-2">
                                <span>📦 2. DAFTAR RINCIAN BARANG & BELANJA MODAL YANG DIADAKAN PADA TRIWULAN INI:</span>
                            </span>
                            <p class="text-[11px] text-slate-400 mt-0.5">Daftar item belanja modal yang dibukukan lengkap dengan dokumen SPK dan nilai realisasinya.</p>
                        </div>

                        <!-- Search Box Filter Barang -->
                        <div class="relative w-full sm:w-64">
                            <input type="text" x-model="searchBarangTriwulan" placeholder="Cari nama barang / kode 108 / penyedia..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-1.5 pl-8 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-purple-500">
                            <svg class="w-3.5 h-3.5 text-purple-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-700 shadow-xl custom-scrollbar" style="max-height: 340px; overflow-y: auto; overflow-x: auto;">
                        <table class="w-full text-left text-xs border-collapse min-w-[900px]">
                            <thead class="text-slate-300 font-bold uppercase tracking-wider border-b border-slate-700 text-[11px]" style="position: sticky; top: 0; z-index: 20; background-color: #020617;">
                                <tr>
                                    <th class="px-3 py-2.5 text-center w-10 border-r border-slate-800">No</th>
                                    <th class="px-3 py-2.5 border-r border-slate-800">Tgl & No SPK</th>
                                    <th class="px-3 py-2.5 border-r border-slate-800">Kode Barang 108</th>
                                    <th class="px-4 py-2.5 border-r border-slate-800">Nama Barang & Spesifikasi</th>
                                    <th class="px-3 py-2.5 border-r border-slate-800">Penyedia / Rekanan</th>
                                    <th class="px-3 py-2.5 text-center border-r border-slate-800">Volume</th>
                                    <th class="px-4 py-2.5 text-right">Nilai Realisasi (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 bg-slate-900/60 font-medium text-[11px]">
                                <template x-for="(b, idx) in filteredDetailBarangTriwulan" :key="b.no">
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="px-3 py-2.5 text-center text-slate-400 font-mono border-r border-slate-800" x-text="idx + 1"></td>
                                        <td class="px-3 py-2.5 border-r border-slate-800">
                                            <div class="font-mono font-bold text-purple-300" x-text="b.nomor_spk"></div>
                                            <div class="text-[10px] text-slate-400" x-text="b.tanggal_sp2d"></div>
                                        </td>
                                        <td class="px-3 py-2.5 font-mono text-cyan-400 font-bold border-r border-slate-800" x-text="b.kode_108"></td>
                                        <td class="px-4 py-2.5 border-r border-slate-800">
                                            <div class="font-bold text-white" x-text="b.nama_barang"></div>
                                            <div class="text-[10px] text-slate-400" x-text="b.spesifikasi"></div>
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-300 border-r border-slate-800 font-semibold" x-text="b.penyedia"></td>
                                        <td class="px-3 py-2.5 text-center font-mono font-bold text-white border-r border-slate-800" x-text="b.volume + ' ' + b.satuan"></td>
                                        <td class="px-4 py-2.5 text-right font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(b.nilai_realisasi)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
