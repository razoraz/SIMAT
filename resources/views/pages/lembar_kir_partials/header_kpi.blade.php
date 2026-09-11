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
