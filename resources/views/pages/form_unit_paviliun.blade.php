<x-layout :title="request()->routeIs('unit.edit') ? 'Ubah Unit / Paviliun - SIMAT-RK' : 'Tambah Unit Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('unit.edit') ? 'Ubah Unit / Paviliun' : 'Tambah Unit Baru')
    @section('breadcrumb', request()->routeIs('unit.edit') ? 'Master Utama / Unit & Paviliun / Ubah' : 'Master Utama / Unit & Paviliun / Tambah Baru')

    <div x-data="{
        isEdit: {{ request()->routeIs('unit.edit') ? 'true' : 'false' }},
        formData: {
            kode: 'UNIT-001',
            nama: 'Paviliun Graha Amukti',
            tipe: 'Rawat Inap VIP & VVIP',
            kepala: 'dr. H. Rahmat Hidayat, Sp.PD',
            pj_aset: 'Siti Aminah, A.Md.Kep',
            kapasitas: '24 Kamar',
            keterangan: 'Fasilitas kamar rawat inap VIP dan VVIP dengan nurse station terpusat'
        },

        init() {
            if (!this.isEdit) {
                this.formData = {
                    kode: 'UNIT-' + String(Math.floor(Math.random() * 900) + 100),
                    nama: '',
                    tipe: 'Rawat Inap',
                    kepala: '',
                    pj_aset: '',
                    kapasitas: '',
                    keterangan: ''
                };
            }
        },

        submitForm() {
            alert('✅ Unit / Paviliun (' + this.formData.nama + ') berhasil disimpan!');
            window.location.href = '{{ route('unit.index') }}';
        }
    }" x-cloak class="space-y-6">

        <!-- Top Navigation Bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <a href="{{ route('unit.index') }}" 
                   class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-[10px] font-bold mb-1">
                        <span x-text="isEdit ? '✏️ UBAH DATA RUANGAN' : '🏥 REGISTER RUANGAN BARU'"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight" x-text="isEdit ? 'Ubah: ' + formData.nama : 'Pendaftaran Unit, Paviliun & Instalasi RSUD'"></h1>
                </div>
            </div>

            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <a href="{{ route('unit.index') }}" 
                   class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()"
                        class="px-5 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Unit Baru'"></span>
                </button>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-7 shadow-xl w-full space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kode Identitas Unit / Paviliun</label>
                    <input type="text" x-model="formData.kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-blue-400 font-mono font-bold focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kapasitas (Bed / Ruang / Meja)</label>
                    <input type="text" x-model="formData.kapasitas" placeholder="Contoh: 24 Kamar / 30 Bed" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-200 font-bold text-xs mb-1.5">Nama Unit / Paviliun / Ruangan</label>
                    <input type="text" x-model="formData.nama" placeholder="Contoh: Paviliun Graha Amukti..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tipe Pelayanan Ruangan</label>
                    <input type="text" x-model="formData.tipe" placeholder="Rawat Inap / Rawat Jalan / Penunjang Medis" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nama Kepala Ruangan / Instalasi</label>
                    <input type="text" x-model="formData.kepala" placeholder="dr... / Ns..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-blue-300 font-semibold text-xs mb-1.5">Penanggung Jawab Aset (PJ Ruangan)</label>
                    <input type="text" x-model="formData.pj_aset" placeholder="Nama PJ Inventaris..." class="w-full bg-slate-950 border border-blue-500/40 rounded-xl px-4 py-3 text-xs text-blue-200 font-semibold focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-slate-300 font-semibold text-xs mb-1.5">Deskripsi / Fasilitas Ruangan</label>
                <textarea x-model="formData.keterangan" rows="3" placeholder="Uraian fasilitas penunjang..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-blue-500"></textarea>
            </div>
        </div>

    </div>
</x-layout>
