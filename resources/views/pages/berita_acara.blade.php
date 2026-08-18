<x-layout title="Berita Acara (BAST) - SIMAT-RK">
    @section('page-title', 'Berita Acara (BAST)')
    @section('breadcrumb', 'Master Utama / Berita Acara (BAST)')

    <div x-data="{
        searchQuery: '',
        tahunFilter: 'all',
        showTemplateModal: false,
        showEditModal: false,
        selectedBast: null,

        // Data Dokumen Template BAST Aktif
        bastDoc: {
            id: 1,
            nomor_surat: '000.2.3.2/224/430.10.7/2026',
            hari_tanggal: 'Selasa tanggal 30 Juni 2026',
            lokasi: 'Rumah Sakit Umum Daerah dr.H.Koesnandi Kabupaten Bondowoso',
            triwulan: 'Triwulan II Tahun 2026',
            
            // Pihak Kesatu (PPK / Penerima Barang - Ditandatangani Otomatis Saat Distribusi)
            pihak1_nama: 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
            pihak1_nip: '19771002 200604 1 006',
            pihak1_nip_ttd: '19771126 199901 1 001',
            pihak1_jabatan: 'Pejabat Pembuat Komitmen / Penerima Barang Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso',
            pihak1_title_ttd: 'PEJABAT PEMBUAT KOMITMEN / PENERIMA BARANG RSUD dr.H.KOESNADI',
            pihak1_signed: true,
            pihak1_tgl_ttd: '30/06/2026 09:15 WIB',

            // Pihak Kedua (Pengurus Barang Aset - Default: BUDI HARTONO, S.Sos)
            pihak2_nama: 'BUDI HARTONO,S.Sos',
            pihak2_nip: '19760229 200801 1 010',
            pihak2_jabatan: 'Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso',
            pihak2_title_ttd: 'PENGURUS BARANG ASET RSUD dr.H.KOESNADI',
            pihak2_signed: true,
            pihak2_tgl_ttd: '30/06/2026 14:32 WIB',
            pihak2_qr_hash: 'BSRE-KOESNANDI-BAST-2026-0887419',

            // Tabel 8 Kategori Aset + Ekstra Komtabel
            items: [
                { no: '1.', nama: 'Tanah', qty: 0, nilai: 0 },
                { no: '2.', nama: 'Peralatan Dan Mesin', qty: 477, nilai: 3854986225 },
                { no: '3.', nama: 'Gedung Dan Bangunan', qty: 0, nilai: 0 },
                { no: '4.', nama: 'Jalan, Irigasi Dan Jaringan', qty: 0, nilai: 0 },
                { no: '5.', nama: 'Aset Tetap Lainnya', qty: 0, nilai: 0 },
                { no: '6.', nama: 'Kontruksi Dalam Pengerjaan', qty: 0, nilai: 0 },
                { no: '7.', nama: 'Aset Tidak Berwujud', qty: 1, nilai: 777000000 },
                { no: '8.', nama: 'Exstra Comtable', qty: 202, nilai: 33302220 }
            ]
        },

        get totalQty() {
            return this.bastDoc.items.reduce((sum, item) => sum + Number(item.qty || 0), 0);
        },

        get totalNilai() {
            return this.bastDoc.items.reduce((sum, item) => sum + Number(item.nilai || 0), 0);
        },

        formatRupiah(val) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val || 0);
        },

        basts: [
            {
                id: 1,
                nomor_surat: '000.2.3.2/224/430.10.7/2026',
                hari_tanggal: 'Selasa tanggal 30 Juni 2026',
                triwulan: 'Triwulan II Tahun 2026',
                pihak1_nama: 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
                pihak2_nama: 'BUDI HARTONO,S.Sos',
                total_barang: '680 Barang',
                total_nilai: 'Rp 4.665.288.445,00',
                pihak2_signed: true,
                status: 'Ditandatangani'
            },
            {
                id: 2,
                nomor_surat: '000.2.3.2/112/430.10.7/2026',
                hari_tanggal: 'Selasa tanggal 31 Maret 2026',
                triwulan: 'Triwulan I Tahun 2026',
                pihak1_nama: 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
                pihak2_nama: 'BUDI HARTONO,S.Sos',
                total_barang: '124 Barang',
                total_nilai: 'Rp 1.120.500.000,00',
                pihak2_signed: true,
                status: 'Ditandatangani'
            },
            {
                id: 3,
                nomor_surat: '000.2.3.2/340/430.10.7/2025',
                hari_tanggal: 'Rabu tanggal 31 Desember 2025',
                triwulan: 'Triwulan IV Tahun 2025',
                pihak1_nama: 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
                pihak2_nama: 'BUDI HARTONO,S.Sos',
                total_barang: '315 Barang',
                total_nilai: 'Rp 2.890.350.000,00',
                pihak2_signed: false,
                status: 'Menunggu TTD Pihak Kedua'
            }
        ],

        get filteredBasts() {
            const query = (this.searchQuery || '').toLowerCase();
            return this.basts.filter(item => {
                const matchSearch = (item.nomor_surat || '').toLowerCase().includes(query) ||
                                    (item.triwulan || '').toLowerCase().includes(query) ||
                                    (item.pihak2_nama || '').toLowerCase().includes(query) ||
                                    (item.pihak1_nama || '').toLowerCase().includes(query);
                const matchTahun = this.tahunFilter === 'all' || item.triwulan.includes(this.tahunFilter);
                return matchSearch && matchTahun;
            });
        },

        resetFilters() {
            this.searchQuery = '';
            this.tahunFilter = 'all';
        },

        openTemplate(item) {
            if (item) {
                this.bastDoc.id = item.id;
                this.bastDoc.nomor_surat = item.nomor_surat;
                this.bastDoc.hari_tanggal = item.hari_tanggal;
                this.bastDoc.triwulan = item.triwulan;
                this.bastDoc.pihak1_nama = item.pihak1_nama;
                this.bastDoc.pihak2_nama = item.pihak2_nama;
                this.bastDoc.pihak2_signed = item.pihak2_signed ?? true;
            }
            this.showTemplateModal = true;
        },

        openEdit(item) {
            this.selectedBast = item ? { ...item } : null;
            this.showEditModal = true;
        },

        // Aksi Tanda Tangan Elektronik Otomatis Pihak Kedua
        signPihak2() {
            this.bastDoc.pihak2_signed = true;
            const now = new Date();
            const timeStr = now.toLocaleDateString('id-ID') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';
            this.bastDoc.pihak2_tgl_ttd = timeStr;
            this.bastDoc.pihak2_qr_hash = 'BSRE-KOESNANDI-BAST-' + Date.now();
            
            // Sinkronisasi status list tabel
            const found = this.basts.find(b => b.id === this.bastDoc.id);
            if (found) {
                found.pihak2_signed = true;
                found.status = 'Ditandatangani';
            }
            alert('✅ Dokumen BAST berhasil ditandatangani secara elektronik (QR Code Resmi BSrE diterbitkan)!');
        },

        // Aksi Pembatalan Tanda Tangan Pihak Kedua
        unsignPihak2() {
            if (confirm('Apakah Anda yakin ingin membatalkan tanda tangan digital Pihak Kedua untuk dokumen BAST ini?')) {
                this.bastDoc.pihak2_signed = false;
                const found = this.basts.find(b => b.id === this.bastDoc.id);
                if (found) {
                    found.pihak2_signed = false;
                    found.status = 'Menunggu TTD Pihak Kedua';
                }
                alert('⚠️ Tanda tangan Pihak Kedua berhasil dibatalkan (Status: Draf / Menunggu TTD).');
            }
        },

        printDoc() {
            window.print();
        },

        deleteItem(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data Berita Acara (BAST) ini?')) {
                this.basts = this.basts.filter(b => b.id !== id);
                alert('🗑️ Berita Acara berhasil dihapus.');
            }
        }
    }" x-cloak>

        <!-- Header Banner & Mini KPI Strip -->
        <div class="no-print bg-gradient-to-r from-purple-600/15 via-slate-900 to-slate-900 border border-purple-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
                        <span>DOKUMEN RESMI SERAH TERIMA BARANG (BAST) RSUD</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Berita Acara Serah Terima Barang (BAST)</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Dokumen serah terima barang hasil pengadaan belanja modal rumah sakit. TTD Pihak Kesatu (PPK) diajukan saat distribusi, dan TTD Pihak Kedua (Pengurus Barang) diverifikasi via Digital Signature QR Code otomatis.
                    </p>
                </div>
                
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <button type="button" @click="openTemplate()"
                        class="px-4 py-2.5 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-bold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Templat BAST</span>
                    </button>
                    <a href="{{ route('bast.create') }}"
                        class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition-all flex items-center space-x-2">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Buat BAST Baru</span>
                    </a>
                </div>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">📄</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total BAST</span>
                        <span class="text-sm sm:text-base font-extrabold text-white" x-text="basts.length + ' Dokumen'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">📱</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">E-Sign QR Code</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-300">Otomatis Terverifikasi</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">📦</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Barang TW II</span>
                        <span class="text-sm sm:text-base font-extrabold text-cyan-300">680 Barang</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">💰</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Nilai Perolehan TW II</span>
                        <span class="text-sm sm:text-base font-extrabold text-amber-300">Rp 4,66 Miliar</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter, Quick Tabs & Search Bar Full-Width -->
        <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col gap-4">
                
                <!-- Quick Filter Tahun Tabs -->
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Periode:</span>
                    <button type="button" @click="tahunFilter = 'all'"
                        :class="tahunFilter === 'all' ? 'bg-purple-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        Semua Periode
                    </button>
                    <button type="button" @click="tahunFilter = '2026'"
                        :class="tahunFilter === '2026' ? 'bg-purple-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        Tahun 2026
                    </button>
                    <button type="button" @click="tahunFilter = '2025'"
                        :class="tahunFilter === '2025' ? 'bg-purple-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        Tahun 2025
                    </button>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari nomor surat BAST / periode triwulan / nama PPK / nama pengurus barang..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 transition-all">
                        <svg class="w-4 h-4 text-purple-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                            Menampilkan <span class="text-purple-400 font-bold" x-text="filteredBasts.length"></span> dari <span class="text-white font-bold" x-text="basts.length"></span> Dokumen
                        </span>
                        <button type="button" @click="resetFilters()"
                            class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                            🔄 Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table BAST -->
        <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 overflow-x-auto mb-6">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12">No</th>
                        <th class="px-4 py-3.5 text-left">Nomor Surat BAST</th>
                        <th class="px-4 py-3.5 text-left">Periode & Tanggal</th>
                        <th class="px-4 py-3.5 text-left">Pihak Kesatu (PPK)</th>
                        <th class="px-4 py-3.5 text-left">Pihak Kedua (Pengurus Barang)</th>
                        <th class="px-4 py-3.5 text-center">Status TTD Pihak 2</th>
                        <th class="px-4 py-3.5 text-right">Nilai Perolehan</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <template x-for="(item, index) in filteredBasts" :key="item.id">
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                            <td class="px-4 py-4 font-mono font-bold text-purple-400" x-text="item.nomor_surat"></td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-white" x-text="item.triwulan"></div>
                                <div class="text-[10px] text-slate-400" x-text="item.hari_tanggal"></div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-slate-200" x-text="item.pihak1_nama"></div>
                                <div class="text-[10px] text-emerald-400 font-mono">✓ TTD saat Distribusi</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-emerald-400" x-text="item.pihak2_nama"></div>
                                <div class="text-[10px] text-slate-400">Pengurus Barang Aset</div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-flex items-center space-x-1"
                                      :class="item.pihak2_signed ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30'">
                                    <span x-text="item.pihak2_signed ? '✅ QR Code Ditandatangani' : '⏳ Belum TTD (Draf)'"></span>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right font-mono font-bold text-amber-300" x-text="item.total_nilai"></td>
                            <td class="px-4 py-4 text-center space-x-1 whitespace-nowrap">
                                <button type="button" @click="openTemplate(item)"
                                    class="px-2.5 py-1.5 rounded-xl bg-purple-500/15 text-purple-300 hover:bg-purple-500/25 border border-purple-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Lihat & TTD</span>
                                </button>
                                <button type="button" @click="openEdit(item)"
                                    class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Ubah</span>
                                </button>
                                <button type="button" @click="deleteItem(item.id)"
                                    class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Hapus</span>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL & DOKUMEN TEMPLAT BAST RESMI DENGAN DIGITAL SIGNATURE QR CODE       -->
        <!-- ========================================================================= -->
        <div x-show="showTemplateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.away="showTemplateModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <!-- Action Bar Modal (Hidden When Printed) -->
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-purple-500/20 text-purple-300 text-sm">🖨️</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Templat Berita Acara Serah Terima Barang</h3>
                            <p class="text-[11px] text-slate-400">Verifikasi Tanda Tangan Elektronik QR Code & Cetak Dokumen</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-end">
                        <!-- Tombol TTD Pihak Kedua / Batalkan -->
                        <template x-if="!bastDoc.pihak2_signed">
                            <button type="button" @click="signPihak2()"
                                class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                <span>✍️ Tanda Tangani (QR Code)</span>
                            </button>
                        </template>

                        <template x-if="bastDoc.pihak2_signed">
                            <button type="button" @click="unsignPihak2()"
                                class="px-3.5 py-2 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Batalkan Tanda Tangan</span>
                            </button>
                        </template>

                        <!-- Tombol Cetak / PDF -->
                        <button type="button" @click="printDoc()"
                            class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak (Print / PDF)</span>
                        </button>

                        <button type="button" @click="showTemplateModal = false" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all active:scale-95">
                            Tutup
                        </button>
                    </div>
                </div>

                <!-- Formulir Cepat Edit Pihak Kesatu & Pihak Kedua (Hidden When Printed) -->
                <div class="no-print bg-slate-950 p-4 rounded-2xl border border-slate-800 text-xs space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-purple-300 text-[11px] uppercase tracking-wider">✏️ Sesuaikan Data Surat & Identitas Kedua Pihak:</span>
                    </div>
                    
                    <!-- Edit Nomor & Tanggal -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nomor Surat BAST</label>
                            <input type="text" x-model="bastDoc.nomor_surat" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Hari & Tanggal</label>
                            <input type="text" x-model="bastDoc.hari_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Periode Triwulan</label>
                            <input type="text" x-model="bastDoc.triwulan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs">
                        </div>
                    </div>

                    <!-- Edit Pihak Kesatu & Kedua -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                        <!-- Pihak Kesatu -->
                        <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800 space-y-1.5">
                            <span class="text-[10px] font-bold text-amber-400 block">PIHAK KESATU (PPK / Penerima Barang):</span>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Nama Lengkap PPK</label>
                                <input type="text" x-model="bastDoc.pihak1_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-semibold text-xs">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[9px]">NIP PPK</label>
                                    <input type="text" x-model="bastDoc.pihak1_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[9px]">NIP Tanda Tangan</label>
                                    <input type="text" x-model="bastDoc.pihak1_nip_ttd" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                                </div>
                            </div>
                        </div>

                        <!-- Pihak Kedua -->
                        <div class="p-2.5 rounded-xl bg-slate-900/80 border border-emerald-500/30 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-emerald-400 block">PIHAK KEDUA (Pengurus Barang):</span>
                                <span class="text-[9px] bg-emerald-500/20 text-emerald-300 px-2 py-0.5 rounded font-bold">Default: Budi Hartono</span>
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Nama Pengurus Barang</label>
                                <input type="text" x-model="bastDoc.pihak2_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-emerald-400 font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">NIP Pengurus Barang</label>
                                <input type="text" x-model="bastDoc.pihak2_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LEMBAR DOKUMEN CETAK ASLI (KERTAS PUTIH RESMI KOP SURAT) -->
                <div id="print-area" class="bg-white text-black p-6 sm:p-10 rounded-2xl shadow-xl max-h-[65vh] overflow-y-auto font-serif text-[11px] leading-relaxed select-text print:max-h-none print:overflow-visible print:p-0 print:m-0 print:shadow-none print:rounded-none">
                    
                    <!-- KOP SURAT RESMI -->
                    <div class="border-b-[3px] border-black pb-1 mb-0.5">
                        <div class="flex items-center justify-between gap-4">
                            <!-- Logo RSUD / Pemkab -->
                            <div class="w-20 shrink-0 flex justify-center">
                                <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="w-16 h-16 object-contain">
                            </div>

                            <!-- Teks Header Kop -->
                            <div class="flex-1 text-center font-sans text-black">
                                <h4 class="font-bold text-xs sm:text-sm uppercase tracking-wide leading-tight">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                <h3 class="font-black text-sm sm:text-base uppercase tracking-tight leading-tight">RUMAH SAKIT UMUM DAERAH dr.H.KOESNADI</h3>
                                <p class="text-[10px] leading-tight mt-0.5">Jl. Kapten Piere Tendean No. 3 Telp. (0332) 421974 Fax.0332 422311</p>
                                <p class="text-[10px] leading-tight">e-mail : rsu.koesnadi@gmail.com, Website : rsudrkoesnadi.go.id</p>
                                <h4 class="font-bold text-xs tracking-[0.3em] uppercase mt-0.5">B O N D O W O S O</h4>
                            </div>

                            <!-- Spacer Simetris Kanan -->
                            <div class="w-16 shrink-0"></div>
                        </div>
                    </div>
                    <!-- Garis Tipis Tambahan Kop -->
                    <div class="border-b border-black mb-4"></div>

                    <!-- JUDUL & NOMOR SURAT -->
                    <div class="text-center font-sans mb-3">
                        <h3 class="font-black text-xs sm:text-sm uppercase underline tracking-wider">BERITA ACARA SERAH TERIMA BARANG</h3>
                        <p class="text-[11px] font-semibold">Nomor : <span x-text="bastDoc.nomor_surat"></span></p>
                    </div>

                    <!-- PARAGRAF PEMBUKA -->
                    <p class="text-justify mb-2 indent-6">
                        Pada hari ini <span x-text="bastDoc.hari_tanggal"></span> bertempat di <span x-text="bastDoc.lokasi"></span>, yang bertanda tangan dibawah ini :
                    </p>

                    <!-- IDENTITAS PIHAK KESATU & KEDUA -->
                    <div class="space-y-1.5 mb-2 ml-2">
                        <!-- 1. Pihak Kesatu -->
                        <div class="flex items-start">
                            <span class="w-5 font-bold">1.</span>
                            <div class="grid grid-cols-[80px_10px_1fr] flex-1">
                                <span>Nama</span><span>:</span><span class="font-bold uppercase" x-text="bastDoc.pihak1_nama"></span>
                                <span>NIP</span><span>:</span><span class="font-mono" x-text="bastDoc.pihak1_nip"></span>
                                <span>Jabatan</span><span>:</span><span x-text="bastDoc.pihak1_jabatan"></span>
                            </div>
                        </div>
                        <p class="ml-6 font-semibold">Dalam Hal ini disebut <span class="font-bold">PIHAK KESATU</span>.</p>

                        <!-- 2. Pihak Kedua (Default Budi Hartono) -->
                        <div class="flex items-start pt-1">
                            <span class="w-5 font-bold">2.</span>
                            <div class="grid grid-cols-[80px_10px_1fr] flex-1">
                                <span>Nama</span><span>:</span><span class="font-bold uppercase" x-text="bastDoc.pihak2_nama"></span>
                                <span>NIP</span><span>:</span><span class="font-mono" x-text="bastDoc.pihak2_nip"></span>
                                <span>Jabatan</span><span>:</span><span x-text="bastDoc.pihak2_jabatan"></span>
                            </div>
                        </div>
                        <p class="ml-6 font-semibold">Dalam Hal ini disebut <span class="font-bold">PIHAK KEDUA</span>.</p>
                    </div>

                    <!-- KETERANGAN PENYERAHAN -->
                    <p class="text-justify mb-1.5 indent-6">
                        Bersama ini PIHAK KESATU menyerahkan Barang dari Hasil Pengadaan Belanja Modal <span x-text="bastDoc.triwulan"></span> Rumah Sakit Umum Daerah dr.H.Koesnadi Kabupaten Bondowoso kepada PIHAK KEDUA sebagai berikut :
                    </p>
                    <p class="mb-1.5">1. PIHAK KESATU menyerahkan Barang kepada PIHAK KEDUA Berupa :</p>

                    <!-- TABEL 8 KATEGORI ASET BMD -->
                    <div class="mb-2">
                        <table class="w-full border-collapse border border-black text-center text-[10.5px]">
                            <thead>
                                <tr class="font-sans font-bold bg-gray-100">
                                    <th class="border border-black px-2 py-1 w-10">NO</th>
                                    <th class="border border-black px-3 py-1 text-left">NAMA BARANG</th>
                                    <th class="border border-black px-3 py-1 w-28">JUMLAH BARANG</th>
                                    <th class="border border-black px-3 py-1 w-44">NILAI PEROLEHAN</th>
                                </tr>
                                <tr class="font-sans font-semibold text-[9.5px] bg-gray-50 border-b border-black">
                                    <th class="border border-black py-0.5">1</th>
                                    <th class="border border-black py-0.5 text-left pl-3">2</th>
                                    <th class="border border-black py-0.5">3</th>
                                    <th class="border border-black py-0.5">4</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="item in bastDoc.items" :key="item.no">
                                    <tr>
                                        <td class="border border-black px-2 py-0.5 text-center" x-text="item.no"></td>
                                        <td class="border border-black px-3 py-0.5 text-left font-semibold" x-text="item.nama"></td>
                                        <td class="border border-black px-3 py-0.5 text-center font-mono" x-text="item.qty"></td>
                                        <td class="border border-black px-3 py-0.5 text-right font-mono" x-text="formatRupiah(item.nilai)"></td>
                                    </tr>
                                </template>
                                <!-- Baris Total Jumlah -->
                                <tr class="font-sans font-bold bg-gray-100 border-t-2 border-black">
                                    <td colspan="2" class="border border-black px-3 py-1 text-center font-bold tracking-wider">JUMLAH</td>
                                    <td class="border border-black px-3 py-1 text-center font-mono font-bold" x-text="totalQty"></td>
                                    <td class="border border-black px-3 py-1 text-right font-mono font-bold" x-text="formatRupiah(totalNilai)"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <p class="mb-1 text-[10px] italic">Dengan rincian terlampir ;</p>

                    <!-- POIN PENERIMAAN & PENUTUP -->
                    <p class="text-justify mb-2">
                        2. PIHAK KEDUA menerima penyerahan Barang dimaksud serta berkewajiban untuk mengamankan dan melakukan entry data ke Rekap Mutasi Barang <span x-text="bastDoc.triwulan"></span>;
                    </p>
                    <p class="text-justify mb-5 indent-6">
                        Demikian Berita Acara ini dibuat untuk dipergunakan sebagaimana mestinya.
                    </p>

                    <!-- BLOK TANDA TANGAN DUA PIHAK DENGAN QR CODE DIGITAL SIGNATURE OTOMATIS -->
                    <div class="grid grid-cols-2 gap-8 text-center font-sans text-[10px] pt-1">
                        
                        <!-- Kolom Tanda Tangan Pihak Kesatu (PPK - TTD Saat Distribusi) -->
                        <div class="flex flex-col items-center justify-between min-h-[140px]">
                            <div>
                                <p class="font-bold">PIHAK KESATU</p>
                                <p class="font-bold uppercase text-[9px] leading-tight mt-0.5" x-text="bastDoc.pihak1_title_ttd"></p>
                            </div>
                            
                            <!-- E-Signature QR Code Pihak Kesatu -->
                            <div class="my-1.5 flex flex-col items-center">
                                <div class="w-16 h-16 p-1 border border-black/80 bg-white flex flex-col items-center justify-center">
                                    <!-- QR Code Mock Vector Resmi -->
                                    <svg class="w-14 h-14" viewBox="0 0 100 100" fill="currentColor">
                                        <rect x="5" y="5" width="25" height="25" fill="black"/>
                                        <rect x="10" y="10" width="15" height="15" fill="white"/>
                                        <rect x="13" y="13" width="9" height="9" fill="black"/>
                                        <rect x="70" y="5" width="25" height="25" fill="black"/>
                                        <rect x="75" y="10" width="15" height="15" fill="white"/>
                                        <rect x="78" y="13" width="9" height="9" fill="black"/>
                                        <rect x="5" y="70" width="25" height="25" fill="black"/>
                                        <rect x="10" y="75" width="15" height="15" fill="white"/>
                                        <rect x="13" y="78" width="9" height="9" fill="black"/>
                                        <rect x="35" y="10" width="8" height="8" fill="black"/>
                                        <rect x="50" y="10" width="8" height="8" fill="black"/>
                                        <rect x="35" y="25" width="15" height="8" fill="black"/>
                                        <rect x="40" y="40" width="20" height="20" fill="black"/>
                                        <rect x="45" y="45" width="10" height="10" fill="white"/>
                                        <rect x="48" y="48" width="4" height="4" fill="black"/>
                                        <rect x="10" y="40" width="12" height="8" fill="black"/>
                                        <rect x="70" y="40" width="12" height="8" fill="black"/>
                                        <rect x="35" y="70" width="8" height="15" fill="black"/>
                                        <rect x="50" y="70" width="15" height="8" fill="black"/>
                                        <rect x="70" y="70" width="20" height="8" fill="black"/>
                                        <rect x="75" y="82" width="15" height="10" fill="black"/>
                                    </svg>
                                </div>
                                <span class="text-[7.5px] font-mono text-gray-600 uppercase tracking-tighter mt-0.5">TERTANDA SAH DISTRIBUSI</span>
                            </div>

                            <div>
                                <p class="font-bold underline uppercase tracking-tight" x-text="bastDoc.pihak1_nama"></p>
                                <p class="text-[9px] font-mono">NIP. <span x-text="bastDoc.pihak1_nip_ttd"></span></p>
                            </div>
                        </div>

                        <!-- Kolom Tanda Tangan Pihak Kedua (Pengurus Barang Aset - Default Budi Hartono) -->
                        <div class="flex flex-col items-center justify-between min-h-[140px]">
                            <div>
                                <p class="font-bold">PIHAK KEDUA</p>
                                <p class="font-bold uppercase text-[9px] leading-tight mt-0.5" x-text="bastDoc.pihak2_title_ttd"></p>
                            </div>

                            <!-- Area QR Code / Ruang TTD -->
                            <template x-if="bastDoc.pihak2_signed">
                                <div class="my-1.5 flex flex-col items-center">
                                    <div class="w-16 h-16 p-1 border border-black/80 bg-white flex flex-col items-center justify-center">
                                        <!-- QR Code Mock Vector Resmi -->
                                        <svg class="w-14 h-14" viewBox="0 0 100 100" fill="currentColor">
                                            <rect x="5" y="5" width="25" height="25" fill="black"/>
                                            <rect x="10" y="10" width="15" height="15" fill="white"/>
                                            <rect x="13" y="13" width="9" height="9" fill="black"/>
                                            <rect x="70" y="5" width="25" height="25" fill="black"/>
                                            <rect x="75" y="10" width="15" height="15" fill="white"/>
                                            <rect x="78" y="13" width="9" height="9" fill="black"/>
                                            <rect x="5" y="70" width="25" height="25" fill="black"/>
                                            <rect x="10" y="75" width="15" height="15" fill="white"/>
                                            <rect x="13" y="78" width="9" height="9" fill="black"/>
                                            <rect x="35" y="15" width="10" height="8" fill="black"/>
                                            <rect x="52" y="15" width="10" height="8" fill="black"/>
                                            <rect x="40" y="35" width="20" height="20" fill="black"/>
                                            <rect x="45" y="40" width="10" height="10" fill="white"/>
                                            <rect x="48" y="43" width="4" height="4" fill="black"/>
                                            <rect x="15" y="45" width="10" height="8" fill="black"/>
                                            <rect x="75" y="45" width="15" height="8" fill="black"/>
                                            <rect x="35" y="75" width="10" height="15" fill="black"/>
                                            <rect x="52" y="75" width="12" height="8" fill="black"/>
                                            <rect x="70" y="70" width="20" height="8" fill="black"/>
                                            <rect x="72" y="82" width="18" height="10" fill="black"/>
                                        </svg>
                                    </div>
                                    <span class="text-[7.5px] font-mono text-gray-600 uppercase tracking-tighter mt-0.5">DITANDATANGANI ELEKTRONIK</span>
                                </div>
                            </template>

                            <!-- Ruang Kosong Jika Belum Ditandatangani -->
                            <template x-if="!bastDoc.pihak2_signed">
                                <div class="h-16 flex items-center justify-center">
                                    <div class="px-3 py-1.5 border border-dashed border-gray-400 rounded text-[9px] text-gray-500 font-sans">
                                        (Belum Ditandatangani)
                                    </div>
                                </div>
                            </template>

                            <div>
                                <p class="font-bold underline uppercase tracking-tight" x-text="bastDoc.pihak2_nama"></p>
                                <p class="text-[9px] font-mono">NIP. <span x-text="bastDoc.pihak2_nip"></span></p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL UBAH BAST                                                           -->
        <!-- ========================================================================= -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative">
                <button type="button" @click="showEditModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center text-sm font-bold transition-all">&times;</button>

                <div class="text-center pb-3 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-extrabold text-white">Ubah Data Berita Acara (BAST)</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Perbarui Nomor Surat, Identitas Kedua Pihak, dan Periode</p>
                </div>

                <form @submit.prevent="showEditModal = false; alert('✅ Perubahan BAST berhasil disimpan!')" class="space-y-3.5 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nomor Surat BAST</label>
                        <input type="text" x-model="selectedBast ? selectedBast.nomor_surat : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-purple-400 font-mono font-bold">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Periode Triwulan</label>
                            <input type="text" x-model="selectedBast ? selectedBast.triwulan : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Hari & Tanggal</label>
                            <input type="text" x-model="selectedBast ? selectedBast.hari_tanggal : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Pihak Kesatu (PPK / Penerima Barang)</label>
                        <input type="text" x-model="selectedBast ? selectedBast.pihak1_nama : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Pihak Kedua (Pengurus Barang Aset)</label>
                        <input type="text" x-model="selectedBast ? selectedBast.pihak2_nama : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-emerald-400 font-bold">
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                        <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-slate-700 hover:border-rose-500/30 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Batal</span>
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Print Styling Khusus Halaman Cetak Dokumen -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #print-area, #print-area * {
                visibility: visible;
            }
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 10mm 15mm !important;
                background: white !important;
                color: black !important;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</x-layout>
