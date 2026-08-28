<x-layout :title="request()->routeIs('pemeliharaan.edit') ? 'Ubah Log Pemeliharaan - SIMAT-RK' : 'Catat Pemeliharaan Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('pemeliharaan.edit') ? 'Ubah Log Pemeliharaan' : 'Catat Pemeliharaan Baru')
    @section('breadcrumb', request()->routeIs('pemeliharaan.edit') ? 'Master Utama / Pemeliharaan / Ubah' : 'Master Utama / Pemeliharaan / Catat Baru')

    <div x-data="{
        isEdit: {{ request()->routeIs('pemeliharaan.edit') ? 'true' : 'false' }},
        editId: '{{ request()->route('id') ?? '1' }}',
        formData: {
            kode: 'MTN-2026-003',
            nama: '',
            jenis: 'Servis Berkala & Maintenance',
            tgl: new Date().toISOString().split('T')[0],
            tgl_selesai: '',
            biaya: '',
            pelaksana: 'Teknisi IPSRS RSUD',
            status: 'Dalam Pengerjaan',
            keterangan: ''
        },

        parseToIso(dateStr) {
            if (!dateStr || dateStr === '-') return '';
            if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return dateStr;
            const months = { 'Jan':'01', 'Feb':'02', 'Mar':'03', 'Apr':'04', 'Mei':'05', 'Jun':'06', 'Jul':'07', 'Ags':'08', 'Sep':'09', 'Okt':'10', 'Nov':'11', 'Des':'12' };
            const parts = dateStr.trim().split(' ');
            if (parts.length === 3) {
                const d = parts[0].padStart(2, '0');
                const m = months[parts[1]] || '01';
                const y = parts[2];
                return `${y}-${m}-${d}`;
            }
            return '';
        },

        formatDateDisplay(dateStr) {
            if (!dateStr || dateStr === '-' || dateStr.trim() === '') return '-';
            if (dateStr.includes(' ') && !dateStr.includes('-')) return dateStr;
            try {
                const parts = dateStr.split('-');
                if (parts.length === 3) {
                    const y = parts[0];
                    const m = parseInt(parts[1]);
                    const d = parts[2].padStart(2, '0');
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                    return `${d} ${months[m - 1]} ${y}`;
                }
            } catch(e) {}
            return dateStr;
        },

        init() {
            let currentData = [];
            const stored = localStorage.getItem('simat_pemeliharaans');
            if (stored) {
                try {
                    currentData = JSON.parse(stored);
                } catch (e) {
                    currentData = [];
                }
            }

            if (this.isEdit) {
                const item = currentData.find(p => String(p.id) === String(this.editId));
                if (item) {
                    this.formData = {
                        kode: item.kode || 'MTN-2026-001',
                        kode_barang: item.kode_barang || '',
                        nama: item.nama || '',
                        jenis: item.jenis || '',
                        tgl: this.parseToIso(item.tgl) || new Date().toISOString().split('T')[0],
                        tgl_selesai: this.parseToIso(item.tgl_selesai) || (item.status === 'Selesai' ? new Date().toISOString().split('T')[0] : ''),
                        biaya: item.biaya || '',
                        pelaksana: item.pelaksana || '',
                        status: item.status || 'Dalam Pengerjaan',
                        keterangan: item.keterangan || ''
                    };
                } else {
                    this.formData = {
                        kode: 'MTN-2026-003',
                        kode_barang: '1.3.2.02.01.01.005',
                        nama: 'CT-Scan 128 Slice Siemens SOMATOM',
                        jenis: 'Kalibrasi Rutin & QC BAPETEN',
                        tgl: '2026-08-05',
                        tgl_selesai: '2026-08-10',
                        biaya: 'Rp 25.000.000',
                        pelaksana: 'PT. Siemens Healthcare Indonesia',
                        status: 'Selesai',
                        keterangan: 'Hasil uji fungsi akurat dan sertifikat kalibrasi terbit resmi'
                    };
                }
            } else {
                const urlParams = new URLSearchParams(window.location.search);
                const paramNama = urlParams.get('nama');
                const paramKodeBarang = urlParams.get('kode_barang');

                this.formData = {
                    kode: 'MTN-2026-' + String(Math.floor(Math.random() * 900) + 100),
                    kode_barang: paramKodeBarang ? decodeURIComponent(paramKodeBarang) : '',
                    nama: paramNama ? decodeURIComponent(paramNama) : '',
                    jenis: 'Servis Berkala & Maintenance',
                    tgl: new Date().toISOString().split('T')[0],
                    tgl_selesai: '',
                    biaya: '',
                    pelaksana: 'Teknisi IPSRS RSUD',
                    status: 'Dalam Pengerjaan',
                    keterangan: ''
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
                btnText: btnText || (type === 'danger' ? 'Ya, Hapus Data' : (type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Simpan Log Pemeliharaan')),
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
            if (!this.formData.nama || this.formData.nama.trim() === '') {
                this.toast = { show: true, message: '⚠️ Mohon masukkan nama aset yang dipelihara / diservis!', type: 'warning' };
                setTimeout(() => { this.toast.show = false; }, 4000);
                return;
            }

            this.askConfirmation({
                title: this.isEdit ? '✏️ Konfirmasi Simpan Perubahan Log Pemeliharaan' : '🛠️ Konfirmasi Catat Log Pemeliharaan Baru',
                message: this.isEdit ? 'Apakah Anda yakin ingin menyimpan perubahan data log pemeliharaan aset ini?' : 'Apakah Anda yakin ingin mencatat kegiatan pemeliharaan / kalibrasi aset ini?',
                itemName: (this.formData.nama || 'Aset ASTAP') + ' (' + (this.formData.kode || 'KODE') + ')',
                type: this.isEdit ? 'warning' : 'success',
                btnText: this.isEdit ? '✏️ Ya, Simpan Perubahan' : '🛠️ Ya, Simpan Log Pemeliharaan',
                onConfirm: () => {
                    let currentData = [];
                    const stored = localStorage.getItem('simat_pemeliharaans');
                    if (stored) {
                        try {
                            currentData = JSON.parse(stored);
                        } catch (e) {
                            currentData = [];
                        }
                    }

                    // Pastikan jika status Selesai, tanggal selesai wajib terisi
                    if (this.formData.status === 'Selesai' && (!this.formData.tgl_selesai || this.formData.tgl_selesai === '-')) {
                        this.formData.tgl_selesai = new Date().toISOString().split('T')[0];
                    }

                    const displayTgl = this.formatDateDisplay(this.formData.tgl);
                    const displayTglSelesai = this.formData.status === 'Selesai' ? this.formatDateDisplay(this.formData.tgl_selesai) : '-';

                    if (this.isEdit) {
                        const idx = currentData.findIndex(p => String(p.id) === String(this.editId) || p.kode === this.formData.kode);
                        const updatedItem = {
                            id: idx !== -1 ? currentData[idx].id : (isNaN(this.editId) ? Date.now() : Number(this.editId)),
                            kode: this.formData.kode,
                            kode_barang: this.formData.kode_barang || '',
                            nama: this.formData.nama,
                            jenis: this.formData.jenis,
                            tgl: displayTgl,
                            tgl_selesai: displayTglSelesai,
                            biaya: this.formData.biaya || 'Rp 0',
                            pelaksana: this.formData.pelaksana || '-',
                            status: this.formData.status,
                            keterangan: this.formData.keterangan || '-'
                        };

                        if (idx !== -1) {
                            currentData[idx] = updatedItem;
                        } else {
                            currentData.unshift(updatedItem);
                        }
                    } else {
                        const newItem = {
                            id: Date.now(),
                            kode: this.formData.kode,
                            kode_barang: this.formData.kode_barang || '',
                            nama: this.formData.nama,
                            jenis: this.formData.jenis,
                            tgl: displayTgl,
                            tgl_selesai: displayTglSelesai,
                            biaya: this.formData.biaya || 'Rp 0',
                            pelaksana: this.formData.pelaksana || '-',
                            status: this.formData.status,
                            keterangan: this.formData.keterangan || '-'
                        };
                        currentData.unshift(newItem);
                    }

                    localStorage.setItem('simat_pemeliharaans', JSON.stringify(currentData));
                    this.toast = { show: true, message: '✅ Log Pemeliharaan berhasil disimpan!', type: 'success' };
                    setTimeout(() => {
                        window.location.href = '{{ route('pemeliharaan.index') }}';
                    }, 1200);
                }
            });
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
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl w-full space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" :class="formData.status === 'Selesai' ? 'md:grid-cols-3' : 'md:grid-cols-2'">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Nomor Registrasi Servis</label>
                    <input type="text" x-model="formData.kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-amber-400 font-mono font-bold">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Tanggal Pelaksanaan</label>
                    <input type="date" x-model="formData.tgl" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                </div>
                <div x-show="formData.status === 'Selesai'" x-transition x-cloak>
                    <label class="block text-emerald-400 font-semibold text-xs mb-1.5">Tanggal Selesai</label>
                    <input type="date" 
                        x-model="formData.tgl_selesai" 
                        class="w-full bg-slate-950 border border-emerald-500/40 rounded-xl px-4 py-3 text-xs text-emerald-300 focus:outline-none focus:border-emerald-400">
                </div>
            </div>

            <div>
                <label class="block text-slate-200 font-bold text-xs mb-1.5 flex items-center justify-between">
                    <span>Nama Aset yang Dipelihara / Diperbaiki</span>
                    <span class="text-[10px] text-amber-400 font-normal">💡 Terhubung dengan Katalog Master ASTAP</span>
                </label>
                <input type="text" list="astapAssetsList" x-model="formData.nama" placeholder="Pilih atau ketik nama aset ASTAP..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white font-bold focus:outline-none focus:border-amber-500">
                <datalist id="astapAssetsList">
                    <option value="Submersible Pump 7.5 HP">1.3.2.01.03.05.005 - Franklin Electric</option>
                    <option value="Pompa Air Shimizu PS-130">1.3.2.01.03.05.010 - Shimizu</option>
                    <option value="CT-Scan 128 Slice High Resolution">1.3.2.02.01.01.005 - Siemens SOMATOM</option>
                    <option value="USG 4D Color Doppler">1.3.2.02.01.01.012 - GE Healthcare</option>
                    <option value="Instalasi Pipa Gas Oksigen IGD">1.3.4.03.01.01.001 - Jaringan Medis</option>
                    <option value="Gedung Paviliun Graha Amukti Lt 1">1.3.3.01.01.01.002 - Gedung Perawatan</option>
                </datalist>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Jenis Tindakan Pemeliharaan</label>
                    <input type="text" x-model="formData.jenis" placeholder="Contoh: Kalibrasi Tahunan / Ganti Sparepart..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                </div>
                <div>
                    <label class="block text-emerald-400 font-bold text-xs mb-1.5">Biaya Pemeliharaan (Rp)</label>
                    <input type="text" 
                        x-model="formData.biaya" 
                        @input="let val = $event.target.value.replace(/[^0-9]/g, ''); formData.biaya = val ? 'Rp ' + val.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : ''"
                        placeholder="Contoh: Rp 5.000.000" 
                        class="w-full bg-slate-950 border border-emerald-500/40 rounded-xl px-4 py-3 text-xs text-emerald-300 font-mono font-bold focus:outline-none focus:border-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Teknisi Pelaksana / Vendor Rekanan</label>
                    <input type="text" x-model="formData.pelaksana" placeholder="IPSRS RSUD / PT. Vendor..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1.5">Status Servis</label>
                    <select x-model="formData.status"
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white"
                            @change="if(formData.status === 'Selesai') formData.tgl_selesai = new Date().toISOString().split('T')[0]">
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
                        <span x-text="confirmData.type === 'danger' ? '🗑️' : (confirmData.type === 'warning' ? '✏️' : '🛠️')"></span>
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
