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
            kepala: '{{ $unit->kepala ?? "" }}',
            nip: '{{ $unit->nip ?? "" }}',
            email: '{{ $unit->email ?? "" }}'
        },

        init() {
            if (!this.isEdit && !this.formData.nama) {
                this.formData = {
                    kode_unit: '{{ $nextKode ?? "UNIT-056" }}',
                    nama: '',
                    kepala: '',
                    nip: '',
                    email: ''
                };
            }
        },

        showConfirmModal: false,
        confirmData: {
            title: 'Konfirmasi Tindakan',
            message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
            itemName: '',
            type: 'success',
            btnText: 'Ya, Lanjutkan',
            onConfirm: null
        },

        toast: { show: false, message: '', type: 'success' },

        askConfirmation({ title, message, itemName, type = 'success', btnText, onConfirm }) {
            this.confirmData = {
                title: title || 'Konfirmasi Tindakan',
                message: message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                itemName: itemName || '',
                type: type,
                btnText: btnText || (type === 'danger' ? 'Ya, Hapus Data' : (type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Simpan Unit')),
                onConfirm: onConfirm
            };
            this.showConfirmModal = true;
        },

        executeConfirmedAction() {
            if (typeof this.confirmData.onConfirm === 'function') {
                this.confirmData.onConfirm();
            }
            this.showConfirmModal = false;
        },

        submitForm() {
            if (!this.formData.nama || !this.formData.kepala) {
                this.toast = { show: true, message: '⚠️ Mohon lengkapi Nama Unit dan Nama Kepala Ruangan.', type: 'warning' };
                setTimeout(() => { this.toast.show = false; }, 4000);
                return;
            }

            const titleAction = this.isEdit ? '✏️ Konfirmasi Simpan Perubahan Unit' : '🏥 Konfirmasi Registrasi Unit Baru';
            const msgAction = this.isEdit ? 'Apakah Anda yakin ingin menyimpan perubahan data unit / paviliun ini?' : 'Apakah Anda yakin ingin mendaftarkan unit / ruangan baru ini? Akun login Sub Admin akan otomatis dibuatkan.';
            const btnAction = this.isEdit ? '✏️ Ya, Simpan Perubahan' : '🏥 Ya, Daftarkan Unit';
            const typeAction = this.isEdit ? 'warning' : 'success';

            this.askConfirmation({
                title: titleAction,
                message: msgAction,
                itemName: (this.formData.nama || 'Unit Ruangan') + ' (' + (this.formData.kode_unit || 'KODE') + ')',
                type: typeAction,
                btnText: btnAction,
                onConfirm: async () => {
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
                            this.toast = { show: true, message: '✅ ' + result.message, type: 'success' };
                            setTimeout(() => {
                                window.location.href = '{{ route("unit.index") }}';
                            }, 1200);
                        } else {
                            this.toast = { show: true, message: '❌ Gagal menyimpan unit: ' + (result.message || 'Terjadi kesalahan'), type: 'error' };
                        }
                    } catch (err) {
                        this.toast = { show: true, message: '❌ Terjadi kesalahan koneksi saat menyimpan data.', type: 'error' };
                        console.error(err);
                    } finally {
                        this.isSaving = false;
                    }
                }
            });
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
            
            <!-- Baris 1: Kode Unit -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-slate-300 font-semibold text-xs">Kode Identitas Unit / Paviliun</label>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-blue-500/20 text-blue-300 border border-blue-500/30">Otomatis UNIT-XXX</span>
                </div>
                <div class="relative">
                    <input type="text" x-model="formData.kode_unit" readonly class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-4 py-3 text-xs text-blue-400 font-mono font-extrabold focus:outline-none cursor-not-allowed select-all" placeholder="UNIT-001">
                    <span class="absolute right-3.5 top-3 text-[10px] text-slate-500 font-medium">3 Digit Nomor Urut</span>
                </div>
                <p class="text-[10px] text-slate-500 mt-1">Dibuat otomatis dengan format <code class="text-blue-400 font-mono">UNIT-(3 digit angka)</code> sesuai urutan database.</p>
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

        <!-- GLOBAL CUSTOM CONFIRMATION DIALOG MODAL (Sleek Dark Theme) -->
        <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
            <div @click.away="showConfirmModal = false"
                 x-show="showConfirmModal"
                 x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-slate-900 border rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl space-y-4 relative overflow-hidden"
                 :class="{
                     'border-rose-500/40': confirmData.type === 'danger',
                     'border-amber-500/40': confirmData.type === 'warning',
                     'border-emerald-500/40': confirmData.type === 'success',
                     'border-cyan-500/40': confirmData.type === 'info'
                 }">
                
                <!-- Subtle Top Accent Strip -->
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r"
                     :class="confirmData.type === 'danger' ? 'from-rose-500 to-red-600' : (confirmData.type === 'warning' ? 'from-amber-500 to-orange-500' : (confirmData.type === 'success' ? 'from-emerald-500 to-teal-500' : 'from-blue-500 to-cyan-500'))">
                </div>

                <!-- Header Icon & Title -->
                <div class="flex items-start space-x-3.5 pt-1">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 border shadow-inner"
                         :class="{
                             'bg-rose-500/20 text-rose-400 border-rose-500/30': confirmData.type === 'danger',
                             'bg-amber-500/20 text-amber-300 border-amber-500/30': confirmData.type === 'warning',
                             'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': confirmData.type === 'success',
                             'bg-cyan-500/20 text-cyan-300 border-cyan-500/30': confirmData.type === 'info'
                         }">
                        <!-- Delete Trash Icon -->
                        <template x-if="confirmData.type === 'danger'">
                            <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </template>
                        <!-- Warning Icon -->
                        <template x-if="confirmData.type === 'warning'">
                            <svg class="w-6 h-6 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </template>
                        <!-- Success Check Icon -->
                        <template x-if="confirmData.type === 'success'">
                            <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </template>
                        <!-- Info Icon -->
                        <template x-if="confirmData.type === 'info'">
                            <svg class="w-6 h-6 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </template>
                    </div>
                    <div class="space-y-1 min-w-0 flex-1">
                        <h3 class="text-base sm:text-lg font-black text-white leading-snug tracking-tight" x-text="confirmData.title"></h3>
                        <p class="text-slate-300 text-xs sm:text-sm leading-relaxed" x-text="confirmData.message"></p>
                    </div>
                </div>

                <!-- Item Target Preview Card -->
                <template x-if="confirmData.itemName">
                    <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800/90 space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Item Target:</span>
                        <p class="text-xs sm:text-sm font-bold text-cyan-300 truncate font-mono" x-text="confirmData.itemName"></p>
                    </div>
                </template>

                <!-- Footer Action Buttons (Persis sesuai foto referensi) -->
                <div class="pt-3 border-t border-slate-800/90 flex items-center justify-end space-x-2.5">
                    <button type="button" @click="showConfirmModal = false"
                        class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-xs sm:text-sm border border-slate-700 transition-all active:scale-95 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="executeConfirmedAction()"
                        class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm shadow-lg transition-all active:scale-95 cursor-pointer flex items-center space-x-2"
                        :class="{
                            'bg-rose-600 hover:bg-rose-500 text-white shadow-rose-600/30 border border-rose-500/30': confirmData.type === 'danger',
                            'bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-amber-500/30 border border-amber-400/30': confirmData.type === 'warning',
                            'bg-emerald-600 hover:bg-emerald-500 text-white shadow-emerald-600/30 border border-emerald-500/30': confirmData.type === 'success',
                            'bg-cyan-600 hover:bg-cyan-500 text-white shadow-cyan-600/30 border border-cyan-500/30': confirmData.type === 'info'
                        }">
                        <template x-if="confirmData.type === 'danger'">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </template>
                        <span x-text="confirmData.btnText"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- GLOBAL FLOATING TOAST NOTIFICATION POPUP -->
        <div x-show="toast.show" x-cloak
             x-transition:enter="transition ease-out duration-300 transform opacity-0 translate-y-4 scale-95"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform opacity-100 translate-y-0 scale-100"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="fixed bottom-6 right-6 z-50 max-w-sm w-full bg-slate-900/95 border rounded-2xl p-4 shadow-2xl backdrop-blur-md flex items-center justify-between space-x-3"
             :class="{
                 'border-emerald-500/40 text-emerald-300': toast.type === 'success',
                 'border-rose-500/40 text-rose-300': toast.type === 'error',
                 'border-amber-500/40 text-amber-300': toast.type === 'warning',
                 'border-cyan-500/40 text-cyan-300': toast.type === 'info'
             }">
            <div class="flex items-center space-x-2.5 min-w-0">
                <span class="text-base shrink-0" x-text="toast.type === 'success' ? '✅' : (toast.type === 'error' ? '⚠️' : 'ℹ️')"></span>
                <p class="text-xs font-bold leading-snug truncate" x-text="toast.message"></p>
            </div>
            <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-white text-base font-bold shrink-0">&times;</button>
        </div>

    </div>
</x-layout>
