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

        submitForm() {
            if (!this.formData.nama || this.formData.nama.trim() === '') {
                alert('⚠️ Mohon masukkan nama aset yang dipelihara / diservis!');
                return;
            }

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
            alert('✅ Log Pemeliharaan (' + this.formData.nama + ') berhasil disimpan ke tabel!');
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

    </div>
</x-layout>
