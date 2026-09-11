<!-- Grid 2 Kolom: Grafik Kondisi Barang & Grafik Distribusi Barang -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    
    <!-- 1. Grafik Keseluruhan Kondisi Barang -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between gap-2 mb-3">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>STATUS FISIK & KELAYAKAN</span>
                </div>
                <span class="text-xs font-mono font-bold text-slate-400">{{ $totalAsetRegisterCount ?? 0 }} Total Register</span>
            </div>
            <h3 class="text-base font-extrabold text-white">Grafik Kondisi Keseluruhan Barang</h3>
            <p class="text-xs text-slate-400 mt-0.5">Proporsi kondisi aset inventaris siap pakai vs memerlukan servis</p>
        </div>

        <!-- Chart Canvas Container -->
        <div class="relative w-full h-[240px] sm:h-[260px] my-4 flex items-center justify-center">
            <canvas id="kondisiChart"></canvas>
        </div>

        <!-- Summary Chips -->
        <div class="grid grid-cols-4 gap-2 pt-4 border-t border-slate-800/80 text-center">
            <div class="p-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                <div class="text-[10px] text-emerald-400 font-bold uppercase">Baik</div>
                <div class="text-sm font-black text-white font-mono">{{ $kondisiBaik ?? 0 }}</div>
            </div>
            <div class="p-2 rounded-xl bg-amber-500/10 border border-amber-500/20">
                <div class="text-[10px] text-amber-400 font-bold uppercase">Kurang Baik</div>
                <div class="text-sm font-black text-white font-mono">{{ $kondisiKurangBaik ?? 0 }}</div>
            </div>
            <div class="p-2 rounded-xl bg-orange-500/10 border border-orange-500/20">
                <div class="text-[10px] text-orange-400 font-bold uppercase">Rusak Ringan</div>
                <div class="text-sm font-black text-white font-mono">{{ $kondisiRusakRingan ?? 0 }}</div>
            </div>
            <div class="p-2 rounded-xl bg-rose-500/10 border border-rose-500/20">
                <div class="text-[10px] text-rose-400 font-bold uppercase">Rusak Berat</div>
                <div class="text-sm font-black text-white font-mono">{{ $kondisiRusakBerat ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- 2. Grafik Distribusi & Penempatan Aset -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between gap-2 mb-3">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 border border-teal-500/30 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                    <span>PERBANDINGAN DISTRIBUSI</span>
                </div>
                <span class="text-xs font-mono font-bold text-teal-400">{{ $persenTerdistribusi ?? 0 }}% Terdistribusi</span>
            </div>
            <h3 class="text-base font-extrabold text-white">Grafik Distribusi Barang ke Ruangan</h3>
            <p class="text-xs text-slate-400 mt-0.5">Perbandingan barang yang sudah didistribusikan vs belum didistribusikan</p>
        </div>

        <!-- Chart Canvas Container -->
        <div class="relative w-full h-[240px] sm:h-[260px] my-4 flex items-center justify-center">
            <canvas id="distribusiChart"></canvas>
        </div>

        <!-- Summary Chips -->
        <div class="grid grid-cols-2 gap-2 pt-4 border-t border-slate-800/80 text-center">
            <div class="p-2 rounded-xl bg-teal-500/10 border border-teal-500/20">
                <div class="text-[10px] text-teal-400 font-bold uppercase">Sudah Didistribusikan</div>
                <div class="text-sm font-black text-white font-mono">{{ $totalTerdistribusiUnit ?? 0 }} Unit</div>
            </div>
            <div class="p-2 rounded-xl bg-purple-500/10 border border-purple-500/20">
                <div class="text-[10px] text-purple-400 font-bold uppercase">Belum Didistribusikan (Gudang)</div>
                <div class="text-sm font-black text-white font-mono">{{ $belumTerdistribusi ?? 0 }} Unit</div>
            </div>
        </div>
    </div>

</div>
