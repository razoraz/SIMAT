<x-layout title="Pilih Sumber Perolehan Aset - SIMAT-RK">
    @section('page-title', 'Pilih Metode Perolehan Aset')
    @section('breadcrumb', 'Master Utama / Data ASTAP / Pilih Perolehan')

    <div x-data="{
        selectedType: 'modal',
        types: [
            {
                id: 'modal',
                title: 'Belanja Modal (APBD / BLUD)',
                shortTitle: '1. Belanja Modal',
                subLabel: 'Rutin APBD / BLUD',
                badge: 'Perolehan Rutin APBD',
                account: 'Akun 1.3 · Aset Tetap',
                icon: '🏢',
                theme: 'emerald',
                route: '{{ route('astap.create') }}',
                actionLabel: 'Buka Form Belanja Modal',
                stepCount: 'Form 4-Langkah Lengkap',
                purpose: 'Pencatatan aset tetap baru yang dibeli atau dibangun langsung menggunakan pagu anggaran Belanja Modal RSUD Koesnandi (DPA / SIPD Belanja Modal Rekening 5.2).',
                cases: 'Pengadaan peralatan medis baru (USG, Ventilator), pembangunan fisik gedung baru, pembelian ambulans baru, atau pengadaan jaringan komputer dari pagu APBD/BLUD RSUD.',
                docs: [
                    'Pagu SIPD & DPA Belanja Modal (Akun 5.2.x)',
                    'Surat Perintah Kerja (SPK) / Kontrak Tender',
                    'Surat Pesanan & Faktur Pembelian',
                    'SP2D (Surat Perintah Pencairan Dana)',
                    'Berita Acara Serah Terima (BAST Fisik Kontrak)'
                ],
                accounting: 'Masuk langsung ke pos Neraca Aset Tetap (Akun 1.3: KIB A s.d. KIB F) sebesar total realisasi kontrak termasuk biaya administrasi proyek.',
                tags: ['✓ Form 4-Langkah', '📋 Pagu SIPD', '📦 KIB A - F']
            },
            {
                id: 'hibah',
                title: 'Hibah / Bantuan Masuk',
                shortTitle: '2. Hibah Masuk',
                subLabel: 'Kemenkes / Dinkes / CSR',
                badge: 'Hibah Masuk RSDK',
                account: 'Akun 1.3 · Tanpa Belanja APBD',
                icon: '🎁',
                theme: 'amber',
                route: '{{ route('astap.create_hibah') }}',
                actionLabel: 'Buka Form Penerimaan Hibah',
                stepCount: 'Form 3-Langkah Cepat',
                purpose: 'Pencatatan penerimaan barang atau aset secara cuma-cuma dari instansi eksternal (Kemenkes RI, Dinas Kesehatan Prov Jatim, donatur swasta, atau CSR perusahaan) tanpa membebani pagu belanja APBD.',
                cases: 'Bantuan alkes Covid/ICU dari Kemenkes RI, hibah mobil jenazah dari CSR Bank/Yayasan, atau alat diagnostik laboratorium bantuan dari pemerintah provinsi.',
                docs: [
                    'Naskah Perjanjian Hibah Daerah (NPHD / BASTB)',
                    'Berita Acara Serah Terima (BAST Hibah Resmi)',
                    'Surat Penetapan Status Penggunaan Barang',
                    'Lampiran Rincian Barang & Taksiran Harga Wajar'
                ],
                accounting: 'Dicatat pada pos Aset Tetap (Akun 1.3) berdasarkan nilai taksiran wajar/buku hibah tanpa membebani pagu belanja APBD RSUD.',
                tags: ['✓ BAST Hibah', '📄 Tanpa SIPD', '📊 Sheet Hibah']
            },
            {
                id: 'perbekalan',
                title: 'Belanja Barang (Perbekalan / Operasional)',
                shortTitle: '3. Belanja Barang',
                subLabel: 'Kode 5.1.02 · Ekstrakom',
                badge: 'Perbekalan / Operasional',
                account: 'Akun 5.1.02 · Ekstrakomptabel',
                icon: '📋',
                theme: 'indigo',
                route: '{{ route('astap.create_rekening') }}',
                actionLabel: 'Buka Form Belanja Barang',
                stepCount: 'Form Ekstrakomptabel',
                purpose: 'Pencatatan perbekalan & barang operasional dari pusat perbekalan atau belanja barang jasa (Akun 5.1.02) untuk pengawasan fisik inventaris ruangan (KIR).',
                cases: 'Tensimeter ruangan, kursi roda, timbangan pasien, perabot kantor kecil, atau alat perbekalan medis yang nilainya di bawah pagu minimum kapitalisasi aset tetap.',
                docs: [
                    'Faktur / Kuitansi Belanja Barang Jasa (5.1.02)',
                    'Bukti Penerimaan Barang dari Bagian Gudang/Perbekalan',
                    'Lembar Kartu Inventaris Ruangan (KIR)'
                ],
                accounting: 'Masuk dalam pengawasan Aset Ekstrakomptabel (KIR Ruangan) tanpa dikapitalisasi ke Neraca Aset Tetap 1.3.',
                tags: ['🏷️ Kode 5.1.02', '🔍 Kontrol KIR', '📦 Ekstrakom']
            },
            {
                id: 'mutasi',
                title: 'Pelimpahan SKPD (Dinas Luar)',
                shortTitle: '4. Pelimpahan SKPD',
                subLabel: 'Mutasi Antar-OPD',
                badge: 'Pelimpahan SKPD Luar',
                account: 'Akun 1.3 · Mutasi Antar-OPD',
                icon: '🔄',
                theme: 'purple',
                route: '{{ route('astap.create_mutasi_eksternal') }}',
                actionLabel: 'Buka Form Mutasi Eksternal',
                stepCount: 'Form Mutasi Masuk',
                purpose: 'Pencatatan penyerahan atau pelimpahan status penggunaan aset dari SKPD/Dinas lain di lingkungan Pemkab Bondowoso atau dinas luar daerah ke RSUD Dr. H. Koesnandi.',
                cases: 'Pelimpahan mobil dinas dari Sekretariat Daerah ke RSUD, penyerahan tanah/gedung dari BPKAD, atau pemindahan perlengkapan server dari Diskominfo.',
                docs: [
                    'Berita Acara Mutasi Barang (BAMB / BAP Eksternal)',
                    'SK Bupati / Kepala Daerah tentang Status Penggunaan Aset',
                    'Kartu Inventaris Barang (KIB) Asal SKPD Pemberi'
                ],
                accounting: 'Dicatat sebagai mutasi aset masuk antar-SKPD Pemkab Bondowoso dengan mengadopsi nilai buku dan akumulasi penyusutan yang tercatat sebelumnya.',
                tags: ['📑 Berita BAMB', '🏢 Tanpa SIPD', '📍 Lokasi Baru']
            },
            {
                id: 'kemitraan',
                title: 'Kemitraan Pihak Ketiga (KSO / BGS / Sewa)',
                shortTitle: '5. Kemitraan (KSO)',
                subLabel: 'Akun 1.5.2 · Konsesi Mitra',
                badge: 'Aset Lainnya · Akun 1.5.2',
                account: 'Akun 1.5.2 · Konsesi Mitra',
                icon: '🤝',
                theme: 'cyan',
                route: '{{ route('astap.create_kemitraan') }}',
                actionLabel: 'Buka Form Aset Kemitraan',
                stepCount: 'Form 3-Langkah PKS',
                purpose: 'Pencatatan perolehan dan pemanfaatan aset melalui perjanjian kerja sama komersial / operasional dengan vendor swasta dengan masa konsesi tertentu (bukan hibah dan bukan belanja modal).',
                cases: 'Penempatan alat laboratorium otomatis (KSO Reagen dengan PT. Roche/Sysmex), KSO mesin Hemodialisis, sewa alat medis canggih, atau pembangunan gedung parkir/fasilitas dengan skema Bangun Guna Serah (BGS).',
                docs: [
                    'Surat Perjanjian Kerja Sama (PKS / MoU) dengan Mitra',
                    'Jangka Waktu Konsesi (Tanggal Mulai & Tanggal Selesai)',
                    'Berita Acara Uji Fungsi & Penempatan Alkes/Fasilitas',
                    'Taksiran Nilai Wajar Aset Kemitraan'
                ],
                accounting: 'Wajib dicatat pada pos Neraca 1.5.2 (Aset Kemitraan dengan Pihak Ketiga) Permendagri 108. Setelah masa konsesi berakhir, aset dapat diserahkan penuh menjadi Aset Tetap melalui Reklasifikasi.',
                tags: ['✓ Dokumen PKS', '🏥 Akun 1.5.2', '🤝 Mitra KSO']
            }
        ],
        getType(id) {
            return this.types.find(t => t.id === id);
        }
    }" x-cloak class="max-w-6xl mx-auto space-y-5 py-2 px-2 sm:px-4">

        <!-- Header Ringkas & Sejajar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-3">
                <a href="{{ route('astap.index') }}"
                    class="p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white transition-all flex items-center justify-center shrink-0 shadow-sm"
                    title="Kembali ke Data ASTAP">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-tight">
                        Pilih Sumber Perolehan Aset
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Pilih jenis transaksi perolehan di bawah untuk melihat rincian kegunaan dan membuka formulir yang sesuai.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('master.reklasifikasi') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-cyan-500/40 text-xs font-bold text-cyan-400 hover:text-cyan-300 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                    </svg>
                    <span>Matriks Reklasifikasi</span>
                </a>
                <span class="hidden md:inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
                    SIMAT-RK v2.6 Ready
                </span>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- FILTER TAB BAR (5 PILIHAN UTAMA BERSIH & SEJAJAR)                         -->
        <!-- ========================================================================= -->
        <div class="space-y-2">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <span>🔍 PILIH JENIS PEROLEHAN:</span>
            </span>

            <!-- 5 Tabs Segmented Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                <template x-for="t in types" :key="t.id">
                    <button type="button" @click="selectedType = t.id"
                        class="p-3 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between group relative overflow-hidden"
                        :class="{
                            'bg-emerald-500/15 border-emerald-400 text-white shadow-lg shadow-emerald-500/15 ring-1 ring-emerald-500/30': selectedType === t.id && t.id === 'modal',
                            'bg-amber-400/15 border-amber-400 text-white shadow-lg shadow-amber-400/15 ring-1 ring-amber-400/30': selectedType === t.id && t.id === 'hibah',
                            'bg-indigo-500/15 border-indigo-400 text-white shadow-lg shadow-indigo-500/15 ring-1 ring-indigo-500/30': selectedType === t.id && t.id === 'perbekalan',
                            'bg-purple-500/15 border-purple-400 text-white shadow-lg shadow-purple-500/15 ring-1 ring-purple-500/30': selectedType === t.id && t.id === 'mutasi',
                            'bg-cyan-500/15 border-cyan-400 text-white shadow-lg shadow-cyan-500/15 ring-1 ring-cyan-500/30': selectedType === t.id && t.id === 'kemitraan',
                            'bg-slate-900/90 border-slate-800 text-slate-400 hover:border-slate-700 hover:bg-slate-800/50 hover:text-slate-200': selectedType !== t.id
                        }">
                        <div class="flex items-center justify-between gap-1 mb-1.5">
                            <span class="text-lg" x-text="t.icon"></span>
                            <span class="w-2 h-2 rounded-full transition-all"
                                  :class="{
                                      'bg-emerald-400 shadow-sm shadow-emerald-400': selectedType === t.id && t.id === 'modal',
                                      'bg-amber-400 shadow-sm shadow-amber-400': selectedType === t.id && t.id === 'hibah',
                                      'bg-indigo-400 shadow-sm shadow-indigo-400': selectedType === t.id && t.id === 'perbekalan',
                                      'bg-purple-400 shadow-sm shadow-purple-400': selectedType === t.id && t.id === 'mutasi',
                                      'bg-cyan-400 shadow-sm shadow-cyan-400': selectedType === t.id && t.id === 'kemitraan',
                                      'bg-slate-800': selectedType !== t.id
                                  }"></span>
                        </div>
                        <div>
                            <span class="block text-xs font-black truncate" :class="selectedType === t.id ? 'text-white' : 'text-slate-300 group-hover:text-white'" x-text="t.shortTitle"></span>
                            <span class="block text-[10px] text-slate-400 truncate mt-0.5" x-text="t.subLabel"></span>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- PANEL PENJELASAN UTAMA ("INI BUAT APA & UNTUK KASUS APA")                 -->
        <!-- ========================================================================= -->
        <div x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="p-5 sm:p-7 rounded-3xl bg-slate-900 border shadow-2xl relative overflow-hidden"
             :class="{
                 'border-emerald-500/40 bg-gradient-to-br from-slate-900 via-slate-900 to-emerald-950/20': selectedType === 'modal',
                 'border-amber-400/40 bg-gradient-to-br from-slate-900 via-slate-900 to-amber-950/20': selectedType === 'hibah',
                 'border-indigo-500/40 bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950/20': selectedType === 'perbekalan',
                 'border-purple-500/40 bg-gradient-to-br from-slate-900 via-slate-900 to-purple-950/20': selectedType === 'mutasi',
                 'border-cyan-500/40 bg-gradient-to-br from-slate-900 via-slate-900 to-cyan-950/20': selectedType === 'kemitraan'
             }">

            <!-- Header Panel Penjelasan -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-800">
                <div class="flex items-center space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-lg"
                         :class="{
                             'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30': selectedType === 'modal',
                             'bg-amber-400/20 text-amber-300 border border-amber-400/30': selectedType === 'hibah',
                             'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30': selectedType === 'perbekalan',
                             'bg-purple-500/20 text-purple-300 border border-purple-500/30': selectedType === 'mutasi',
                             'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30': selectedType === 'kemitraan'
                         }">
                        <span x-text="getType(selectedType)?.icon"></span>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg sm:text-xl font-black text-white" x-text="getType(selectedType)?.title"></h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider"
                                  :class="{
                                      'bg-emerald-500/10 text-emerald-300 border border-emerald-500/30': selectedType === 'modal',
                                      'bg-amber-400/10 text-amber-300 border border-amber-400/30': selectedType === 'hibah',
                                      'bg-indigo-500/10 text-indigo-300 border border-indigo-500/30': selectedType === 'perbekalan',
                                      'bg-purple-500/10 text-purple-300 border border-purple-500/30': selectedType === 'mutasi',
                                      'bg-cyan-500/10 text-cyan-300 border border-cyan-500/30': selectedType === 'kemitraan'
                                  }"
                                  x-text="getType(selectedType)?.account"></span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5" x-text="getType(selectedType)?.badge"></p>
                    </div>
                </div>

                <!-- Direct CTA Action Button (Besar & Jelas) -->
                <div>
                    <a :href="getType(selectedType)?.route"
                       class="w-full sm:w-auto px-7 py-3 rounded-2xl font-black text-xs transition-all duration-200 flex items-center justify-center gap-2 shadow-xl hover:scale-102 active:scale-98 group cursor-pointer"
                       :class="{
                           'bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-emerald-500/25': selectedType === 'modal',
                           'bg-amber-400 hover:bg-amber-300 text-slate-950 shadow-amber-400/25': selectedType === 'hibah',
                           'bg-indigo-500 hover:bg-indigo-400 text-white shadow-indigo-500/25': selectedType === 'perbekalan',
                           'bg-purple-500 hover:bg-purple-400 text-white shadow-purple-500/25': selectedType === 'mutasi',
                           'bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-cyan-500/25': selectedType === 'kemitraan'
                       }">
                        <span x-text="getType(selectedType)?.actionLabel"></span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Grid 3 Kolom Penjelasan Mendalam ("Ini Buat Apa & Apa") -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-5">
                
                <!-- Kolom 1: Untuk Apa Jalur Ini? -->
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                        <span>🎯 UNTUK APA JALUR INI?</span>
                    </span>
                    <p class="text-xs text-slate-300 leading-relaxed font-medium" x-text="getType(selectedType)?.purpose"></p>
                    
                    <div class="pt-2 border-t border-slate-800/80">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Perlakuan Akuntansi:</span>
                        <p class="text-[11px] text-slate-400 italic" x-text="getType(selectedType)?.accounting"></p>
                    </div>
                </div>

                <!-- Kolom 2: Contoh Kasus Nyata di RSUD -->
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                        <span>💡 CONTOH KASUS DI RSUD:</span>
                    </span>
                    <p class="text-xs text-slate-300 leading-relaxed font-medium" x-text="getType(selectedType)?.cases"></p>
                    
                    <div class="pt-2 border-t border-slate-800/80">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Struktur Formulir:</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-800 text-slate-300" x-text="getType(selectedType)?.stepCount"></span>
                    </div>
                </div>

                <!-- Kolom 3: Dokumen & Syarat Wajib -->
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                        <span>📄 DOKUMEN &amp; SYARAT WAJIB:</span>
                    </span>
                    <ul class="space-y-1.5 pt-0.5">
                        <template x-for="(doc, idx) in (getType(selectedType)?.docs || [])" :key="idx">
                            <li class="text-xs text-slate-300 flex items-start gap-2">
                                <span class="text-slate-500 mt-0.5 shrink-0 font-bold">•</span>
                                <span class="leading-tight" x-text="doc"></span>
                            </li>
                        </template>
                    </ul>
                </div>

            </div>

            <!-- Footer Tags & Navigasi Alternatif -->
            <div class="mt-5 pt-4 border-t border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-400">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Fitur Formulir:</span>
                    <template x-for="(tag, idx) in (getType(selectedType)?.tags || [])" :key="idx">
                        <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800 text-[10px] font-semibold text-slate-300" x-text="tag"></span>
                    </template>
                </div>

                <div class="flex items-center gap-3">
                    <template x-if="selectedType === 'mutasi'">
                        <a href="{{ route('mutasi.index') }}" class="text-[11px] text-purple-400 hover:text-purple-300 transition-colors">
                            Buka mutasi internal ruangan &rarr;
                        </a>
                    </template>
                    <template x-if="selectedType === 'kemitraan'">
                        <a href="{{ route('master.reklasifikasi') }}" class="text-[11px] text-cyan-400 hover:text-cyan-300 transition-colors">
                            Buka matriks reklasifikasi neraca &rarr;
                        </a>
                    </template>
                </div>
            </div>

        </div>

    </div>
</x-layout>
