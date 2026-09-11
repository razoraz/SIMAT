<!-- 3. GRAFIK NILAI ASET RUANGAN & GRAFIK KONDISI ASET RUANGAN -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- GRAFIK 1 (2 Kolom): GRAFIK NILAI ASET RUANGAN (VALUASI & KUANTITAS) -->
    <div class="lg:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl flex flex-col justify-between">
        <div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-xs font-bold mb-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>GRAFIK NILAI ASET RUANGAN</span>
                    </div>
                    <h3 class="text-base sm:text-lg font-extrabold text-white">Pertumbuhan Nilai & Kuantitas Aset {{ $unitNama }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Visualisasi akumulasi valuasi harga dan jumlah unit barang per tahun pengadaan di ruangan ini</p>
                </div>

                <!-- Mode Selector Toggle Pills -->
                <div class="inline-flex p-1 bg-slate-950 border border-slate-800 rounded-2xl shrink-0 text-xs font-semibold">
                    <button id="btnKumulatifSub" onclick="switchSubAdminChartMode('kumulatif')" class="px-3 py-1.5 rounded-xl bg-emerald-500 text-slate-950 font-bold transition-all shadow-md">
                        📈 Akumulasi
                    </button>
                    <button id="btnPerTahunSub" onclick="switchSubAdminChartMode('pertahun')" class="px-3 py-1.5 rounded-xl text-slate-400 hover:text-white transition-all">
                        📊 Per Tahun
                    </button>
                </div>
            </div>

            <!-- Canvas Chart Nilai Aset -->
            <div class="relative w-full h-[280px] sm:h-[310px] p-3 bg-slate-950/50 rounded-2xl border border-slate-800/80">
                <canvas id="roomAstapGrowthChart"></canvas>
            </div>
        </div>

        <!-- Footer Keterangan Chart Nilai -->
        <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span>Total Valuasi: <strong class="text-white">{{ $unitTotalNilai }}</strong></span>
            </div>
            <span class="text-[11px] text-slate-500">Unit: {{ $unitNama }}</span>
        </div>
    </div>

    <!-- GRAFIK 2 (1 Kolom): GRAFIK KONDISI ASET RUANGAN -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between gap-2 mb-3">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 border border-teal-500/30 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                    <span>KONDISI FISIK ASET</span>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400">{{ $unitTotalAset }} Unit Total</span>
            </div>
            <h3 class="text-base font-extrabold text-white">Kondisi Aset Ruangan</h3>
            <p class="text-xs text-slate-400 mt-0.5">Proporsi fisik kesiapan inventaris ruangan</p>

            <!-- Chart Canvas Container -->
            <div class="relative w-full h-[200px] sm:h-[220px] my-3 flex items-center justify-center">
                <canvas id="roomKondisiChart"></canvas>
            </div>
        </div>

        <!-- Summary Chips Kondisi -->
        <div class="grid grid-cols-4 gap-1.5 pt-3 border-t border-slate-800/80 text-center">
            <div class="p-1.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                <div class="text-[9px] text-emerald-400 font-bold uppercase">Baik</div>
                <div class="text-xs sm:text-sm font-black text-white font-mono">{{ $kondisiBaik ?? 0 }}</div>
            </div>
            <div class="p-1.5 rounded-xl bg-amber-500/10 border border-amber-500/20">
                <div class="text-[9px] text-amber-400 font-bold uppercase">K. Baik</div>
                <div class="text-xs sm:text-sm font-black text-white font-mono">{{ $kondisiKurangBaik ?? 0 }}</div>
            </div>
            <div class="p-1.5 rounded-xl bg-orange-500/10 border border-orange-500/20">
                <div class="text-[9px] text-orange-400 font-bold uppercase">R. Ringan</div>
                <div class="text-xs sm:text-sm font-black text-white font-mono">{{ $kondisiRusakRingan ?? 0 }}</div>
            </div>
            <div class="p-1.5 rounded-xl bg-rose-500/10 border border-rose-500/20">
                <div class="text-[9px] text-rose-400 font-bold uppercase">R. Berat</div>
                <div class="text-xs sm:text-sm font-black text-white font-mono">{{ $kondisiRusakBerat ?? 0 }}</div>
            </div>
        </div>
    </div>

</div>
