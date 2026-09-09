<x-layout title="Lembar Kartu Inventaris Ruangan (KIR) - SIMAT-RK">
    @section('page-title', 'Lembar KIR Ruangan')
    @section('breadcrumb', 'Master Utama / Lembar KIR Ruangan')

    @php
        $unitNama = $currentUnit->nama ?? 'Unit Ruangan';
        $unitKode = $currentUnit->kode_unit ?? ('UNIT-' . str_pad($currentUnit->id ?? 1, 3, '0', STR_PAD_LEFT));
        $unitTipe = $currentUnit->tipe ?? 'Unit Pelayanan / Instalasi RSUD';
        $unitKepala = $currentUnit->kepala ?? Auth::user()->name;
        $unitNip = $currentUnit->nip ?? Auth::user()->nip ?? '-';
        $isSubAdmin = (Auth::user()->role ?? '') === 'sub_admin';
    @endphp

    <script>
    function kirRuanganData() {
        return {
            searchQuery: '',
            statusFilter: 'all',
            tahunFilter: 'all',
            showPrintModal: false,
            showDetailModal: false,
            showEditModal: false,
            selectedAsset: null,
            editAsset: null,
            isSavingKondisi: false,
            showToast: false,
            toastMessage: '',

            init() {
                const updateBodyScroll = () => {
                    const isOpen = this.showEditModal || this.showDetailModal || this.showPrintModal;
                    document.body.style.overflow = isOpen ? 'hidden' : '';
                };
                this.$watch('showEditModal', updateBodyScroll);
                this.$watch('showDetailModal', updateBodyScroll);
                this.$watch('showPrintModal', updateBodyScroll);
            },

            // Form Ubah Kondisi
            editForm: {
                kondisi: 'Baik'
            },

            // Data Aset Ruangan
            assets: {!! json_encode($assets ?? []) !!},

            // Dokumen Pengesahan KIR
            kirDoc: {
                nomor_surat: '000.2.3.2/KIR/{{ $currentUnit->id ?? 1 }}/430.10.7/2026',
                pj_nama: {!! json_encode($unitKepala) !!},
                pj_nip: {!! json_encode($unitNip) !!},
                pj_jabatan: {!! json_encode('Penanggung Jawab ' . $unitNama) !!},
                pengurus_nama: 'BUDI HARTONO, S.Sos',
                pengurus_nip: '19760229 200801 1 010',
                pengurus_jabatan: 'Pengurus Barang Pengguna RSUD',
                direktur_nama: 'dr. YUS PRIYATNA ADRYANTO, Sp.P, FISR',
                direktur_nip: '19771002 200604 1 006',
                direktur_jabatan: 'Direktur RSUD dr. H. Koesnandi Bondowoso',
                kota_tanggal: 'Bondowoso, {{ date('d F Y') }}'
            },

            get filteredAssets() {
                const q = (this.searchQuery || '').toLowerCase();
                return this.assets.filter(a => {
                    const matchSearch = !q || 
                        (a.nama || '').toLowerCase().includes(q) || 
                        (a.kode || '').toLowerCase().includes(q) || 
                        (a.kode_108 || '').toLowerCase().includes(q) || 
                        (a.nibar || '').toLowerCase().includes(q) || 
                        (a.merk || '').toLowerCase().includes(q) ||
                        (a.no_seri || '').toLowerCase().includes(q);

                    const matchStatus = this.statusFilter === 'all' || a.kondisi === this.statusFilter;
                    const matchTahun = this.tahunFilter === 'all' || String(a.tahun) === String(this.tahunFilter);

                    return matchSearch && matchStatus && matchTahun;
                });
            },

            get countBaik() {
                return this.assets.filter(a => a.kondisi === 'Baik').length;
            },
            get countKurangBaik() {
                return this.assets.filter(a => a.kondisi === 'Kurang Baik' || a.kondisi === 'Rusak Ringan').length;
            },
            get countRusakBerat() {
                return this.assets.filter(a => a.kondisi === 'Rusak Berat' || a.kondisi === 'Rusak').length;
            },

            get persentaseBaik() {
                return this.assets.length ? ((this.countBaik / this.assets.length) * 100).toFixed(1) + '% Siap Digunakan' : '0%';
            },

            openDetail(ast) {
                this.selectedAsset = ast;
                this.showDetailModal = true;
            },

            openEditKondisi(ast) {
                this.editAsset = ast;
                this.editForm.kondisi = ast.kondisi || 'Baik';
                this.showEditModal = true;
            },

            async saveKondisi() {
                if (!this.editAsset) return;
                this.isSavingKondisi = true;
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                try {
                    const res = await fetch(`/lembar-kir-ruangan/kondisi/${this.editAsset.id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            kondisi: this.editForm.kondisi
                        })
                    });
                    const data = await res.json();
                    this.isSavingKondisi = false;

                    if (data.success) {
                        // Update state di array assets
                        const target = this.assets.find(a => a.id === this.editAsset.id);
                        if (target) {
                            target.kondisi = data.kondisi;
                        }
                        if (this.selectedAsset && this.selectedAsset.id === this.editAsset.id) {
                            this.selectedAsset.kondisi = data.kondisi;
                        }
                        this.showEditModal = false;
                        this.triggerToast(data.message || 'Kondisi barang berhasil diperbarui!');
                    } else {
                        alert('⚠️ ' + (data.message || 'Gagal mengubah kondisi barang.'));
                    }
                } catch(err) {
                    this.isSavingKondisi = false;
                    alert('⚠️ Terjadi kendala saat menyimpan perubahan kondisi.');
                }
            },

            triggerToast(msg) {
                this.toastMessage = msg;
                this.showToast = true;
                setTimeout(() => { this.showToast = false; }, 3500);
            },

            printKir() {
                window.print();
            },

            formatRupiah(num) {
                return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
            }
        };
    }
    </script>

    <div x-data="kirRuanganData()" x-cloak class="space-y-6">

        <!-- FLOATING TOAST NOTIFICATION -->
        <div x-show="showToast" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
            x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed top-5 right-5 z-50 max-w-md bg-slate-900 border border-emerald-500/40 rounded-2xl p-4 shadow-2xl flex items-center space-x-3 text-xs text-white">
            <div class="p-2 rounded-xl bg-emerald-500/20 text-emerald-400 font-bold text-base shrink-0">
                ✓
            </div>
            <div class="flex-1">
                <p class="font-bold text-emerald-300">Pembaruan Kondisi Berhasil</p>
                <p class="text-slate-300 text-[11px] mt-0.5" x-text="toastMessage"></p>
            </div>
        </div>

        <!-- 1. HEADER BANNER KARTU INVENTARIS RUANGAN (KIR) (NO-PRINT) -->
        <div class="no-print bg-gradient-to-r from-emerald-600/15 via-teal-950/40 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-56 h-56 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute top-0 right-1/4 w-32 h-32 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
                <!-- Info Header Ruangan -->
                <div class="space-y-3 max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold tracking-wide">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>KARTU INVENTARIS RUANGAN (KIR) RESMI</span>
                        </span>
                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 border border-slate-700 text-xs font-mono font-semibold">
                            <span>{{ $unitKode }}</span>
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-teal-500/15 text-teal-300 border border-teal-500/30 text-xs font-semibold">
                            <span>{{ $unitTipe }}</span>
                        </span>
                    </div>

                    <div>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight flex flex-wrap items-center gap-3">
                            <span>📋 Lembar KIR: {{ $unitNama }}</span>
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1.5 leading-relaxed">
                            Penanggung Jawab: <span class="font-bold text-white">{{ $unitKepala }}</span> 
                            <span class="text-slate-400 font-mono">(NIP. {{ $unitNip }})</span> · 
                            <span class="text-emerald-400 font-semibold">{{ $totalAsetCount ?? 0 }} Unit Aset Terpasang</span>
                        </p>
                    </div>

                    <p class="text-xs text-slate-400 leading-relaxed">
                        Dokumen Kartu Inventaris Ruangan (KIR) memuat seluruh daftar fisik barang aset tetap yang ditempatkan resmi pada ruangan ini. Anda dapat memperbarui kondisi fisik barang secara langsung serta mencetak lembar KIR ber-barcode resmi.
                    </p>
                </div>

                <!-- Action Buttons: Ganti Ruangan & Cetak Dokumen -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0 w-full sm:w-auto">
                    @if (!$isSubAdmin && count($units) > 1)
                        <!-- Dropdown Pilihan Ruangan (Untuk Admin / Master Admin) -->
                        <div class="relative min-w-[200px]">
                            <select onchange="window.location.href = '{{ route('kir.index') }}?unit_id=' + this.value"
                                class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-3.5 py-2.5 text-xs font-bold focus:outline-none focus:border-emerald-500 shadow-sm cursor-pointer">
                                @foreach ($units as $u)
                                    <option value="{{ $u->id }}" {{ ($currentUnit->id ?? 0) == $u->id ? 'selected' : '' }}>
                                        {{ $u->nama }} ({{ $u->kode_unit ?? ('UNIT-' . $u->id) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <button type="button" @click="showPrintModal = true"
                        class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/25 transition-all flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Dokumen KIR</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. SUMMARY KPI STATS RUANGAN (NO-PRINT) -->
        <div class="no-print grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- 1. Total Aset -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-emerald-500/40 transition-all">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Aset di Ruangan Ini</span>
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline space-x-2">
                    <p class="text-2xl sm:text-3xl font-black text-white" x-text="assets.length">0</p>
                    <span class="text-xs font-bold text-emerald-400">Unit Barang</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">Tercatat di Dokumen KIR Ruangan</p>
            </div>

            <!-- 2. Nilai Valuasi Aset Ruangan -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-cyan-500/40 transition-all">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Valuasi Nilai Ruangan</span>
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline space-x-1">
                    <p class="text-xl sm:text-2xl font-black text-white">{{ $totalNilaiFmt ?? 'Rp 0' }}</p>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">Estimasi Nilai Buku Inventaris Unit</p>
            </div>

            <!-- 3. Kondisi Baik (Siap Pakai) -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-teal-500/40 transition-all">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Kondisi Siap Pakai</span>
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline space-x-2">
                    <p class="text-2xl sm:text-3xl font-black text-white" x-text="countBaik">0</p>
                    <span class="text-xs font-bold text-emerald-400">Unit Baik</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">
                    <span x-text="persentaseBaik"></span>
                </p>
            </div>

            <!-- 4. Kondisi Perlu Perhatian / Servis -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-amber-500/40 transition-all">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Perlu Penanganan</span>
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline space-x-2">
                    <p class="text-2xl sm:text-3xl font-black text-white" x-text="countKurangBaik + countRusakBerat">0</p>
                    <span class="text-xs font-bold text-amber-400">Unit Servis/Rusak</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">
                    <span x-text="countKurangBaik + ' Kurang Baik · ' + countRusakBerat + ' Rusak Berat'"></span>
                </p>
            </div>
        </div>

        <!-- 3. TABEL DAFTAR ASET RUANGAN & FILTER (NO-PRINT) -->
        <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            
            <!-- Filter & Search Controls Header -->
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                <!-- Filter Status Pills -->
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <button type="button" @click="statusFilter = 'all'"
                        :class="statusFilter === 'all' ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        Semua (<span x-text="assets.length"></span>)
                    </button>
                    <button type="button" @click="statusFilter = 'Baik'"
                        :class="statusFilter === 'Baik' ? 'bg-emerald-500/20 text-emerald-300 font-extrabold border border-emerald-500/40 shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1">
                        <span>Baik</span>
                        <span class="px-1.5 py-0.2 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px]" x-text="countBaik"></span>
                    </button>
                    <button type="button" @click="statusFilter = 'Kurang Baik'"
                        :class="statusFilter === 'Kurang Baik' ? 'bg-amber-500/20 text-amber-300 font-extrabold border border-amber-500/40 shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1">
                        <span>Kurang Baik</span>
                        <span class="px-1.5 py-0.2 rounded-full bg-amber-500/20 text-amber-300 text-[10px]" x-text="countKurangBaik"></span>
                    </button>
                    <button type="button" @click="statusFilter = 'Rusak Berat'"
                        :class="statusFilter === 'Rusak Berat' ? 'bg-rose-500/20 text-rose-300 font-extrabold border border-rose-500/40 shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1">
                        <span>Rusak Berat</span>
                        <span class="px-1.5 py-0.2 rounded-full bg-rose-500/20 text-rose-300 text-[10px]" x-text="countRusakBerat"></span>
                    </button>
                </div>

                <!-- Search Input Box -->
                <div class="relative min-w-[260px]">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="searchQuery" placeholder="Cari nama, NIBAR, kode 108..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-10 pr-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
                </div>
            </div>

            <!-- Tabel Data Aset Fisik Ruangan (Scrollable 5-6 rows: max-h-[380px]) -->
            <div class="overflow-x-auto overflow-y-auto rounded-2xl border border-slate-800/80 bg-slate-950/50 relative shadow-inner"
                 style="max-height: 380px !important; overflow-y: auto !important; overflow-x: auto !important;">
                <table class="w-full text-left text-xs text-slate-300 border-separate border-spacing-0">
                    <thead class="sticky top-0 z-20 bg-slate-950 text-slate-400 font-bold uppercase tracking-wider shadow-sm">
                        <tr>
                            <th class="px-3 py-3 text-center whitespace-nowrap w-10 bg-slate-950 border-b border-slate-800">No</th>
                            <th class="px-3.5 py-3 text-left min-w-[160px] bg-slate-950 border-b border-slate-800">Nomor Register NIBAR</th>
                            <th class="px-3.5 py-3 text-left min-w-[220px] bg-slate-950 border-b border-slate-800">Nama Barang / ASTAP</th>
                            <th class="px-3.5 py-3 text-left min-w-[140px] bg-slate-950 border-b border-slate-800">Merk / Tipe</th>
                            <th class="px-3.5 py-3 text-center whitespace-nowrap bg-slate-950 border-b border-slate-800">No. Seri/Pabrik</th>
                            <th class="px-3.5 py-3 text-center whitespace-nowrap bg-slate-950 border-b border-slate-800">Tahun</th>
                            <th class="px-3.5 py-3 text-right whitespace-nowrap bg-slate-950 border-b border-slate-800">Nilai Buku</th>
                            <th class="px-3.5 py-3 text-center whitespace-nowrap min-w-[120px] bg-slate-950 border-b border-slate-800">Kondisi Barang</th>
                            <th class="px-3.5 py-3 text-center whitespace-nowrap border-l border-b border-slate-800 shrink-0 min-w-[170px] w-[170px]" 
                                style="position: sticky; right: 0; top: 0; z-index: 30; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredAssets" :key="item.id">
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-3 py-3 text-center font-bold text-slate-500" x-text="index + 1"></td>
                                <td class="px-3.5 py-3">
                                    <div class="flex items-center space-x-1.5">
                                        <span class="font-mono font-semibold text-emerald-400 text-xs block truncate max-w-[200px]" 
                                              :title="item.nibar" x-text="item.nibar"></span>
                                    </div>
                                    <span class="font-mono text-[10px] text-slate-500 block" x-text="'108: ' + item.kode_108"></span>
                                </td>
                                <td class="px-3.5 py-3">
                                    <p class="font-bold text-white text-xs leading-snug" x-text="item.nama"></p>
                                    <p class="text-[10px] text-slate-400 truncate max-w-xs mt-0.5" x-text="item.kategori"></p>
                                </td>
                                <td class="px-3.5 py-3 text-slate-300" x-text="item.merk || '-'"></td>
                                <td class="px-3.5 py-3 text-center font-mono text-[11px] text-slate-400" x-text="item.no_seri || '-'"></td>
                                <td class="px-3.5 py-3 text-center font-mono text-slate-300 font-semibold" x-text="item.tahun || '-'"></td>
                                <td class="px-3.5 py-3 text-right font-mono font-semibold text-white whitespace-nowrap" x-text="item.harga_fmt"></td>
                                
                                <!-- Kolom Kondisi dengan Tombol Cepat Ubah -->
                                <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                    <button type="button" @click="openEditKondisi(item)"
                                        class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold cursor-pointer hover:ring-2 hover:ring-amber-400/50 transition-all group"
                                        :class="item.kondisi === 'Baik' ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 
                                               (item.kondisi === 'Kurang Baik' ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30' : 
                                               (item.kondisi === 'Rusak Ringan' ? 'bg-orange-500/15 text-orange-300 border border-orange-500/30' : 'bg-rose-500/15 text-rose-300 border border-rose-500/30'))"
                                        title="Klik untuk ubah kondisi barang">
                                        <span x-text="item.kondisi"></span>
                                        <svg class="w-3 h-3 opacity-60 group-hover:opacity-100 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                </td>

                                <!-- Kolom Aksi — FREEZE STICKY RIGHT -->
                                <td class="px-3.5 py-3 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[170px] w-[170px]" 
                                    style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- 1. Tombol Detail (Kaya Data ASTAP) -->
                                        <button type="button" @click="openDetail(item)"
                                            title="Lihat Detail ASTAP"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all shadow-sm active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Detail</span>
                                        </button>

                                        <!-- 2. Tombol Ubah Kondisi -->
                                        <button type="button" @click="openEditKondisi(item)"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold text-xs transition-all shadow-sm active:scale-95 cursor-pointer leading-none"
                                            title="Ubah Kondisi Fisik Barang">
                                            <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            <span>Ubah</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredAssets.length === 0">
                            <td colspan="9" class="p-8 text-center text-slate-400">
                                <span class="text-2xl block mb-2">🔍</span>
                                <p class="text-sm font-semibold">Tidak ada aset yang sesuai dengan pencarian atau filter Anda.</p>
                                <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci atau bersihkan filter kondisi.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer Info Table -->
            <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-400 gap-2">
                <div>
                    Menampilkan <strong class="text-white" x-text="filteredAssets.length"></strong> dari <strong class="text-white" x-text="assets.length"></strong> unit inventaris ruangan
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" @click="showPrintModal = true"
                        class="text-emerald-400 hover:underline font-semibold flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Pratinjau Lembar Cetak KIR &rarr;</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 4. MODAL UBAH KONDISI BARANG (INTERAKTIF & REALTIME) -->
        <div x-show="showEditModal" x-cloak 
            class="flex items-center justify-center p-4"
            style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 99999 !important; background-color: rgba(2, 6, 23, 0.88) !important; backdrop-filter: blur(14px) !important; -webkit-backdrop-filter: blur(14px) !important;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            
            <div @click.away="if (!isSavingKondisi) showEditModal = false"
                class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative overflow-hidden"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                
                <template x-if="editAsset">
                    <div>
                        <!-- Header Modal Ubah -->
                        <div class="flex items-start justify-between border-b border-slate-800 pb-4 mb-4">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2.5 py-0.5 rounded font-mono text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        🛠️ UBAH KONDISI FISIK
                                    </span>
                                    <span class="font-mono text-xs text-slate-400" x-text="editAsset.nibar"></span>
                                </div>
                                <h3 class="text-base sm:text-lg font-black text-white mt-1.5" x-text="editAsset.nama"></h3>
                            </div>
                            <button type="button" @click="showEditModal = false" :disabled="isSavingKondisi" class="text-slate-400 hover:text-white p-1 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Body Modal Ubah -->
                        <div class="space-y-4 text-xs">
                            <!-- Info Singkat Aset -->
                            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 flex items-center justify-between text-xs">
                                <div>
                                    <span class="text-slate-500 text-[10px] block uppercase font-bold">Ruangan Terpasang</span>
                                    <span class="text-white font-semibold">{{ $unitNama }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-slate-500 text-[10px] block uppercase font-bold">Kondisi Saat Ini</span>
                                    <span class="font-bold text-amber-400" x-text="editAsset.kondisi"></span>
                                </div>
                            </div>

                            <!-- Opsi Pilihan Kondisi (3 Radio Cards) -->
                            <div>
                                <label class="block text-slate-300 font-bold mb-2">Pilih Kondisi Fisik Baru:</label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                    <!-- 1. Baik -->
                                    <label class="p-3 rounded-xl border cursor-pointer transition-all flex items-start space-x-2.5"
                                        :class="editForm.kondisi === 'Baik' ? 'bg-emerald-500/15 border-emerald-500 text-white shadow-sm' : 'bg-slate-950 border-slate-800 text-slate-400 hover:border-slate-700'">
                                        <input type="radio" name="pilihan_kondisi" value="Baik" x-model="editForm.kondisi" class="mt-0.5 text-emerald-500 focus:ring-0">
                                        <div>
                                            <span class="font-bold block text-xs" :class="editForm.kondisi === 'Baik' ? 'text-emerald-300' : 'text-white'">🟢 Baik</span>
                                            <span class="text-[10px] text-slate-400 leading-tight block mt-0.5">Berfungsi normal &amp; siap digunakan</span>
                                        </div>
                                    </label>

                                    <!-- 2. Kurang Baik -->
                                    <label class="p-3 rounded-xl border cursor-pointer transition-all flex items-start space-x-2.5"
                                        :class="editForm.kondisi === 'Kurang Baik' ? 'bg-amber-500/15 border-amber-500 text-white shadow-sm' : 'bg-slate-950 border-slate-800 text-slate-400 hover:border-slate-700'">
                                        <input type="radio" name="pilihan_kondisi" value="Kurang Baik" x-model="editForm.kondisi" class="mt-0.5 text-amber-500 focus:ring-0">
                                        <div>
                                            <span class="font-bold block text-xs" :class="editForm.kondisi === 'Kurang Baik' ? 'text-amber-300' : 'text-white'">🟡 Kurang Baik</span>
                                            <span class="text-[10px] text-slate-400 leading-tight block mt-0.5">Kendala minor / aus / perlu servis</span>
                                        </div>
                                    </label>

                                    <!-- 3. Rusak Berat -->
                                    <label class="p-3 rounded-xl border cursor-pointer transition-all flex items-start space-x-2.5"
                                        :class="editForm.kondisi === 'Rusak Berat' ? 'bg-rose-500/15 border-rose-500 text-white shadow-sm' : 'bg-slate-950 border-slate-800 text-slate-400 hover:border-slate-700'">
                                        <input type="radio" name="pilihan_kondisi" value="Rusak Berat" x-model="editForm.kondisi" class="mt-0.5 text-rose-500 focus:ring-0">
                                        <div>
                                            <span class="font-bold block text-xs" :class="editForm.kondisi === 'Rusak Berat' ? 'text-rose-300' : 'text-white'">🔴 Rusak Berat</span>
                                            <span class="text-[10px] text-slate-400 leading-tight block mt-0.5">Mati total / tidak dapat digunakan</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Modal Ubah -->
                        <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-end space-x-2.5">
                            <button type="button" @click="showEditModal = false" :disabled="isSavingKondisi"
                                class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all">
                                Batal
                            </button>
                            <button type="button" @click="saveKondisi()" :disabled="isSavingKondisi"
                                class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5 disabled:opacity-50">
                                <span x-show="isSavingKondisi" class="animate-spin text-sm leading-none">⚙️</span>
                                <span x-text="isSavingKondisi ? 'Menyimpan...' : 'Simpan Perubahan Kondisi'"></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- 5. MODAL PRATINJAU & CETAK DOKUMEN KIR RESMI (KERTAS PUTIH STANDAR PEMERINTAH) -->
        <div x-show="showPrintModal" x-cloak
            class="flex items-center justify-center p-3 sm:p-6 overflow-y-auto"
            style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 99999 !important; background-color: rgba(2, 6, 23, 0.88) !important; backdrop-filter: blur(14px) !important; -webkit-backdrop-filter: blur(14px) !important;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            
            <div @click.away="showPrintModal = false"
                class="bg-slate-900 border border-slate-800 rounded-3xl max-w-5xl w-full p-4 sm:p-6 shadow-2xl relative overflow-hidden flex flex-col max-h-[92vh]">
                
                <!-- Modal Top Action Bar -->
                <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-3 shrink-0">
                    <div class="flex items-center space-x-2">
                        <span class="text-xl">🖨️</span>
                        <h3 class="text-base font-extrabold text-white">Pratinjau Cetak Lembar Kartu Inventaris Ruangan (KIR)</h3>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="button" @click="printKir()"
                            class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5">
                            <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak Sekarang (Print)</span>
                        </button>
                        <button type="button" @click="showPrintModal = false" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <!-- LEMBAR DOKUMEN CETAK KERTAS PUTIH (PRINT READY) -->
                <div class="flex-1 overflow-y-auto rounded-2xl bg-white text-black p-6 sm:p-8 font-serif text-[11px] leading-relaxed shadow-inner">
                    
                    <!-- KOP SURAT RESMI RSUD -->
                    <div class="border-b-[3px] border-black pb-1 mb-0.5">
                        <div class="flex items-center justify-between gap-4">
                            <div class="w-20 shrink-0 flex justify-center">
                                <img src="{{ asset('img/logo-bondowoso.png') }}" alt="Logo Bondowoso" class="h-16 w-16 object-contain">
                            </div>

                            <div class="flex-1 text-center font-sans text-black">
                                <h4 class="font-bold text-xs sm:text-sm uppercase tracking-wide leading-tight">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                <h3 class="font-black text-sm sm:text-base uppercase tracking-tight leading-tight">RUMAH SAKIT UMUM DAERAH dr. H. KOESNANDI</h3>
                                <p class="text-[10px] leading-tight mt-0.5">Jl. Kapten Piere Tendean No. 3 Telp. (0332) 421974 Fax. (0332) 422311</p>
                                <p class="text-[10px] leading-tight">e-mail : rsu.koesnadi@gmail.com · Website : rsudrkoesnadi.go.id</p>
                                <h4 class="font-bold text-xs tracking-[0.3em] uppercase mt-0.5">B O N D O W O S O</h4>
                            </div>

                            <div class="w-20 shrink-0 flex justify-center">
                                <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="h-16 w-16 object-contain">
                            </div>
                        </div>
                    </div>
                    <div class="border-b border-black mb-3"></div>

                    <!-- JUDUL LEMBAR KIR -->
                    <div class="text-center font-sans mb-3">
                        <h3 class="font-black text-xs sm:text-sm uppercase underline tracking-wider">KARTU INVENTARIS RUANGAN (KIR)</h3>
                        <p class="text-[10px] font-semibold">Nomor Registrasi : <span x-text="kirDoc.nomor_surat"></span></p>
                    </div>

                    <!-- ATRIBUT IDENTITAS RUANGAN -->
                    <div class="grid grid-cols-2 gap-x-6 gap-y-1 font-sans text-[10px] mb-3 border p-2.5 bg-gray-50 border-gray-300 rounded">
                        <div class="flex">
                            <span class="w-36 font-bold">SKPD / Unit Kerja</span>
                            <span class="w-3">:</span>
                            <span class="font-semibold text-black">RSUD dr. H. KOESNANDI BONDOWOSO</span>
                        </div>
                        <div class="flex">
                            <span class="w-36 font-bold">KABUPATEN / PROVINSI</span>
                            <span class="w-3">:</span>
                            <span class="font-semibold text-black">BONDOWOSO / JAWA TIMUR</span>
                        </div>
                        <div class="flex">
                            <span class="w-36 font-bold">NAMA RUANGAN / UNIT</span>
                            <span class="w-3">:</span>
                            <span class="font-bold text-black uppercase">{{ $unitNama }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-36 font-bold">KODE RUANGAN / LOKASI</span>
                            <span class="w-3">:</span>
                            <span class="font-mono font-bold text-black">{{ $unitKode }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-36 font-bold">PENANGGUNG JAWAB</span>
                            <span class="w-3">:</span>
                            <span class="font-semibold text-black">{{ $unitKepala }} (NIP. {{ $unitNip }})</span>
                        </div>
                        <div class="flex">
                            <span class="w-36 font-bold">TAHUN ANGGARAN</span>
                            <span class="w-3">:</span>
                            <span class="font-bold text-black">2026</span>
                        </div>
                    </div>

                    <!-- TABEL RESMI FORMAT 12 KOLOM PERMENDAGRI BMD UNTUK LEMBAR KIR -->
                    <div class="mb-4 overflow-x-auto">
                        <table class="w-full border-collapse border border-black text-[9.5px]">
                            <thead class="bg-gray-100 font-sans text-center font-bold">
                                <tr>
                                    <th rowspan="2" class="border border-black px-1 py-1.5 w-6">No</th>
                                    <th rowspan="2" class="border border-black px-2 py-1.5">Jenis Barang / Nama Barang</th>
                                    <th rowspan="2" class="border border-black px-2 py-1.5">Merk / Type / Spesifikasi</th>
                                    <th rowspan="2" class="border border-black px-2 py-1.5">No. Pabrik / No. Seri</th>
                                    <th rowspan="2" class="border border-black px-2 py-1.5 w-12">Tahun</th>
                                    <th rowspan="2" class="border border-black px-2 py-1.5">Kode 108 / Register NIBAR</th>
                                    <th rowspan="2" class="border border-black px-1 py-1.5 w-8">Jml</th>
                                    <th rowspan="2" class="border border-black px-2 py-1.5 text-right">Harga Beli / Nilai (Rp)</th>
                                    <th colspan="3" class="border border-black px-1 py-1">Keadaan Barang</th>
                                    <th rowspan="2" class="border border-black px-2 py-1.5">Keterangan</th>
                                </tr>
                                <tr>
                                    <th class="border border-black px-1 py-0.5 w-7">B</th>
                                    <th class="border border-black px-1 py-0.5 w-7">KB</th>
                                    <th class="border border-black px-1 py-0.5 w-7">RB</th>
                                </tr>
                                <tr class="bg-gray-50 text-[8px] font-mono">
                                    <th class="border border-black">1</th>
                                    <th class="border border-black">2</th>
                                    <th class="border border-black">3</th>
                                    <th class="border border-black">4</th>
                                    <th class="border border-black">5</th>
                                    <th class="border border-black">6</th>
                                    <th class="border border-black">7</th>
                                    <th class="border border-black">8</th>
                                    <th class="border border-black">9</th>
                                    <th class="border border-black">10</th>
                                    <th class="border border-black">11</th>
                                    <th class="border border-black">12</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(ast, idx) in assets" :key="idx">
                                    <tr>
                                        <td class="border border-black px-1 py-1 text-center font-mono" x-text="idx + 1"></td>
                                        <td class="border border-black px-2 py-1 font-sans font-bold" x-text="ast.nama"></td>
                                        <td class="border border-black px-2 py-1 font-sans" x-text="ast.merk"></td>
                                        <td class="border border-black px-2 py-1 font-mono text-[9px]" x-text="ast.no_seri"></td>
                                        <td class="border border-black px-1 py-1 text-center font-mono" x-text="ast.tahun"></td>
                                        <td class="border border-black px-2 py-1 font-mono text-center text-[8.5px]" x-text="ast.nibar || ast.kode"></td>
                                        <td class="border border-black px-1 py-1 text-center font-mono font-bold">1</td>
                                        <td class="border border-black px-2 py-1 text-right font-mono font-semibold" x-text="formatRupiah(ast.harga || 0)"></td>
                                        
                                        <!-- Keadaan Barang: B, KB, RB -->
                                        <td class="border border-black px-1 py-1 text-center font-bold font-sans">
                                            <span x-text="ast.kondisi === 'Baik' ? '✓' : ''"></span>
                                        </td>
                                        <td class="border border-black px-1 py-1 text-center font-bold font-sans text-amber-700">
                                            <span x-text="ast.kondisi === 'Kurang Baik' || ast.kondisi === 'Rusak Ringan' ? '✓' : ''"></span>
                                        </td>
                                        <td class="border border-black px-1 py-1 text-center font-bold font-sans text-red-700">
                                            <span x-text="ast.kondisi === 'Rusak Berat' || ast.kondisi === 'Rusak' ? '✓' : ''"></span>
                                        </td>
                                        <td class="border border-black px-2 py-1 text-[8.5px] font-sans" x-text="ast.kategori"></td>
                                    </tr>
                                </template>

                                <!-- Baris Total Akumulasi -->
                                <tr class="bg-gray-100 font-sans font-bold">
                                    <td colspan="6" class="border border-black px-2 py-1.5 text-right uppercase">TOTAL ASET RUANGAN :</td>
                                    <td class="border border-black px-1 py-1.5 text-center font-mono" x-text="assets.length"></td>
                                    <td class="border border-black px-2 py-1.5 text-right font-mono" x-text="'{{ $totalNilaiFmt }}'"></td>
                                    <td class="border border-black px-1 py-1.5 text-center font-mono" x-text="countBaik"></td>
                                    <td class="border border-black px-1 py-1.5 text-center font-mono" x-text="countKurangBaik + countRusakRingan"></td>
                                    <td class="border border-black px-1 py-1.5 text-center font-mono" x-text="countRusakBerat"></td>
                                    <td class="border border-black px-1 py-1.5 text-center text-[8.5px]">Lengkap</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- TANDA TANGAN PENGESAHAN DOKUMEN KIR -->
                    <div class="grid grid-cols-3 gap-4 text-center font-sans text-[10px] mt-6 pt-3 border-t border-gray-300">
                        <!-- Kolom 1: Pengurus Barang Aset -->
                        <div class="space-y-1">
                            <p>Mengetahui,</p>
                            <p class="font-bold">PENGURUS BARANG ASET RSUD</p>
                            <div class="h-16 flex items-center justify-center">
                                <!-- QR TTE Otentikasi BSrE -->
                                <div class="border border-gray-300 p-1 rounded bg-gray-50 flex items-center space-x-1">
                                    <span class="text-xs">🛡️</span>
                                    <span class="text-[8px] font-mono text-gray-600">TTE Terverifikasi BSrE</span>
                                </div>
                            </div>
                            <p class="font-bold underline" x-text="kirDoc.pengurus_nama"></p>
                            <p class="font-mono text-[9px]" x-text="'NIP. ' + kirDoc.pengurus_nip"></p>
                        </div>

                        <!-- Kolom 2: Direktur RSUD -->
                        <div class="space-y-1">
                            <p>Disetujui,</p>
                            <p class="font-bold">DIREKTUR RSUD dr. H. KOESNANDI</p>
                            <div class="h-16 flex items-center justify-center">
                                <div class="border border-gray-300 p-1 rounded bg-gray-50 flex items-center space-x-1">
                                    <span class="text-xs">📜</span>
                                    <span class="text-[8px] font-mono text-gray-600">Sertifikat BSrE Sah</span>
                                </div>
                            </div>
                            <p class="font-bold underline" x-text="kirDoc.direktur_nama"></p>
                            <p class="font-mono text-[9px]" x-text="'NIP. ' + kirDoc.direktur_nip"></p>
                        </div>

                        <!-- Kolom 3: Penanggung Jawab Ruangan -->
                        <div class="space-y-1">
                            <p x-text="kirDoc.kota_tanggal"></p>
                            <p class="font-bold">PENANGGUNG JAWAB RUANGAN</p>
                            <div class="h-16 flex items-center justify-center">
                                <div class="border border-gray-300 p-1 rounded bg-gray-50 flex items-center space-x-1">
                                    <span class="text-xs">✍️</span>
                                    <span class="text-[8px] font-mono text-gray-600">Paraf / TTE Fisik</span>
                                </div>
                            </div>
                            <p class="font-bold underline" x-text="kirDoc.pj_nama"></p>
                            <p class="font-mono text-[9px]" x-text="'NIP. ' + kirDoc.pj_nip"></p>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- 6. FRONTEND MODAL: DETAIL ASET RUANGAN (SESUAI KATALOG DATA ASTAP) -->
        <div x-show="showDetailModal" x-cloak @click.self="showDetailModal = false" 
            class="flex items-center justify-center p-3 sm:p-4 md:p-6 overflow-y-auto" 
            style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 99999 !important; background-color: rgba(2, 6, 23, 0.88) !important; backdrop-filter: blur(14px) !important; -webkit-backdrop-filter: blur(14px) !important;">
            <div class="border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 md:p-8 shadow-2xl overflow-y-auto max-h-[90vh] space-y-5 my-auto" style="background-color: #0f172a;">
                
                <template x-if="selectedAsset">
                    <div class="space-y-5">
                        <!-- Modal Header -->
                        <div class="flex items-start justify-between pb-4 border-b border-slate-800 gap-4">
                            <div class="space-y-1.5 min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0"
                                        :class="{
                                            'bg-amber-500/20 text-amber-300 border-amber-500/30': (selectedAsset.kategori_kib === 'KIB A'),
                                            'bg-cyan-500/20 text-cyan-300 border-cyan-500/30':     (selectedAsset.kategori_kib === 'KIB B' || !selectedAsset.kategori_kib),
                                            'bg-purple-500/20 text-purple-300 border-purple-500/30': (selectedAsset.kategori_kib === 'KIB C'),
                                            'bg-teal-500/20 text-teal-300 border-teal-500/30':     (selectedAsset.kategori_kib === 'KIB D'),
                                            'bg-orange-500/20 text-orange-300 border-orange-500/30': (selectedAsset.kategori_kib === 'KIB E'),
                                            'bg-rose-500/20 text-rose-300 border-rose-500/30':     (selectedAsset.kategori_kib === 'KIB F'),
                                            'bg-indigo-500/20 text-indigo-300 border-indigo-500/30': (selectedAsset.kategori_kib === 'ATB'),
                                            'bg-amber-400/20 text-amber-300 border-amber-400/30': (selectedAsset.kategori_kib === 'EXTRACOM')
                                        }"
                                        x-text="selectedAsset.kategori_kib || selectedAsset.kategori || 'ASTAP'"></span>

                                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-cyan-400 font-mono font-bold text-[11px] truncate max-w-full"
                                        x-text="'Kode: ' + (selectedAsset.kode_barang || selectedAsset.kode_108 || '-')"></span>

                                    <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 font-mono font-bold text-[11px] truncate max-w-full"
                                        x-text="'NIBAR: ' + selectedAsset.nibar"></span>

                                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-300 font-mono text-[11px] flex items-center space-x-1.5 shrink-0">
                                        <span class="text-slate-400">📅 Tahun:</span>
                                        <span class="text-cyan-300 font-bold" x-text="selectedAsset.tahun"></span>
                                    </span>
                                </div>
                                <h3 class="text-base sm:text-lg md:text-xl font-extrabold text-white leading-snug break-words mt-1" x-text="selectedAsset.nama"></h3>
                            </div>

                            <!-- Tombol Close -->
                            <button type="button" @click="showDetailModal = false" class="w-8 h-8 rounded-full bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-lg font-bold transition-all shrink-0 cursor-pointer">&times;</button>
                        </div>

                        <!-- Top 4 Metric KPI Cards (Persis Data ASTAP) -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3 text-xs">
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">🏷️ Jenis PMDN 108</span>
                                <span class="text-white font-bold text-xs sm:text-sm leading-tight block truncate" :title="selectedAsset.kategori" x-text="selectedAsset.kategori"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">📅 Tahun Masuk</span>
                                <span class="text-cyan-300 font-extrabold font-mono text-xs sm:text-sm block" x-text="selectedAsset.tahun"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">⚡ Kondisi Fisik</span>
                                <div class="mt-0.5">
                                    <span class="px-2 py-0.5 rounded-lg text-xs font-bold border inline-block"
                                        :class="{
                                            'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': selectedAsset.kondisi === 'Baik',
                                            'bg-amber-500/20 text-amber-300 border-amber-500/30': (selectedAsset.kondisi === 'Kurang Baik' || selectedAsset.kondisi === 'Rusak Ringan'),
                                            'bg-rose-500/20 text-rose-300 border-rose-500/30': (selectedAsset.kondisi === 'Rusak Berat' || selectedAsset.kondisi === 'Rusak')
                                        }"
                                        x-text="selectedAsset.kondisi"></span>
                                </div>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">💰 Nilai Satuan / Buku</span>
                                <span class="text-emerald-400 font-extrabold font-mono text-xs sm:text-sm block truncate" x-text="selectedAsset.harga_fmt"></span>
                            </div>
                        </div>

                        <!-- 1. SPESIFIKASI TEKNIS RINCI (LANGKAH 3 BELANJA MODAL) -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 text-xs">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-cyan-400 flex items-center space-x-1.5">
                                    <span>🔍 Rincian Spesifikasi Teknis Belanja Modal</span>
                                </h4>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-400" x-text="selectedAsset.kategori_kib || 'KIB B'"></span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏷️ Merk / Brand</span>
                                    <span class="text-white font-bold" x-text="selectedAsset.merk || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">⚙️ Type / Model</span>
                                    <span class="text-white font-bold" x-text="selectedAsset.tipe || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🧪 Bahan / Material</span>
                                    <span class="text-white font-bold" x-text="selectedAsset.bahan || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🔢 No. Pabrik / Seri</span>
                                    <span class="text-cyan-300 font-mono font-bold" x-text="selectedAsset.no_pabrik || selectedAsset.no_seri || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🚗 No. Rangka / Mesin</span>
                                    <span class="text-slate-200 font-mono font-semibold" x-text="(selectedAsset.no_rangka || '-') + ' / ' + (selectedAsset.no_mesin || '-')"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📐 Ukuran / Kapasitas</span>
                                    <span class="text-slate-200 font-bold" x-text="selectedAsset.ukuran || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 col-span-full">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏥 Ruang / Unit Pemegang Terpasang</span>
                                    <span class="text-amber-300 font-bold text-sm" x-text="selectedAsset.ruang"></span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. ADMINISTRASI PENGADAAN & DOKUMEN SPK/SP2D (LANGKAH 4) -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 text-xs">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-emerald-400 flex items-center space-x-1.5">
                                    <span>📋 Dokumen Administrasi & Pengadaan SIPD</span>
                                </h4>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-400">Riwayat Pengadaan</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏢 Pihak Rekanan / Penyedia</span>
                                    <span class="text-teal-300 font-bold block truncate" x-text="selectedAsset.penyedia || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📜 Nomor & Tgl SPK</span>
                                    <span class="text-slate-200 font-mono font-medium block truncate" x-text="(selectedAsset.spk_nomor || '-') + ' (' + (selectedAsset.spk_tanggal || '-') + ')'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">💳 Nomor & Tgl SP2D</span>
                                    <span class="text-slate-200 font-mono font-medium block truncate" x-text="(selectedAsset.sp2d_nomor || '-') + ' (' + (selectedAsset.sp2d_tanggal || '-') + ')'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📑 Nomor BAST / Bukti Terima</span>
                                    <span class="text-slate-200 font-mono font-medium block truncate" x-text="selectedAsset.bast_nomor || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 sm:col-span-2">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📍 Alamat Pengiriman / Lokasi Barang</span>
                                    <span class="text-slate-200 font-medium block truncate" x-text="selectedAsset.alamat || 'RSUD dr. H. Koesnandi Bondowoso'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Modal Detail -->
                        <div class="pt-3 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div class="flex items-center space-x-2 w-full sm:w-auto">
                                <!-- Tombol Ubah Kondisi Barang -->
                                <button type="button" @click="showDetailModal = false; openEditKondisi(selectedAsset)"
                                    class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-lg shadow-amber-500/20 transition-all flex items-center justify-center space-x-1.5 cursor-pointer w-full sm:w-auto">
                                    <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Ubah Kondisi Barang</span>
                                </button>

                                <!-- Tombol Scan QR Publik -->
                                <a :href="'/scan/' + (selectedAsset.nibar || selectedAsset.kode)" target="_blank"
                                    class="px-4 py-2.5 rounded-xl bg-cyan-500/15 hover:bg-cyan-500/25 text-cyan-300 border border-cyan-500/30 font-bold text-xs transition-all flex items-center justify-center space-x-1.5 cursor-pointer w-full sm:w-auto">
                                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                    <span>Buka QR Scan &rarr;</span>
                                </a>
                            </div>

                            <button type="button" @click="showDetailModal = false"
                                class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all cursor-pointer w-full sm:w-auto text-center">
                                Tutup
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>

    <!-- Print Media Query Styling untuk Mencetak Dokumen KIR Kertas Putih Sempurna -->
    <style>
    @media print {
        body {
            background: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .no-print, aside, header, nav, footer {
            display: none !important;
        }
        #print-area-kir {
            display: block !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
            border: none !important;
        }
    }
    </style>
</x-layout>
