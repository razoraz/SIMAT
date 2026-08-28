<x-layout title="Jenis ASTAP Kode 108 BMD - SIMAT-RK">
    @section('page-title', 'Master Jenis ASTAP (Kode 108 BMD)')
    @section('breadcrumb', 'Master Data System / Jenis ASTAP Kode 108')

    <script>
        function masterJenisAstap() {
            return {
                showAddModal: false,
                showEditModal: false,
                showImportModal: false,
                selectedKode: null,

                newFormData: {
                    jenis: '1.3.1',
                    nama_jenis: 'TANAH',
                    sub_rincian_objek: '1.3.1.01.01.01',
                    uraian_sub_rincian: '',
                    sub_sub_rincian_objek: '1.3.1.01.01.01.001',
                    uraian_sub_sub_rincian: ''
                },

                editFormData: {
                    id: null,
                    jenis: '',
                    nama_jenis: '',
                    sub_rincian_objek: '',
                    uraian_sub_rincian: '',
                    sub_sub_rincian_objek: '',
                    uraian_sub_sub_rincian: ''
                },

                openEdit(item) {
                    this.editFormData = { ...item };
                    this.showEditModal = true;
                },

                onJenisChangeNew() {
                    const lookup = {
                        '1.3.1': { nama: 'TANAH', sub_prefix: '1.3.1.01.01.01', sub_sub_prefix: '1.3.1.01.01.01.001' },
                        '1.3.2': { nama: 'PERALATAN DAN MESIN', sub_prefix: '1.3.2.02.01.01', sub_sub_prefix: '1.3.2.02.01.01.001' },
                        '1.3.3': { nama: 'GEDUNG DAN BANGUNAN', sub_prefix: '1.3.3.01.01.01', sub_sub_prefix: '1.3.3.01.01.01.001' },
                        '1.3.4': { nama: 'JALAN, IRIGASI DAN JARINGAN', sub_prefix: '1.3.4.03.01.01', sub_sub_prefix: '1.3.4.03.01.01.001' },
                        '1.3.5': { nama: 'ASET TETAP LAINNYA', sub_prefix: '1.3.5.01.01.01', sub_sub_prefix: '1.3.5.01.01.01.001' },
                        '1.3.6': { nama: 'KONSTRUKSI DALAM PENGERJAAN', sub_prefix: '1.3.6.01.01.01', sub_sub_prefix: '1.3.6.01.01.01.001' },
                        '1.5.3': { nama: 'ASET TIDAK BERWUJUD', sub_prefix: '1.5.3.01.01.01', sub_sub_prefix: '1.5.3.01.01.01.001' },
                        '1.3.7': { nama: 'ASET TETAP DALAM RENOVASI', sub_prefix: '1.3.7.01.01.01', sub_sub_prefix: '1.3.7.01.01.01.001' }
                    };
                    const item = lookup[this.newFormData.jenis];
                    if (item) {
                        this.newFormData.nama_jenis = item.nama;
                        this.newFormData.sub_rincian_objek = item.sub_prefix;
                        this.newFormData.sub_sub_rincian_objek = item.sub_sub_prefix;
                    }
                },

                onJenisChangeEdit() {
                    const lookup = {
                        '1.3.1': 'TANAH',
                        '1.3.2': 'PERALATAN DAN MESIN',
                        '1.3.3': 'GEDUNG DAN BANGUNAN',
                        '1.3.4': 'JALAN, IRIGASI DAN JARINGAN',
                        '1.3.5': 'ASET TETAP LAINNYA',
                        '1.3.6': 'KONSTRUKSI DALAM PENGERJAAN',
                        '1.5.3': 'ASET TIDAK BERWUJUD',
                        '1.3.7': 'ASET TETAP DALAM RENOVASI'
                    };
                    if (lookup[this.editFormData.jenis]) {
                        this.editFormData.nama_jenis = lookup[this.editFormData.jenis];
                    }
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

                confirmDeleteForm(event, itemName) {
                    event.preventDefault();
                    const formElement = event.target;
                    this.askConfirmation({
                        title: '🗑️ Konfirmasi Hapus Kode 108',
                        message: 'Apakah Anda yakin ingin menghapus data klasifikasi Kode 108 BMD ini dari master data?',
                        itemName: itemName || '',
                        type: 'danger',
                        btnText: '🗑️ Ya, Hapus Kode 108',
                        onConfirm: () => {
                            formElement.submit();
                        }
                    });
                },

                confirmAddForm(event) {
                    event.preventDefault();
                    const formElement = event.target;
                    const itemPreview = (this.newFormData.uraian_sub_sub_rincian || 'Klasifikasi Baru') + ' (' + (this.newFormData.sub_sub_rincian_objek || 'Kode 108') + ')';
                    this.askConfirmation({
                        title: '➕ Konfirmasi Tambah Kode 108',
                        message: 'Apakah Anda yakin ingin mendaftarkan data klasifikasi Kode 108 BMD baru ini?',
                        itemName: itemPreview,
                        type: 'success',
                        btnText: '➕ Ya, Simpan Kode 108',
                        onConfirm: () => {
                            formElement.submit();
                        }
                    });
                },

                confirmEditForm(event) {
                    event.preventDefault();
                    const formElement = event.target;
                    const itemPreview = (this.editFormData.uraian_sub_sub_rincian || 'Klasifikasi ASTAP') + ' (' + (this.editFormData.sub_sub_rincian_objek || 'Kode 108') + ')';
                    this.askConfirmation({
                        title: '✏️ Konfirmasi Simpan Perubahan Kode 108',
                        message: 'Apakah Anda yakin ingin menyimpan perubahan data klasifikasi Kode 108 ini?',
                        itemName: itemPreview,
                        type: 'warning',
                        btnText: '✏️ Ya, Simpan Perubahan',
                        onConfirm: () => {
                            formElement.submit();
                        }
                    });
                }
            };
        }
    </script>

    <div x-data="masterJenisAstap()" x-cloak>

        @if (session('success'))
            <div class="mb-4 p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-bold flex items-center justify-between shadow-lg">
                <span>✅ {{ session('success') }}</span>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-400 hover:text-white font-bold text-base">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 rounded-2xl bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs font-bold flex items-center justify-between shadow-lg">
                <span>⚠️ {{ session('error') }}</span>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-400 hover:text-white font-bold text-base">&times;</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 rounded-2xl bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs font-semibold shadow-lg space-y-1">
                <div class="font-bold flex items-center space-x-1.5">
                    <span>⚠️ Terjadi kesalahan:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header Banner & Mini KPI Strip -->
        <div class="bg-gradient-to-r from-emerald-600/15 via-slate-900 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>KODE 108 PERMENDAGRI (BARANG MILIK DAERAH)</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Master Jenis ASTAP (Kode 108 BMD)</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Struktur 3 tingkatan klasifikasi aset tetap resmi pemerintah daerah (Jenis Utama, Sub Rincian Objek, dan Sub-Sub Rincian Objek).
                    </p>
                </div>
                
                <div class="flex items-center space-x-2 shrink-0">
                    <button type="button" @click="showImportModal = true"
                        class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-400 font-bold text-xs border border-emerald-500/30 shadow-lg transition-all flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span>Import CSV/Excel</span>
                    </button>
                    <button type="button" @click="showAddModal = true"
                        class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Jenis ASTAP</span>
                    </button>
                </div>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-4 flex items-center space-x-3">
                    <div class="p-3 rounded-xl bg-emerald-500/10 text-emerald-400 text-xl">🏛️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Objek Aset</span>
                        <span class="text-base sm:text-lg font-extrabold text-emerald-400">{{ number_format($totalCount ?? 0) }} Data Kode 108</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-4 flex items-center space-x-3">
                    <div class="p-3 rounded-xl bg-cyan-500/10 text-cyan-400 text-xl">📋</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Klasifikasi Aset</span>
                        <span class="text-base sm:text-lg font-extrabold text-cyan-300">8 Kategori Aset Tetap</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter, Quick Tabs & Search Bar Full-Width -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <form method="GET" action="{{ route('master.jenis_astap') }}" class="flex flex-col gap-4">
                
                <!-- Quick Filter Jenis Utama Tabs -->
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Kategori Aset:</span>
                    <a href="{{ route('master.jenis_astap', ['search' => request('search')]) }}"
                        class="px-3 py-1.5 rounded-xl transition-all {{ request('jenis', 'all') === 'all' ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                        Semua Aset
                    </a>
                    <a href="{{ route('master.jenis_astap', ['jenis' => '1.3.1', 'search' => request('search')]) }}"
                        class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.3.1' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                        <span>🌾 Aset Tanah</span>
                    </a>
                    <a href="{{ route('master.jenis_astap', ['jenis' => '1.3.2', 'search' => request('search')]) }}"
                        class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.3.2' ? 'bg-cyan-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                        <span>🔬 Aset Peralatan dan Mesin</span>
                    </a>
                    <a href="{{ route('master.jenis_astap', ['jenis' => '1.3.3', 'search' => request('search')]) }}"
                        class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.3.3' ? 'bg-purple-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                        <span>🏢 Aset Gedung & Bangunan</span>
                    </a>
                    <a href="{{ route('master.jenis_astap', ['jenis' => '1.3.4', 'search' => request('search')]) }}"
                        class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.3.4' ? 'bg-teal-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                        <span>🚰 Aset Jalan, Irigasi dan Jaringan</span>
                    </a>
                    <a href="{{ route('master.jenis_astap', ['jenis' => '1.3.5', 'search' => request('search')]) }}"
                        class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.3.5' ? 'bg-orange-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                        <span>📦 Aset Tetap Lainnya</span>
                    </a>
                    <a href="{{ route('master.jenis_astap', ['jenis' => '1.3.6', 'search' => request('search')]) }}"
                        class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.3.6' ? 'bg-rose-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                        <span>🏗️ Aset Konstruksi Dalam Pengerjaan</span>
                    </a>
                    <a href="{{ route('master.jenis_astap', ['jenis' => '1.5.3', 'search' => request('search')]) }}"
                        class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.5.3' ? 'bg-indigo-500 text-white font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                        <span>💾 Aset Tidak Berwujud</span>
                    </a>
                    <a href="{{ route('master.jenis_astap', ['jenis' => '1.3.7', 'search' => request('search')]) }}"
                        class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.3.7' ? 'bg-pink-500 text-white font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                        <span>🔨 Aset Tetap Dalam Renovasi</span>
                    </a>
                </div>

                @if(request('jenis'))
                    <input type="hidden" name="jenis" value="{{ request('jenis') }}">
                @endif

                <!-- Search Bar & Counter -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
                    <div class="relative flex-1 w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode 108 / nama jenis / sub rincian / uraian spesifik barang..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
                        <svg class="w-4 h-4 text-emerald-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <button type="submit"
                            class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs transition-all flex items-center space-x-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>Cari</span>
                        </button>

                        <a href="{{ route('master.jenis_astap') }}"
                            class="px-3 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                            🔄 Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- ========================================================================= -->
        <!-- TABEL STRUKTUR KODE 108 BMD                                               -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-5 mb-6">
            <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 350px; overflow-y: auto; overflow-x: auto;">
                <table class="w-full text-left text-xs text-slate-300 border-collapse">
                    <thead class="shadow-sm" style="position: sticky; top: 0; z-index: 10; background-color: #020617;">
                        <tr class="text-center font-extrabold uppercase tracking-wider text-xs border-b border-slate-800">
                            <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 border-r border-slate-800 w-12 text-center align-middle">
                                NO
                            </th>
                            <th colspan="2" class="px-4 py-3 bg-emerald-950/60 text-emerald-300 border-r border-slate-800">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>🏛️</span>
                                    <span>JENIS UTAMA</span>
                                </div>
                            </th>
                            <th colspan="2" class="px-4 py-3 bg-amber-950/60 text-amber-300 border-r border-slate-800">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>📁</span>
                                    <span>SUB RINCIAN OBJEK</span>
                                </div>
                            </th>
                            <th colspan="2" class="px-4 py-3 bg-purple-950/60 text-purple-300 border-r border-slate-800">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>🏷️</span>
                                    <span>SUB - SUB RINCIAN OBJEK</span>
                                </div>
                            </th>
                            <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 text-center align-middle w-28">
                                Aksi
                            </th>
                        </tr>

                        <tr class="text-center font-bold uppercase tracking-wider text-[11px] border-b border-slate-800">
                            <th class="px-3 py-2.5 bg-emerald-950/40 text-emerald-400 border-r border-slate-800/80 w-24">
                                JENIS
                            </th>
                            <th class="px-4 py-2.5 bg-emerald-950/40 text-emerald-200 border-r border-slate-800/80 min-w-[180px]">
                                NAMA JENIS
                            </th>

                            <th class="px-3 py-2.5 bg-amber-950/40 text-amber-400 border-r border-slate-800/80 w-36">
                                SUB RINCIAN OBJEK
                            </th>
                            <th class="px-4 py-2.5 bg-amber-950/40 text-amber-200 border-r border-slate-800/80 min-w-[240px]">
                                URAIAN
                            </th>

                            <th class="px-3 py-2.5 bg-purple-950/40 text-purple-400 border-r border-slate-800/80 w-44">
                                SUB - SUB RINCIAN OBJEK
                            </th>
                            <th class="px-4 py-2.5 bg-purple-950/40 text-purple-200 border-r border-slate-800/80 min-w-[260px]">
                                URAIAN
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-800/80">
                        @forelse ($kode108List as $index => $item)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="px-4 py-4 text-center font-bold text-slate-400 border-r border-slate-800/80">
                                    {{ $kode108List->firstItem() + $index }}
                                </td>

                                <td class="px-3 py-4 text-center font-mono font-bold text-emerald-400 bg-emerald-950/10 border-r border-slate-800/80">
                                    {{ $item->jenis }}
                                </td>

                                <td class="px-4 py-4 font-bold text-slate-200 uppercase bg-emerald-950/10 border-r border-slate-800/80">
                                    {{ $item->nama_jenis }}
                                </td>

                                <td class="px-3 py-4 text-center font-mono font-bold text-amber-400 bg-amber-950/10 border-r border-slate-800/80">
                                    {{ $item->sub_rincian_objek }}
                                </td>

                                <td class="px-4 py-4 font-semibold text-slate-300 uppercase bg-amber-950/10 border-r border-slate-800/80 text-[11px]">
                                    {{ $item->uraian_sub_rincian }}
                                </td>

                                <td class="px-3 py-4 text-center font-mono font-bold text-purple-400 bg-purple-950/10 border-r border-slate-800/80">
                                    {{ $item->sub_sub_rincian_objek }}
                                </td>

                                <td class="px-4 py-4 font-semibold text-white bg-purple-950/10 border-r border-slate-800/80">
                                    {{ $item->uraian_sub_sub_rincian }}
                                </td>

                                <td class="px-3 py-4 text-center space-x-1 whitespace-nowrap">
                                    <button type="button" @click="openEdit({{ json_encode($item) }})"
                                        class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Ubah</span>
                                    </button>

                                    <form action="{{ route('master.jenis_astap.destroy', $item->id) }}" method="POST" class="inline-block" @submit="confirmDeleteForm($event, '{{ $item->uraian_sub_sub_rincian }} ({{ $item->sub_sub_rincian_objek }})')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                                    <div class="text-3xl mb-2">🔍</div>
                                    <p class="font-semibold text-sm">Tidak ada data Kode 108 BMD yang ditemukan.</p>
                                    <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci pencarian atau filter KIB Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Laravel Pagination Links -->
            @if ($kode108List->hasPages())
                <div class="px-6 py-4 bg-slate-900 border-t border-slate-800">
                    {{ $kode108List->links() }}
                </div>
            @endif
        </div>
        
        <!-- ========================================================================= -->
        <!-- MODAL TAMBAH JENIS ASTAP KODE 108 BMD                                     -->
        <!-- ========================================================================= -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-5 relative">
                <button type="button" @click="showAddModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-sm font-bold transition-all">&times;</button>

                <div class="text-center pb-3 border-b border-slate-800">
                    <h3 class="text-base font-extrabold text-white">Tambah Data Klasifikasi Kode 108 BMD</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Input Objek &amp; Uraian Kode 108 Baru</p>
                </div>

                <form action="{{ route('master.jenis_astap.store') }}" method="POST" class="space-y-4 text-xs" @submit="confirmAddForm($event)">
                    @csrf
                    <div class="p-3.5 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 space-y-2">
                        <span class="font-bold text-emerald-400 uppercase tracking-wider text-[10px] block">1. Jenis Utama</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Jenis</label>
                                <select name="jenis" x-model="newFormData.jenis" @change="onJenisChangeNew()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-emerald-400 font-mono font-bold focus:border-emerald-500">
                                    <option value="1.3.1">1.3.1 (Tanah)</option>
                                    <option value="1.3.2">1.3.2 (Peralatan &amp; Mesin)</option>
                                    <option value="1.3.3">1.3.3 (Gedung &amp; Bangunan)</option>
                                    <option value="1.3.4">1.3.4 (Jalan, Irigasi &amp; Jaringan)</option>
                                    <option value="1.3.5">1.3.5 (Aset Tetap Lainnya)</option>
                                    <option value="1.3.6">1.3.6 (KDP)</option>
                                    <option value="1.5.3">1.5.3 (Aset Tidak Berwujud)</option>
                                    <option value="1.3.7">1.3.7 (Aset Dalam Renovasi)</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Jenis Utama</label>
                                <input type="text" name="nama_jenis" x-model="newFormData.nama_jenis" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-emerald-500">
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-amber-950/20 border border-amber-500/30 space-y-2">
                        <span class="font-bold text-amber-400 uppercase tracking-wider text-[10px] block">2. Sub Rincian Objek</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Sub Rincian</label>
                                <input type="text" name="sub_rincian_objek" x-model="newFormData.sub_rincian_objek" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-400 font-mono font-bold focus:border-amber-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Uraian Sub Rincian</label>
                                <input type="text" name="uraian_sub_rincian" x-model="newFormData.uraian_sub_rincian" required placeholder="Contoh: ALAT KEDOKTERAN UMUM" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-amber-500">
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-purple-950/20 border border-purple-500/30 space-y-2">
                        <span class="font-bold text-purple-400 uppercase tracking-wider text-[10px] block">3. Sub - Sub Rincian Objek (Spesifik)</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Sub-Sub Rincian</label>
                                <input type="text" name="sub_sub_rincian_objek" x-model="newFormData.sub_sub_rincian_objek" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-purple-400 font-mono font-bold focus:border-purple-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Uraian Sub-Sub Rincian (Nama Barang)</label>
                                <input type="text" name="uraian_sub_sub_rincian" x-model="newFormData.uraian_sub_sub_rincian" required placeholder="Contoh: Patient Monitor &amp; Defibrillator" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-purple-500">
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                        <button type="button" @click="showAddModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 text-slate-300 font-bold text-xs">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all">
                            Simpan Kode 108 Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL EDIT KLASIFIKASI KODE 108 BMD                                       -->
        <!-- ========================================================================= -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-5 relative">
                <button type="button" @click="showEditModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-sm font-bold transition-all">&times;</button>

                <div class="text-center pb-3 border-b border-slate-800">
                    <h3 class="text-base font-extrabold text-white">Edit Klasifikasi Kode 108 BMD</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Perbarui Data Klasifikasi Kode 108</p>
                </div>

                <form :action="'/master-data/jenis-astap/' + editFormData.id" method="POST" class="space-y-4 text-xs" @submit="confirmEditForm($event)">
                    @csrf
                    @method('PUT')
                    
                    <div class="p-3.5 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 space-y-2">
                        <span class="font-bold text-emerald-400 uppercase tracking-wider text-[10px] block">1. Jenis Utama</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Jenis</label>
                                <select name="jenis" x-model="editFormData.jenis" @change="onJenisChangeEdit()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-emerald-400 font-mono font-bold focus:border-emerald-500">
                                    <option value="1.3.1">1.3.1 (Tanah)</option>
                                    <option value="1.3.2">1.3.2 (Peralatan &amp; Mesin)</option>
                                    <option value="1.3.3">1.3.3 (Gedung &amp; Bangunan)</option>
                                    <option value="1.3.4">1.3.4 (Jalan, Irigasi &amp; Jaringan)</option>
                                    <option value="1.3.5">1.3.5 (Aset Tetap Lainnya)</option>
                                    <option value="1.3.6">1.3.6 (KDP)</option>
                                    <option value="1.5.3">1.5.3 (Aset Tidak Berwujud)</option>
                                    <option value="1.3.7">1.3.7 (Aset Dalam Renovasi)</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Jenis</label>
                                <input type="text" name="nama_jenis" x-model="editFormData.nama_jenis" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-emerald-500">
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-amber-950/20 border border-amber-500/30 space-y-2">
                        <span class="font-bold text-amber-400 uppercase tracking-wider text-[10px] block">2. Sub Rincian Objek</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Sub Rincian</label>
                                <input type="text" name="sub_rincian_objek" x-model="editFormData.sub_rincian_objek" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-400 font-mono font-bold focus:border-amber-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Uraian Sub Rincian</label>
                                <input type="text" name="uraian_sub_rincian" x-model="editFormData.uraian_sub_rincian" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-amber-500">
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-purple-950/20 border border-purple-500/30 space-y-2">
                        <span class="font-bold text-purple-400 uppercase tracking-wider text-[10px] block">3. Sub - Sub Rincian Objek (Spesifik)</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Sub-Sub Rincian</label>
                                <input type="text" name="sub_sub_rincian_objek" x-model="editFormData.sub_sub_rincian_objek" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-purple-400 font-mono font-bold focus:border-purple-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Uraian Sub-Sub Rincian (Nama Barang)</label>
                                <input type="text" name="uraian_sub_sub_rincian" x-model="editFormData.uraian_sub_sub_rincian" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-purple-500">
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                        <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 text-slate-300 font-bold text-xs">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-cyan-500/20 transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL IMPORT CSV -->
        <div x-show="showImportModal" x-transition.opacity class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4" x-cloak>
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl relative" @click.away="showImportModal = false">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="p-2.5 rounded-2xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Import Data Kode 108 BMD</h3>
                            <p class="text-xs text-slate-400">Upload file Excel (.xlsx, .xls) atau CSV</p>
                        </div>
                    </div>
                    <button type="button" @click="showImportModal = false" class="text-slate-400 hover:text-white p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('master.jenis_astap.import') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
                    @csrf
                    <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                        <label class="block text-xs font-semibold text-slate-300">Pilih File Excel / CSV (*.xlsx, *.xls, *.csv):</label>
                        <input type="file" name="file" accept=".xlsx, .xls, .csv, .txt" required
                            class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-500/20 file:text-emerald-400 hover:file:bg-emerald-500/30 cursor-pointer">
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            💡 Format file yang didukung: <b>.xlsx</b>, <b>.xls</b>, atau <b>.csv</b>.
                        </p>
                        <div class="flex items-center space-x-2 pt-2 border-t border-slate-800/80">
                            <input type="checkbox" id="reset_existing" name="reset_existing" value="1" checked
                                class="w-4 h-4 rounded border-slate-700 bg-slate-950 text-emerald-500 focus:ring-0 cursor-pointer">
                            <label for="reset_existing" class="text-xs text-slate-300 cursor-pointer select-none font-medium">
                                Kosongkan data lama sebelum impor (Mencegah duplikasi data)
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <a href="{{ route('master.jenis_astap.template') }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 underline flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Template CSV Contoh</span>
                        </a>

                        <div class="flex items-center space-x-2">
                            <button type="button" @click="showImportModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all">
                                Upload &amp; Import
                            </button>
                        </div>
                    </div>
                </form>
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