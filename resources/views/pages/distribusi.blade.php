@php
    $isSubAdmin = (Auth::user()->role ?? '') === 'sub_admin';
    $pageTitle = $isSubAdmin ? 'Pengajuan Baru' : 'Distribusi ASTAP';
    $breadcrumbTitle = $isSubAdmin ? 'Master Utama / Pengajuan Baru' : 'Master Utama / Distribusi ASTAP';
@endphp

<x-layout :title="$pageTitle . ' - SIMAT-RK'">
    @section('page-title', $pageTitle)
    @section('breadcrumb', $breadcrumbTitle)

    <script>
        function distribusiCatalog() {
            return {
                userRole: {{ Js::from(Auth::user()->role) }},
                userUnit: {{ Js::from(Auth::user()->unitModel?->nama ?? Auth::user()->unit ?? '') }},
                userName: {{ Js::from(Auth::user()->name) }},

                searchQuery: '',
                unitFilter: 'all',
                statusFilter: 'all',
                showDetailModal: false,
                showEditModal: false,
                showPrintBastModal: false,
                showEditBastForm: false,
                selectedDistribusi: null,

                unitList: {{ Js::from($units ?? []) }},

                distribusis: {{ Js::from($distribusis ?? []) }},

                init() {
                    // Sub admin: SELALU gunakan data dari backend (jangan baca localStorage)
                    // karena localStorage bisa berisi data sesi admin/user lain
                    if (this.userRole === 'sub_admin') {
                        localStorage.removeItem('simat_distribusis');
                        return; // distribusis sudah di-set dari {{ Js::from($distribusis) }}
                    }

                    // Admin/Master: prioritaskan data dari DB, fallback ke localStorage
                    if (this.distribusis && this.distribusis.length > 0) {
                        localStorage.setItem('simat_distribusis', JSON.stringify(this.distribusis));
                        return;
                    }
                    const stored = localStorage.getItem('simat_distribusis');
                    if (stored) {
                        try {
                            const parsed = JSON.parse(stored);
                            if (Array.isArray(parsed) && parsed.length > 0) {
                                this.distribusis = parsed;
                            }
                        } catch (e) {
                            this.distribusis = [];
                        }
                    }
                },

                saveToStorage() {
                    // Sub admin: jangan simpan ke localStorage
                    if (this.userRole === 'sub_admin') return;
                    localStorage.setItem('simat_distribusis', JSON.stringify(this.distribusis));
                },

                showConfirmModal: false,
                confirmData: {
                    title: 'Konfirmasi Tindakan',
                    message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                    itemName: '',
                    type: 'danger',
                    btnText: 'Ya, Lanjutkan',
                    onConfirm: null
                },

                toast: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                askConfirmation({ title, message, itemName, type = 'danger', btnText, onConfirm }) {
                    this.confirmData = {
                        title: title || 'Konfirmasi Tindakan',
                        message: message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                        itemName: itemName || '',
                        type: type,
                        btnText: btnText || (type === 'danger' ? 'Ya, Hapus Data' : (type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Tambahkan')),
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

                showSimatToast(message, type = 'success') {
                    this.toast = { show: true, message: message, type: type };
                    setTimeout(() => { this.toast.show = false; }, 4000);
                },

                deleteDistribusi(item) {
                    if (!item) return;
                    const targetObj = typeof item === 'object' && item !== null ? item : this.distribusis.find(d => d.id === item);
                    const targetId = typeof item === 'object' && item !== null ? item.id : item;

                    this.askConfirmation({
                        title: '⚠️ Konfirmasi Hapus Transaksi Distribusi',
                        message: 'Apakah Anda yakin ingin menghapus data transaksi distribusi aset ini dari sistem? Status unit register NIBAR terkait akan dikembalikan ke Gudang Aset Utama.',
                        itemName: targetObj ? ((targetObj.kode || 'DIST') + ' - ' + (targetObj.nama || 'Aset') + ' (' + (targetObj.tujuan || 'Unit') + ')') : ('ID: ' + targetId),
                        type: 'danger',
                        btnText: '🗑️ Ya, Hapus Transaksi Distribusi',
                        onConfirm: async () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            try {
                                await fetch('/distribusi/' + targetId, {
                                    method: 'DELETE',
                                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                                });
                                window.location.reload();
                            } catch(err) {
                                window.location.reload();
                            }
                        }
                    });
                },

                unitSearchQuery: '',
                isUnitDropdownOpen: false,

                get filteredUnitDropdownList() {
                    if (!this.unitSearchQuery || this.unitSearchQuery.trim().length === 0) {
                        return this.unitList;
                    }
                    const q = this.unitSearchQuery.toLowerCase().trim();
                    return this.unitList.filter(u => 
                        (u.nama || '').toLowerCase().includes(q) ||
                        (u.kode || '').toLowerCase().includes(q) ||
                        (u.tipe || '').toLowerCase().includes(q) ||
                        (u.kepala || '').toLowerCase().includes(q)
                    );
                },

                selectUnitFilter(unit) {
                    if (!unit) {
                        this.unitFilter = 'all';
                        this.unitSearchQuery = '';
                    } else {
                        this.unitFilter = unit.nama;
                        this.unitSearchQuery = unit.nama;
                    }
                    this.isUnitDropdownOpen = false;
                },

                clearUnitFilter() {
                    this.unitFilter = 'all';
                    this.unitSearchQuery = '';
                    this.isUnitDropdownOpen = false;
                },

                get filteredDistribusis() {
                    const query = (this.searchQuery || '').toLowerCase().trim();
                    const role = this.userRole;
                    const myUnit = (this.userUnit || '').toLowerCase().trim();
                    const myName = (this.userName || '').toLowerCase().trim();

                    return this.distribusis.filter(item => {
                        // Jika Sub Admin: hanya tampilkan distribusi yang melibatkan unit atau penerima dirinya
                        if (role === 'sub_admin' && myUnit) {
                            const isUnitMatch = (item.tujuan || '').toLowerCase().includes(myUnit) ||
                                                (item.pj_ruangan || '').toLowerCase().includes(myUnit) ||
                                                myUnit.includes((item.tujuan || '').toLowerCase());
                            const isPenerimaMatch = (item.penerima || '').toLowerCase().includes(myName) ||
                                                    (item.pj_nama || '').toLowerCase().includes(myName);
                            if (!isUnitMatch && !isPenerimaMatch) return false;
                        }

                        const matchSearch = !query ||
                                            (item.nama || '').toLowerCase().includes(query) || 
                                            (item.kode || '').toLowerCase().includes(query) || 
                                            (item.penerima || '').toLowerCase().includes(query) ||
                                            (item.items || []).some(it => (it.nama_barang || '').toLowerCase().includes(query));

                        const matchUnit = this.unitFilter === 'all' || 
                                          item.tujuan.toLowerCase().includes(this.unitFilter.toLowerCase()) ||
                                          this.unitFilter.toLowerCase().includes(item.tujuan.toLowerCase());

                        const matchStatus = this.statusFilter === 'all' || item.status === this.statusFilter;
                        return matchSearch && matchUnit && matchStatus;
                    });
                },

                getTotalQty(items) {
                    if (!items || !items.length) return 0;
                    return items.reduce((acc, curr) => acc + (parseInt(curr.qty) || 0), 0);
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.unitFilter = 'all';
                    this.unitSearchQuery = '';
                    this.isUnitDropdownOpen = false;
                    this.statusFilter = 'all';
                },

                openDetail(item) {
                    this.selectedDistribusi = item;
                    this.showDetailModal = true;
                },

                openEdit(item) {
                    this.selectedDistribusi = { ...item };
                    this.showEditModal = true;
                },

                openPrintBast(item) {
                    const unitPerbekalan = (this.unitList || []).find(u => (u.nama || '').toLowerCase().includes('perbekalan') || (u.nama || '').toLowerCase().includes('rumah tangga'));
                    this.selectedDistribusi = { 
                        ...item,
                        bast_nomor: item.bast_nomor || item.nomor_bast || '032 / 034 / 430.10.7 / 2026',
                        nomor_bast: item.nomor_bast || item.bast_nomor || '032 / 034 / 430.10.7 / 2026',
                        hari: item.hari || 'Kamis',
                        tanggal_angka: item.tanggal_angka || '13',
                        bulan: item.bulan || 'Agustus',
                        tahun: item.tahun || '2026',
                        tahun_anggaran: item.tahun_anggaran || '2026',
                        sk_bupati_nomor: item.sk_bupati_nomor || '188.45/430.10.7/2026',
                        sk_bupati_tanggal: item.sk_bupati_tanggal || '02 Januari 2026',
                        pengurus_nama: item.pengurus_nama || unitPerbekalan?.kepala || 'BUDI HARTONO,S.Sos',
                        pengurus_nip: item.pengurus_nip || unitPerbekalan?.nip || '19760229 200801 1 010',
                        pengurus_jabatan: item.pengurus_jabatan || 'Pengurus Barang',
                        pengurus_ruangan: item.pengurus_ruangan || unitPerbekalan?.nama || 'Gudang Perbekalan',
                        pj_nama: item.pj_nama || item.penerima || 'ESTU PRATIKA SARI, SST',
                        pj_nip: item.pj_nip || '199409242023212002',
                        pj_jabatan: item.pj_jabatan || ('Supervisor ' + (item.tujuan || item.pj_ruangan || 'Front Office')),
                        unit_nama: item.tujuan || item.pj_ruangan || 'FO',
                    };
                    this.showPrintBastModal = true;
                },

                terimaDistribusi(item) {
                    this.toggleSignDistribusi(item);
                },

                toggleSignDistribusi(item) {
                    if (!item) return;
                    const target = this.distribusis.find(d => d.id === item.id) || item;
                    if (target.signed) {
                        target.signed = false;
                        target.tgl_signed = '-';
                        target.qr_hash = '';
                        target.status = 'Menunggu Konfirmasi';
                        item.signed = false;
                        item.tgl_signed = '-';
                        item.qr_hash = '';
                        item.status = 'Menunggu Konfirmasi';
                        alert('↩️ Tanda tangan digital BSrE BAST Distribusi (' + (target.nomor_bast || target.kode) + ') berhasil dibatalkan.');
                    } else {
                        target.signed = true;
                        const now = new Date();
                        target.tgl_signed = now.toLocaleDateString('id-ID') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';
                        target.qr_hash = 'BSRE-KOESNANDI-DST-' + Date.now();
                        target.status = 'Telah Diterima';
                        item.signed = true;
                        item.tgl_signed = target.tgl_signed;
                        item.qr_hash = target.qr_hash;
                        item.status = 'Telah Diterima';
                        alert('✍️ BAST Distribusi (' + (target.nomor_bast || target.kode) + ') berhasil ditandatangani secara digital (QR Code BSrE Aktif)!');
                    }
                    this.saveToStorage();
                },

                tolakDistribusi(item) {
                    if (!item) return;
                    item.signed = false;
                    item.tgl_signed = '-';
                    item.qr_hash = '';
                    item.status = 'Ditolak';

                    // Sync with main array item
                    const found = this.distribusis.find(d => d.id === item.id);
                    if (found) {
                        found.signed = false;
                        found.tgl_signed = '-';
                        found.qr_hash = '';
                        found.status = 'Ditolak';
                    }
                    this.saveToStorage();
                    alert('❌ BAST Distribusi (' + (item.kode || item.bast_nomor) + ') ditolak! Tanda tangan digital telah dihapus.');
                },

                copiedNibar: null,
                copyNibar(nibar) {
                    navigator.clipboard.writeText(nibar).then(() => {
                        this.copiedNibar = nibar;
                        setTimeout(() => {
                            if (this.copiedNibar === nibar) {
                                this.copiedNibar = null;
                            }
                        }, 2000);
                    });
                },

                kondisiSaving: {},   // { reg_id: true/false }
                kondisiSaved: {},    // { reg_id: true } — tampil centang sesaat

                updateKondisiRegister(regId, newKondisi, item) {
                    if (!regId) {
                        alert('⚠️ Register ID tidak ditemukan untuk NIBAR ini. Kondisi tidak dapat disimpan ke database.');
                        return;
                    }
                    this.kondisiSaving[regId] = true;

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    fetch('/distribusi/register-kondisi/' + regId, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ kondisi: newKondisi })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.kondisiSaving[regId] = false;
                        if (data.success) {
                            this.kondisiSaved[regId] = true;
                            // Update kondisi di nibar_registers item yang bersangkutan
                            if (item && item.nibar_registers) {
                                const reg = item.nibar_registers.find(r => r.reg_id == regId);
                                if (reg) reg.kondisi = newKondisi;
                            }
                            // Update kondisi ringkasan item (ambil dari register pertama)
                            if (item && item.nibar_registers && item.nibar_registers.length > 0) {
                                item.kondisi = item.nibar_registers[0].kondisi;
                            }
                            setTimeout(() => { delete this.kondisiSaved[regId]; }, 2000);
                        } else {
                            alert('⚠️ Gagal menyimpan kondisi: ' + (data.message || 'Terjadi kesalahan.'));
                        }
                    })
                    .catch(() => {
                        this.kondisiSaving[regId] = false;
                        alert('⚠️ Koneksi gagal. Pastikan server berjalan dan coba kembali.');
                    });
                },

                printCurrentBast() {
                    const el = document.getElementById('print-area-bast');
                    if (!el) {
                        window.print();
                        return;
                    }

                    let iframe = document.getElementById('simat-print-frame');
                    if (iframe) {
                        iframe.remove();
                    }

                    iframe = document.createElement('iframe');
                    iframe.id = 'simat-print-frame';
                    iframe.style.position = 'fixed';
                    iframe.style.right = '0';
                    iframe.style.bottom = '0';
                    iframe.style.width = '0';
                    iframe.style.height = '0';
                    iframe.style.border = '0';
                    document.body.appendChild(iframe);

                    const doc = iframe.contentWindow.document;
                    doc.open();
                    doc.write(`<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Penyerahan Barang - RSUD dr. H. Koesnadi</title>
    <script src="https://cdn.tailwindcss.com"><\/script>
    <style>
        @page {
            size: auto;
            margin: 12mm 15mm;
        }
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif !important;
            font-size: 10pt !important;
            line-height: 1.5 !important;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        .page-break-avoid {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
    </style>
</head>
<body class="bg-white text-black font-sans text-[10pt]">
    <div style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 10pt; color: #000000;">
        ${el.innerHTML}
    </div>
</body>
</html>`);
                    doc.close();

                    setTimeout(() => {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    }, 400);
                }
            }
        }
    </script>

    <div x-data="distribusiCatalog()" x-cloak>

        <!-- KONTEN UTAMA KATALOG DISTRIBUSI (DISEMBUNYIKAN SAAT DICETAK) -->
        <div class="no-print space-y-6">

            <!-- Header Banner & Mini KPI Strip -->
            <div class="bg-gradient-to-r from-teal-600/15 via-slate-900 to-slate-900 border border-teal-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                        <span>{{ $isSubAdmin ? 'PENGAJUAN & PERMOHONAN BARANG ASET RUANGAN' : 'PENYERAHAN & ALOKASI BARANG ASET RSUD' }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">{{ $isSubAdmin ? 'Katalog Pengajuan Baru' : 'Katalog Distribusi ASTAP' }}</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        {{ $isSubAdmin 
                            ? 'Daftar pengajuan permohonan alokasi barang aset ruangan. Anda dapat mengajukan permintaan kebutuhan barang baru untuk unit/ruangan Anda.' 
                            : 'Pengelolaan alokasi penyerahan barang aset dari inventaris ke paviliun rawat inap, unit RSUD, dan instalasi RSUD. Mendukung distribusi beberapa barang sekaligus dalam satu transaksi.' }}
                    </p>
                </div>
                
                <!-- Action Button Input Baru -->
                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('distribusi.create') }}"
                        class="px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>{{ $isSubAdmin ? 'Input Pengajuan Baru' : 'Input Distribusi Baru' }}</span>
                    </a>
                </div>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 text-lg">🚚</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Distribusi</span>
                        <span class="text-sm sm:text-base font-extrabold text-white" x-text="distribusis.length + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">📦</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Telah Diterima</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-300" x-text="distribusis.filter(d => d.status === 'Telah Diterima' || d.status === 'Diterima').length + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">🚛</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Dalam Kirim</span>
                        <span class="text-sm sm:text-base font-extrabold text-cyan-300" x-text="distribusis.filter(d => d.status === 'Dalam Pengiriman' || d.status === 'Dikirim').length + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">⏳</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Menunggu Konfirmasi</span>
                        <span class="text-sm sm:text-base font-extrabold text-amber-300" x-text="distribusis.filter(d => d.status === 'Menunggu Konfirmasi' || d.status === 'Pending').length + ' Transaksi'"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Section (Filter Unit Mengambil Dinamis dari Database Unit) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col gap-4">
                
                <!-- Baris Atas: Input Pencarian & Counter Data -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari no. distribusi / nama aset barang / pegawai penerima..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 transition-all">
                        <svg class="w-4 h-4 text-teal-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                            Menampilkan <span class="text-teal-400 font-bold" x-text="filteredDistribusis.length"></span> dari <span class="text-white font-bold" x-text="distribusis.length"></span> Data
                        </span>
                        <button type="button" @click="resetFilters()"
                            class="px-3 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                            🔄 Reset
                        </button>
                    </div>
                </div>

                <!-- Baris Bawah: Filter Dropdown Unit (Searchable Ketik Filter) & Filter Status -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-800/80">
                    
                    <!-- 1. FILTER UNIT / PAVILIUN (SEARCHABLE INPUT / KETIK FILTER DINAMIS DARI DATABASE) -->
                    <div class="relative" @click.away="isUnitDropdownOpen = false">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center space-x-1.5">
                                <span>🏥 Filter Unit / Paviliun Penerima</span>
                                <span class="text-teal-400 font-mono text-[10px]" x-text="'(' + unitList.length + ' Unit RSUD)'"></span>
                            </label>
                            <template x-if="unitFilter !== 'all'">
                                <button type="button" @click="clearUnitFilter()" class="text-[10px] text-rose-400 hover:text-rose-300 font-bold flex items-center space-x-0.5">
                                    <span>✕ Reset Filter Unit</span>
                                </button>
                            </template>
                        </div>

                        <div class="relative">
                            <input type="text" 
                                   x-model="unitSearchQuery" 
                                   @focus="isUnitDropdownOpen = true"
                                   @input="isUnitDropdownOpen = true; if(unitSearchQuery.trim() === '') unitFilter = 'all'"
                                   placeholder="Ketik nama unit (misal: 'igd', 'melati', 'radiologi', 'graha')..." 
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 pl-9 pr-8 text-xs text-white font-semibold placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all">
                            
                            <svg class="w-4 h-4 text-teal-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>

                            <template x-if="unitSearchQuery && unitSearchQuery.length > 0">
                                <button type="button" @click="clearUnitFilter()" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-rose-400 p-0.5 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </template>
                        </div>

                        <!-- Dropdown Floating Hasil Pencarian Unit -->
                        <div x-show="isUnitDropdownOpen" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute left-0 right-0 z-40 mt-1 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-64 overflow-y-auto divide-y divide-slate-800">
                            
                            <!-- Opsi Semua Unit -->
                            <div @click="selectUnitFilter(null)" 
                                 class="px-4 py-2.5 hover:bg-teal-500/15 cursor-pointer transition-colors flex items-center justify-between font-bold text-xs"
                                 :class="unitFilter === 'all' ? 'bg-teal-500/10 text-teal-300' : 'text-slate-300'">
                                <div class="flex items-center space-x-2">
                                    <span>🏢</span>
                                    <span>Semua Unit & Paviliun (Seluruh RSUD)</span>
                                </div>
                                <template x-if="unitFilter === 'all'">
                                    <span class="text-teal-400 text-xs">✓ Aktif</span>
                                </template>
                            </div>

                            <!-- List Unit Terfilter -->
                            <template x-for="u in filteredUnitDropdownList" :key="u.id">
                                <div @click="selectUnitFilter(u)" 
                                     class="px-4 py-2.5 hover:bg-teal-500/15 cursor-pointer transition-colors group flex items-center justify-between"
                                     :class="unitFilter === u.nama ? 'bg-teal-500/10 text-teal-300' : 'text-slate-300'">
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-xs group-hover:text-teal-300 flex items-center space-x-1.5 text-white">
                                            <span>🏥</span>
                                            <span x-text="u.nama"></span>
                                        </div>
                                        <div class="text-[10px] text-slate-400" x-text="(u.kode ? u.kode + ' • ' : '') + u.tipe + ' • PJ: ' + u.kepala"></div>
                                    </div>
                                    <template x-if="unitFilter === u.nama">
                                        <span class="text-teal-400 font-bold text-xs">✓ Terpilih</span>
                                    </template>
                                </div>
                            </template>

                            <template x-if="filteredUnitDropdownList.length === 0">
                                <div class="px-4 py-4 text-center text-xs text-slate-400">
                                    <p class="font-semibold text-amber-400">Unit "<span x-text="unitSearchQuery"></span>" tidak ditemukan</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Coba ketik kata kunci nama ruangan lainnya</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- 2. FILTER STATUS PENYERAHAN -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <span>Status Penyerahan Distribusi</span>
                            </label>
                            <template x-if="statusFilter !== 'all'">
                                <button type="button" @click="statusFilter = 'all'" class="text-[10px] text-rose-400 hover:text-rose-300 font-bold">
                                    <span>✕ Reset Status</span>
                                </button>
                            </template>
                        </div>
                        <div>
                            <select x-model="statusFilter" 
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 font-semibold focus:outline-none focus:border-teal-500 transition-all cursor-pointer">
                                <option value="all">🔍 Semua Status Penyerahan BAST</option>
                                <option value="Telah Diterima">✅ Telah Diterima & Disahkan</option>
                                <option value="Ditolak">❌ Ditolak</option>
                                <option value="Dalam Pengiriman">🚚 Dalam Pengiriman (Siap Kirim)</option>
                                <option value="Menunggu Konfirmasi">⏳ Menunggu Konfirmasi (Verifikasi)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Distribusi ASTAP (Multi-Barang / Transaksi) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
            <div class="overflow-x-auto rounded-2xl border border-slate-800/80 bg-slate-950/40">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 shadow-sm shrink-0">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap">No</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">No. Distribusi</th>
                            <th class="px-4 py-3.5 text-left min-w-[260px]">Rincian Barang yang Didistribusikan</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Tujuan Unit / Ruangan</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Tgl Distribusi</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Penerima</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Status</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950 border-l border-slate-800" style="position: sticky; right: 0; z-index: 20; background-color: #020617; box-shadow: -4px 0 10px rgba(0,0,0,0.4);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredDistribusis" :key="item.id">
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>
                                <td class="px-4 py-4 text-center font-mono font-semibold text-teal-400 whitespace-nowrap" x-text="item.kode"></td>
                                
                                <!-- Kolom Barang: Ringkasan Rapi (Detail Lengkap Dapat Dilihat di Modal Detail / BAST) -->
                                <td class="px-4 py-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center space-x-2">
                                            <p class="font-extrabold text-white text-xs" x-text="item.nama"></p>
                                            <template x-if="item.items && item.items.length > 0">
                                                <span class="px-2.5 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] font-bold shrink-0"
                                                      x-text="item.items.length + ' Jenis Barang'">
                                                </span>
                                            </template>
                                        </div>
                                        <template x-if="item.keterangan">
                                            <p class="text-[11px] text-slate-400 truncate max-w-xs sm:max-w-md" x-text="'Catatan: ' + item.keterangan"></p>
                                        </template>
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="font-bold text-slate-200 bg-slate-950 px-2.5 py-1 rounded-lg border border-slate-800" x-text="item.tujuan"></span>
                                </td>
                                <td class="px-4 py-4 text-center font-mono text-slate-300 whitespace-nowrap" x-text="item.tgl"></td>
                                <td class="px-4 py-4 text-center font-semibold text-white whitespace-nowrap" x-text="item.penerima"></td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap tracking-wide leading-none border shadow-sm select-none"
                                        :class="{
                                            'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': item.status === 'Telah Diterima' || item.status === 'Diterima',
                                            'bg-cyan-500/15 text-cyan-300 border-cyan-500/30': item.status === 'Dalam Pengiriman' || item.status === 'Dikirim',
                                            'bg-amber-500/15 text-amber-300 border-amber-500/30': item.status === 'Menunggu Konfirmasi' || item.status === 'Pending',
                                            'bg-slate-500/15 text-slate-300 border-slate-500/30': item.status === 'Draft' || !item.status
                                        }"
                                        x-text="item.status || 'Draft'"></span>
                                </td>
                                <!-- Kolom Aksi — FREEZE STICKY RIGHT -->
                                <td class="px-4 py-4 text-center space-x-1.5 whitespace-nowrap border-l border-slate-800 bg-slate-900" style="position: sticky; right: 0; z-index: 10; background-color: #0f172a; box-shadow: -4px 0 8px rgba(0,0,0,0.3);">
                                    
                                    <!-- 1. Tombol Cetak Berita Acara (BAST) di Kolom Aksi -->
                                    <button type="button" @click="openPrintBast(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-purple-500/15 text-purple-300 hover:bg-purple-500/25 border border-purple-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        <span>Cetak Berita Acara</span>
                                    </button>

                                    <!-- 2. Tombol Detail Modal -->
                                    <button type="button" @click="openDetail(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-teal-500/15 text-teal-300 hover:bg-teal-500/25 border border-teal-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Detail</span>
                                    </button>

                                    <!-- 3. Tombol Ubah Form -->
                                    <a :href="'/distribusi/' + item.id + '/edit'"
                                        class="px-2.5 py-1.5 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white border border-slate-700 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Ubah</span>
                                    </a>
                                    
                                    <!-- 4. Tombol Hapus -->
                                    <button type="button" @click="deleteDistribusi(item.id)"
                                        class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus</span>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State Baris Penuh Tinggi -->
                        <template x-if="filteredDistribusis.length === 0">
                            <tr>
                                <td colspan="8" class="text-center align-middle py-28 text-slate-400">
                                    <div class="flex flex-col items-center justify-center space-y-2 py-4">
                                        <p class="text-sm font-semibold text-slate-300">
                                            {{ $isSubAdmin ? 'Belum ada data permohonan pengajuan baru.' : 'Tidak ada data transaksi distribusi yang cocok.' }}
                                        </p>
                                        <p class="text-xs text-slate-500">Coba sesuaikan kata kunci pencarian atau filter status Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- END KONTEN UTAMA KATALOG (no-print) -->

        <!-- ========================================================================= -->
        <!-- MODAL CETAK BERITA ACARA PENYERAHAN BARANG (DISTRIBUSI RSUD KOESNANDI)   -->
        <!-- FORMAT RESMI 100% SESUAI FISIK STANDAR RUMAH SAKIT UMUM DAERAH           -->
        <!-- ========================================================================= -->
        <div x-show="showPrintBastModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.away="showPrintBastModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <!-- Action Bar Modal (Hidden When Printed) -->
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-purple-500/20 text-purple-300 text-sm">📑</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Berita Acara Penyerahan Barang (Distribusi)</h3>
                            <p class="text-[11px] text-slate-400" x-text="selectedDistribusi ? ('Penyerahan kepada: ' + (selectedDistribusi.pj_nama || selectedDistribusi.penerima) + ' (' + selectedDistribusi.tujuan + ')') : ''"></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                        <button type="button" @click="terimaDistribusi(selectedDistribusi)"
                            class="px-3.5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg transition-all active:scale-95 flex items-center space-x-1"
                            title="Tanda Tangan Digital BSrE">
                            <span>✍️ TTD BSrE</span>
                        </button>

                        <button type="button" @click="showEditBastForm = !showEditBastForm"
                            class="px-3.5 py-2 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95">
                            <span x-text="showEditBastForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                        </button>

                        <button type="button" @click="printCurrentBast()"
                            class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak (Print / PDF)</span>
                        </button>

                        <button type="button" @click="showPrintBastModal = false" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all active:scale-95">
                            Tutup
                        </button>
                    </div>
                </div>

                <!-- Formulir Cepat Live Edit Pejabat & Data Surat BAST Distribusi -->
                <template x-if="selectedDistribusi">
                    <div x-show="showEditBastForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-teal-500/40 text-xs space-y-3 shadow-inner">
                        <div class="font-bold text-teal-300 text-[11px] uppercase tracking-wider border-b border-slate-800 pb-2">
                            ✏️ Live Edit Berita Acara Penyerahan Barang (Otomatis Berubah Pada Lembar Cetak):
                        </div>
                        <!-- Info Surat, Tanggal & SK Bupati (1 grid gabungan) -->
                        <div class="grid grid-cols-2 sm:grid-cols-7 gap-2.5">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor Surat</label>
                                <input type="text" x-model="selectedDistribusi.bast_nomor" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-teal-300 font-mono font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Hari</label>
                                <input type="text" x-model="selectedDistribusi.hari" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tgl</label>
                                <input type="text" x-model="selectedDistribusi.tanggal_angka" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Bulan</label>
                                <input type="text" x-model="selectedDistribusi.bulan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tahun</label>
                                <input type="text" x-model="selectedDistribusi.tahun" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tahun Anggaran</label>
                                <input type="text" x-model="selectedDistribusi.tahun_anggaran" placeholder="2026" class="w-full bg-slate-900 border border-amber-700/50 rounded-lg px-2.5 py-1 text-amber-300 font-bold text-xs">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor SK Bupati</label>
                                <input type="text" x-model="selectedDistribusi.sk_bupati_nomor" placeholder="188.45/430.10.7/2026" class="w-full bg-slate-900 border border-amber-700/50 rounded-lg px-2.5 py-1 text-amber-200 font-mono text-xs">
                            </div>
                        </div>
                        <!-- Tanggal SK Bupati -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div class="sm:col-span-1">
                                <label class="block text-slate-400 text-[10px] mb-1">Tanggal SK Bupati</label>
                                <input type="text" x-model="selectedDistribusi.sk_bupati_tanggal" placeholder="02 Januari 2026" class="w-full bg-slate-900 border border-amber-700/50 rounded-lg px-2.5 py-1 text-amber-200 text-xs">
                            </div>
                        </div>
                        <!-- 2 Pihak: Pengurus Barang & Yang Menerima -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                            <div class="space-y-1.5">
                                <span class="text-[10px] font-bold text-teal-400 uppercase">👤 Pihak Kesatu (Pengurus Barang)</span>
                                <div>
                                    <label class="block text-slate-400 text-[9px]">Nama Lengkap</label>
                                    <input type="text" x-model="selectedDistribusi.pengurus_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-white font-bold text-xs">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">NIP</label>
                                        <input type="text" x-model="selectedDistribusi.pengurus_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Ruangan</label>
                                        <input type="text" x-model="selectedDistribusi.pengurus_ruangan" placeholder="Gudang Perbekalan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <span class="text-[10px] font-bold text-emerald-400 uppercase">👤 Pihak Kedua (Penerima Barang)</span>
                                <div>
                                    <label class="block text-slate-400 text-[9px]">Nama Lengkap & Gelar</label>
                                    <input type="text" x-model="selectedDistribusi.pj_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-emerald-300 font-bold text-xs">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">NIP</label>
                                        <input type="text" x-model="selectedDistribusi.pj_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Jabatan / Ruangan</label>
                                        <input type="text" x-model="selectedDistribusi.pj_jabatan" placeholder="Supervisor Front Office" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- LEMBAR DOKUMEN CETAK ASLI BAST PENYERAHAN BARANG (KERTAS PUTIH STANDAR RUMAH SAKIT) -->
                <template x-if="selectedDistribusi">
                    <div class="bg-slate-950/80 p-2 sm:p-6 rounded-2xl border border-slate-800 flex justify-center overflow-y-auto max-h-[75vh] custom-scrollbar shadow-inner">
                        <div id="print-area-bast" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 10pt; color: #000000;" class="w-full max-w-[760px] bg-white text-black p-8 sm:p-12 md:p-14 shadow-2xl rounded-sm space-y-3.5 select-text print:p-0 print:m-0 print:shadow-none print:max-w-none">
                            
                            <!-- KOP SURAT RESMI -->
                            <div class="border-b-[2.5px] border-black pb-2 mb-1" style="border-bottom: 2.5px solid #000000;">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                        <img src="{{ asset('img/logo-bondowoso.png') }}" alt="Logo Dinas Bondowoso" class="h-16 w-16 object-contain">
                                    </div>
                                    <div class="flex-1 text-center text-black" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                        <h4 class="font-bold text-[11pt] sm:text-[12pt] uppercase tracking-normal leading-tight text-black m-0 p-0">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                        <h3 class="font-bold text-[12.5pt] sm:text-[13.5pt] uppercase tracking-normal leading-tight text-black mt-0.5 mb-0 p-0">RUMAH SAKIT UMUM DAERAH dr. H. KOESNADI</h3>
                                        <p class="text-[8.5pt] sm:text-[9pt] italic leading-tight text-black mt-0.5 mb-0 p-0">Jl. Kapten Pierre Tendean No. 3 Telepon (0332) 421974. Fax.0332 422311</p>
                                        <p class="text-[8.5pt] sm:text-[9pt] leading-tight text-black m-0 p-0">Website: rsudrkoesnadi.go.id, Email: rsu.koesnadi@gmail.com</p>
                                        <p class="font-bold text-[10pt] sm:text-[10.5pt] uppercase text-black mt-1 mb-0 p-0" style="letter-spacing: 0.35em;">B O N D O W O S O</p>
                                    </div>
                                    <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                        <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="h-16 w-16 object-contain">
                                    </div>
                                </div>
                            </div>
                            <div class="text-right text-[8pt] text-black pr-1 mb-2 font-normal">
                                Kode Pos: 68214
                            </div>

                            <!-- JUDUL SURAT & NOMOR -->
                            <div class="text-center text-black mb-3">
                                <h3 class="font-bold text-[11pt] sm:text-[11.5pt] uppercase underline tracking-normal text-black m-0">BERITA ACARA PENYERAHAN BARANG</h3>
                                <p class="text-[9.5pt] sm:text-[10pt] font-semibold text-black mt-1 m-0">Nomor : <span x-text="selectedDistribusi.bast_nomor || selectedDistribusi.nomor_bast || '032 / 034 / 430.10.7 / 2026'"></span></p>
                            </div>

                            <!-- PEMBUKA -->
                            <p class="text-justify mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                                Pada hari ini <strong x-text="selectedDistribusi.hari || 'Kamis'"></strong> tanggal <strong x-text="selectedDistribusi.tanggal_angka || '13'"></strong> bulan <strong x-text="selectedDistribusi.bulan || 'Agustus'"></strong> tahun <strong x-text="selectedDistribusi.tahun || '2026'"></strong>, yang bertanda tangan di bawah ini :
                            </p>

                            <!-- PIHAK KESATU (PENGURUS BARANG) -->
                            <div class="space-y-1 mb-2 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                                <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; width: 85px; vertical-align: top; padding: 1.5px 0;">Nama</td>
                                        <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold" x-text="selectedDistribusi.pengurus_nama || 'BUDI HARTONO,S.Sos'">BUDI HARTONO,S.Sos</td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">NIP</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pengurus_nip || '19760229 200801 1 010'">19760229 200801 1 010</td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Jabatan</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pengurus_jabatan || 'Pengurus Barang'">Pengurus Barang</td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Ruangan</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pengurus_ruangan || 'Gudang Perbekalan'">Gudang Perbekalan</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- PARAGRAF PENGESAHAN SK BUPATI -->
                            <p class="text-justify my-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                                Dalam hal ini selaku Pengurus Barang Aset Tahun Anggaran <span x-text="selectedDistribusi.tahun_anggaran || selectedDistribusi.tahun || '2026'"></span> Rumah Sakit Umum Dr. H. Koesnadi Bondowoso, berdasarkan Surat Keputusan Bupati Kabupaten Bondowoso sesuai Nomor : <span x-text="selectedDistribusi.sk_bupati_nomor || '188.45/430.10.7/2026'"></span> tanggal <span x-text="selectedDistribusi.sk_bupati_tanggal || '02 Januari 2026'"></span>
                            </p>

                            <!-- PIHAK KEDUA (PENERIMA BARANG) -->
                            <p class="text-[9.5pt] sm:text-[10pt] mb-1 font-normal text-black">
                                Dengan ini menyerahkan barang kepada :
                            </p>
                            <div class="space-y-1 mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                                <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; width: 85px; vertical-align: top; padding: 1.5px 0;">Nama</td>
                                        <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold" x-text="selectedDistribusi.pj_nama || selectedDistribusi.penerima"></td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">NIP</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pj_nip || '-'"></td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Jabatan</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pj_jabatan || ('Supervisor ' + (selectedDistribusi.tujuan || selectedDistribusi.unit_nama || 'Unit'))"></td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Ruangan</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pj_ruangan || selectedDistribusi.tujuan || selectedDistribusi.unit_nama"></td>
                                    </tr>
                                </table>
                            </div>

                            <!-- TABEL RESMI BARANG DISTRIBUSI (DENGAN 3 SUB-KOLOM KONDISI: BAIK, KB, RUSAK) -->
                            <div class="my-2.5 overflow-x-auto">
                                <table class="w-full text-black border-collapse border border-black text-[9pt] sm:text-[9.5pt]" style="border-collapse: collapse; width: 100%; border: 1px solid black;">
                                    <thead>
                                        <tr class="bg-gray-100 font-bold border border-black" style="border: 1px solid black;">
                                            <th rowspan="2" class="border border-black px-2 py-1.5 text-center font-bold" style="width: 5%; border: 1px solid black;">No</th>
                                            <th rowspan="2" class="border border-black px-3 py-1.5 text-left font-bold" style="width: 25%; border: 1px solid black;">Uraian Barang</th>
                                            <th rowspan="2" class="border border-black px-3 py-1.5 text-left font-bold" style="width: 27%; border: 1px solid black;">Merk /Type</th>
                                            <th rowspan="2" class="border border-black px-2 py-1.5 text-center font-bold" style="width: 6%; border: 1px solid black;">Vol</th>
                                            <th rowspan="2" class="border border-black px-2 py-1.5 text-center font-bold" style="width: 8%; border: 1px solid black;">Satuan</th>
                                            <th colspan="3" class="border border-black px-1 py-1 text-center font-bold" style="width: 14%; border: 1px solid black;">Kondisi</th>
                                            <th rowspan="2" class="border border-black px-3 py-1.5 text-left font-bold" style="width: 15%; border: 1px solid black;">Keterangan</th>
                                        </tr>
                                        <tr class="bg-gray-100 font-bold border border-black" style="border: 1px solid black;">
                                            <th class="border border-black px-1 py-0.5 text-center font-bold text-[8.5pt]" style="border: 1px solid black;">Baik</th>
                                            <th class="border border-black px-1 py-0.5 text-center font-bold text-[8.5pt]" style="border: 1px solid black;">KB</th>
                                            <th class="border border-black px-1 py-0.5 text-center font-bold text-[8.5pt]" style="border: 1px solid black;">Rusak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(sub, idx) in (selectedDistribusi.items || [])" :key="idx">
                                            <tr class="border border-black" style="border: 1px solid black;">
                                                <td class="border border-black px-2 py-1.5 text-center" style="border: 1px solid black;" x-text="idx + 1"></td>
                                                <td class="border border-black px-3 py-1.5 text-left font-bold" style="border: 1px solid black;" x-text="sub.nama_barang"></td>
                                                <td class="border border-black px-3 py-1.5 text-left font-mono text-[9px]" style="border: 1px solid black;" x-text="sub.spesifikasi || sub.merk_type || sub.merk || '-'"></td>
                                                <td class="border border-black px-2 py-1.5 text-center font-bold" style="border: 1px solid black;" x-text="sub.qty"></td>
                                                <td class="border border-black px-2 py-1.5 text-center" style="border: 1px solid black;" x-text="sub.satuan"></td>
                                                <td class="border border-black px-1 py-1.5 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(sub.kondisi === 'Baik' || !sub.kondisi) ? 'Baik' : ''"></td>
                                                <td class="border border-black px-1 py-1.5 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(sub.kondisi === 'Kurang Baik' || sub.kondisi === 'KB') ? 'KB' : ''"></td>
                                                <td class="border border-black px-1 py-1.5 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(sub.kondisi === 'Rusak' || sub.kondisi === 'Rusak Berat') ? 'Rusak' : ''"></td>
                                                <td class="border border-black px-3 py-1.5 text-left text-[8.5pt]" style="border: 1px solid black;" x-text="sub.keterangan || selectedDistribusi.keterangan || '-'"></td>
                                            </tr>
                                        </template>
                                        <template x-if="!selectedDistribusi.items || selectedDistribusi.items.length === 0">
                                            <tr class="border border-black" style="border: 1px solid black;">
                                                <td class="border border-black px-2 py-1.5 text-center" style="border: 1px solid black;">1</td>
                                                <td class="border border-black px-3 py-1.5 text-left font-bold" style="border: 1px solid black;" x-text="selectedDistribusi.nama || selectedDistribusi.barang_nama"></td>
                                                <td class="border border-black px-3 py-1.5 text-left font-mono text-[9px]" style="border: 1px solid black;" x-text="(selectedDistribusi.merk ? (selectedDistribusi.merk + ' ' + (selectedDistribusi.type || '')) : (selectedDistribusi.spesifikasi || '-'))"></td>
                                                <td class="border border-black px-2 py-1.5 text-center font-bold" style="border: 1px solid black;" x-text="selectedDistribusi.volume || selectedDistribusi.qty || 1"></td>
                                                <td class="border border-black px-2 py-1.5 text-center" style="border: 1px solid black;" x-text="selectedDistribusi.satuan || 'Unit'"></td>
                                                <td class="border border-black px-1 py-1.5 text-center text-[8.5pt]" style="border: 1px solid black;">Baik</td>
                                                <td class="border border-black px-1 py-1.5 text-center text-[8.5pt]" style="border: 1px solid black;"></td>
                                                <td class="border border-black px-1 py-1.5 text-center text-[8.5pt]" style="border: 1px solid black;"></td>
                                                <td class="border border-black px-3 py-1.5 text-left text-[8.5pt]" style="border: 1px solid black;" x-text="selectedDistribusi.keterangan || '-'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <!-- PENUTUP -->
                            <p class="text-justify my-3 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                                Demikian Berita Acara Penyerahan Barang ini dibuat rangkap secukupnya untuk dipergunakan sebagaimana mestinya.
                            </p>

                            <!-- TANDA TANGAN (KIRI: TTD ELEKTRONIK PENGURUS BARANG, KANAN: TTD BASAH KEPALA RUANGAN/PJ) -->
                            <div class="grid grid-cols-2 gap-8 text-center text-black text-[9.5pt] sm:text-[10pt] mt-6 pt-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                                <div>
                                    <p class="m-0">Yang Menyerahkan</p>
                                    <p class="font-bold m-0">Pengurus Barang Aset</p>
                                    
                                    <!-- TTD Elektronik BSrE Pengurus Barang -->
                                    <div class="my-1 flex items-center justify-center" style="height: 52px; min-height: 52px;">
                                        <div class="p-1 border border-teal-600 bg-teal-50 rounded flex items-center space-x-1.5 text-left">
                                            <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(window.location.origin + '/validasi-tte/' + encodeURIComponent(selectedDistribusi.bast_nomor || selectedDistribusi.nomor_bast || 'BSRE-DISTRIBUSI'))" class="w-9 h-9 shrink-0">
                                            <div class="text-[7.5px] leading-tight text-slate-800">
                                                <div class="font-bold text-teal-900">DITANDATANGANI ELEKTRONIK</div>
                                                <div>Pengurus Barang Aset</div>
                                                <div class="text-[6.5px] text-slate-500 font-mono">Sertifikat BSrE - BSSN</div>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="font-bold underline uppercase m-0" x-text="selectedDistribusi.pengurus_nama || 'BUDI HARTONO,S.Sos'"></p>
                                    <p class="m-0" x-text="'NIP. ' + (selectedDistribusi.pengurus_nip || '19760229 200801 1 010')"></p>
                                </div>

                                <div>
                                    <p class="m-0">Yang Menerima</p>
                                    <p class="font-bold m-0" x-text="'Kepala Ruangan ' + (selectedDistribusi.pj_ruangan || selectedDistribusi.tujuan || selectedDistribusi.unit_nama || 'Unit')"></p>
                                    
                                    <!-- Ruang Tanda Tangan Basah Manual (Tinggi presisi 52px agar nama sejajar horizontal sempurna) -->
                                    <div class="my-1 flex items-center justify-center" style="height: 52px; min-height: 52px;"></div>

                                    <p class="font-bold underline uppercase m-0" x-text="selectedDistribusi.pj_nama || selectedDistribusi.penerima"></p>
                                    <p class="m-0" x-text="'NIP. ' + (selectedDistribusi.pj_nip || '-')"></p>
                                </div>
                            </div>

                        </div>
                    </div>
                </template>

            </div>
        </div>

        <!-- MODAL DETAIL RINCIAN DISTRIBUSI BARANG (MENDUKUNG MULTI-BARANG & NIBAR REGISTER) -->
        <div x-show="showDetailModal" class="no-print fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-6 sm:p-8 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <span class="p-2.5 rounded-2xl bg-teal-500/20 text-teal-300 text-xl border border-teal-500/30">🚚</span>
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-white">Detail Alokasi Penyerahan & Register NIBAR</h3>
                            <p class="text-xs text-slate-400 font-mono" x-text="selectedDistribusi ? ('Nomor Registrasi: ' + selectedDistribusi.kode + ' • BAST: ' + (selectedDistribusi.bast_nomor || selectedDistribusi.nomor_bast)) : ''"></p>
                        </div>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg text-lg font-bold">&times;</button>
                </div>

                <template x-if="selectedDistribusi">
                    <div class="space-y-5 text-xs">
                        
                        <!-- Informasi Ringkas Transaksi -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 bg-slate-950 p-4 sm:p-5 rounded-2xl border border-slate-800">
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Tujuan Unit / Ruangan</span>
                                <span class="font-bold text-teal-300 text-xs block mt-0.5" x-text="selectedDistribusi.tujuan"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Pegawai Penerima (PJ)</span>
                                <span class="font-bold text-white text-xs block mt-0.5" x-text="selectedDistribusi.penerima || selectedDistribusi.pj_nama"></span>
                                <span class="text-[10px] text-slate-400 font-mono" x-text="selectedDistribusi.pj_nip ? ('NIP: ' + selectedDistribusi.pj_nip) : ''"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Tanggal Distribusi</span>
                                <span class="font-mono text-slate-300 text-xs block mt-0.5" x-text="selectedDistribusi.tgl || selectedDistribusi.tanggal_distribusi"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Status Penyerahan</span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-extrabold border mt-0.5"
                                      :class="{
                                          'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': selectedDistribusi.status === 'Telah Diterima' || selectedDistribusi.status === 'Diterima',
                                          'bg-cyan-500/15 text-cyan-300 border-cyan-500/30': selectedDistribusi.status === 'Dalam Pengiriman' || selectedDistribusi.status === 'Dikirim',
                                          'bg-amber-500/15 text-amber-300 border-amber-500/30': selectedDistribusi.status === 'Menunggu Konfirmasi' || selectedDistribusi.status === 'Pending',
                                          'bg-slate-500/15 text-slate-300 border-slate-500/30': selectedDistribusi.status === 'Draft' || !selectedDistribusi.status
                                      }"
                                      x-text="selectedDistribusi.status"></span>
                            </div>
                            <div class="col-span-2 sm:col-span-4 pt-2 border-t border-slate-900 flex items-center justify-between">
                                <div class="text-slate-400 text-[11px]">
                                    <span class="font-bold text-slate-300">Catatan:</span>
                                    <span class="italic ml-1" x-text="selectedDistribusi.keterangan || '-'"></span>
                                </div>
                                <template x-if="selectedDistribusi.signed">
                                    <span class="inline-flex items-center space-x-1 text-[10px] text-emerald-400 font-mono bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">
                                        <span>✍️ E-Sign BSrE Sah:</span>
                                        <span x-text="selectedDistribusi.tgl_signed || 'Terverifikasi'"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Tabel Rincian Semua Barang & Register NIBAR yang Didistribusikan -->
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-slate-200 font-extrabold text-xs flex items-center space-x-2">
                                    <span>📦 Rincian Barang & Nomor NIBAR 45 Karakter</span>
                                    <span class="px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] font-bold"
                                          x-text="(selectedDistribusi.items ? selectedDistribusi.items.length : 0) + ' Jenis Barang'"></span>
                                </span>
                                <span class="text-[11px] text-emerald-400 font-bold bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20"
                                      x-text="'Total Volume: ' + getTotalQty(selectedDistribusi.items) + ' Item'"></span>
                            </div>

                            <div class="overflow-x-auto overflow-y-auto rounded-2xl border border-slate-800 bg-slate-950 shadow-inner" style="max-height: 380px; overflow-y: auto;">
                                <table class="w-full text-left text-xs text-slate-300">
                                    <thead class="bg-slate-900 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-800 shadow-sm" style="position: sticky; top: 0; z-index: 10; background-color: #0f172a;">
                                        <tr>
                                            <th class="px-3.5 py-3 text-center w-8">No</th>
                                            <th class="px-3.5 py-3 text-left min-w-[180px]">Nama Barang & Kode 108</th>
                                            <th class="px-3.5 py-3 text-left min-w-[140px]">Merk / Spesifikasi</th>
                                            <th class="px-3.5 py-3 text-left min-w-[300px]">Nomor Register NIBAR (45 Digit)</th>
                                            <th class="px-3.5 py-3 text-center w-24">Kondisi</th>
                                            <th class="px-3.5 py-3 text-center w-20">Aksi</th>
                                            <th class="px-3.5 py-3 text-center w-16">Vol</th>
                                            <th class="px-3.5 py-3 text-center w-16">Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/80">
                                        <template x-for="(item, idx) in selectedDistribusi.items" :key="idx">
                                            <tr class="hover:bg-slate-900/40 transition-colors">
                                                <td class="px-3.5 py-3 text-center font-bold text-slate-500 align-top" x-text="idx + 1"></td>
                                                
                                                <!-- Nama Barang & Kode 108 -->
                                                <td class="px-3.5 py-3 align-top">
                                                    <p class="font-extrabold text-white text-xs leading-snug" x-text="item.nama_barang"></p>
                                                    <p class="text-[10px] text-teal-400 font-mono mt-0.5" x-text="'Kode 108: ' + (item.kode_barang || '-')"></p>
                                                    <template x-if="item.jenis_nama">
                                                        <span class="text-[9.5px] text-slate-500 block truncate max-w-xs" x-text="item.jenis_nama"></span>
                                                    </template>
                                                </td>

                                                <!-- Merk & Spesifikasi -->
                                                <td class="px-3.5 py-3 text-slate-300 text-[11px] align-top">
                                                    <span class="font-semibold text-slate-200" x-text="item.merk_type || item.merk || '-'"></span>
                                                    <template x-if="item.keterangan">
                                                        <p class="text-[10px] text-slate-500 italic mt-0.5" x-text="'Ket: ' + item.keterangan"></p>
                                                    </template>
                                                </td>

                                                <!-- Kolom NIBAR -->
                                                <td class="px-3.5 py-3 align-top">
                                                    <!-- Ada nibar_registers -->
                                                    <template x-if="item.nibar_registers && item.nibar_registers.length > 0">
                                                        <div class="flex flex-col gap-3">
                                                            <template x-for="(reg, nIdx) in item.nibar_registers" :key="reg.nibar || nIdx">
                                                                <div class="flex items-center h-[32px] bg-slate-800/90 hover:bg-slate-800 border border-slate-700/60 hover:border-teal-500/50 rounded-lg px-3 transition-all group">
                                                                    <span class="text-slate-400 font-mono text-[10px] font-semibold shrink-0 mr-2" x-text="'#' + (nIdx + 1)"></span>
                                                                    <a :href="'/scan/' + reg.nibar" target="_blank"
                                                                       class="font-mono text-[11px] font-bold text-teal-300 group-hover:text-teal-200 group-hover:underline tracking-tight select-all truncate"
                                                                       title="Buka Detail Barang Aset"
                                                                       x-text="reg.nibar">
                                                                    </a>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    <!-- Fallback: nibar_list -->
                                                    <template x-if="(!item.nibar_registers || item.nibar_registers.length === 0) && item.nibar_list && item.nibar_list.length > 0">
                                                        <div class="flex flex-col gap-3">
                                                            <template x-for="(nibar, nIdx) in item.nibar_list" :key="nIdx">
                                                                <div class="flex items-center h-[32px] bg-slate-800/90 hover:bg-slate-800 border border-slate-700/60 hover:border-teal-500/50 rounded-lg px-3 transition-all group">
                                                                    <span class="text-slate-400 font-mono text-[10px] font-semibold shrink-0 mr-2" x-text="'#' + (nIdx + 1)"></span>
                                                                    <a :href="'/scan/' + nibar" target="_blank"
                                                                       class="font-mono text-[11px] font-bold text-teal-300 group-hover:text-teal-200 group-hover:underline tracking-tight select-all truncate"
                                                                       title="Buka Detail Barang Aset"
                                                                       x-text="nibar">
                                                                    </a>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    <!-- Belum ada NIBAR -->
                                                    <template x-if="(!item.nibar_registers || item.nibar_registers.length === 0) && (!item.nibar_list || item.nibar_list.length === 0)">
                                                        <div class="inline-flex items-center h-[32px] space-x-1.5 px-3 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-300 text-[10.5px] font-mono">
                                                            <span>⚠️</span>
                                                            <span>Belum ditentukan</span>
                                                        </div>
                                                    </template>
                                                </td>

                                                <!-- Kolom Kondisi — tinggi h-[32px] & gap-3 sama persis dengan NIBAR -->
                                                <td class="px-2 py-3 align-top">
                                                    <template x-if="item.nibar_registers && item.nibar_registers.length > 0">
                                                        <div class="flex flex-col gap-3">
                                                            <template x-for="(reg, kIdx) in item.nibar_registers" :key="'k-' + (reg.nibar || kIdx)">
                                                                <div class="flex items-center justify-center h-[32px]">
                                                                    <span class="h-[32px] px-3 flex items-center justify-center rounded-lg text-[10px] font-bold whitespace-nowrap"
                                                                          :class="{
                                                                              'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30': reg.kondisi === 'Baik' || !reg.kondisi,
                                                                              'bg-amber-500/15 text-amber-300 border border-amber-500/30': reg.kondisi === 'Kurang Baik',
                                                                              'bg-orange-500/15 text-orange-300 border border-orange-500/30': reg.kondisi === 'Rusak Ringan',
                                                                              'bg-rose-500/15 text-rose-300 border border-rose-500/30': reg.kondisi === 'Rusak Berat' || reg.kondisi === 'Rusak'
                                                                          }"
                                                                          x-text="reg.kondisi || 'Baik'"></span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template x-if="!item.nibar_registers || item.nibar_registers.length === 0">
                                                        <div class="flex items-center justify-center h-[32px]">
                                                            <span class="h-[32px] px-3 flex items-center justify-center rounded-lg text-[10px] font-bold"
                                                                  :class="{
                                                                      'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30': item.kondisi === 'Baik',
                                                                      'bg-amber-500/15 text-amber-300 border border-amber-500/30': item.kondisi === 'Kurang Baik' || item.kondisi === 'KB',
                                                                      'bg-orange-500/15 text-orange-300 border border-orange-500/30': item.kondisi === 'Rusak Ringan',
                                                                      'bg-rose-500/15 text-rose-300 border border-rose-500/30': item.kondisi === 'Rusak Berat' || item.kondisi === 'Rusak'
                                                                  }"
                                                                  x-text="item.kondisi || 'Baik'"></span>
                                                        </div>
                                                    </template>
                                                </td>

                                                <!-- Kolom Aksi — tinggi h-[32px] & gap-3 sama persis dengan NIBAR dan Kondisi -->
                                                <td class="px-2 py-3 align-top">
                                                    <template x-if="item.nibar_registers && item.nibar_registers.length > 0">
                                                        <div class="flex flex-col gap-3">
                                                            <template x-for="(reg, aIdx) in item.nibar_registers" :key="'aksi-' + (reg.nibar || aIdx)">
                                                                <div class="flex items-center justify-center gap-1.5 h-[32px]">
                                                                    <button type="button" @click="copyNibar(reg.nibar)"
                                                                            class="h-[32px] w-[32px] flex items-center justify-center rounded-lg bg-slate-800 hover:bg-teal-500 text-slate-400 hover:text-slate-950 border border-slate-700/60 transition-all text-xs"
                                                                            :title="copiedNibar === reg.nibar ? 'Tersalin!' : 'Salin NIBAR'">
                                                                        <span x-show="copiedNibar !== reg.nibar">📋</span>
                                                                        <span x-show="copiedNibar === reg.nibar" class="text-emerald-400 font-bold">✓</span>
                                                                    </button>
                                                                    <a :href="'/scan/' + reg.nibar" target="_blank"
                                                                       class="h-[32px] w-[32px] flex items-center justify-center rounded-lg bg-teal-500/15 hover:bg-teal-500 text-teal-300 hover:text-slate-950 border border-teal-500/30 transition-all text-xs"
                                                                       title="Lihat Detail Barang Aset">
                                                                        🔍
                                                                    </a>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    <!-- Fallback nibar_list -->
                                                    <template x-if="(!item.nibar_registers || item.nibar_registers.length === 0) && item.nibar_list && item.nibar_list.length > 0">
                                                        <div class="flex flex-col gap-3">
                                                            <template x-for="(nibar, aIdx) in item.nibar_list" :key="'aksi-' + aIdx">
                                                                <div class="flex items-center justify-center gap-1.5 h-[32px]">
                                                                    <button type="button" @click="copyNibar(nibar)"
                                                                            class="h-[32px] w-[32px] flex items-center justify-center rounded-lg bg-slate-800 hover:bg-teal-500 text-slate-400 hover:text-slate-950 border border-slate-700/60 transition-all text-xs"
                                                                            :title="copiedNibar === nibar ? 'Tersalin!' : 'Salin NIBAR'">
                                                                        <span x-show="copiedNibar !== nibar">📋</span>
                                                                        <span x-show="copiedNibar === nibar" class="text-emerald-400 font-bold">✓</span>
                                                                    </button>
                                                                    <a :href="'/scan/' + nibar" target="_blank"
                                                                       class="h-[32px] w-[32px] flex items-center justify-center rounded-lg bg-teal-500/15 hover:bg-teal-500 text-teal-300 hover:text-slate-950 border border-teal-500/30 transition-all text-xs"
                                                                       title="Lihat Detail Barang Aset">
                                                                        🔍
                                                                    </a>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    <!-- Tidak ada NIBAR -->
                                                    <template x-if="(!item.nibar_registers || item.nibar_registers.length === 0) && (!item.nibar_list || item.nibar_list.length === 0)">
                                                        <div class="h-[32px] flex items-center justify-center">
                                                            <span class="text-slate-600 text-[10px]">—</span>
                                                        </div>
                                                    </template>
                                                </td>

                                                <!-- Volume & Satuan -->
                                                <td class="px-3.5 py-3 text-center font-bold text-emerald-400 text-xs align-top" x-text="item.qty"></td>
                                                <td class="px-3.5 py-3 text-center text-slate-300 text-xs align-top" x-text="item.satuan"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </template>

                <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                    <div class="text-[11px] text-slate-500 font-mono">
                        <span>Format NIBAR: 45 Digit Kode BMD RSUD dr. H. Koesnandi</span>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all shadow-md active:scale-95">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL UBAH STATUS DISTRIBUSI -->
        <div x-show="showEditModal" class="no-print fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-white">✏️ Ubah Status Distribusi</h3>
                    <button type="button" @click="showEditModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <form @submit.prevent="showEditModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Judul / Ringkasan Barang</label>
                        <input type="text" x-model="selectedDistribusi ? selectedDistribusi.nama : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Penerima Barang</label>
                        <input type="text" x-model="selectedDistribusi ? selectedDistribusi.penerima : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Status Distribusi</label>
                        <select x-model="selectedDistribusi ? selectedDistribusi.status : 'Telah Diterima'" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                            <option value="Telah Diterima">Telah Diterima</option>
                            <option value="Dalam Pengiriman">Dalam Pengiriman</option>
                            <option value="Menunggu Konfirmasi">Menunggu Konfirmasi</option>
                        </select>
                    </div>
                    <div class="pt-4 flex items-center justify-end space-x-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-teal-500 text-slate-950 font-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- GLOBAL CUSTOM CONFIRMATION DIALOG MODAL (Sleek Dark Theme) -->
        <div x-show="showConfirmModal" x-cloak class="no-print fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
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
                        <span x-text="confirmData.type === 'danger' ? '🗑️' : (confirmData.type === 'warning' ? '✏️' : '➕')"></span>
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
        <div x-show="toast.show" x-cloak class="no-print fixed bottom-6 right-6 z-50 max-w-sm w-full bg-slate-900/95 border rounded-2xl p-4 shadow-2xl backdrop-blur-md flex items-center justify-between space-x-3"
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
