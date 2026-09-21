                <!-- Header Langkah 3 -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-bold mb-2"
                             :class="formData.is_extracomtable ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' : (isTanah ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : (isMesin ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : (isGedung ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : (isJaringan ? 'bg-teal-500/20 text-teal-300 border border-teal-500/30' : (isAsetLainnya ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : (isAtb ? 'bg-violet-500/20 text-violet-300 border border-violet-500/30' : (isKdp ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30'))))))))">
                            <span x-text="formData.is_extracomtable ? '📦 RINCIAN KHUSUS BARANG EKSTRAKOMTABEL (EXTRACOM)' : (isTanah ? '🌾 RINCIAN KHUSUS BELANJA MODAL TANAH (KIB A)' : (isMesin ? '⚙️ RINCIAN KHUSUS PERALATAN DAN MESIN (KIB B)' : (isGedung ? '🏢 RINCIAN KHUSUS GEDUNG DAN BANGUNAN (KIB C)' : (isJaringan ? '🚰 RINCIAN KHUSUS JALAN, IRIGASI & JARINGAN (KIB D)' : (isAsetLainnya ? '📚 RINCIAN KHUSUS ASET TETAP LAINNYA (KIB E)' : (isAtb ? '💻 RINCIAN KHUSUS ASET TIDAK BERWUJUD (1.5.3)' : (isKdp ? '🏗️ RINCIAN KHUSUS KONSTRUKSI DALAM PENGERJAAN (KIB F)' : '📑 DOKUMEN PEMBELIAN BARANG'))))))))"></span>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-white tracking-tight"
                            x-text="formData.is_extracomtable ? 'Langkah 3: Rincian Barang Ekstrakomtabel Sesuai SPK / Surat Pesanan / Kwitansi / Invoice' : (isTanah ? 'Langkah 3: Rincian Belanja Modal Tanah Sesuai SPK / Kwitansi / Invoice' : (isMesin ? 'Langkah 3: Rincian Peralatan dan Mesin Sesuai SPK / Kwitansi / Invoice' : (isGedung ? 'Langkah 3: Rincian Belanja Gedung dan Bangunan Sesuai SPK / Invoice' : (isJaringan ? 'Langkah 3: Rincian Belanja Jalan, Irigasi dan Jaringan Sesuai SPK / Invoice' : (isAsetLainnya ? 'Langkah 3: Rincian Aset Tetap Lainnya Sesuai SPK / Invoice' : (isAtb ? 'Langkah 3: Rincian Aset Tidak Berwujud Sesuai SPK / Invoice' : (isKdp ? 'Langkah 3: Rincian Konstruksi Dalam Pengerjaan Sesuai SPK / MC' : 'Langkah 3: Dokumen Pengadaan & Bukti Transaksi'))))))))"></h2>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- DASHBOARD INFO: JUMLAH ANGGARAN, REALISASI & PERINGATAN OVERBUDGET (L3)  -->
                <!-- ========================================================================= -->
                <div class="p-4 sm:p-5 rounded-3xl bg-slate-900/95 border transition-all shadow-2xl space-y-4"
                     :class="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? 'border-rose-500/80 bg-rose-950/20 shadow-rose-950/50' : 'border-slate-800'">
                    
                    <!-- 4 Kartu Metrik Anggaran & Realisasi Terperinci -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <!-- 1. Pagu Anggaran Triwulan -->
                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 shadow-inner">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">1. PAGU ANGGARAN (TW)</span>
                            <div class="text-sm sm:text-base font-black font-mono text-white truncate" x-text="'Rp ' + formatRupiah(formData.jumlah_anggaran)"></div>
                            <span class="text-[10px] text-slate-500 mt-1 block truncate" x-text="'Pagu ' + (formData.triwulan || 'TW') + ' ' + (formData.tahun_anggaran || '')"></span>
                        </div>

                        <!-- 2. Realisasi Sebelumnya (TW Ini) -->
                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 shadow-inner">
                            <span class="text-[10px] font-bold text-amber-400 uppercase tracking-wider block mb-1">2. REALISASI SEBELUMNYA</span>
                            <div class="text-sm sm:text-base font-black font-mono text-amber-300 truncate" x-text="'Rp ' + formatRupiah(existingRealisasiDb)"></div>
                            <span class="text-[10px] text-slate-500 mt-1 block">Dari pengadaan lain di TW ini</span>
                        </div>

                        <!-- 3. Nilai Pengadaan Barang Ini (Langkah 3) -->
                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-cyan-500/40 bg-cyan-950/10 shadow-inner">
                            <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider block mb-1">3. NILAI PENGADAAN INI</span>
                            <div class="text-sm sm:text-base font-black font-mono text-cyan-300 truncate" x-text="'Rp ' + formatRupiah(nilaiBarangSaatIni)"></div>
                            <span class="text-[10px] text-cyan-400/80 mt-1 block">⚡ Terhitung otomatis dari rincian</span>
                        </div>

                        <!-- 4. Total Akumulasi Realisasi -->
                        <div class="p-3.5 rounded-2xl bg-slate-950 border transition-colors shadow-inner"
                             :class="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? 'border-rose-500/60 bg-rose-950/30' : 'border-emerald-500/40 bg-emerald-950/10'">
                            <span class="text-[10px] font-bold uppercase tracking-wider block mb-1"
                                  :class="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? 'text-rose-400' : 'text-emerald-400'">
                                4. TOTAL AKUMULASI (TW)
                            </span>
                            <div class="text-sm sm:text-base font-black font-mono truncate"
                                 :class="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? 'text-rose-400' : 'text-emerald-300'"
                                 x-text="'Rp ' + formatRupiah(formData.jumlah_realisasi)"></div>
                            <span class="text-[10px] mt-1 block truncate"
                                  :class="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? 'text-rose-400 font-bold' : 'text-slate-400'"
                                  x-text="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? '❌ Overbudget Rp ' + formatRupiah((formData.jumlah_realisasi || 0) - (formData.jumlah_anggaran || 0)) : 'Sisa Pagu: Rp ' + formatRupiah(Math.max(0, (formData.jumlah_anggaran || 0) - (formData.jumlah_realisasi || 0)))"></span>
                        </div>
                    </div>

                    <!-- Progress Bar Persentase Penyerapan -->
                    <div class="space-y-1.5 pt-1">
                        <div class="flex items-center justify-between text-[11px] font-mono">
                            <span class="text-slate-400">Persentase Penyerapan Anggaran Triwulan:</span>
                            <span class="font-black"
                                  :class="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? 'text-rose-400' : 'text-emerald-400'"
                                  x-text="(Number(formData.jumlah_anggaran || 0) > 0 ? ((Number(formData.jumlah_realisasi || 0) / Number(formData.jumlah_anggaran || 1)) * 100).toFixed(2) : 0) + '%'"></span>
                        </div>
                        <div class="w-full bg-slate-950 rounded-full h-2.5 overflow-hidden border border-slate-800">
                            <div class="h-2.5 rounded-full transition-all duration-300"
                                 :class="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? 'bg-rose-500' : 'bg-gradient-to-r from-cyan-500 via-teal-400 to-emerald-400'"
                                 :style="'width: ' + Math.min(100, Math.round(((Number(formData.jumlah_realisasi || 0)) / (Number(formData.jumlah_anggaran || 1))) * 100)) + '%'"></div>
                        </div>
                    </div>

                    <!-- ALERT BOX MERAH: MUNCUL JIKA NILAI REALISASI MELEBIHI PAGU ANGGARAN -->
                    <div x-show="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0"
                         x-cloak
                         x-transition
                         class="p-4 rounded-2xl bg-rose-950/80 border-2 border-rose-500/80 text-rose-200 text-xs flex items-start space-x-3.5 shadow-2xl">
                        <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center text-lg shrink-0 font-bold">
                            ⚠️
                        </div>
                        <div class="space-y-1 min-w-0 flex-1">
                            <h4 class="font-extrabold text-white text-sm tracking-wide">PERINGATAN: Total Realisasi Melebihi Pagu Anggaran!</h4>
                            <p class="leading-relaxed text-slate-300 text-xs">
                                Total nilai barang yang diinputkan saat ini (<strong class="text-rose-300 font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_realisasi)"></strong>) telah <strong class="text-rose-400">melebihi pagu anggaran</strong> yang ditetapkan (<strong class="text-white font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_anggaran)"></strong>) untuk <span class="font-bold text-amber-300" x-text="(formData.triwulan || 'Triwulan Ini') + ' ' + (formData.tahun_anggaran || '')"></span>.
                            </p>
                            <div class="pt-1 flex items-center space-x-2 text-[11px]">
                                <span class="text-slate-400">Selisih Kelebihan:</span>
                                <span class="px-2 py-0.5 rounded-lg bg-rose-500/30 text-rose-300 font-mono font-black border border-rose-500/50"
                                      x-text="'Rp ' + formatRupiah((formData.jumlah_realisasi || 0) - (formData.jumlah_anggaran || 0))"></span>
                                <span class="text-slate-400 italic">Mohon koreksi kembali nominal rincian nilai barang Anda sebelum lanjut.</span>
                            </div>
                        </div>
                    </div>

                    <!-- ALERT BOX KUNING: MUNCUL JIKA NILAI REALISASI BELUM DIISI / RP 0 -->
                    <div x-show="Number(formData.jumlah_realisasi || 0) <= 0"
                         x-cloak
                         x-transition
                         class="p-4 rounded-2xl bg-amber-950/80 border-2 border-amber-500/80 text-amber-200 text-xs flex items-start space-x-3.5 shadow-2xl">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/40 flex items-center justify-center text-lg shrink-0 font-bold">
                            ⚠️
                        </div>
                        <div class="space-y-1 min-w-0 flex-1">
                            <h4 class="font-extrabold text-white text-sm tracking-wide">PERINGATAN: Nilai Realisasi Belum Diisi!</h4>
                            <p class="leading-relaxed text-slate-300 text-xs">
                                Total nilai perolehan barang saat ini masih <strong class="text-amber-300 font-mono">Rp 0</strong>. Anda wajib mengisi rincian harga/nilai perolehan barang pada Langkah 3 ini sebelum dapat melanjutkan ke <strong class="text-white">Langkah 4 (Data Rekanan & Pengesahan)</strong>.
                            </p>
                        </div>
                    </div>

                    <!-- PANDUAN BATAS KAPITALISASI BMD (RP 300.000) / PENCATATAN ASET -->
                    <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800 text-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 shadow-inner">
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider shrink-0"
                                  :class="formData.is_extracomtable ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40'">
                                <span x-text="formData.is_extracomtable ? '📦 ATURAN EXTRACOM' : '🏛️ ATURAN ASET TETAP'"></span>
                            </span>
                            <span class="text-slate-300 text-[11px]"
                                  x-text="formData.is_extracomtable ? 'Nilai satuan per barang wajib ≤ Rp 300.000 (tidak boleh melebihi batas kapitalisasi daerah).' : ((isMesin || isAsetLainnya) ? 'Nilai satuan barang reguler wajib > Rp 300.000 (jika ≤ Rp 300.000 wajib dialihkan ke Ekstrakomtabel).' : 'Nilai perolehan wajib diisi (> Rp 0). Seluruh nilai perolehan aset tetap ini langsung dikapitalisasi ke Neraca.')">
                            </span>
                        </div>
                        <div x-show="formData.is_extracomtable || isMesin || isAsetLainnya" class="text-[10px] text-slate-400 font-mono shrink-0 bg-slate-900 px-2.5 py-1 rounded-lg border border-slate-800">
                            Batas Kapitalisasi: <span class="font-bold text-amber-300">Rp 300.000</span>
                        </div>
                        <div x-show="!formData.is_extracomtable && !isMesin && !isAsetLainnya" class="text-[10px] text-emerald-400 font-mono shrink-0 bg-slate-900 px-2.5 py-1 rounded-lg border border-slate-800">
                            Status: <span class="font-bold text-emerald-300">Kapitalisasi Penuh (SAP)</span>
                        </div>
                    </div>

                </div>
