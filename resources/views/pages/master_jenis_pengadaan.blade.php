<x-layout title="Jenis Pengadaan SIPD - SIMAT-RK">
    @section('page-title', 'Master Jenis Pengadaan (SIPD)')
    @section('breadcrumb', 'Master Data System / Jenis Pengadaan SIPD')

    <script>
        function masterJenisPengadaan() {
            return {
                searchQuery: '{{ request('search', '') }}',
                programFilter: '{{ request('program', 'all') }}',
                showAddModal: false,
                showEditModal: false,
                editActionUrl: '',

                editFormData: {
                    id: null,
                    program_kode: '',
                    program_nama: '',
                    kegiatan_kode: '',
                    kegiatan_nama: '',
                    sub_kegiatan_kode: '',
                    sub_kegiatan_nama: ''
                },

                openEdit(item) {
                    this.editFormData = { ...item };
                    this.editActionUrl = '/master-data/jenis-pengadaan/' + item.id;
                    this.showEditModal = true;
                },

                filterByProgram(progKode) {
                    this.programFilter = progKode;
                    let url = new URL(window.location.href);
                    if (progKode === 'all') {
                        url.searchParams.delete('program');
                    } else {
                        url.searchParams.set('program', progKode);
                    }
                    window.location.href = url.toString();
                },

                resetFilters() {
                    window.location.href = '{{ route('master.jenis_pengadaan') }}';
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
                        title: '🗑️ Konfirmasi Hapus Jenis Pengadaan',
                        message: 'Apakah Anda yakin ingin menghapus data jenis pengadaan SIPD ini dari master data?',
                        itemName: itemName || '',
                        type: 'danger',
                        btnText: '🗑️ Ya, Hapus Pengadaan',
                        onConfirm: () => {
                            formElement.submit();
                        }
                    });
                },

                confirmAddForm(event) {
                    event.preventDefault();
                    const formElement = event.target;
                    const itemPreview = (formElement.querySelector('[name="sub_kegiatan_nama"]')?.value || 'Sub Kegiatan Baru') + ' (' + (formElement.querySelector('[name="sub_kegiatan_kode"]')?.value || 'SIPD') + ')';
                    this.askConfirmation({
                        title: '➕ Konfirmasi Tambah Pengadaan SIPD',
                        message: 'Apakah Anda yakin ingin mendaftarkan jenis pengadaan SIPD baru ini ke master data?',
                        itemName: itemPreview,
                        type: 'success',
                        btnText: '➕ Ya, Simpan Pengadaan',
                        onConfirm: () => {
                            formElement.submit();
                        }
                    });
                },

                confirmEditForm(event) {
                    event.preventDefault();
                    const formElement = event.target;
                    const itemPreview = (this.editFormData.sub_kegiatan_nama || 'Sub Kegiatan') + ' (' + (this.editFormData.sub_kegiatan_kode || 'SIPD') + ')';
                    this.askConfirmation({
                        title: '✏️ Konfirmasi Simpan Perubahan Pengadaan',
                        message: 'Apakah Anda yakin ingin menyimpan perubahan data pengadaan SIPD ini?',
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

    <div x-data="masterJenisPengadaan()" x-cloak>



        @if ($errors->any())
            <div class="mb-5 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs font-semibold shadow-lg">
                <div class="flex items-center space-x-2.5 mb-1.5">
                    <span class="text-base">⚠️</span>
                    <span class="font-bold">Terjadi kesalahan validasi:</span>
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
                        <span>HIERARKI PENGADAAN SIPD (SISTEM INFORMASI PEMERINTAH DAERAH)</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Jenis & Hierarki Pengadaan SIPD</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Struktur 3 tingkatan penganggaran pengadaan barang & jasa (Program Pengadaan, Kegiatan Pengadaan, dan Sub Kegiatan Pengadaan) sesuai standar DPA SIPD.
                    </p>
                </div>
                
                <button type="button" @click="showAddModal = true"
                    class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Pengadaan SIPD</span>
                </button>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">📑</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Program</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-400">{{ count($uniquePrograms) }} Program</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">📁</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Kegiatan</span>
                        <span class="text-sm sm:text-base font-extrabold text-amber-300">{{ count($uniqueKegiatan) }} Kegiatan</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">📄</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Sub Kegiatan</span>
                        <span class="text-sm sm:text-base font-extrabold text-purple-300">{{ $totalCount }} Sub Kegiatan</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">🔗</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Sinkronisasi</span>
                        <span class="text-sm sm:text-base font-extrabold text-cyan-300">DPA SIPD RSUD</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar Only -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <form method="GET" action="{{ route('master.jenis_pengadaan') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full text-xs">
                
                <!-- Search Input Field -->
                <div class="relative flex-1 w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode / nama program, kegiatan, atau sub kegiatan SIPD... (Tekan Enter)"
                        class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
                    <svg class="w-4 h-4 text-emerald-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    @if (request('search'))
                        <a href="{{ route('master.jenis_pengadaan') }}" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</a>
                    @endif
                </div>

                <!-- Action Buttons & Counter Badge -->
                <div class="flex items-center space-x-2 shrink-0 w-full sm:w-auto">
                    <button type="submit" class="px-5 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Cari</span>
                    </button>

                    <span class="px-3.5 py-3 rounded-2xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300 whitespace-nowrap">
                        Menampilkan <span class="text-emerald-400 font-bold">{{ count($sipdList) }}</span> dari <span class="text-white font-bold">{{ $totalCount }}</span> Data SIPD
                    </span>

                    @if (request('search'))
                        <button type="button" @click="resetFilters()" class="px-3.5 py-3 rounded-2xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all shrink-0">
                            🔄 Reset
                        </button>
                    @endif
                </div>

            </form>
        </div>

        <!-- ========================================================================= -->
        <!-- TABEL STRUKTUR JENIS PENGADAAN SIPD (3 BLOK SESUAI FORMAT EXCEL)          -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-5 mb-6">
            <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 350px; overflow-y: auto; overflow-x: auto;">
                <table class="w-full text-left text-xs text-slate-300 border-collapse">
                    
                    <!-- 2-Tier Header Sesuai Desain Gambar -->
                    <thead class="shadow-sm" style="position: sticky; top: 0; z-index: 10; background-color: #020617;">
                        <!-- Baris Header 1 (Kelompok 3 Blok) -->
                        <tr class="text-center font-extrabold uppercase tracking-wider text-xs border-b border-slate-800">
                            <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 border-r border-slate-800 w-12 text-center align-middle">
                                NO
                            </th>
                            <th colspan="2" class="px-4 py-3 bg-emerald-950/60 text-emerald-300 border-r border-slate-800">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>📑</span>
                                    <span>Program Pengadaan SIPD</span>
                                </div>
                            </th>
                            <th colspan="2" class="px-4 py-3 bg-amber-950/60 text-amber-300 border-r border-slate-800">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>📁</span>
                                    <span>Kegiatan Pengadaan SIPD</span>
                                </div>
                            </th>
                            <th colspan="2" class="px-4 py-3 bg-purple-950/60 text-purple-300 border-r border-slate-800">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>📄</span>
                                    <span>Sub Kegiatan Pengadaan SIPD</span>
                                </div>
                            </th>
                            <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 text-center align-middle w-28">
                                Aksi
                            </th>
                        </tr>

                        <!-- Baris Header 2 (Sub Kolom: Kode & Nama) -->
                        <tr class="text-center font-bold uppercase tracking-wider text-[11px] border-b border-slate-800">
                            <!-- Program -->
                            <th class="px-3 py-2.5 bg-emerald-950/40 text-emerald-400 border-r border-slate-800/80 w-28">
                                Kode
                            </th>
                            <th class="px-4 py-2.5 bg-emerald-950/40 text-emerald-200 border-r border-slate-800/80 min-w-[220px]">
                                Nama Program
                            </th>

                            <!-- Kegiatan -->
                            <th class="px-3 py-2.5 bg-amber-950/40 text-amber-400 border-r border-slate-800/80 w-32">
                                Kode
                            </th>
                            <th class="px-4 py-2.5 bg-amber-950/40 text-amber-200 border-r border-slate-800/80 min-w-[220px]">
                                Nama Kegiatan Pengadaan
                            </th>

                            <!-- Sub Kegiatan -->
                            <th class="px-3 py-2.5 bg-purple-950/40 text-purple-400 border-r border-slate-800/80 w-36">
                                Kode
                            </th>
                            <th class="px-4 py-2.5 bg-purple-950/40 text-purple-200 border-r border-slate-800/80 min-w-[240px]">
                                Nama Sub Kegiatan Pengadaan
                            </th>
                        </tr>
                    </thead>

                    <!-- Body Tabel -->
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse ($sipdList as $index => $item)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <!-- Kolom 1: No -->
                                <td class="px-4 py-4 text-center font-bold text-slate-400 border-r border-slate-800/80">{{ $index + 1 }}</td>

                                <!-- Kolom 2: Kode Program -->
                                <td class="px-3 py-4 text-center font-mono font-bold text-emerald-400 bg-emerald-950/10 border-r border-slate-800/80">{{ $item->program_kode }}</td>

                                <!-- Kolom 3: Nama Program -->
                                <td class="px-4 py-4 font-semibold text-slate-200 bg-emerald-950/10 border-r border-slate-800/80">{{ $item->program_nama }}</td>

                                <!-- Kolom 4: Kode Kegiatan -->
                                <td class="px-3 py-4 text-center font-mono font-bold text-amber-400 bg-amber-950/10 border-r border-slate-800/80">{{ $item->kegiatan_kode }}</td>

                                <!-- Kolom 5: Nama Kegiatan -->
                                <td class="px-4 py-4 font-semibold text-slate-200 bg-amber-950/10 border-r border-slate-800/80">{{ $item->kegiatan_nama }}</td>

                                <!-- Kolom 6: Kode Sub Kegiatan -->
                                <td class="px-3 py-4 text-center font-mono font-bold text-purple-400 bg-purple-950/10 border-r border-slate-800/80">{{ $item->sub_kegiatan_kode }}</td>

                                <!-- Kolom 7: Nama Sub Kegiatan -->
                                <td class="px-4 py-4 font-semibold text-white bg-purple-950/10 border-r border-slate-800/80">{{ $item->sub_kegiatan_nama }}</td>

                                <!-- Kolom 8: Aksi -->
                                <td class="px-3 py-4 text-center space-x-1 whitespace-nowrap">
                                    <button type="button" @click="openEdit({{ json_encode($item) }})"
                                        class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Ubah</span>
                                    </button>

                                    <form method="POST" action="{{ route('master.jenis_pengadaan.destroy', $item->id) }}" class="inline-block" @submit="confirmDeleteForm($event, '{{ $item->sub_kegiatan_nama }} ({{ $item->sub_kegiatan_kode }})')">
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
                                <td colspan="8" class="text-center py-10 text-slate-500">
                                    <div class="text-2xl mb-2">📂</div>
                                    <div class="font-semibold text-slate-400">Tidak ada data jenis pengadaan ditemukan.</div>
                                    <div class="text-[11px] text-slate-600 mt-0.5">Silakan tambahkan data baru atau sesuaikan filter pencarian Anda.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL TAMBAH JENIS PENGADAAN SIPD                                         -->
        <!-- ========================================================================= -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl relative">
                <!-- Tombol Close Corner -->
                <button type="button" @click="showAddModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center text-sm font-bold transition-all">&times;</button>

                <!-- Header Center -->
                <div class="text-center pb-3 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-extrabold text-white">Tambah Pengadaan SIPD Baru</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Input struktur 3 tingkatan (Program, Kegiatan, Sub Kegiatan)</p>
                </div>

                <form method="POST" action="{{ route('master.jenis_pengadaan.store') }}" class="space-y-3.5 text-xs" @submit="confirmAddForm($event)">
                    @csrf

                    <!-- Blok 1: Program -->
                    <div class="p-3.5 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 space-y-2">
                        <span class="font-bold text-emerald-400 uppercase tracking-wider text-[10px] block">1. Program Pengadaan SIPD</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Program</label>
                                <input type="text" inputmode="numeric" @input="$event.target.value = $event.target.value.replace(/[^0-9.]/g, '')" name="program_kode" placeholder="0.00.01" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-emerald-400 font-mono font-bold focus:border-emerald-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Program</label>
                                <input type="text" name="program_nama" placeholder="Contoh: Program Penunjang Urusan Pemda..." required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-emerald-500">
                            </div>
                        </div>
                    </div>

                    <!-- Blok 2: Kegiatan -->
                    <div class="p-3.5 rounded-2xl bg-amber-950/20 border border-amber-500/30 space-y-2">
                        <span class="font-bold text-amber-400 uppercase tracking-wider text-[10px] block">2. Kegiatan Pengadaan SIPD</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Kegiatan</label>
                                <input type="text" inputmode="numeric" @input="$event.target.value = $event.target.value.replace(/[^0-9.]/g, '')" name="kegiatan_kode" placeholder="0.00.01.2.10" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-400 font-mono font-bold focus:border-amber-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Kegiatan Pengadaan</label>
                                <input type="text" name="kegiatan_nama" placeholder="Contoh: Peningkatan Pelayanan BLUD..." required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-amber-500">
                            </div>
                        </div>
                    </div>

                    <!-- Blok 3: Sub Kegiatan -->
                    <div class="p-3.5 rounded-2xl bg-purple-950/20 border border-purple-500/30 space-y-2">
                        <span class="font-bold text-purple-400 uppercase tracking-wider text-[10px] block">3. Sub Kegiatan Pengadaan SIPD</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Sub Kegiatan</label>
                                <input type="text" inputmode="numeric" @input="$event.target.value = $event.target.value.replace(/[^0-9.]/g, '')" name="sub_kegiatan_kode" placeholder="0.00.01.2.10.0001" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-purple-400 font-mono font-bold focus:border-purple-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Sub Kegiatan Pengadaan</label>
                                <input type="text" name="sub_kegiatan_nama" placeholder="Contoh: Pelayanan dan Penunjang Pelayanan..." required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-purple-500">
                            </div>
                        </div>
                    </div>
                    <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                        <button type="button" @click="showAddModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-slate-700 hover:border-rose-500/30 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Batal</span>
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Pengadaan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL UBAH JENIS PENGADAAN SIPD                                           -->
        <!-- ========================================================================= -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl relative">
                <!-- Tombol Close Corner -->
                <button type="button" @click="showEditModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center text-sm font-bold transition-all">&times;</button>

                <!-- Header Center -->
                <div class="text-center pb-3 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-extrabold text-white">Ubah Data Pengadaan SIPD</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Perbarui struktur 3 tingkatan (Program, Kegiatan, Sub Kegiatan)</p>
                </div>

                <form method="POST" :action="editActionUrl" class="space-y-3.5 text-xs" @submit="confirmEditForm($event)">
                    @csrf
                    @method('PUT')

                    <!-- Blok 1: Program -->
                    <div class="p-3.5 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 space-y-2">
                        <span class="font-bold text-emerald-400 uppercase tracking-wider text-[10px] block">1. Program Pengadaan SIPD</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Program</label>
                                <input type="text" inputmode="numeric" @input="editFormData.program_kode = $event.target.value = $event.target.value.replace(/[^0-9.]/g, '')" name="program_kode" x-model="editFormData.program_kode" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-emerald-400 font-mono font-bold focus:border-emerald-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Program</label>
                                <input type="text" name="program_nama" x-model="editFormData.program_nama" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-emerald-500">
                            </div>
                        </div>
                    </div>

                    <!-- Blok 2: Kegiatan -->
                    <div class="p-3.5 rounded-2xl bg-amber-950/20 border border-amber-500/30 space-y-2">
                        <span class="font-bold text-amber-400 uppercase tracking-wider text-[10px] block">2. Kegiatan Pengadaan SIPD</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Kegiatan</label>
                                <input type="text" inputmode="numeric" @input="editFormData.kegiatan_kode = $event.target.value = $event.target.value.replace(/[^0-9.]/g, '')" name="kegiatan_kode" x-model="editFormData.kegiatan_kode" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-400 font-mono font-bold focus:border-amber-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Kegiatan Pengadaan</label>
                                <input type="text" name="kegiatan_nama" x-model="editFormData.kegiatan_nama" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-amber-500">
                            </div>
                        </div>
                    </div>

                    <!-- Blok 3: Sub Kegiatan -->
                    <div class="p-3.5 rounded-2xl bg-purple-950/20 border border-purple-500/30 space-y-2">
                        <span class="font-bold text-purple-400 uppercase tracking-wider text-[10px] block">3. Sub Kegiatan Pengadaan SIPD</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Sub Kegiatan</label>
                                <input type="text" inputmode="numeric" @input="editFormData.sub_kegiatan_kode = $event.target.value = $event.target.value.replace(/[^0-9.]/g, '')" name="sub_kegiatan_kode" x-model="editFormData.sub_kegiatan_kode" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-purple-400 font-mono font-bold focus:border-purple-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Sub Kegiatan Pengadaan</label>
                                <input type="text" name="sub_kegiatan_nama" x-model="editFormData.sub_kegiatan_nama" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-purple-500">
                            </div>
                        </div>
                    </div>
                    <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                        <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-slate-700 hover:border-rose-500/30 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Batal</span>
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan</span>
                        </button>
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
