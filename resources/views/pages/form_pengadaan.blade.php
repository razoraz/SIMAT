<x-layout :title="request()->routeIs('pengadaan.edit') ? 'Ubah Pengadaan ASTAP - SIMAT-RK' : 'Tambah Pengadaan Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('pengadaan.edit') ? 'Ubah Pengadaan ASTAP' : 'Tambah Pengadaan Baru')
    @section('breadcrumb', request()->routeIs('pengadaan.edit') ? 'Master Utama / Pengadaan ASTAP / Ubah' : 'Master Utama / Pengadaan ASTAP / Tambah Baru')

    <div x-data="{
        isEdit: {{ request()->routeIs('pengadaan.edit') ? 'true' : 'false' }},
        formData: {
            kode: 'PGD-2026-001',
            nama: 'Pengadaan Alat Medis ICU & Radiologi',
            program_kode: '0.00.01',
            program_nama: 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/kota',
            kegiatan_kode: '0.00.01.2.10',
            kegiatan_nama: 'Peningkatan Pelayanan BLUD',
            sub_kegiatan_kode: '0.00.01.2.10.0002',
            sub_kegiatan_nama: 'Pengadaan Sarana dan Prasarana Pendukung Faskes',
            sumber_dana: 'DAK Kesehatan',
            tahun: '2026',
            nilai: 'Rp 1.450.000.000',
            rekanan: 'PT. Medika Sejahtera Jaya',
            tgl_kontrak: '2026-02-10',
            status: 'Selesai',
            keterangan: 'Pengadaan paket ventilator dan patient monitor unit ICU'
        },

        init() {
            if (!this.isEdit) {
                this.formData = {
                    kode: 'PGD-2026-' + String(Math.floor(Math.random() * 900) + 100),
                    nama: '',
                    program_kode: '0.00.01',
                    program_nama: 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/kota',
                    kegiatan_kode: '0.00.01.2.10',
                    kegiatan_nama: 'Peningkatan Pelayanan BLUD',
                    sub_kegiatan_kode: '0.00.01.2.10.0001',
                    sub_kegiatan_nama: 'Pelayanan dan Penunjang Pelayanan BLUD',
                    sumber_dana: 'BLUD RSUD Dr. H. Koesnandi',
                    tahun: '2026',
                    nilai: '',
                    rekanan: '',
                    tgl_kontrak: new Date().toISOString().split('T')[0],
                    status: 'Proses Lelang',
                    keterangan: ''
                };
            }
        },

        submitForm() {
            alert('✅ Paket Pengadaan (' + this.formData.nama + ') berhasil disimpan ke database!');
            window.location.href = '{{ route('pengadaan.index') }}';
        }
    }" x-cloak class="space-y-6">

        <!-- Top Navigation Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <a href="{{ route('pengadaan.index') }}" 
                   class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-[10px] font-bold mb-1">
                        <span x-text="isEdit ? '✏️ UBAH PAKET PENGADAAN' : '🛒 FORM PENGADAAN ASTAP (SIPD)'"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight" x-text="isEdit ? 'Ubah Paket: ' + formData.nama : 'Input Pengadaan Barang & Jasa ASTAP'"></h1>
                </div>
            </div>

            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <a href="{{ route('pengadaan.index') }}" 
                   class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()"
                        class="px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Pengadaan'"></span>
                </button>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl max-w-4xl space-y-6">
            
            <!-- Identitas Pokok Pengadaan -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                    <span>1. Identitas Paket Pengadaan</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nomor Registrasi Pengadaan</label>
                        <input type="text" x-model="formData.kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-cyan-400 font-mono font-bold">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tahun Anggaran</label>
                        <input type="text" x-model="formData.tahun" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-200 font-bold text-xs mb-1.5">Nama Paket Pengadaan Barang / Jasa</label>
                    <input type="text" x-model="formData.nama" placeholder="Contoh: Pengadaan Alat Kesehatan ICU & Radiologi..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <!-- 3 Tingkatan Hierarki SIPD -->
            <div class="p-5 rounded-2xl bg-slate-950 border border-slate-800 space-y-4">
                <h3 class="text-sm font-bold text-emerald-400 uppercase tracking-wider flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>2. Hierarki Penganggaran SIPD RSUD</span>
                </h3>

                <!-- Program SIPD -->
                <div class="p-3.5 rounded-xl bg-emerald-950/20 border border-emerald-500/30 space-y-2">
                    <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider block">Program Pengadaan SIPD</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                        <div>
                            <input type="text" x-model="formData.program_kode" placeholder="0.00.01" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-emerald-400 font-mono font-bold">
                        </div>
                        <div class="sm:col-span-2">
                            <input type="text" x-model="formData.program_nama" placeholder="Nama Program SIPD..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold">
                        </div>
                    </div>
                </div>

                <!-- Kegiatan SIPD -->
                <div class="p-3.5 rounded-xl bg-amber-950/20 border border-amber-500/30 space-y-2">
                    <span class="text-[10px] font-bold text-amber-400 uppercase tracking-wider block">Kegiatan Pengadaan SIPD</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                        <div>
                            <input type="text" x-model="formData.kegiatan_kode" placeholder="0.00.01.2.10" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-400 font-mono font-bold">
                        </div>
                        <div class="sm:col-span-2">
                            <input type="text" x-model="formData.kegiatan_nama" placeholder="Nama Kegiatan Pengadaan..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold">
                        </div>
                    </div>
                </div>

                <!-- Sub Kegiatan SIPD -->
                <div class="p-3.5 rounded-xl bg-purple-950/20 border border-purple-500/30 space-y-2">
                    <span class="text-[10px] font-bold text-purple-400 uppercase tracking-wider block">Sub Kegiatan Pengadaan SIPD</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                        <div>
                            <input type="text" x-model="formData.sub_kegiatan_kode" placeholder="0.00.01.2.10.0001" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-purple-400 font-mono font-bold">
                        </div>
                        <div class="sm:col-span-2">
                            <input type="text" x-model="formData.sub_kegiatan_nama" placeholder="Nama Sub Kegiatan..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Nilai & Vendor -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                    <span>3. Anggaran & Rekanan Vendor</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Sumber Dana / Anggaran</label>
                        <select x-model="formData.sumber_dana" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                            <option>BLUD RSUD Dr. H. Koesnandi</option>
                            <option>APBD Kab. Bondowoso</option>
                            <option>DAK Kesehatan</option>
                            <option>Hibah Pemerintah</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-emerald-400 font-bold text-xs mb-1.5">Total Nilai Anggaran Kontrak (Rp)</label>
                        <input type="text" x-model="formData.nilai" placeholder="Contoh: Rp 500.000.000" class="w-full bg-slate-950 border border-emerald-500/40 rounded-xl px-4 py-3 text-xs text-emerald-300 font-mono font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nama Perusahaan Rekanan / Vendor</label>
                        <input type="text" x-model="formData.rekanan" placeholder="PT. / CV...." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tanggal Penandatanganan Kontrak</label>
                        <input type="date" x-model="formData.tgl_kontrak" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Status Pengadaan</label>
                    <select x-model="formData.status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                        <option value="Selesai">Selesai</option>
                        <option value="Proses Lelang">Proses Lelang</option>
                        <option value="Pelaksanaan Pekerjaan">Pelaksanaan Pekerjaan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Keterangan / Uraian Tambahan</label>
                    <textarea x-model="formData.keterangan" rows="3" placeholder="Uraian rincian pekerjaan atau spesifikasi pengadaan..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white"></textarea>
                </div>
            </div>

            <!-- Submit Button Footer -->
            <div class="pt-6 border-t border-slate-800 flex justify-end space-x-3">
                <a href="{{ route('pengadaan.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 text-slate-300 font-semibold text-xs hover:bg-slate-700 transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()" class="px-6 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-cyan-500/20 transition-all">
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Pengadaan'"></span>
                </button>
            </div>

        </div>

    </div>
</x-layout>
