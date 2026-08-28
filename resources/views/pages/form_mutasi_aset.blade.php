<x-layout :title="isset($mutasi) ? 'Ubah Pengajuan Mutasi - SIMAT-RK' : 'Pengajuan Mutasi Baru - SIMAT-RK'">
    @section('page-title', isset($mutasi) ? 'Ubah Pengajuan Mutasi' : 'Pengajuan Mutasi Baru')
    @section('breadcrumb', isset($mutasi) ? 'Master Utama / Mutasi Aset / Ubah' : 'Master Utama / Mutasi Aset / Pengajuan Baru')

    <div x-data="{
        isEdit: {{ isset($mutasi) ? 'true' : 'false' }},

        /* ---- Jenis Mutasi ---- */
        jenis_mutasi: '{{ old('jenis_mutasi', $mutasi->jenis_mutasi ?? 'Pemindahan') }}',
        jenisMutasiOptions: [
            { value: 'Pemindahan',   emoji: '🔄', label: 'Pemindahan Kebutuhan', desc: 'Barang surplus dipindah ke unit yang lebih membutuhkan', color: 'blue' },
            { value: 'Perbaikan',    emoji: '🔧', label: 'Perbaikan / Servis',   desc: 'Barang rusak dikirim ke unit/IPSRS yang bisa memperbaiki', color: 'amber' },
            { value: 'Pengembalian', emoji: '↩️', label: 'Pengembalian Barang',  desc: 'Barang selesai diperbaiki dikembalikan ke unit asal', color: 'teal' },
            { value: 'Penghapusan',  emoji: '🗑️', label: 'Penghapusan Aset',    desc: 'Barang rusak berat dimutasi ke Bagian Perbekalan/Admin', color: 'rose' }
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
                'Pemindahan':   'Ruangan Tujuan (Unit Penerima)',
                'Perbaikan':    'Ruangan Tujuan (Unit / IPSRS yang Memperbaiki)',
                'Pengembalian': 'Ruangan Tujuan (Unit Asal / Kembali)',
                'Penghapusan':  'Ruangan Tujuan (Bagian Rumah Tangga & Inst Perbekalan)'
            };
            return map[this.jenis_mutasi] || 'Ruangan Tujuan';
        },

        get alasanPlaceholder() {
            const map = {
                'Pemindahan':   'Contoh: Unit penerima sangat membutuhkan alat ini untuk peningkatan pelayanan pasien...',
                'Perbaikan':    'Contoh: Alat mengalami gangguan fungsi / error, perlu perbaikan oleh teknisi...',
                'Pengembalian': 'Contoh: Barang telah selesai diperbaiki dan siap difungsikan kembali di unit asal...',
                'Penghapusan':  'Contoh: Barang mengalami kerusakan berat permanen, tidak ekonomis untuk diperbaiki dan diusulkan penghapusan aset...'
            };
            return map[this.jenis_mutasi] || 'Alasan pemindahan / mutasi aset...';
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
                this.toast = { show: true, message: '⚠️ Ruangan Asal dan Ruangan Tujuan tidak boleh sama! Silakan pilih ruangan tujuan yang berbeda.', type: 'warning' };
                setTimeout(() => { this.toast.show = false; }, 4000);
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

        {{-- ===== TOP BAR ===== --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <a href="{{ route('mutasi.index') }}"
                   class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[10px] font-bold mb-1">
                        <span x-text="isEdit ? '✏️ UBAH PENGAJUAN MUTASI' : '🔄 PENGAJUAN MUTASI ASET'"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white">Form Pengajuan Mutasi Aset</h1>
                </div>
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

            {{-- ===== SECTION 1: JENIS MUTASI ===== --}}
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-5">
                <div class="flex items-center space-x-2 pb-4 border-b border-slate-800">
                    <div class="w-7 h-7 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs font-extrabold">1</div>
                    <h2 class="text-sm font-extrabold text-white">Jenis Pengajuan Mutasi</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <template x-for="opt in jenisMutasiOptions" :key="opt.value">
                        <button type="button" @click="jenis_mutasi = opt.value"
                            :class="{
                                'border-blue-500/70 bg-blue-500/10 ring-1 ring-blue-500/30':   jenis_mutasi === opt.value && opt.color === 'blue',
                                'border-amber-500/70 bg-amber-500/10 ring-1 ring-amber-500/30': jenis_mutasi === opt.value && opt.color === 'amber',
                                'border-teal-500/70 bg-teal-500/10 ring-1 ring-teal-500/30':   jenis_mutasi === opt.value && opt.color === 'teal',
                                'border-rose-500/70 bg-rose-500/10 ring-1 ring-rose-500/30':   jenis_mutasi === opt.value && opt.color === 'rose',
                                'border-slate-700 bg-slate-950/60 opacity-55 hover:opacity-80': jenis_mutasi !== opt.value
                            }"
                            class="flex items-start space-x-3 p-4 rounded-2xl border-2 text-left transition-all w-full active:scale-[0.99]">
                            <span class="text-xl shrink-0" x-text="opt.emoji"></span>
                            <div>
                                <p class="text-xs font-bold text-white" x-text="opt.label"></p>
                                <p class="text-[10.5px] text-slate-400 mt-0.5 leading-relaxed" x-text="opt.desc"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            {{-- ===== SECTION 2: TANGGAL & DATA BARANG ===== --}}
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-5">
                <div class="flex items-center space-x-2 pb-4 border-b border-slate-800">
                    <div class="w-7 h-7 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs font-extrabold">2</div>
                    <h2 class="text-sm font-extrabold text-white">Tanggal & Data Barang yang Dimutasi</h2>
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
                            Pilih Aset dari Register (Cari NIBAR / Nama Barang / Ruangan) <span class="text-rose-400">*</span>
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
                                    class="w-full flex items-start gap-3 px-4 py-3 hover:bg-slate-800 text-left transition-colors border-b border-slate-800/60 last:border-b-0">
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

                {{-- Preview Barang Dipilih (Kondisi diambil otomatis dari AstapRegister) --}}
                <div x-show="selectedRegisterId && namaBarangPreview"
                    class="bg-slate-950/80 border border-rose-500/30 rounded-2xl p-4 flex items-start gap-4 mt-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white" x-text="namaBarangPreview"></p>
                        <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                            <span class="text-[10.5px] text-slate-400">Unit Terdaftar: <span class="text-slate-200 font-semibold" x-text="unitAsalPreview || '-'"></span></span>
                            <span class="w-1 h-1 rounded-full bg-slate-600 shrink-0"></span>
                            <span class="text-[10.5px] text-slate-400">Kondisi Aset: 
                                <span class="font-bold px-2 py-0.5 rounded-lg border text-[10px]"
                                    :class="{
                                        'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': kondisiPreview === 'Baik',
                                        'bg-amber-500/15 text-amber-300 border-amber-500/30':       kondisiPreview === 'Kurang Baik',
                                        'bg-rose-500/15 text-rose-300 border-rose-500/30':         kondisiPreview === 'Rusak Berat'
                                    }"
                                    x-text="kondisiPreview">
                                </span>
                            </span>
                        </div>

                        {{-- Peringatan kontekstual kondisi aset --}}
                        <div x-show="jenis_mutasi === 'Penghapusan' && kondisiPreview !== 'Rusak Berat'"
                            class="mt-2.5 p-2.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-[10.5px] font-semibold flex items-center gap-2">
                            <span>⚠️</span><span>Catatan: Mutasi <strong>Penghapusan</strong> umumnya ditujukan untuk aset dengan kondisi <strong>Rusak Berat</strong>.</span>
                        </div>
                        <div x-show="jenis_mutasi === 'Perbaikan' && kondisiPreview === 'Baik'"
                            class="mt-2.5 p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-[10.5px] font-semibold flex items-center gap-2">
                            <span>⚠️</span><span>Catatan: Pengajuan <strong>Perbaikan</strong> umumnya untuk aset yang berkondisi <strong>Kurang Baik</strong> atau <strong>Rusak Berat</strong>.</span>
                        </div>
                    </div>
                    <button type="button" @click="clearSelectedBarang()"
                        class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-500 hover:text-rose-300 border border-slate-700 flex items-center justify-center transition-all shrink-0"
                        title="Batal Pilih">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- ===== SECTION 3: LOKASI & PJ ===== --}}
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-5">
                <div class="flex items-center space-x-2 pb-4 border-b border-slate-800">
                    <div class="w-7 h-7 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs font-extrabold">3</div>
                    <h2 class="text-sm font-extrabold text-white">Lokasi & Penanggung Jawab</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Pengirim (Asal) --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1.5">📤 Ruangan Asal (Pengirim) <span class="text-rose-400">*</span></label>
                            <select name="ruangan_asal" id="ruangan_asal" x-model="ruangan_asal" required
                                @change="onUnitAsalChange($event.target.value)"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-rose-500 transition-all">
                                <option value="">— Pilih Unit / Ruangan Asal —</option>
                                @foreach($units as $unit)
                                <option value="{{ $unit->nama }}">{{ $unit->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1.5">👤 Penanggung Jawab Pengirim <span class="text-rose-400">*</span></label>
                            <input type="text" name="penanggung_jawab_asal" id="penanggung_jawab_asal" x-model="penanggung_jawab_asal" required
                                placeholder="Nama penanggung jawab ruangan asal..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-rose-500 transition-all">
                        </div>
                    </div>

                    {{-- Penerima (Tujuan) --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-rose-400 text-[10.5px] font-bold uppercase tracking-wider mb-1.5">
                                📥 <span x-text="labelTujuan"></span> <span class="text-rose-400">*</span>
                            </label>
                            <select name="ruangan_tujuan" id="ruangan_tujuan" x-model="ruangan_tujuan" required
                                @change="onUnitTujuanChange($event.target.value)"
                                class="w-full bg-slate-950 border border-rose-500/40 rounded-xl px-4 py-3 text-xs text-rose-200 font-bold focus:outline-none focus:border-rose-400 transition-all">
                                <option value="">— Pilih Unit / Ruangan Tujuan —</option>
                                @foreach($units as $unit)
                                <option value="{{ $unit->nama }}">{{ $unit->nama }}</option>
                                @endforeach
                            </select>
                            <p x-show="jenis_mutasi === 'Penghapusan'" class="text-[10px] text-rose-400 mt-1 font-semibold">
                                💡 Saran: Pilih <strong>Bagian Rumah Tangga & Inst Perbekalan</strong>
                            </p>
                            <p x-show="jenis_mutasi === 'Perbaikan'" class="text-[10px] text-amber-400 mt-1 font-semibold">
                                💡 Saran: Pilih <strong>Inst. IPS RS</strong> atau unit teknis terkait
                            </p>
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1.5">👤 Penanggung Jawab Penerima <span class="text-rose-400">*</span></label>
                            <input type="text" name="penanggung_jawab_tujuan" id="penanggung_jawab_tujuan" x-model="penanggung_jawab_tujuan" required
                                placeholder="Nama penanggung jawab ruangan tujuan..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-rose-500 transition-all">
                        </div>
                    </div>
                </div>

                {{-- Visualisasi Alur Perpindahan --}}
                <div class="flex items-center justify-center gap-3 pt-4 border-t border-slate-800/60 flex-wrap">
                    <span class="px-3.5 py-1.5 rounded-xl bg-slate-800 text-slate-300 text-[11px] font-bold border border-slate-700"
                        x-text="ruangan_asal || 'Ruangan Asal'"></span>
                    <div class="flex items-center space-x-1 text-rose-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </div>
                    <span class="px-3.5 py-1.5 rounded-xl bg-rose-500/20 text-rose-300 text-[11px] font-bold border border-rose-500/30"
                        x-text="ruangan_tujuan || 'Ruangan Tujuan'"></span>
                </div>
            </div>

            {{-- ===== SECTION 4: ALASAN ===== --}}
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-4">
                <div class="flex items-center space-x-2 pb-4 border-b border-slate-800">
                    <div class="w-7 h-7 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs font-extrabold">4</div>
                    <h2 class="text-sm font-extrabold text-white">Alasan & Keterangan</h2>
                </div>
                <div>
                    <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1.5">
                        Alasan / Urgensi <span class="text-white" x-text="jenis_mutasi"></span> <span class="text-rose-400">*</span>
                    </label>
                    <textarea name="alasan_mutasi" rows="4" :placeholder="alasanPlaceholder" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white resize-none focus:outline-none focus:border-rose-500 transition-all">{{ old('alasan_mutasi', $mutasi->alasan_mutasi ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1.5">Catatan Tambahan (Opsional)</label>
                    <textarea name="catatan_penerima" rows="2"
                        placeholder="Catatan tambahan untuk penerima / admin..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white resize-none focus:outline-none focus:border-rose-500 transition-all">{{ old('catatan_penerima', $mutasi->catatan_penerima ?? '') }}</textarea>
                </div>
            </div>

            {{-- ===== SECTION 5: DIAGRAM ALUR PERSETUJUAN ===== --}}
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl">
                <div class="flex items-center space-x-2 pb-5 border-b border-slate-800 mb-5">
                    <div class="w-7 h-7 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs font-extrabold">5</div>
                    <h2 class="text-sm font-extrabold text-white">Alur Persetujuan Setelah Pengajuan Dikirim</h2>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-1">
                    <div class="flex sm:flex-col items-center gap-3 sm:gap-2 flex-1 p-3 rounded-2xl bg-slate-950/60 border border-slate-800">
                        <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-lg shrink-0">📤</div>
                        <div class="sm:text-center">
                            <p class="text-[11px] font-extrabold text-white">Pengajuan Dikirim</p>
                            <p class="text-[9.5px] text-slate-400 mt-0.5">Unit pengirim mengajukan mutasi</p>
                        </div>
                    </div>
                    <div class="w-px h-6 sm:w-8 sm:h-px bg-gradient-to-b sm:bg-gradient-to-r from-slate-700 to-slate-600 mx-auto"></div>
                    <div class="flex sm:flex-col items-center gap-3 sm:gap-2 flex-1 p-3 rounded-2xl bg-slate-950/60 border border-slate-800">
                        <div class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-300 border border-teal-500/30 flex items-center justify-center text-lg shrink-0">🤝</div>
                        <div class="sm:text-center">
                            <p class="text-[11px] font-extrabold text-white">Penerima Menyetujui</p>
                            <p class="text-[9.5px] text-slate-400 mt-0.5">Ka. Ruangan unit tujuan setujui</p>
                        </div>
                    </div>
                    <div class="w-px h-6 sm:w-8 sm:h-px bg-gradient-to-b sm:bg-gradient-to-r from-slate-700 to-slate-600 mx-auto"></div>
                    <div class="flex sm:flex-col items-center gap-3 sm:gap-2 flex-1 p-3 rounded-2xl bg-slate-950/60 border border-slate-800">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center justify-center text-lg shrink-0">✅</div>
                        <div class="sm:text-center">
                            <p class="text-[11px] font-extrabold text-white">Admin Mengesahkan</p>
                            <p class="text-[9.5px] text-slate-400 mt-0.5">Final oleh Inst. Pembekalan</p>
                        </div>
                    </div>
                    <div class="w-px h-6 sm:w-8 sm:h-px bg-gradient-to-b sm:bg-gradient-to-r from-slate-700 to-slate-600 mx-auto"></div>
                    <div class="flex sm:flex-col items-center gap-3 sm:gap-2 flex-1 p-3 rounded-2xl bg-slate-950/60 border border-slate-800">
                        <div class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-300 border border-purple-500/30 flex items-center justify-center text-lg shrink-0">📄</div>
                        <div class="sm:text-center">
                            <p class="text-[11px] font-extrabold text-white">BAMB Dicetak</p>
                            <p class="text-[9.5px] text-slate-400 mt-0.5">Berita Acara Mutasi selesai</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="flex items-center justify-end gap-3 pb-4">
                <a href="{{ route('mutasi.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">Batal</a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-rose-500/20 transition-all flex items-center space-x-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span>{{ isset($mutasi) ? 'Simpan Perubahan' : 'Kirim Pengajuan Mutasi' }}</span>
                </button>
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
