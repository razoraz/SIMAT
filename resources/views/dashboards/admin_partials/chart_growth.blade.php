<!-- Grafik Peningkatan Aset (Posisi Kedua) -->
<style>
    @media (max-width: 1023px) {
        .dash-growth-header {
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
        }
        .dash-growth-header p {
            margin-left: auto !important;
            margin-right: auto !important;
        }
        .dash-growth-pills {
            display: flex !important;
            flex-wrap: wrap !important;
            justify-content: center !important;
            max-width: 100% !important;
        }
        .dash-growth-footer {
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
            gap: 0.375rem !important;
        }
    }
    @media (min-width: 1024px) {
        .dash-growth-header {
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            text-align: left !important;
        }
        .dash-growth-header p {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        .dash-growth-pills {
            display: inline-flex !important;
            flex-wrap: nowrap !important;
            justify-content: flex-start !important;
        }
        .dash-growth-footer {
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            text-align: left !important;
        }
    }
</style>

<div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 mb-8">
    <div class="dash-growth-header flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-6">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 text-xs font-bold mb-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                <span>GRAFIK PERTUMBUHAN ASET TETAP</span>
            </div>
            <h3 class="text-lg font-extrabold text-white">Peningkatan Aset: Valuasi Harga & Kuantitas Volume</h3>
            <p class="text-xs text-slate-400 mt-0.5">Visualisasi tren pertumbuhan akumulasi nilai investasi dan jumlah unit aset RSUD Dr. H. Koesnandi</p>
        </div>

        <!-- Mode Selector Toggle Pills -->
        <div class="dash-growth-pills inline-flex p-1 bg-slate-950 border border-slate-800 rounded-2xl shrink-0 text-xs font-semibold gap-1">
            <button id="btnKumulatif" onclick="switchChartMode('kumulatif')" class="px-3.5 py-1.5 rounded-xl bg-cyan-500 text-slate-950 font-bold transition-all shadow-md">
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
    <div class="dash-growth-footer mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
        <div class="flex items-center space-x-2">
            <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
            <span>Data Valuasi & Kuantitas Terhubung Real-Time dengan Database</span>
        </div>
        <div class="text-[11px] text-slate-500 hidden sm:block">
            RSUD dr. H. Koesnandi Bondowoso
        </div>
    </div>
</div>
