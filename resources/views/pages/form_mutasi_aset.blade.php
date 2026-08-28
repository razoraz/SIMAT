<x-layout :title="isset($mutasi) ? 'Ubah Pengajuan Mutasi - SIMAT-RK' : 'Pengajuan Mutasi Baru - SIMAT-RK'">
    @section('page-title', isset($mutasi) ? 'Ubah Pengajuan Mutasi' : 'Pengajuan Mutasi Baru')
    @section('breadcrumb', isset($mutasi) ? 'Master Utama / Mutasi Aset / Ubah' : 'Master Utama / Mutasi Aset / Pengajuan Baru')

    <div x-data="{
        step: 1,
        isEdit: {{ isset($mutasi) ? 'true' : 'false' }},

        /* ---- Jenis Mutasi ---- */
        jenis_mutasi: '{{ old('jenis_mutasi', $mutasi->jenis_mutasi ?? 'Ajukan Mutasi') }}',
        jenisMutasiOptions: [
            { value: 'Ajukan Mutasi',  emoji: '🔄', label: 'Ajukan Mutasi',     desc: 'Unit asal mengajukan pemindahan/penyerahan barang miliknya ke unit tujuan', color: 'blue' },
            { value: 'Perbaikan',      emoji: '🔧', label: 'Perbaikan / Servis',   desc: 'Barang rusak dikirim ke unit/IPSRS yang bisa memperbaiki', color: 'amber' },
            { value: 'Minta Mutasi',   emoji: '📥', label: 'Minta Mutasi',      desc: 'Unit B meminta aset milik Unit A untuk dipindahkan ke Unit B', color: 'teal' },
            { value: 'Pengembalian',   emoji: '↩️', label: 'Pengembalian Barang', desc: 'Barang yang tidak dibutuhkan / selesai dipakai dikembalikan ke Pengurus Barang / Admin Aset', color: 'rose' }
        ],

        /* ---- State Ruangan & PJ ---- */
        ruangan_asal: '{{ old('ruangan_asal', $mutasi->ruangan_asal ?? '') }}',
        ruangan_tujuan: '{{ old('ruangan_tujuan', $mutasi->ruangan_tujuan ?? '') }}',
        penanggung_jawab_asal: '{{ old('penanggung_jawab_asal', $mutasi->penanggung_jawab_asal ?? '') }}',
        penanggung_jawab_tujuan: '{{ old('penanggung_jawab_tujuan', $mutasi->penanggung_jawab_tujuan ?? '') }}',

        /* ---- Autocomplete Barang ---- */
        selectedRegisterId: '{{ old('astap_register_id', $mutasi->astap_register_id ?? '') }}',
        searchBarang: '',
        showDropdown: false,
        namaBarangPreview: '',
        kondisiPreview: '',
        unitAsalPreview: '',
        kepalaAsalPreview: '',

        registers: {{ Js::from($registers) }},
        units: {{ Js::from($units) }},

        init() {
            if (this.selectedRegisterId) {
                const reg = this.registers.find(r => String(r.id) === String(this.selectedRegisterId));
                if (reg) {
                    this.searchBarang      = reg.nibar + ' — ' + reg.nama_barang;
                    this.namaBarangPreview = reg.nama_barang;
                    this.kondisiPreview    = reg.kondisi;
                    this.unitAsalPreview   = reg.unit_nama;
                    this.kepalaAsalPreview = reg.unit_kepala;
                }
            }
        },

        selectJenisMutasi(val) {
            this.jenis_mutasi = val;
            if (val === 'Perbaikan') {
                const ipsrs = this.units.find(u => (u.nama || '').toLowerCase().includes('ips') || (u.nama || '').toLowerCase().includes('sarana'));
                if (ipsrs) {
                    this.ruangan_tujuan = ipsrs.nama;
                    this.penanggung_jawab_tujuan = ipsrs.kepala || 'TEKNISI IPSRS';
                }
            } else if (val === 'Pengembalian') {
                const perbekalan = this.units.find(u => (u.nama || '').toLowerCase().includes('perbekalan') || (u.nama || '').toLowerCase().includes('rumah tangga'));
                if (perbekalan) {
                    this.ruangan_tujuan = perbekalan.nama;
                    this.penanggung_jawab_tujuan = perbekalan.kepala || 'PENGURUS BARANG / ADMIN';
                }
            }
        },

        get filteredRegisters() {
            if (!this.searchBarang) return this.registers.slice(0, 20);
            const q = this.searchBarang.toLowerCase();
            return this.registers.filter(r =>
                (r.nibar && r.nibar.toLowerCase().includes(q)) ||
                (r.nama_barang && r.nama_barang.toLowerCase().includes(q)) ||
                (r.unit_nama && r.unit_nama.toLowerCase().includes(q))
            ).slice(0, 20);
        },

        selectRegister(r) {
            this.selectedRegisterId = r.id;
            this.searchBarang       = r.nibar + ' — ' + r.nama_barang;
            this.namaBarangPreview  = r.nama_barang;
            this.kondisiPreview     = r.kondisi;
            this.unitAsalPreview    = r.unit_nama;
            this.kepalaAsalPreview  = r.unit_kepala;
            this.showDropdown       = false;

            // Auto-fill ruangan asal & penanggung jawab jika unit terdaftar
            if (r.unit_nama && r.unit_nama !== '-') {
                this.ruangan_asal = r.unit_nama;
                if (r.unit_kepala && r.unit_kepala !== '-') {
                    this.penanggung_jawab_asal = r.unit_kepala;
                } else {
                    const u = this.units.find(item => item.nama === r.unit_nama);
                    if (u) this.penanggung_jawab_asal = u.kepala;
                }
            }
        },

        clearSelectedBarang() {
            this.selectedRegisterId = '';
            this.searchBarang       = '';
            this.namaBarangPreview  = '';
            this.kondisiPreview     = '';
            this.unitAsalPreview    = '';
            this.kepalaAsalPreview  = '';
        },

        onUnitAsalChange(unitNama) {
            this.ruangan_asal = unitNama;
            const unit = this.units.find(u => u.nama === unitNama);
            if (unit && unit.kepala) {
                this.penanggung_jawab_asal = unit.kepala;
            }
        },

        onUnitTujuanChange(unitNama) {
            this.ruangan_tujuan = unitNama;
            const unit = this.units.find(u => u.nama === unitNama);
            if (unit && unit.kepala) {
                this.penanggung_jawab_tujuan = unit.kepala;
            }
        },

        /* ---- Computed ---- */
        get selectedJenis() {
            return this.jenisMutasiOptions.find(j => j.value === this.jenis_mutasi) || this.jenisMutasiOptions[0];
        },

        get labelTujuan() {
            const map = {
                'Ajukan Mutasi':   'Ruangan Tujuan (Unit Penerima Aset)',
                'Pemindahan':      'Ruangan Tujuan (Unit Penerima Aset)',
                'Perbaikan':       'Ruangan Tujuan (Unit / IPSRS yang Memperbaiki)',
                'Minta Mutasi':    'Ruangan Asal (Unit Pemilik Aset yang Diminta)',
                'Minta_Mutasi':    'Ruangan Asal (Unit Pemilik Aset yang Diminta)',
                'Pengembalian':    'Ruangan Tujuan (Pengurus Barang / Admin Aset RSUD)',
                'Penghapusan':     'Ruangan Tujuan (Pengurus Barang / Admin Aset RSUD)'
            };
            return map[this.jenis_mutasi] || 'Ruangan Tujuan';
        },

        get alasanPlaceholder() {
            const map = {
                'Ajukan Mutasi':   'Contoh: Unit asal menyerahkan/memindahkan aset ini ke unit tujuan untuk mendukung operasional...',
                'Pemindahan':      'Contoh: Unit asal menyerahkan/memindahkan aset ini ke unit tujuan untuk mendukung operasional...',
                'Perbaikan':       'Contoh: Alat mengalami gangguan fungsi / error, perlu perbaikan oleh teknisi IPSRS...',
                'Minta Mutasi':    'Contoh: Unit B membutuhkan aset milik Unit A dan mengajukan permohonan pemindahan barang...',
                'Minta_Mutasi':    'Contoh: Unit B membutuhkan aset milik Unit A dan mengajukan permohonan pemindahan barang...',
                'Pengembalian':    'Contoh: Barang tidak dibutuhkan lagi / selesai masa pakai dan dikembalikan ke Pengurus Barang / Admin Aset...',
                'Penghapusan':     'Contoh: Barang tidak dibutuhkan lagi / selesai masa pakai dan dikembalikan ke Pengurus Barang / Admin Aset...'
            };
            return map[this.jenis_mutasi] || 'Alasan pemindahan / mutasi aset...';
        },

        get isStep1Valid() {
            return !!this.jenis_mutasi;
        },

        get isStep2Valid() {
            return !!this.selectedRegisterId;
        },

        get isStep3Valid() {
            return !!this.ruangan_asal && !!this.ruangan_tujuan && (this.ruangan_asal !== this.ruangan_tujuan);
        },

        nextStep() {
            if (this.step === 1 && !this.isStep1Valid) {
                this.showToast('Silakan pilih salah satu Jenis Pengajuan Mutasi terlebih dahulu.', 'warning');
                return;
            }
            if (this.step === 2 && !this.isStep2Valid) {
                this.showToast('Silakan cari dan pilih aset dari register terlebih dahulu.', 'warning');
                return;
            }
            if (this.step === 3) {
                if (!this.ruangan_asal || !this.ruangan_tujuan) {
                    this.showToast('Ruangan Asal dan Ruangan Tujuan wajib diisi.', 'warning');
                    return;
                }
                if (this.ruangan_asal === this.ruangan_tujuan) {
                    this.showToast('Ruangan Asal dan Ruangan Tujuan tidak boleh sama!', 'warning');
                    return;
                }
            }
            if (this.step < 4) {
                this.step++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        prevStep() {
            if (this.step > 1) {
                this.step--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        goToStep(s) {
            if (s < this.step) {
                // Boleh kembali ke tahap yang sudah pernah diisi
                this.step = s;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else if (s > this.step) {
                // Tidak boleh melompat ke tahap berikutnya sebelum menekan tombol Lanjut!
                this.showToast('⚠️ Mohon selesaikan pengisian dan tekan tombol Lanjut ke Langkah ' + (this.step + 1) + ' terlebih dahulu.', 'warning');
            }
        },

        showToast(msg, type = 'warning') {
            this.toast = { show: true, message: msg, type: type };
            setTimeout(() => { this.toast.show = false; }, 4000);
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
                btnText: btnText || (type === 'danger' ? 'Ya, Hapus Data' : (type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Ajukan Mutasi')),
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

        validateBeforeSubmit(e) {
            if (this.ruangan_asal && this.ruangan_tujuan && this.ruangan_asal === this.ruangan_tujuan) {
                this.showToast('⚠️ Ruangan Asal dan Ruangan Tujuan tidak boleh sama! Silakan pilih ruangan tujuan yang berbeda.', 'warning');
                e.preventDefault();
                return false;
            }
            e.preventDefault();
            const formElement = e.target;
            const targetItem = (this.namaBarangPreview || 'Aset Mutasi') + ' (' + (this.ruangan_asal || 'Asal') + ' ➔ ' + (this.ruangan_tujuan || 'Tujuan') + ')';
            this.askConfirmation({
                title: this.isEdit ? '✏️ Konfirmasi Simpan Perubahan Mutasi' : '🔄 Konfirmasi Pengajuan Mutasi Baru',
                message: this.isEdit ? 'Apakah Anda yakin ingin menyimpan perubahan data pengajuan mutasi ini?' : 'Apakah Anda yakin ingin mengajukan mutasi aset barang ini? Pindah tangan ruangan akan diproses setelah persetujuan.',
                itemName: targetItem,
                type: this.isEdit ? 'warning' : 'success',
                btnText: this.isEdit ? '✏️ Ya, Simpan Perubahan' : '🔄 Ya, Ajukan Mutasi',
                onConfirm: () => {
                    formElement.submit();
                }
            });
            return false;
        }
    }" x-cloak class="space-y-6">

        {{-- ===== TOP HEADER ===== --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <a href="{{ route('mutasi.index') }}"
                   class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[10px] font-bold mb-1">
                        <span x-text="isEdit ? '✏️ UBAH PENGAJUAN MUTASI' : '🔄 PENGAJUAN MUTASI WIZARD (4 LANGKAH)'"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white">Form Multi-Tahap Mutasi Aset</h1>
                </div>
            </div>
            
            <div class="flex items-center space-x-2 text-xs text-slate-400 font-bold bg-slate-950 px-3.5 py-2 rounded-2xl border border-slate-800">
                <span>Tahap saat ini:</span>
                <span class="text-rose-400 font-mono text-sm font-black" x-text="'Langkah ' + step + ' dari 4'"></span>
            </div>
        </div>

        {{-- ===== MULTI-STEP STEPPER HEADER (TEMA MERAH ROSE MUTASI) ===== --}}
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xl">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
                
                <!-- Step 1 Tab -->
                <button type="button" @click="goToStep(1)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 1 ? 'bg-rose-500 text-slate-950 shadow-lg shadow-rose-500/30 font-black' : (step > 1 ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="step <= 1">1</span>
                            <span x-show="step > 1">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 1 ? 'text-rose-400 font-black' : (step > 1 ? 'text-rose-400' : 'text-slate-500')">LANGKAH 1</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Jenis Pengajuan</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 1 ? 'bg-rose-500 shadow-sm shadow-rose-500/50' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 2 Tab -->
                <button type="button" @click="goToStep(2)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 2 ? 'bg-rose-500 text-slate-950 shadow-lg shadow-rose-500/30 font-black' : (step > 2 ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="step <= 2">2</span>
                            <span x-show="step > 2">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 2 ? 'text-rose-400 font-black' : (step > 2 ? 'text-rose-400' : 'text-slate-500')">LANGKAH 2</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Pilih Aset & Tanggal</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 2 ? 'bg-rose-500 shadow-sm shadow-rose-500/50' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 3 Tab -->
                <button type="button" @click="goToStep(3)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 3 ? 'bg-rose-500 text-slate-950 shadow-lg shadow-rose-500/30 font-black' : (step > 3 ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="step <= 3">3</span>
                            <span x-show="step > 3">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 3 ? 'text-rose-400 font-black' : (step > 3 ? 'text-rose-400' : 'text-slate-500')">LANGKAH 3</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Lokasi & Alasan</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 3 ? 'bg-rose-500 shadow-sm shadow-rose-500/50' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 4 Tab -->
                <button type="button" @click="goToStep(4)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 4 ? 'bg-rose-500 text-slate-950 shadow-lg shadow-rose-500/30 font-black' : 'bg-slate-950 text-slate-500 border border-slate-800'">
                            <span>4</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 4 ? 'text-rose-400 font-black' : 'text-slate-500'">LANGKAH 4</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Review & BAMB</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 4 ? 'bg-rose-500 shadow-sm shadow-rose-500/50' : 'bg-slate-950'"></div>
                </button>

            </div>
        </div>

        @if(isset($errors) && $errors->any())
        <div class="bg-rose-500/10 border border-rose-500/30 rounded-2xl px-5 py-3 text-rose-300 text-xs font-semibold space-y-1">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ isset($mutasi) ? route('mutasi.update', $mutasi->id) : route('mutasi.store') }}" @submit="validateBeforeSubmit($event)" class="space-y-6">
            @csrf
            @if(isset($mutasi)) @method('PUT') @endif

            {{-- Field hidden jenis_mutasi --}}
            <input type="hidden" name="jenis_mutasi" :value="jenis_mutasi">
            {{-- Field hidden astap_register_id --}}
            <input type="hidden" name="astap_register_id" :value="selectedRegisterId">

            {{-- ========================================================================= --}}
            {{-- ===== STEP 1: PILIH JENIS PENGAJUAN MUTASI ===== --}}
            {{-- ========================================================================= --}}
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs font-extrabold">1</div>
                        <div>
                            <h2 class="text-base font-extrabold text-white">Langkah 1: Pilih Jenis Pengajuan Mutasi</h2>
                            <p class="text-xs text-slate-400">Pilih salah satu dari 4 opsi pengajuan mutasi di bawah ini terlebih dahulu.</p>
                        </div>
                    </div>
                    <span class="text-[11px] px-3 py-1 rounded-full bg-rose-500/10 text-rose-300 border border-rose-500/30 font-bold">Wajib Pilih 1</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <template x-for="opt in jenisMutasiOptions" :key="opt.value">
                        <button type="button" @click="selectJenisMutasi(opt.value)"
                            :class="{
                                'border-blue-500 bg-blue-500/15 ring-2 ring-blue-500/40 shadow-xl shadow-blue-500/10':   jenis_mutasi === opt.value && opt.color === 'blue',
                                'border-amber-500 bg-amber-500/15 ring-2 ring-amber-500/40 shadow-xl shadow-amber-500/10': jenis_mutasi === opt.value && opt.color === 'amber',
                                'border-teal-500 bg-teal-500/15 ring-2 ring-teal-500/40 shadow-xl shadow-teal-500/10':   jenis_mutasi === opt.value && opt.color === 'teal',
                                'border-rose-500 bg-rose-500/15 ring-2 ring-rose-500/40 shadow-xl shadow-rose-500/10':   jenis_mutasi === opt.value && opt.color === 'rose',
                                'border-slate-800 bg-slate-950/60 opacity-60 hover:opacity-100 hover:border-slate-700': jenis_mutasi !== opt.value
                            }"
                            class="relative flex items-start space-x-4 p-5 rounded-2xl border-2 text-left transition-all w-full active:scale-[0.99] cursor-pointer group">
                            
                            <!-- Selected Indicator Checkmark -->
                            <div x-show="jenis_mutasi === opt.value" class="absolute top-4 right-4 w-6 h-6 rounded-full bg-emerald-500 text-slate-950 flex items-center justify-center text-xs font-black shadow">✓</div>

                            <span class="text-2xl shrink-0 p-3 rounded-2xl bg-slate-900 border border-slate-800 group-hover:scale-110 transition-transform" x-text="opt.emoji"></span>
                            <div class="pr-6">
                                <p class="text-sm font-extrabold text-white" x-text="opt.label"></p>
                                <p class="text-xs text-slate-400 mt-1 leading-relaxed" x-text="opt.desc"></p>
                            </div>
                        </button>
                    </template>
                </div>

                {{-- Action Navigation Buttons --}}
                <div class="flex items-center justify-end pt-4 border-t border-slate-800">
                    <button type="button" @click="nextStep()"
                        class="px-6 py-3 rounded-xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-rose-500/20 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                        <span>Lanjut ke Langkah 2: Pilih Aset →</span>
                    </button>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- ===== STEP 2: PILIH BARANG ASET & TANGGAL ===== --}}
            {{-- ========================================================================= --}}
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs font-extrabold">2</div>
                        <div>
                            <h2 class="text-base font-extrabold text-white">Langkah 2: Tanggal & Data Barang Aset yang Dimutasi</h2>
                            <p class="text-xs text-slate-400">Pilih tanggal pengajuan dan cari barang aset dari inventaris register RSUD.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                    {{-- Tanggal Pengajuan --}}
                    <div class="md:col-span-4">
                        <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1.5">Tanggal Pengajuan <span class="text-rose-400">*</span></label>
                        <input type="date" name="tanggal_mutasi" required
                            value="{{ old('tanggal_mutasi', isset($mutasi) ? $mutasi->tanggal_mutasi->format('Y-m-d') : date('Y-m-d')) }}"
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-rose-500 transition-all">
                    </div>

                    {{-- Pencarian / Pilih Barang (Autocomplete) --}}
                    <div class="md:col-span-8 relative">
                        <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1.5">
                            Cari Aset dari Register (Ketik NIBAR / Nama Barang / Unit Ruangan) <span class="text-rose-400">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none text-rose-400" style="padding-left: 1.1rem;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" x-model="searchBarang"
                                @focus="showDropdown = true"
                                @input="showDropdown = true"
                                @click.outside="showDropdown = false"
                                placeholder="Ketik NIBAR, nama barang, atau nama unit..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 pr-4 text-xs text-white font-mono placeholder-slate-500 focus:outline-none focus:border-rose-500 transition-all"
                                style="padding-left: 2.85rem;">
                        </div>

                        {{-- Dropdown Hasil Pencarian --}}
                        <div x-show="showDropdown && filteredRegisters.length > 0"
                            class="absolute z-50 w-full mt-1 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto custom-scrollbar">
                            <template x-for="r in filteredRegisters" :key="r.id">
                                <button type="button" @click="selectRegister(r)"
                                    class="w-full flex items-start gap-3 px-4 py-3 hover:bg-slate-800 text-left transition-colors border-b border-slate-800/60 last:border-b-0 cursor-pointer">
                                    <div class="shrink-0 w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-bold text-white truncate" x-text="r.nama_barang"></p>
                                        <p class="text-[10px] text-rose-400 font-mono" x-text="r.nibar"></p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[9px] text-slate-400" x-text="r.unit_nama"></span>
                                            <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                                            <span class="text-[9px] font-bold"
                                                :class="{
                                                    'text-emerald-400': r.kondisi === 'Baik',
                                                    'text-amber-400':   r.kondisi === 'Kurang Baik',
                                                    'text-rose-400':    r.kondisi === 'Rusak Berat'
                                                }"
                                                x-text="'Kondisi: ' + r.kondisi"></span>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Preview Barang Dipilih --}}
                <div x-show="selectedRegisterId && namaBarangPreview"
                    class="bg-slate-950/80 border border-rose-500/30 rounded-2xl p-5 flex items-start gap-4 shadow-lg">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center shrink-0 text-xl font-bold text-rose-400">
                        📦
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <span class="text-[10px] px-2 py-0.5 rounded bg-slate-800 text-rose-300 font-mono font-bold">Aset Terpilih</span>
                            <span class="text-xs font-mono font-bold text-slate-400" x-text="searchBarang.split(' — ')[0]"></span>
                        </div>
                        <p class="text-base font-extrabold text-white mt-0.5" x-text="namaBarangPreview"></p>
                        <div class="flex items-center gap-3 mt-2 flex-wrap text-xs">
                            <span class="text-slate-400">Unit Asal/Terdaftar: <span class="text-white font-bold" x-text="unitAsalPreview || '-'"></span></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-700"></span>
                            <span class="text-slate-400">Kondisi: 
                                <span class="font-bold px-2 py-0.5 rounded-lg border text-[11px]"
                                    :class="{
                                        'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': kondisiPreview === 'Baik',
                                        'bg-amber-500/15 text-amber-300 border-amber-500/30':       kondisiPreview === 'Kurang Baik',
                                        'bg-rose-500/15 text-rose-300 border-rose-500/30':         kondisiPreview === 'Rusak Berat'
                                    }"
                                    x-text="kondisiPreview">
                                </span>
                            </span>
                        </div>
                    </div>
                    <button type="button" @click="clearSelectedBarang()"
                        class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-slate-700 text-xs font-bold transition-all shrink-0 cursor-pointer">
                        ✕ Ganti Barang
                    </button>
                </div>

                {{-- Action Navigation Buttons --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <button type="button" @click="prevStep()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all cursor-pointer">
                        ← Kembali ke Langkah 1
                    </button>
                    <button type="button" @click="nextStep()"
                        class="px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-rose-500/20 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                        <span>Lanjut ke Langkah 3: Lokasi & Alasan →</span>
                    </button>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- ===== STEP 3: LOKASI, PENANGGUNG JAWAB & ALASAN ===== --}}
            {{-- ========================================================================= --}}
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs font-extrabold">3</div>
                        <div>
                            <h2 class="text-base font-extrabold text-white">Langkah 3: Lokasi, Penanggung Jawab & Alasan Mutasi</h2>
                            <p class="text-xs text-slate-400">Atur unit ruangan pengirim, penerima, penanggung jawab, dan uraian alasan mutasi.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Pengirim (Asal) --}}
                    <div class="space-y-4 p-5 rounded-2xl bg-slate-950/60 border border-slate-800">
                        <div class="border-b border-slate-800 pb-2">
                            <label class="block text-slate-300 text-xs font-bold uppercase tracking-wider">📤 Ruangan Asal (Pengirim)</label>
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1.5">Pilih Ruangan Asal <span class="text-rose-400">*</span></label>
                            <select name="ruangan_asal" id="ruangan_asal" x-model="ruangan_asal" required
                                @change="onUnitAsalChange($event.target.value)"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-rose-500 transition-all">
                                <option value="">— Pilih Unit / Ruangan Asal —</option>
                                @foreach($units as $unit)
                                <option value="{{ $unit->nama }}">{{ $unit->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1.5">Penanggung Jawab Ruangan Asal <span class="text-rose-400">*</span></label>
                            <input type="text" name="penanggung_jawab_asal" id="penanggung_jawab_asal" x-model="penanggung_jawab_asal" required
                                placeholder="Nama penanggung jawab ruangan asal..."
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-rose-500 transition-all">
                        </div>
                    </div>

                    {{-- Penerima (Tujuan) --}}
                    <div class="space-y-4 p-5 rounded-2xl bg-slate-950/60 border border-rose-500/30">
                        <div class="border-b border-rose-500/30 pb-2">
                            <label class="block text-rose-300 text-xs font-bold uppercase tracking-wider">
                                📥 <span x-text="labelTujuan"></span>
                            </label>
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1.5">Pilih Ruangan Tujuan <span class="text-rose-400">*</span></label>
                            <select name="ruangan_tujuan" id="ruangan_tujuan" x-model="ruangan_tujuan" required
                                @change="onUnitTujuanChange($event.target.value)"
                                class="w-full bg-slate-900 border border-rose-500/40 rounded-xl px-4 py-3 text-xs text-rose-200 font-bold focus:outline-none focus:border-rose-400 transition-all">
                                <option value="">— Pilih Unit / Ruangan Tujuan —</option>
                                @foreach($units as $unit)
                                <option value="{{ $unit->nama }}">{{ $unit->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1.5">Penanggung Jawab Ruangan Tujuan <span class="text-rose-400">*</span></label>
                            <input type="text" name="penanggung_jawab_tujuan" id="penanggung_jawab_tujuan" x-model="penanggung_jawab_tujuan" required
                                placeholder="Nama penanggung jawab ruangan tujuan..."
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-rose-500 transition-all">
                        </div>
                    </div>
                </div>

                {{-- Visualisasi Alur Perpindahan --}}
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-between gap-3 flex-wrap">
                    <span class="text-xs text-slate-400 font-semibold">Skema Alur Pemindahan:</span>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-xl bg-slate-900 text-white text-xs font-bold border border-slate-700" x-text="ruangan_asal || 'Asal'"></span>
                        <span class="text-rose-400 font-bold">➔</span>
                        <span class="px-3 py-1 rounded-xl bg-rose-500/20 text-rose-300 text-xs font-bold border border-rose-500/30" x-text="ruangan_tujuan || 'Tujuan'"></span>
                    </div>
                </div>

                {{-- Alasan Mutasi --}}
                <div class="space-y-3">
                    <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1.5">
                        Alasan / Urgensi Pengajuan Mutasi (<span class="text-white font-bold" x-text="jenis_mutasi"></span>) <span class="text-rose-400">*</span>
                    </label>
                    <textarea name="alasan_mutasi" rows="3" :placeholder="alasanPlaceholder" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white resize-none focus:outline-none focus:border-rose-500 transition-all">{{ old('alasan_mutasi', $mutasi->alasan_mutasi ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1.5">Catatan Tambahan (Opsional)</label>
                    <textarea name="catatan_penerima" rows="2"
                        placeholder="Catatan khusus untuk verifikasi ruangan penerima atau admin..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white resize-none focus:outline-none focus:border-rose-500 transition-all">{{ old('catatan_penerima', $mutasi->catatan_penerima ?? '') }}</textarea>
                </div>

                {{-- Action Navigation Buttons --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <button type="button" @click="prevStep()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all cursor-pointer">
                        ← Kembali ke Langkah 2
                    </button>
                    <button type="button" @click="nextStep()"
                        class="px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-rose-500/20 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                        <span>Lanjut ke Langkah 4: Preview & Konfirmasi →</span>
                    </button>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- ===== STEP 4: PREVIEW & KONFIRMASI (RINGKASAN BAMB) ===== --}}
            {{-- ========================================================================= --}}
            <div x-show="step === 4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center justify-center text-xs font-extrabold">4</div>
                        <div>
                            <h2 class="text-base font-extrabold text-white">Langkah 4: Review & Konfirmasi Pengajuan Mutasi</h2>
                            <p class="text-xs text-slate-400">Periksa kembali seluruh ringkasan data sebelum mengirimkan dokumen pengajuan mutasi aset.</p>
                        </div>
                    </div>
                    <span class="text-[11px] px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/30 font-bold">Siap Dikirim</span>
                </div>

                {{-- Summary Card Review --}}
                <div class="p-6 rounded-2xl bg-slate-950 border border-slate-800 space-y-5">
                    
                    {{-- Jenis Mutasi Badge --}}
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <span class="text-xs font-bold text-slate-400">Jenis Pengajuan:</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-xl" x-text="selectedJenis?.emoji"></span>
                            <span class="text-sm font-black text-white" x-text="jenis_mutasi"></span>
                        </div>
                    </div>

                    {{-- Data Barang --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 space-y-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Barang Aset yang Dimutasi</span>
                            <p class="text-sm font-extrabold text-white" x-text="namaBarangPreview || '-'"></p>
                            <p class="text-xs text-rose-400 font-mono" x-text="searchBarang.split(' — ')[0] || '-'"></p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 space-y-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kondisi Aset Terdaftar</span>
                            <p class="text-sm font-extrabold text-emerald-300" x-text="kondisiPreview || 'Baik'"></p>
                            <p class="text-xs text-slate-400">Status Register Aset Tersedia</p>
                        </div>
                    </div>

                    {{-- Alur Perpindahan --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">📤 Ruangan Pengirim (Asal)</span>
                            <p class="text-sm font-bold text-white" x-text="ruangan_asal || '-'"></p>
                            <p class="text-xs text-slate-400 mt-1" x-text="'PJ: ' + (penanggung_jawab_asal || '-')"></p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-900 border border-rose-500/30">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-400 block mb-1">📥 Ruangan Penerima (Tujuan)</span>
                            <p class="text-sm font-bold text-white" x-text="ruangan_tujuan || '-'"></p>
                            <p class="text-xs text-slate-400 mt-1" x-text="'PJ: ' + (penanggung_jawab_tujuan || '-')"></p>
                        </div>
                    </div>

                    {{-- Alasan Mutasi --}}
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Alasan & Urgensi Mutasi</span>
                        <p class="text-xs text-slate-200 leading-relaxed italic" x-text="formData?.alasan_mutasi || document.querySelector('[name=alasan_mutasi]')?.value || '-'"></p>
                    </div>

                </div>

                {{-- Action Navigation Buttons --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <button type="button" @click="prevStep()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all cursor-pointer">
                        ← Kembali ke Langkah 3
                    </button>
                    <button type="submit"
                        class="px-8 py-3 rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-400 hover:to-rose-500 text-slate-950 font-black text-xs shadow-xl shadow-rose-500/20 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                        <span>🚀 {{ isset($mutasi) ? 'Simpan Perubahan Mutasi' : 'Kirim Pengajuan Mutasi Sekarang' }}</span>
                    </button>
                </div>
            </div>

        </form>

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
                 class="bg-slate-900 border rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 relative"
                 :class="{
                     'border-rose-500/40': confirmData.type === 'danger',
                     'border-amber-500/40': confirmData.type === 'warning',
                     'border-emerald-500/40': confirmData.type === 'success',
                     'border-cyan-500/40': confirmData.type === 'info'
                 }">
                
                <!-- Header Icon & Title -->
                <div class="flex items-start space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0 font-bold border"
                         :class="{
                             'bg-rose-500/20 text-rose-400 border-rose-500/30': confirmData.type === 'danger',
                             'bg-amber-500/20 text-amber-300 border-amber-500/30': confirmData.type === 'warning',
                             'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': confirmData.type === 'success',
                             'bg-cyan-500/20 text-cyan-300 border-cyan-500/30': confirmData.type === 'info'
                         }">
                        <span x-text="confirmData.type === 'danger' ? '🗑️' : (confirmData.type === 'warning' ? '✏️' : '🔄')"></span>
                    </div>
                    <div class="space-y-1 min-w-0 flex-1">
                        <h3 class="text-base font-extrabold text-white leading-snug" x-text="confirmData.title"></h3>
                        <p class="text-slate-300 text-xs leading-relaxed" x-text="confirmData.message"></p>
                    </div>
                </div>

                <!-- Item Target Preview Card -->
                <template x-if="confirmData.itemName">
                    <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Item Target:</span>
                        <p class="text-xs font-bold text-cyan-300 truncate font-mono" x-text="confirmData.itemName"></p>
                    </div>
                </template>

                <!-- Footer Action Buttons -->
                <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2.5">
                    <button type="button" @click="showConfirmModal = false"
                        class="px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700 transition-all active:scale-95 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="executeConfirmedAction()"
                        class="px-5 py-2.5 rounded-xl font-extrabold text-xs shadow-lg transition-all active:scale-95 cursor-pointer flex items-center space-x-1.5"
                        :class="{
                            'bg-rose-500 hover:bg-rose-400 text-white shadow-rose-500/20': confirmData.type === 'danger',
                            'bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-amber-500/20': confirmData.type === 'warning',
                            'bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-emerald-500/20': confirmData.type === 'success',
                            'bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-cyan-500/20': confirmData.type === 'info'
                        }">
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
