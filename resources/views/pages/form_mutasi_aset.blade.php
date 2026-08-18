<x-layout :title="request()->routeIs('mutasi.edit') ? 'Ubah Pengajuan Mutasi - SIMAT-RK' : 'Pengajuan Mutasi Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('mutasi.edit') ? 'Ubah Pengajuan Mutasi' : 'Pengajuan Mutasi Baru')
    @section('breadcrumb', request()->routeIs('mutasi.edit') ? 'Master Utama / Mutasi Aset / Ubah' : 'Master Utama / Mutasi Aset / Pengajuan Baru')

    <div x-data="{
        isEdit: {{ request()->routeIs('mutasi.edit') ? 'true' : 'false' }},
        formData: {
            kode: 'MTS-2026-002',
            nama: 'Bed Pasien Crank Manual (3 Unit)',
            asal: 'Ruang Rawat Inap Melati',
            tujuan: 'Paviliun Graha Amukti',
            tgl: new Date().toISOString().split('T')[0],
            pemohon: 'dr. H. Rahmat Hidayat, Sp.PD',
            status: 'Disetujui',
            keterangan: 'Penambahan kapasitas ranjang cadangan ruang isolasi VIP'
        },

        init() {
            if (!this.isEdit) {
                this.formData = {
                    kode: 'MTS-2026-' + String(Math.floor(Math.random() * 900) + 100),
                    nama: '',
                    asal: 'Gudang Inventaris Pusat',
                    tujuan: 'Paviliun Graha Amukti',
                    tgl: new Date().toISOString().split('T')[0],
                    pemohon: '',
                    status: 'Menunggu Persetujuan',
                    keterangan: ''
                };
            }
        },

        submitForm() {
            alert('✅ Pengajuan Mutasi (' + this.formData.nama + ') berhasil disimpan!');
            window.location.href = '{{ route('mutasi.index') }}';
        }
    }" x-cloak class="space-y-6">

        <!-- Top Navigation Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <a href="{{ route('mutasi.index') }}" 
                   class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[10px] font-bold mb-1">
                        <span x-text="isEdit ? '✏️ UBAH PENGAJUAN MUTASI' : '🔄 PENGAJUAN MUTASI RUANGAN'"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight" x-text="isEdit ? 'Ubah Mutasi: ' + formData.nama : 'Pengajuan Pemindahan / Mutasi Aset'"></h1>
                </div>
            </div>

            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <a href="{{ route('mutasi.index') }}" 
                   class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()"
                        class="px-5 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-rose-500/20 transition-all flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Kirim Pengajuan Mutasi'"></span>
                </button>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl max-w-4xl space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nomor Registrasi Mutasi</label>
                    <input type="text" x-model="formData.kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-rose-400 font-mono font-bold">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tanggal Pengajuan</label>
                    <input type="date" x-model="formData.tgl" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                </div>
            </div>

            <div>
                <label class="block text-slate-200 font-bold text-xs mb-1.5">Nama Barang ASTAP yang Dimutasi</label>
                <input type="text" x-model="formData.nama" placeholder="Pilih atau cari barang aset..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-rose-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Lokasi / Ruangan Asal</label>
                    <input type="text" x-model="formData.asal" placeholder="Ruangan asal..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                </div>
                <div>
                    <label class="block text-rose-400 font-bold text-xs mb-1.5">Lokasi / Ruangan Tujuan Mutasi</label>
                    <input type="text" x-model="formData.tujuan" placeholder="Ruangan tujuan..." class="w-full bg-slate-950 border border-rose-500/40 rounded-xl px-4 py-3 text-xs text-rose-200 font-bold focus:outline-none focus:border-rose-400">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nama Pemohon Mutasi</label>
                    <input type="text" x-model="formData.pemohon" placeholder="Nama Kepala Ruangan / Pegawai..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Status Persetujuan</label>
                    <select x-model="formData.status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                        <option value="Disetujui">Disetujui</option>
                        <option value="Menunggu Persetujuan">Menunggu Persetujuan</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-slate-300 font-semibold text-xs mb-1.5">Alasan Pemindahan / Urgensi Mutasi</label>
                <textarea x-model="formData.keterangan" rows="3" placeholder="Alasan pemindahan aset..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white"></textarea>
            </div>

            <div class="pt-6 border-t border-slate-800 flex items-center justify-end space-x-3">
                <a href="{{ route('mutasi.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()" class="px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-rose-500/20 transition-all">
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Kirim Pengajuan Mutasi'"></span>
                </button>
            </div>
        </div>

    </div>
</x-layout>
