<x-layout :title="isset($mutasi) ? 'Ubah Pengajuan Mutasi - SIMAT-RK' : 'Pengajuan Mutasi Baru - SIMAT-RK'">
    @section('page-title', isset($mutasi) ? 'Ubah Pengajuan Mutasi' : 'Pengajuan Mutasi Baru')
    @section('breadcrumb', isset($mutasi) ? 'Master Utama / Mutasi Aset / Ubah' : 'Master Utama / Mutasi Aset / Pengajuan Baru')

    @php
        $user = Auth::user();
        $isSubAdmin = in_array($user->role ?? '', ['sub_admin']);
        $userUnitObj = $user->unitModel ?? null;
        if (!$userUnitObj && $user->unit_id) {
            $userUnitObj = \App\Models\Unit::find($user->unit_id);
        }
        $userUnitNama = $userUnitObj?->nama ?? (is_string($user->unit) ? $user->unit : '');
        $userUnitKepala = $userUnitObj?->kepala ?? ($user->name ?? '');
    @endphp

    <div x-data="{
        step: 1,
        isEdit: {{ isset($mutasi) ? 'true' : 'false' }},
        isSubAdmin: {{ $isSubAdmin ? 'true' : 'false' }},
        userUnitNama: '{{ $userUnitNama }}',
        userUnitKepala: '{{ $userUnitKepala }}',

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
        alasan_mutasi: '{{ old('alasan_mutasi', $mutasi->alasan_mutasi ?? '') }}',

        /* ---- State Search Filterable Unit Selector ---- */
        searchUnitAsal: '',
        isUnitAsalOpen: false,
        searchUnitTujuan: '',
        isUnitTujuanOpen: false,

        /* ---- Multi-Select Barang Aset ---- */
        selectedRegisterIds: {{ old('astap_register_id', $mutasi->astap_register_id ?? false) ? '['.old('astap_register_id', $mutasi->astap_register_id).']' : '[]' }},
        searchBarang: '',
        showDropdown: false,

        registers: {{ Js::from($registers) }},
        units: {{ Js::from($units) }},

        init() {
            // Jika role sub_admin dan belum ada ruangan_asal, kunci ke unit sub admin tersebut
            if (this.isSubAdmin && this.userUnitNama && !this.ruangan_asal) {
                this.ruangan_asal = this.userUnitNama;
                this.penanggung_jawab_asal = this.userUnitKepala;
            }

            if (this.ruangan_asal) {
                this.searchUnitAsal = this.ruangan_asal;
                const u = this.units.find(item => item.nama === this.ruangan_asal);
                if (u && !this.penanggung_jawab_asal) this.penanggung_jawab_asal = u.kepala || ('Kepala Ruangan ' + u.nama);
            }

            if (this.ruangan_tujuan) {
                this.searchUnitTujuan = this.ruangan_tujuan;
                const u = this.units.find(item => item.nama === this.ruangan_tujuan);
                if (u && !this.penanggung_jawab_tujuan) this.penanggung_jawab_tujuan = u.kepala || ('Kepala Ruangan ' + u.nama);
            }
        },

        selectJenisMutasi(val) {
            this.jenis_mutasi = val;
            if (val === 'Perbaikan') {
                const ipsrs = this.units.find(u => (u.nama || '').toLowerCase().includes('ips') || (u.nama || '').toLowerCase().includes('sarana'));
                if (ipsrs) {
                    this.selectUnitTujuan(ipsrs);
                }
            } else if (val === 'Pengembalian') {
                const perbekalan = this.units.find(u => (u.nama || '').toLowerCase().includes('perbekalan') || (u.nama || '').toLowerCase().includes('rumah tangga'));
                if (perbekalan) {
                    this.selectUnitTujuan(perbekalan);
                }
            }
        },

        /* Filter unit pengirim (Kecuali Unit Tujuan jika sudah dipilih) */
        get filteredUnitsAsal() {
            let list = this.units || [];
            if (this.ruangan_tujuan) {
                const tujuanNorm = this.ruangan_tujuan.toLowerCase().trim();
                list = list.filter(u => (u.nama || '').toLowerCase().trim() !== tujuanNorm);
            }
            if (!this.searchUnitAsal || this.searchUnitAsal === this.ruangan_asal) {
                return list;
            }
            const q = this.searchUnitAsal.toLowerCase();
            return list.filter(u =>
                (u.nama && u.nama.toLowerCase().includes(q)) ||
                (u.kepala && u.kepala.toLowerCase().includes(q))
            );
        },

        selectUnitAsal(u) {
            this.ruangan_asal = u.nama;
            this.penanggung_jawab_asal = u.kepala || ('Kepala Ruangan ' + u.nama);
            this.searchUnitAsal = u.nama;
            this.isUnitAsalOpen = false;
            this.selectedRegisterIds = [];
        },

        /* Filter unit penerima (Kecuali Unit Pengirim yang sedang dipilih) */
        get filteredUnitsTujuan() {
            let list = this.units || [];
            if (this.ruangan_asal) {
                const asalNorm = this.ruangan_asal.toLowerCase().trim();
                list = list.filter(u => (u.nama || '').toLowerCase().trim() !== asalNorm);
            }
            if (!this.searchUnitTujuan || this.searchUnitTujuan === this.ruangan_tujuan) {
                return list;
            }
            const q = this.searchUnitTujuan.toLowerCase();
            return list.filter(u =>
                (u.nama && u.nama.toLowerCase().includes(q)) ||
                (u.kepala && u.kepala.toLowerCase().includes(q))
            );
        },

        selectUnitTujuan(u) {
            this.ruangan_tujuan = u.nama;
            this.penanggung_jawab_tujuan = u.kepala || ('Kepala Ruangan ' + u.nama);
            this.searchUnitTujuan = u.nama;
            this.isUnitTujuanOpen = false;
        },

        /* Semua register barang yang tersedia di unit pengirim */
        get availableRegistersForAsal() {
            if (!this.ruangan_asal) return [];
            const selectedAsalNorm = this.ruangan_asal.toLowerCase().trim();
            return (this.registers || []).filter(r => {
                const unitNorm = (r.unit_nama || '').toLowerCase().trim();
                return unitNorm === selectedAsalNorm || unitNorm.includes(selectedAsalNorm) || selectedAsalNorm.includes(unitNorm);
            });
        },

        /* Filter register barang di unit pengirim sesuai kata kunci pencarian */
        get filteredRegisters() {
            let list = this.availableRegistersForAsal;
            if (!this.searchBarang) return list;
            const q = this.searchBarang.toLowerCase();
            return list.filter(r =>
                (r.nibar && r.nibar.toLowerCase().includes(q)) ||
                (r.nama_barang && r.nama_barang.toLowerCase().includes(q))
            );
        },

        /* Toggle pilihan barang (Multi Select) */
        toggleSelectRegister(reg) {
            const idStr = Number(reg.id);
            const idx = this.selectedRegisterIds.indexOf(idStr);
            if (idx > -1) {
                this.selectedRegisterIds.splice(idx, 1);
            } else {
                this.selectedRegisterIds.push(idStr);
            }
        },

        isRegisterSelected(id) {
            return this.selectedRegisterIds.includes(Number(id));
        },

        selectAllRegisters() {
            this.selectedRegisterIds = this.availableRegistersForAsal.map(r => Number(r.id));
        },

        clearAllSelectedRegisters() {
            this.selectedRegisterIds = [];
        },

        /* Daftar barang terpilih lengkap untuk preview */
        get selectedRegistersList() {
            return this.registers.filter(r => this.selectedRegisterIds.includes(Number(r.id)));
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
            return !!this.ruangan_asal && !!this.ruangan_tujuan && (this.ruangan_asal !== this.ruangan_tujuan);
        },

        get isStep3Valid() {
            return this.selectedRegisterIds.length > 0;
        },

        nextStep() {
            if (this.step === 1 && !this.isStep1Valid) {
                this.showToast('Silakan pilih salah satu Jenis Pengajuan Mutasi terlebih dahulu.', 'warning');
                return;
            }
            if (this.step === 2) {
                if (!this.ruangan_asal || !this.ruangan_tujuan) {
                    this.showToast('Ruangan Asal (Pengirim) dan Ruangan Tujuan (Penerima) wajib dipilih.', 'warning');
                    return;
                }
                if (this.ruangan_asal === this.ruangan_tujuan) {
                    this.showToast('Ruangan Asal dan Ruangan Tujuan tidak boleh sama! Silakan pilih unit tujuan yang berbeda.', 'warning');
                    return;
                }
            }
            if (this.step === 3 && !this.isStep3Valid) {
                this.showToast('Silakan pilih minimal 1 barang aset yang akan dimutasi terlebih dahulu.', 'warning');
                return;
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
                this.step = s;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else if (s > this.step) {
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
            if (this.selectedRegisterIds.length === 0) {
                this.showToast('⚠️ Silakan pilih minimal 1 barang aset yang akan dimutasi.', 'warning');
                e.preventDefault();
                return false;
            }
            e.preventDefault();
            const formElement = e.target;
            const targetItem = this.selectedRegisterIds.length + ' Barang Aset (' + (this.ruangan_asal || 'Asal') + ' ➔ ' + (this.ruangan_tujuan || 'Tujuan') + ')';
            this.askConfirmation({
                title: this.isEdit ? '✏️ Konfirmasi Simpan Perubahan Mutasi' : '🔄 Konfirmasi Pengajuan Mutasi Baru',
                message: this.isEdit ? 'Apakah Anda yakin ingin menyimpan perubahan data pengajuan mutasi ini?' : 'Apakah Anda yakin ingin mengajukan mutasi untuk ' + this.selectedRegisterIds.length + ' barang aset ini? Pindah tangan ruangan akan diproses setelah persetujuan.',
                itemName: targetItem,
                type: this.isEdit ? 'warning' : 'success',
                btnText: this.isEdit ? '✏️ Ya, Simpan Perubahan' : '🔄 Ya, Ajukan Mutasi (' + this.selectedRegisterIds.length + ' Barang)',
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

                <!-- Step 2 Tab (Unit Pengirim & Penerima) -->
                <button type="button" @click="goToStep(2)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 2 ? 'bg-rose-500 text-slate-950 shadow-lg shadow-rose-500/30 font-black' : (step > 2 ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="step <= 2">2</span>
                            <span x-show="step > 2">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 2 ? 'text-rose-400 font-black' : (step > 2 ? 'text-rose-400' : 'text-slate-500')">LANGKAH 2</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Unit Pengirim & Penerima</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 2 ? 'bg-rose-500 shadow-sm shadow-rose-500/50' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 3 Tab (Pilih Barang Aset Multi & Alasan) -->
                <button type="button" @click="goToStep(3)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 3 ? 'bg-rose-500 text-slate-950 shadow-lg shadow-rose-500/30 font-black' : (step > 3 ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="step <= 3">3</span>
                            <span x-show="step > 3">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 3 ? 'text-rose-400 font-black' : (step > 3 ? 'text-rose-400' : 'text-slate-500')">LANGKAH 3</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Pilih Aset & Alasan</span>
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
            
            {{-- Array hidden input untuk Multi-Select Register IDs --}}
            <template x-for="id in selectedRegisterIds" :key="id">
                <input type="hidden" name="astap_register_ids[]" :value="id">
            </template>
            <input type="hidden" name="astap_register_id" :value="selectedRegisterIds[0] || ''">

            {{-- ========================================================================= --}}
            {{-- ===== STEP 1: PILIH JENIS PENGAJUAN MUTASI ===== --}}
            {{-- ========================================================================= --}}
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs font-extrabold">1</div>
                        <div>
                            <h2 class="text-base font-extrabold text-white">Langkah 1: Pilih Jenis Pengajuan Mutasi</h2>
                            <p class="text-xs text-slate-400">Pilih salah satu dari 4 jenis pengajuan mutasi di bawah ini.</p>
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
                            <div x-show="jenis_mutasi === opt.value" class="absolute top-4 right-4 w-6 h-6 rounded-full bg-rose-500 text-slate-950 flex items-center justify-center text-xs font-black shadow">✓</div>

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
                        class="px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-extrabold text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                        <span>Lanjut ke Langkah 2: Unit Pengirim & Penerima →</span>
                    </button>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- ===== STEP 2: PILIH UNIT PENGIRIM & PENERIMA ===== --}}
            {{-- ========================================================================= --}}
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs font-extrabold">2</div>
                        <div>
                            <h2 class="text-base font-extrabold text-white">Langkah 2: Tentukan Unit Pengirim (Asal) & Unit Penerima (Tujuan)</h2>
                            <p class="text-xs text-slate-400">Tentukan lokasi unit pengirim barang dan unit penerima tujuan mutasi aset.</p>
                        </div>
                    </div>
                </div>

                {{-- Hidden input tanggal_mutasi (otomatis hari ini) --}}
                <input type="hidden" name="tanggal_mutasi" value="{{ old('tanggal_mutasi', isset($mutasi) ? $mutasi->tanggal_mutasi->format('Y-m-d') : date('Y-m-d')) }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Ruangan Asal (Pengirim) --}}
                    <div class="space-y-4 p-5 rounded-2xl bg-slate-950/60 border border-slate-800 relative">
                        <div class="border-b border-slate-800 pb-2 flex items-center justify-between">
                            <label class="block text-slate-300 text-xs font-bold uppercase tracking-wider">📤 Unit Pengirim (Ruangan Asal)</label>
                            <template x-if="isSubAdmin && userUnitNama">
                                <span class="text-[9.5px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Terunci Role Sub Admin</span>
                            </template>
                        </div>

                        {{-- Untuk Role Sub Admin (Terkunci Sesuai Unitnya) --}}
                        <template x-if="isSubAdmin && userUnitNama">
                            <div class="space-y-3">
                                <div>
                                    <span class="text-[10px] text-slate-400 block mb-1">Nama Unit/Ruangan:</span>
                                    <div class="px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white font-extrabold text-xs flex items-center justify-between">
                                        <span x-text="ruangan_asal || userUnitNama"></span>
                                        <span>🔒</span>
                                    </div>
                                    <input type="hidden" name="ruangan_asal" :value="ruangan_asal || userUnitNama" required>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1">Penanggung Jawab Pengirim (Kepala Ruangan)</label>
                                    <div class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-emerald-300 font-extrabold text-xs flex items-center justify-between">
                                        <span x-text="penanggung_jawab_asal || userUnitKepala || 'Kepala Ruangan'"></span>
                                        <span>🔒</span>
                                    </div>
                                    <input type="hidden" name="penanggung_jawab_asal" :value="penanggung_jawab_asal || userUnitKepala" required>
                                </div>
                            </div>
                        </template>

                        {{-- Untuk Role Master Admin & Admin (Pencarian Filter Unit Pengirim) --}}
                        <template x-if="!isSubAdmin || !userUnitNama">
                            <div class="space-y-3">
                                <div class="space-y-1.5 relative" @click.outside="isUnitAsalOpen = false">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider">
                                            Pilih Unit Pengirim (Asal) <span class="text-rose-400">*</span>
                                        </label>
                                        <button type="button" x-show="ruangan_asal && !isUnitAsalOpen"
                                            @click="isUnitAsalOpen = true; searchUnitAsal = ''"
                                            class="text-[11px] font-bold text-rose-400 hover:text-rose-300 transition-colors cursor-pointer">
                                            ✕ Ganti Unit
                                        </button>
                                    </div>
                                    
                                    <div class="relative">
                                        <input type="text"
                                            :value="(!isUnitAsalOpen && ruangan_asal) ? ruangan_asal : searchUnitAsal"
                                            @input="searchUnitAsal = $event.target.value; isUnitAsalOpen = true"
                                            @focus="isUnitAsalOpen = true"
                                            placeholder="Ketik nama unit pengirim..."
                                            class="w-full bg-slate-900 border rounded-xl py-3 pr-4 text-xs font-bold transition-all shadow-inner focus:outline-none"
                                            :class="ruangan_asal && !isUnitAsalOpen ? 'border-rose-500/60 text-rose-200' : 'border-slate-700 text-white focus:border-rose-400'"
                                            style="padding-left: 3.1rem !important;">
                                        <svg class="w-4 h-4 text-rose-400 absolute pointer-events-none" style="left: 1.25rem; top: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <input type="hidden" name="ruangan_asal" :value="ruangan_asal" required>

                                    {{-- Dropdown Scrollable List (Excludes Unit Tujuan) --}}
                                    <div x-show="isUnitAsalOpen" x-transition x-cloak style="max-height: 210px !important; overflow-y: auto !important;"
                                        class="absolute z-50 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-rose-500/40 rounded-2xl shadow-2xl backdrop-blur-xl">
                                        <template x-for="u in filteredUnitsAsal" :key="u.id">
                                            <div @click="selectUnitAsal(u)"
                                                class="p-2.5 rounded-xl bg-slate-950 border transition-all flex items-center justify-between cursor-pointer group hover:bg-slate-800"
                                                :class="u.nama === ruangan_asal ? 'border-rose-500 bg-rose-950/40 shadow-lg' : 'border-slate-800/80 hover:border-rose-500/40'">
                                                <div class="min-w-0 pr-3">
                                                    <p class="text-xs font-bold text-white group-hover:text-rose-300 transition-colors truncate" x-text="u.nama"></p>
                                                    <p class="text-[10px] text-slate-400 truncate" x-text="'PJ / Kepala: ' + (u.kepala || 'Belum Diatur')"></p>
                                                </div>
                                                <span class="shrink-0 text-[11px] font-bold px-2.5 py-1 rounded-lg"
                                                    :class="u.nama === ruangan_asal ? 'bg-rose-500 text-slate-950' : 'bg-slate-800 text-slate-400 group-hover:text-white'">
                                                    <span x-text="u.nama === ruangan_asal ? '✓ Terpilih' : 'Pilih'"></span>
                                                </span>
                                            </div>
                                        </template>
                                        <template x-if="filteredUnitsAsal.length === 0">
                                            <div class="p-3 text-center text-xs text-rose-400 font-semibold">Tidak menemukan unit dengan kata kunci tersebut.</div>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1">Penanggung Jawab Pengirim (Kepala Ruangan)</label>
                                    <div class="px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-emerald-300 font-extrabold text-xs flex items-center justify-between shadow-inner">
                                        <span x-text="penanggung_jawab_asal || 'Pilih Unit Pengirim Terlebih Dahulu'"></span>
                                        <span>🔒</span>
                                    </div>
                                    <input type="hidden" name="penanggung_jawab_asal" :value="penanggung_jawab_asal" required>
                                    <template x-if="penanggung_jawab_asal">
                                        <p class="text-[10px] text-emerald-400 font-semibold flex items-center space-x-1 mt-1">
                                            <span>✓ Terkunci otomatis dari data Kepala Ruangan RSUD</span>
                                        </p>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Ruangan Tujuan (Penerima) --}}
                    <div class="space-y-4 p-5 rounded-2xl bg-slate-950/60 border border-rose-500/30">
                        <div class="border-b border-rose-500/30 pb-2">
                            <label class="block text-rose-300 text-xs font-bold uppercase tracking-wider">
                                📥 <span x-text="labelTujuan"></span>
                            </label>
                        </div>
                        <div class="space-y-3">
                            <div class="space-y-1.5 relative" @click.outside="isUnitTujuanOpen = false">
                                <div class="flex items-center justify-between">
                                    <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider">
                                        Pilih Unit Penerima (Tujuan) <span class="text-rose-400">*</span>
                                    </label>
                                    <button type="button" x-show="ruangan_tujuan && !isUnitTujuanOpen"
                                        @click="isUnitTujuanOpen = true; searchUnitTujuan = ''"
                                        class="text-[11px] font-bold text-rose-400 hover:text-rose-300 transition-colors cursor-pointer">
                                        ✕ Ganti Unit
                                    </button>
                                </div>
                                
                                <div class="relative">
                                    <input type="text"
                                        :value="(!isUnitTujuanOpen && ruangan_tujuan) ? ruangan_tujuan : searchUnitTujuan"
                                        @input="searchUnitTujuan = $event.target.value; isUnitTujuanOpen = true"
                                        @focus="isUnitTujuanOpen = true"
                                        placeholder="Ketik nama unit penerima..."
                                        class="w-full bg-slate-900 border rounded-xl py-3 pr-4 text-xs font-bold transition-all shadow-inner focus:outline-none"
                                        :class="ruangan_tujuan && !isUnitTujuanOpen ? 'border-rose-500/60 text-rose-200' : 'border-rose-500/40 text-white focus:border-rose-400'"
                                        style="padding-left: 3.1rem !important;">
                                    <svg class="w-4 h-4 text-rose-400 absolute pointer-events-none" style="left: 1.25rem; top: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <input type="hidden" name="ruangan_tujuan" :value="ruangan_tujuan" required>

                                {{-- Dropdown Scrollable List (Excludes Unit Pengirim) --}}
                                <div x-show="isUnitTujuanOpen" x-transition x-cloak style="max-height: 210px !important; overflow-y: auto !important;"
                                    class="absolute z-50 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-rose-500/40 rounded-2xl shadow-2xl backdrop-blur-xl">
                                    <template x-for="u in filteredUnitsTujuan" :key="u.id">
                                        <div @click="selectUnitTujuan(u)"
                                            class="p-2.5 rounded-xl bg-slate-950 border transition-all flex items-center justify-between cursor-pointer group hover:bg-slate-800"
                                            :class="u.nama === ruangan_tujuan ? 'border-rose-500 bg-rose-950/40 shadow-lg' : 'border-slate-800/80 hover:border-rose-500/40'">
                                            <div class="min-w-0 pr-3">
                                                <p class="text-xs font-bold text-white group-hover:text-rose-300 transition-colors truncate" x-text="u.nama"></p>
                                                <p class="text-[10px] text-slate-400 truncate" x-text="'PJ / Kepala: ' + (u.kepala || 'Belum Diatur')"></p>
                                            </div>
                                            <span class="shrink-0 text-[11px] font-bold px-2.5 py-1 rounded-lg"
                                                :class="u.nama === ruangan_tujuan ? 'bg-rose-500 text-slate-950' : 'bg-slate-800 text-slate-400 group-hover:text-white'">
                                                <span x-text="u.nama === ruangan_tujuan ? '✓ Terpilih' : 'Pilih'"></span>
                                            </span>
                                        </div>
                                    </template>
                                    <template x-if="filteredUnitsTujuan.length === 0">
                                        <div class="p-3 text-center text-xs text-rose-400 font-semibold">Tidak menemukan unit tujuan dengan kata kunci tersebut.</div>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1">Penanggung Jawab Penerima (Kepala Ruangan)</label>
                                <div class="px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-emerald-300 font-extrabold text-xs flex items-center justify-between shadow-inner">
                                    <span x-text="penanggung_jawab_tujuan || 'Pilih Unit Penerima Terlebih Dahulu'"></span>
                                    <span>🔒</span>
                                </div>
                                <input type="hidden" name="penanggung_jawab_tujuan" :value="penanggung_jawab_tujuan" required>
                                <template x-if="penanggung_jawab_tujuan">
                                    <p class="text-[10px] text-emerald-400 font-semibold flex items-center space-x-1 mt-1">
                                        <span>✓ Terkunci otomatis dari data Kepala Ruangan RSUD</span>
                                    </p>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Visualisasi Alur Perpindahan --}}
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-between gap-3 flex-wrap">
                    <span class="text-xs text-slate-400 font-semibold">Skema Alur Pemindahan:</span>
                    <div class="flex items-center gap-2">
                        <span class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold border border-slate-700" x-text="ruangan_asal || 'Ruangan Asal'"></span>
                        <span class="text-rose-400 font-bold">➔</span>
                        <span class="px-3.5 py-1.5 rounded-xl bg-rose-500/20 text-rose-300 text-xs font-bold border border-rose-500/30" x-text="ruangan_tujuan || 'Ruangan Tujuan'"></span>
                    </div>
                </div>

                {{-- Action Navigation Buttons --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <button type="button" @click="prevStep()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all active:scale-95 cursor-pointer">
                        ← Kembali ke Langkah 1
                    </button>
                    <button type="button" @click="nextStep()"
                        class="px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-extrabold text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                        <span>Lanjut ke Langkah 3: Pilih Barang Aset →</span>
                    </button>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- ===== STEP 3: PILIH BARANG ASET MULTI-SELECT & ALASAN ===== --}}
            {{-- ========================================================================= --}}
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs font-extrabold">3</div>
                        <div>
                            <h2 class="text-base font-extrabold text-white">Langkah 3: Pilih Barang Aset (Bisa Pilih Banyak Sekaligus) & Alasan</h2>
                            <p class="text-xs text-slate-400">Centang satu atau beberapa barang aset register dari unit pengirim yang akan dimutasi.</p>
                        </div>
                    </div>
                </div>

                {{-- Banner Filter Unit Pengirim Aktif --}}
                <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex items-center space-x-2.5">
                        <span class="text-lg">📍</span>
                        <div>
                            <p class="text-xs font-extrabold text-rose-300 uppercase tracking-wider">Menampilkan Aset dari Unit Pengirim:</p>
                            <p class="text-sm font-black text-white font-mono" x-text="ruangan_asal || 'Belum Dipilih'"></p>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold px-3 py-1 rounded-xl bg-slate-900 text-rose-300 border border-rose-500/30"
                        x-text="availableRegistersForAsal.length + ' Barang Tersedia'"></span>
                </div>

                {{-- Filter & Batch Action Buttons --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 flex items-center pointer-events-none text-rose-400" style="left: 1.25rem;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" x-model="searchBarang"
                            placeholder="Ketik NIBAR / nama barang..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 pr-4 text-xs text-white font-mono placeholder-slate-500 focus:outline-none focus:border-rose-500 transition-all"
                            style="padding-left: 3.1rem !important;">
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <button type="button" @click="selectAllRegisters()"
                            class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-rose-300 text-xs font-bold border border-slate-700 transition-all cursor-pointer flex items-center space-x-1.5">
                            <span>✓ Pilih Semua (<span x-text="availableRegistersForAsal.length"></span>)</span>
                        </button>
                        <button type="button" x-show="selectedRegisterIds.length > 0" @click="clearAllSelectedRegisters()"
                            class="px-3.5 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 text-xs font-bold border border-rose-500/20 transition-all cursor-pointer">
                            ✕ Hapus Pilihan
                        </button>
                    </div>
                </div>

                {{-- Counter Badge Multi-Select --}}
                <div class="flex items-center justify-between px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-xs text-slate-400 font-semibold">Total Aset Terpilih untuk Dimutasi:</span>
                    <span class="text-xs font-mono font-black px-3 py-1 rounded-lg"
                        :class="selectedRegisterIds.length > 0 ? 'bg-rose-500 text-slate-950 shadow-md' : 'bg-slate-800 text-slate-500'"
                        x-text="selectedRegisterIds.length + ' Barang Terpilih'"></span>
                </div>

                {{-- Daftar Aset (Cards Grid dengan Checkbox Multi Select) --}}
                <div class="space-y-2.5 custom-scrollbar pr-2 p-1 rounded-2xl bg-slate-950/40 border border-slate-800/80" style="max-height: 280px !important; overflow-y: auto !important;">
                    <template x-for="r in filteredRegisters" :key="r.id">
                        <div @click="toggleSelectRegister(r)"
                            class="p-4 rounded-2xl border transition-all flex items-center justify-between gap-4 cursor-pointer group active:scale-[0.99]"
                            :class="isRegisterSelected(r.id) ? 'border-rose-500 bg-rose-950/25 ring-2 ring-rose-500/30 shadow-lg' : 'border-slate-800 bg-slate-950/80 hover:border-slate-700 hover:bg-slate-900'">
                            
                            <div class="flex items-center space-x-3.5 min-w-0">
                                {{-- Checkbox Icon --}}
                                <div class="w-6 h-6 rounded-lg flex items-center justify-center transition-all shrink-0 font-black text-xs"
                                     :class="isRegisterSelected(r.id) ? 'bg-rose-500 text-slate-950 shadow-md' : 'bg-slate-900 border border-slate-700 text-transparent group-hover:border-rose-400'">
                                    ✓
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-extrabold text-white group-hover:text-rose-300 transition-colors truncate" x-text="r.nama_barang"></p>
                                    <div class="flex items-center gap-3 mt-1 flex-wrap text-[10.5px]">
                                        <span class="text-rose-400 font-mono font-bold" x-text="r.nibar"></span>
                                        <span class="text-slate-500">•</span>
                                        <span class="text-slate-400" x-text="'Unit: ' + r.unit_nama"></span>
                                        <span class="text-slate-500">•</span>
                                        <span class="font-bold"
                                            :class="{
                                                'text-emerald-400': r.kondisi === 'Baik',
                                                'text-amber-400':   r.kondisi === 'Kurang Baik',
                                                'text-rose-400':    r.kondisi === 'Rusak Berat'
                                            }"
                                            x-text="'Kondisi: ' + r.kondisi"></span>
                                    </div>
                                </div>
                            </div>

                            <span class="text-xs font-bold px-3 py-1.5 rounded-xl shrink-0 transition-all"
                                :class="isRegisterSelected(r.id) ? 'bg-rose-500 text-slate-950 font-black' : 'bg-slate-900 text-slate-400 border border-slate-800 group-hover:text-white'">
                                <span x-text="isRegisterSelected(r.id) ? '✓ Terpilih' : '+ Pilih'"></span>
                            </span>
                        </div>
                    </template>

                    <template x-if="filteredRegisters.length === 0">
                        <div class="p-8 text-center bg-slate-950/60 rounded-2xl border border-slate-800 space-y-2">
                            <span class="text-2xl">📦</span>
                            <p class="text-xs text-rose-400 font-bold">Tidak ada barang aset register yang tersedia pada unit pengirim ini.</p>
                            <p class="text-[11px] text-slate-400">Pastikan Anda memilih unit pengirim yang memiliki register aset di Langkah 2.</p>
                        </div>
                    </template>
                </div>

                {{-- Alasan Mutasi --}}
                <div class="space-y-3 pt-2">
                    <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1.5">
                        Alasan / Urgensi Pengajuan Mutasi (<span class="text-white font-bold" x-text="jenis_mutasi"></span>) <span class="text-rose-400">*</span>
                    </label>
                    <textarea name="alasan_mutasi" x-model="alasan_mutasi" rows="3" :placeholder="alasanPlaceholder" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white resize-none focus:outline-none focus:border-rose-500 transition-all"></textarea>
                </div>

                {{-- Action Navigation Buttons --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <button type="button" @click="prevStep()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all active:scale-95 cursor-pointer">
                        ← Kembali ke Langkah 2
                    </button>
                    <button type="button" @click="nextStep()"
                        class="px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-extrabold text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                        <span>Lanjut ke Langkah 4: Preview & Konfirmasi →</span>
                    </button>
                </div>
            </div>

            {{-- ========================================================================= --}}
            {{-- ===== STEP 4: PREVIEW & KONFIRMASI (RINGKASAN BAMB MULTI-ITEM) ===== --}}
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

                    {{-- Data Daftar Barang Terpilih --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">📦 Daftar Barang Aset Terpilih (<span x-text="selectedRegistersList.length"></span> Barang):</span>
                            <span class="text-[10.5px] text-rose-400 font-mono font-bold" x-text="selectedRegistersList.length + ' Item Terdaftar'"></span>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-900">
                            <table class="w-full text-left text-xs text-slate-300">
                                <thead>
                                    <tr class="bg-slate-950 text-slate-400 font-bold border-b border-slate-800 text-[10.5px] uppercase">
                                        <th class="py-2.5 px-3 w-10 text-center">#</th>
                                        <th class="py-2.5 px-3">NIBAR</th>
                                        <th class="py-2.5 px-3">Nama Barang</th>
                                        <th class="py-2.5 px-3">Kondisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, idx) in selectedRegistersList" :key="item.id">
                                        <tr class="border-b border-slate-800/60 last:border-b-0 hover:bg-slate-800/50 transition-colors">
                                            <td class="py-2 px-3 text-center text-slate-500 font-mono font-bold" x-text="idx + 1"></td>
                                            <td class="py-2 px-3 font-mono font-bold text-rose-400" x-text="item.nibar"></td>
                                            <td class="py-2 px-3 font-extrabold text-white" x-text="item.nama_barang"></td>
                                            <td class="py-2 px-3 font-bold"
                                                :class="{
                                                    'text-emerald-400': item.kondisi === 'Baik',
                                                    'text-amber-400':   item.kondisi === 'Kurang Baik',
                                                    'text-rose-400':    item.kondisi === 'Rusak Berat'
                                                }"
                                                x-text="item.kondisi"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
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
                        <p class="text-xs text-slate-200 leading-relaxed italic" x-text="alasan_mutasi || 'Belum diisi'"></p>
                    </div>

                </div>

                {{-- Action Navigation Buttons --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <button type="button" @click="prevStep()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all active:scale-95 cursor-pointer">
                        ← Kembali ke Langkah 3
                    </button>
                    <button type="submit"
                        class="px-7 py-3 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-extrabold text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                        <span>🚀 Kirim Pengajuan Mutasi</span>
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
