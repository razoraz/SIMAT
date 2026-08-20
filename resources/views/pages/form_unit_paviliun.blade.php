<x-layout :title="request()->routeIs('unit.edit') ? 'Ubah Unit / Paviliun - SIMAT-RK' : 'Tambah Unit Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('unit.edit') ? 'Ubah Unit / Paviliun' : 'Tambah Unit Baru')
    @section('breadcrumb', request()->routeIs('unit.edit') ? 'Master Utama / Unit & Paviliun / Ubah' : 'Master Utama / Unit & Paviliun / Tambah Baru')

    <div x-data="{
        isEdit: {{ request()->routeIs('unit.edit') ? 'true' : 'false' }},
        unitId: {{ $id ?? ($unit->id ?? 'null') }},
        isSaving: false,
        csrfToken: '{{ csrf_token() }}',
        
        formData: {
            kode_unit: '{{ $unit->kode_unit ?? ($nextKode ?? "UNIT-056") }}',
            nama: '{{ $unit->nama ?? "" }}',
            tipe: '{{ $unit->tipe ?? "Rawat Inap & Paviliun" }}',
            kepala: '{{ $unit->kepala ?? "" }}',
            nip: '{{ $unit->nip ?? "" }}',
            email: '{{ $unit->email ?? "" }}'
        },

        init() {
            if (!this.isEdit && !this.formData.nama) {
                this.formData = {
                    kode_unit: '{{ $nextKode ?? "UNIT-056" }}',
                    nama: '',
                    tipe: 'Rawat Inap & Paviliun',
                    kepala: '',
                    nip: '',
                    email: ''
                };
            }
        },

        async submitForm() {
            if (!this.formData.nama || !this.formData.kepala) {
                alert('⚠️ Mohon lengkapi Nama Unit dan Nama Kepala Ruangan.');
                return;
            }

            this.isSaving = true;
            const url = this.isEdit ? `/unit-paviliun/${this.unitId}` : '/unit-paviliun';
            const method = this.isEdit ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify(this.formData)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    alert('✅ ' + result.message);
                    window.location.href = '{{ route("unit.index") }}';
                } else {
                    alert('❌ Gagal menyimpan unit: ' + (result.message || 'Terjadi kesalahan'));
                }
            } catch (err) {
                alert('❌ Terjadi kesalahan koneksi saat menyimpan data.');
                console.error(err);
            } finally {
                this.isSaving = false;
            }
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
                        <span x-text="isEdit ? '✏️ UBAH DATA UNIT / RUANGAN' : '🏥 REGISTER UNIT & RUANGAN BARU'"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight" x-text="isEdit ? 'Ubah: ' + (formData.nama || 'Unit Ruangan') : 'Pendaftaran Unit, Paviliun & Instalasi RSUD'"></h1>
                    <p class="text-xs text-slate-400 mt-0.5">Setiap unit baru yang didaftarkan akan otomatis dibuatkan akun login Sub Admin ruangan</p>
                </div>
            </div>

            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <a href="{{ route('unit.index') }}" 
                   class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()" :disabled="isSaving"
                        class="px-5 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-1.5 active:scale-95 disabled:opacity-50">
                    <svg x-show="!isSaving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="isSaving ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Simpan Unit Baru')"></span>
                </button>
            </div>
        </div>

        <!-- Banner Info Auto-Create Sub Admin -->
        <div class="p-4 rounded-3xl bg-blue-500/10 border border-blue-500/30 flex items-start space-x-3 text-xs text-blue-200">
            <span class="text-lg">💡</span>
            <div>
                <span class="font-bold text-white block mb-0.5">Otomatisasi Akun Sub Admin RSUD:</span>
                <span>Ketika Anda menyimpan unit ini, sistem otomatis mendaftarkan akun <strong>Sub Admin</strong> untuk Kepala Ruangan dengan username/email unit dan password default <code class="bg-blue-950 px-1.5 py-0.5 rounded text-cyan-300 font-mono">rsud123</code>. Total Aset unit default <strong>0 item</strong> hingga didistribusikan.</span>
            </div>
        </div>

        <!-- Form Card Utama -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl w-full space-y-5">
            
            <!-- Baris 1: Kode Unit & Tipe Klasifikasi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kode Identitas Unit / Paviliun</label>
                    <input type="text" x-model="formData.kode_unit" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-blue-400 font-mono font-bold focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tipe Klasifikasi Ruangan</label>
                    <select x-model="formData.tipe" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-blue-500">
                        <option value="Rawat Inap & Paviliun">Rawat Inap & Paviliun</option>
                        <option value="Pelayanan Kritis & Tindakan Medis">Pelayanan Kritis & Tindakan Medis</option>
                        <option value="Penunjang Medis & Fasilitas">Penunjang Medis & Fasilitas</option>
                        <option value="Pelayanan Umum & Operasional">Pelayanan Umum & Operasional</option>
                        <option value="Manajemen & Struktural">Manajemen & Struktural</option>
                    </select>
                </div>
            </div>

            <!-- Baris 2: Nama Unit -->
            <div>
                <label class="block text-slate-200 font-bold text-xs mb-1.5">Nama Unit / Paviliun / Ruangan <span class="text-rose-400">*</span></label>
                <input type="text" x-model="formData.nama" required placeholder="Contoh: Paviliun Graha Amukti..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-blue-500">
            </div>

            <!-- Baris 3: Kepala Ruangan & NIP -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nama Kepala Ruangan / Penanggung Jawab Unit <span class="text-rose-400">*</span></label>
                    <input type="text" x-model="formData.kepala" required placeholder="Nama lengkap & gelar (misal: dr... / Ns...)" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">NIP Kepala Ruangan</label>
                    <input 
                        type="text" 
                        inputmode="numeric"
                        x-model="formData.nip"
                        @input="formData.nip = $event.target.value.replace(/[^0-9]/g, '')"
                        placeholder="198805122012012004" 
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-mono focus:outline-none focus:border-blue-500"
                    >
                </div>
            </div>

            <!-- Baris 4: Email Akun Login Sub Admin -->
            <div>
                <label class="block text-slate-300 font-semibold text-xs mb-1.5">Email Kredensial Login Sub Admin</label>
                <input type="email" x-model="formData.email" placeholder="unit@rsudkoesnandi.id (kosongkan untuk dibuatkan otomatis)" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-cyan-400 font-mono focus:outline-none focus:border-blue-500">
            </div>

            <!-- Tombol Aksi Simpan -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-end space-x-3">
                <a href="{{ route('unit.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()" :disabled="isSaving"
                        class="px-6 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-1.5 active:scale-95 disabled:opacity-50">
                    <svg x-show="!isSaving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="isSaving ? 'Menyimpan...' : (isEdit ? 'Simpan Perubahan' : 'Simpan Unit Baru')"></span>
                </button>
            </div>
        </div>

    </div>
</x-layout>
