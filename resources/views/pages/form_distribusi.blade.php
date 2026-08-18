<x-layout :title="request()->routeIs('distribusi.edit') ? 'Ubah Distribusi ASTAP - SIMAT-RK' : 'Input Distribusi Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('distribusi.edit') ? 'Ubah Distribusi ASTAP' : 'Input Distribusi Baru')
    @section('breadcrumb', request()->routeIs('distribusi.edit') ? 'Master Utama / Distribusi ASTAP / Ubah' : 'Master Utama / Distribusi ASTAP / Input Baru')

    <div x-data="{
        isEdit: {{ request()->routeIs('distribusi.edit') ? 'true' : 'false' }},
        formData: {
            kode: 'DST-2026-004',
            nama: 'Bed Patient Electric 3 Crank',
            tujuan: 'Paviliun Graha Amukti',
            tgl: new Date().toISOString().split('T')[0],
            penerima: 'Siti Aminah, A.Md.Kep',
            status: 'Telah Diterima',
            bast_nomor: 'BAST-2026-044',
            keterangan: 'Penempatan di Ruang VIP 01-04 Paviliun Graha Amukti'
        },

        init() {
            if (!this.isEdit) {
                this.formData = {
                    kode: 'DST-2026-' + String(Math.floor(Math.random() * 900) + 100),
                    nama: '',
                    tujuan: 'Paviliun Graha Amukti',
                    tgl: new Date().toISOString().split('T')[0],
                    penerima: '',
                    status: 'Dalam Pengiriman',
                    bast_nomor: 'BAST-2026-' + String(Math.floor(Math.random() * 900) + 100),
                    keterangan: ''
                };
            }
        },

        submitForm() {
            alert('✅ Distribusi Barang (' + this.formData.nama + ') berhasil disimpan!');
            window.location.href = '{{ route('distribusi.index') }}';
        }
    }" x-cloak class="space-y-6">

        <!-- Top Navigation Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <a href="{{ route('distribusi.index') }}" 
                   class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] font-bold mb-1">
                        <span x-text="isEdit ? '✏️ UBAH STATUS DISTRIBUSI' : '🚚 FORM DISTRIBUSI BARU'"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight" x-text="isEdit ? 'Ubah Distribusi: ' + formData.nama : 'Input Distribusi & Penyerahan ASTAP'"></h1>
                </div>
            </div>

            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <a href="{{ route('distribusi.index') }}" 
                   class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()"
                        class="px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Distribusi Baru'"></span>
                </button>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl max-w-4xl space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nomor Registrasi Distribusi</label>
                    <input type="text" x-model="formData.kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-teal-400 font-mono font-bold">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nomor BAST Terkait</label>
                    <input type="text" x-model="formData.bast_nomor" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-purple-300 font-mono font-bold">
                </div>
            </div>

            <div>
                <label class="block text-slate-200 font-bold text-xs mb-1.5">Nama Barang ASTAP yang Didistribusikan</label>
                <input type="text" x-model="formData.nama" placeholder="Pilih atau ketik nama barang..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-teal-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tujuan Unit / Paviliun</label>
                    <select x-model="formData.tujuan" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                        <option>Paviliun Graha Amukti</option>
                        <option>Instalasi Gawat Darurat (IGD)</option>
                        <option>Ruang Utility & Pompa Sentral</option>
                        <option>Instalasi Radiologi & Imaging</option>
                        <option>Instalasi Bedah Sentral (IBS)</option>
                        <option>Instalasi Rawat Intensif (ICU)</option>
                        <option>Instalasi Rekam Medis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tanggal Distribusi</label>
                    <input type="date" x-model="formData.tgl" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nama Pegawai Penerima (Unit)</label>
                    <input type="text" x-model="formData.penerima" placeholder="Nama lengkap & gelar..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Status Penyerahan</label>
                    <select x-model="formData.status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                        <option value="Telah Diterima">Telah Diterima</option>
                        <option value="Dalam Pengiriman">Dalam Pengiriman</option>
                        <option value="Menunggu Konfirmasi">Menunggu Konfirmasi</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-slate-300 font-semibold text-xs mb-1.5">Catatan / Keterangan Penempatan</label>
                <textarea x-model="formData.keterangan" rows="3" placeholder="Rincian ruangan atau posisi penempatan..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white"></textarea>
            </div>

            <div class="pt-6 border-t border-slate-800 flex items-center justify-end space-x-3">
                <a href="{{ route('distribusi.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()" class="px-6 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-teal-500/20 transition-all">
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Distribusi Baru'"></span>
                </button>
            </div>
        </div>

    </div>
</x-layout>
