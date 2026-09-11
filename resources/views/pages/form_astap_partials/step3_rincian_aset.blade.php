            <!-- ========================================================================= -->
            <!-- LANGKAH 3: RINCIAN BELANJA MODAL (TANAH / MESIN / GEDUNG / JARINGAN / LAIN)-->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 3" class="space-y-6">

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
