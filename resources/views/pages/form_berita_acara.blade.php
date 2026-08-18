<x-layout :title="request()->routeIs('bast.edit') ? 'Ubah Berita Acara (BAST) - SIMAT-RK' : 'Buat Dokumen BAST Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('bast.edit') ? 'Ubah Berita Acara (BAST)' : 'Buat Dokumen BAST Baru')
    @section('breadcrumb', request()->routeIs('bast.edit') ? 'Master Utama / Berita Acara / Ubah' : 'Master Utama / Berita Acara / Buat Baru')

    <div x-data="{
        isEdit: {{ request()->routeIs('bast.edit') ? 'true' : 'false' }},
        formData: {
            nomor_surat: '000.2.3.2/224/430.10.7/2026',
            hari_tanggal: 'Selasa tanggal 30 Juni 2026',
            lokasi: 'Rumah Sakit Umum Daerah dr.H.Koesnadi Kabupaten Bondowoso',
            triwulan: 'Triwulan II Tahun 2026',
            
            // Pihak Kesatu (PPK)
            pihak1_nama: 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
            pihak1_nip: '19771002 200604 1 006',
            pihak1_jabatan: 'Pejabat Pembuat Komitmen / Penerima Barang Pada RSUD dr.H.Koesnadi Kabupaten Bondowoso',

            // Pihak Kedua (Default: BUDI HARTONO, S.Sos)
            pihak2_nama: 'BUDI HARTONO,S.Sos',
            pihak2_nip: '19760229 200801 1 010',
            pihak2_jabatan: 'Pengurus Barang Aset Pada RSUD dr.H.Koesnadi Kabupaten Bondowoso',

            // 8 Kategori Aset
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

        init() {
            if (!this.isEdit) {
                this.formData.nomor_surat = '000.2.3.2/' + String(Math.floor(Math.random() * 400) + 100) + '/430.10.7/2026';
            }
        },

        get totalQty() {
            return this.formData.items.reduce((sum, item) => sum + Number(item.qty || 0), 0);
        },

        get totalNilai() {
            return this.formData.items.reduce((sum, item) => sum + Number(item.nilai || 0), 0);
        },

        formatRupiah(val) {
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val || 0);
        },

        submitForm() {
            alert('✅ Dokumen BAST (' + this.formData.nomor_surat + ') dengan Pengurus Barang ' + this.formData.pihak2_nama + ' berhasil disimpan!');
            window.location.href = '{{ route('bast.index') }}';
        }
    }" x-cloak class="space-y-6">

        <!-- Top Navigation Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <a href="{{ route('bast.index') }}" 
                   class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-[10px] font-bold mb-1">
                        <span x-text="isEdit ? '✏️ UBAH DOKUMEN BAST RESMI' : '📄 TERBITKAN DOKUMEN BAST BARU'"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight" x-text="isEdit ? 'Ubah BAST: ' + formData.nomor_surat : 'Penerbitan Berita Acara Serah Terima (BAST)'"></h1>
                </div>
            </div>

            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <a href="{{ route('bast.index') }}" 
                   class="px-5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-slate-700 hover:border-rose-500/30 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>Batal</span>
                </a>
                <button type="button" @click="submitForm()"
                        class="px-5 py-2.5 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Terbitkan Dokumen BAST'"></span>
                </button>
            </div>
        </div>

        <!-- Form Cards Container -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Kolom Kiri & Tengah: Data Surat & Identitas Pihak (2 Kolom) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- 1. Header Informasi Surat BAST -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                    <div class="flex items-center space-x-2 border-b border-slate-800 pb-3">
                        <span class="text-purple-400 font-bold">📄</span>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">1. Rincian Dokumen & Waktu Pelaksanaan</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1.5">Nomor Surat BAST</label>
                            <input type="text" x-model="formData.nomor_surat" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-purple-400 font-mono font-bold focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1.5">Periode Belanja Modal</label>
                            <input type="text" x-model="formData.triwulan" placeholder="Contoh: Triwulan II Tahun 2026" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:border-purple-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1.5">Hari & Tanggal Serah Terima</label>
                            <input type="text" x-model="formData.hari_tanggal" placeholder="Selasa tanggal 30 Juni 2026" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1.5">Tempat Pelaksanaan</label>
                            <input type="text" x-model="formData.lokasi" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:border-purple-500">
                        </div>
                    </div>
                </div>

                <!-- 2. Identitas Pihak Kesatu & Pihak Kedua (Editable) -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-5">
                    <div class="flex items-center space-x-2 border-b border-slate-800 pb-3">
                        <span class="text-emerald-400 font-bold">👥</span>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">2. Identitas Pihak Penyerah & Penerima Barang</h3>
                    </div>

                    <!-- Pihak Kesatu (PPK) -->
                    <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                        <span class="text-[11px] font-bold text-amber-400 uppercase tracking-wider block">PIHAK KESATU (Pejabat Pembuat Komitmen / Penerima Barang):</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <label class="block text-slate-400 mb-1">Nama Lengkap & Gelar</label>
                                <input type="text" x-model="formData.pihak1_nama" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white font-bold">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">NIP Pegawai</label>
                                <input type="text" x-model="formData.pihak1_nip" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white font-mono">
                            </div>
                        </div>
                        <div class="text-xs">
                            <label class="block text-slate-400 mb-1">Jabatan Resmi</label>
                            <input type="text" x-model="formData.pihak1_jabatan" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-slate-300">
                        </div>
                    </div>

                    <!-- Pihak Kedua (Default: BUDI HARTONO, S.Sos) -->
                    <div class="p-4 rounded-2xl bg-slate-950 border border-emerald-500/30 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider block">PIHAK KEDUA (Pengurus Barang Aset RSUD):</span>
                            <span class="text-[10px] bg-emerald-500/20 text-emerald-300 px-2.5 py-0.5 rounded-full font-bold border border-emerald-500/30">Default: Budi Hartono</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <label class="block text-slate-400 mb-1">Nama Pengurus Barang</label>
                                <input type="text" x-model="formData.pihak2_nama" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-emerald-400 font-bold focus:border-emerald-500">
                            </div>
                            <div>
                                <label class="block text-slate-400 mb-1">NIP Pengurus Barang</label>
                                <input type="text" x-model="formData.pihak2_nip" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-white font-mono focus:border-emerald-500">
                            </div>
                        </div>
                        <div class="text-xs">
                            <label class="block text-slate-400 mb-1">Jabatan Resmi</label>
                            <input type="text" x-model="formData.pihak2_jabatan" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-slate-300 focus:border-emerald-500">
                        </div>
                    </div>

                </div>

            </div>

            <!-- Kolom Kanan: Rincian 8 Kategori Aset (1 Kolom) -->
            <div class="space-y-6">
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-cyan-400 font-bold">📦</span>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">3. Rekap 8 Kategori Aset</h3>
                        </div>
                    </div>

                    <div class="space-y-2.5 text-xs max-h-[480px] overflow-y-auto pr-1">
                        <template x-for="(item, idx) in formData.items" :key="item.no">
                            <div class="p-3 rounded-2xl bg-slate-950 border border-slate-800 space-y-2">
                                <div class="flex items-center justify-between font-bold text-white">
                                    <span x-text="item.no + ' ' + item.nama"></span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] text-slate-400 mb-0.5">Jumlah (Qty)</label>
                                        <input type="number" x-model.number="item.qty" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-cyan-400 font-mono font-bold text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-slate-400 mb-0.5">Nilai Perolehan (Rp)</label>
                                        <input type="number" x-model.number="item.nilai" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-amber-300 font-mono font-bold text-xs">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Total Summary Strip -->
                    <div class="p-4 rounded-2xl bg-purple-950/40 border border-purple-500/30 space-y-2 text-xs">
                        <div class="flex justify-between items-center text-slate-300">
                            <span>Total Volume Barang:</span>
                            <span class="font-mono font-extrabold text-cyan-300 text-sm" x-text="totalQty + ' Barang'"></span>
                        </div>
                        <div class="flex justify-between items-center text-slate-300 pt-1 border-t border-purple-500/20">
                            <span>Total Nilai Perolehan:</span>
                            <span class="font-mono font-extrabold text-amber-300 text-sm" x-text="'Rp ' + formatRupiah(totalNilai)"></span>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</x-layout>
