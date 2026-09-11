<!-- Grafik Peningkatan Aset (Posisi Kedua) -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 mb-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-xs font-bold mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>GRAFIK PERTUMBUHAN ASET TETAP</span>
            </div>
            <h3 class="text-lg font-extrabold text-white">Peningkatan Aset: Valuasi Harga & Kuantitas Volume</h3>
            <p class="text-xs text-slate-400 mt-0.5">Visualisasi tren pertumbuhan akumulasi nilai investasi dan jumlah unit aset RSUD Dr. H. Koesnandi</p>
        </div>

        <!-- Mode Selector Toggle Pills -->
        <div class="inline-flex p-1 bg-slate-950 border border-slate-800 rounded-2xl shrink-0 text-xs font-semibold">
            <button id="btnKumulatif" onclick="switchChartMode('kumulatif')" class="px-3.5 py-1.5 rounded-xl bg-emerald-500 text-slate-950 font-bold transition-all shadow-md">
                📈 Akumulasi Peningkatan
            </button>
            <button id="btnPerTahun" onclick="switchChartMode('pertahun')" class="px-3.5 py-1.5 rounded-xl text-slate-400 hover:text-white transition-all">
                📊 Per Tahun Pengadaan
            </button>
        </div>
    </div>

    <!-- Canvas Chart -->
    <div class="relative w-full h-[320px] sm:h-[360px] p-3 bg-slate-950/50 rounded-2xl border border-slate-800/80">
        <canvas id="astapGrowthChart"></canvas>
    </div>

    <!-- Indicator Keterangan Di Bagian Bawah Chart -->
    <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
        <div class="flex items-center space-x-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>Data Valuasi & Kuantitas Terhubung Real-Time dengan Database</span>
        </div>
        <div class="text-[11px] text-slate-500 hidden sm:block">
            RSUD dr. H. Koesnandi Bondowoso
        </div>
    </div>
</div>
