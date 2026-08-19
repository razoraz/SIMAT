<x-layout :title="request()->routeIs('pemeliharaan.edit') ? 'Ubah Log Pemeliharaan - SIMAT-RK' : 'Catat Pemeliharaan Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('pemeliharaan.edit') ? 'Ubah Log Pemeliharaan' : 'Catat Pemeliharaan Baru')
    @section('breadcrumb', request()->routeIs('pemeliharaan.edit') ? 'Master Utama / Pemeliharaan / Ubah' : 'Master Utama / Pemeliharaan / Catat Baru')

    <div x-data="{
        isEdit: {{ request()->routeIs('pemeliharaan.edit') ? 'true' : 'false' }},
        formData: {
            kode: 'MTN-2026-003',
            nama: 'CT-Scan 128 Slice Siemens SOMATOM',
            jenis: 'Kalibrasi Rutin & QC BAPETEN',
            tgl: new Date().toISOString().split('T')[0],
            biaya: 'Rp 25.000.000',
            pelaksana: 'PT. Siemens Healthcare Indonesia',
            status: 'Selesai',
            keterangan: 'Hasil uji fungsi akurat dan sertifikat kalibrasi terbit resmi'
        },

        init() {
            if (!this.isEdit) {
                this.formData = {
                    kode: 'MTN-2026-' + String(Math.floor(Math.random() * 900) + 100),
                    nama: '',
                    jenis: 'Servis Berkala & Maintenance',
                    tgl: new Date().toISOString().split('T')[0],
                    biaya: '',
                    pelaksana: 'Teknisi IPSRS RSUD',
                    status: 'Dalam Pengerjaan',
                    keterangan: ''
                };
            }
        },

        submitForm() {
            alert('✅ Log Pemeliharaan (' + this.formData.nama + ') berhasil disimpan!');
            window.location.href = '{{ route('pemeliharaan.index') }}';
        }
    }" x-cloak class="space-y-6">

        <!-- Top Navigation Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <a href="{{ route('pemeliharaan.index') }}" 
                   class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-bold mb-1">
                        <span x-text="isEdit ? '✏️ UBAH LOG SERVIS' : '🛠️ LOG PEMELIHARAAN BARU'"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight" x-text="isEdit ? 'Ubah Log Servis: ' + formData.nama : 'Catat Pemeliharaan & Kalibrasi Aset'"></h1>
                </div>
            </div>

            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <a href="{{ route('pemeliharaan.index') }}" 
                   class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()"
                        class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-amber-500/20 transition-all flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Log Pemeliharaan'"></span>
                </button>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl  w-full space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nomor Registrasi Servis</label>
                    <input type="text" x-model="formData.kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-amber-400 font-mono font-bold">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tanggal Pelaksanaan</label>
                    <input type="date" x-model="formData.tgl" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                </div>
            </div>

            <div>
                <label class="block text-slate-200 font-bold text-xs mb-1.5">Nama Aset yang Dipelihara / Diperbaiki</label>
                <input type="text" x-model="formData.nama" placeholder="Pilih atau ketik nama aset..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-amber-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Jenis Tindakan Pemeliharaan</label>
                    <input type="text" x-model="formData.jenis" placeholder="Contoh: Kalibrasi Tahunan / Ganti Sparepart..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                </div>
                <div>
                    <label class="block text-emerald-400 font-bold text-xs mb-1.5">Biaya Pemeliharaan (Rp)</label>
                    <input type="text" x-model="formData.biaya" placeholder="Contoh: Rp 5.000.000" class="w-full bg-slate-950 border border-emerald-500/40 rounded-xl px-4 py-3 text-xs text-emerald-300 font-mono font-bold">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Teknisi Pelaksana / Vendor Rekanan</label>
                    <input type="text" x-model="formData.pelaksana" placeholder="IPSRS RSUD / PT. Vendor..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Status Servis</label>
                    <select x-model="formData.status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                        <option value="Selesai">Selesai</option>
                        <option value="Dalam Pengerjaan">Dalam Pengerjaan</option>
                        <option value="Menunggu Sparepart">Menunggu Sparepart</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-slate-300 font-semibold text-xs mb-1.5">Catatan Hasil Pengerjaan / Uji Fungsi</label>
                <textarea x-model="formData.keterangan" rows="3" placeholder="Uraian perbaikan..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white"></textarea>
            </div>
        </div>

    </div>
</x-layout>
