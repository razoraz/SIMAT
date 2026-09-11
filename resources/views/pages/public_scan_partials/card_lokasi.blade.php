<!-- ========================================================================= -->
<!-- LOKASI PENEMPATAN & UNIT PEMEGANG BARANG                                  -->
<!-- ========================================================================= -->
<div class="p-4 sm:p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-3">
    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
        <span class="text-xs font-extrabold text-white flex items-center space-x-2 tracking-wide uppercase">
            <span class="text-teal-400 text-sm">📍</span>
            <span>Lokasi Penempatan &amp; Unit Pemegang</span>
        </span>
        <span class="text-[10.5px] text-teal-400/90 font-bold px-2 py-0.5 rounded-lg bg-teal-500/10 border border-teal-500/20">
            Fisik Barang
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
        <!-- Ruangan / Paviliun -->
        <div class="p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800/80 space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">
                Ruang / Paviliun / Instalasi:
            </span>
            <div class="text-sm font-extrabold text-white">
                @if(!empty($ruang))
                    <span class="text-teal-300 flex items-center space-x-1.5">
                        <span>🏛️</span>
                        <span>{{ $ruang }}</span>
                    </span>
                @else
                    <span class="text-amber-400 flex items-center space-x-1.5">
                        <span>⚠️</span>
                        <span>Gudang Aset / Belum Ditempatkan</span>
                    </span>
                @endif
            </div>
            <p class="text-[10px] text-slate-400 leading-normal pt-0.5">
                Unit kerja yang bertanggung jawab memegang barang fisik ini.
            </p>
        </div>

        <!-- Gedung / Letak Kompleks -->
        <div class="p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800/80 space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">
                Alamat / Kompleks Rumah Sakit:
            </span>
            <div class="text-sm font-extrabold text-white flex items-center space-x-1.5">
                <span>🏥</span>
                <span class="text-slate-200 truncate">
                    {{ $astap->alamat_barang ?? ($astap->letak_lokasi ?? 'Kompleks RSUD Dr. H. Koesnandi') }}
                </span>
            </div>
            <p class="text-[10px] text-slate-400 leading-normal pt-0.5">
                Jl. Piere Tendean No. 1, Kabupaten Bondowoso, Jawa Timur.
            </p>
        </div>
    </div>
</div>
