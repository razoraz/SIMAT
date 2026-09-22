            <!-- ========================================================================= -->
            <!-- LANGKAH 3: RINCIAN BELANJA MODAL (TANAH / MESIN / GEDUNG / JARINGAN / LAIN)-->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 3" class="space-y-6">

                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold mb-2">
                        <span>📦 DOKUMEN & RINCIAN REALISASI ASET</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400 text-sm">📋</span>
                        <span>Langkah 3: Dokumen Pengadaan & Rincian Realisasi Belanja Modal</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Lengkapi riwayat dokumen pengadaan (SPK / Surat Pesanan / Kwitansi / Faktur), SP2D, BAST serta spesifikasi teknis barang:</p>
                </div>

                <!-- 1. Header & Dashboard Pagu / Realisasi -->
                @include('pages.form_astap_partials.step3_rincian.summary_anggaran')

                <!-- 2. Kondisi A: Tanah (KIB A) -->
                @include('pages.form_astap_partials.step3_rincian.kib_a_tanah')

                <!-- 3. Kondisi B: Peralatan dan Mesin (KIB B) -->
                @include('pages.form_astap_partials.step3_rincian.kib_b_peralatan_mesin')

                <!-- 4. Kondisi C: Gedung dan Bangunan (KIB C) -->
                @include('pages.form_astap_partials.step3_rincian.kib_c_gedung_bangunan')

                <!-- 5. Kondisi D: Jalan, Irigasi & Jaringan (KIB D) -->
                @include('pages.form_astap_partials.step3_rincian.kib_d_jaringan_irigasi')

                <!-- 6. Kondisi E: Aset Tetap Lainnya (KIB E) -->
                @include('pages.form_astap_partials.step3_rincian.kib_e_aset_lainnya')

                <!-- 7. Kondisi ATB: Aset Tidak Berwujud -->
                @include('pages.form_astap_partials.step3_rincian.atb_aset_tak_berwujud')

                <!-- 8. Kondisi F: Konstruksi Dalam Pengerjaan (KIB F) -->
                @include('pages.form_astap_partials.step3_rincian.kib_f_kdp')

                <!-- 9. Kondisi G: Kategori Lainnya (Renovasi) -->
                @include('pages.form_astap_partials.step3_rincian.kategori_lainnya')

            </div>
