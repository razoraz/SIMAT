<!-- ========================================================================= -->
<!-- SPESIFIKASI FISIK & CIRI-CIRI BARANG                                     -->
<!-- ========================================================================= -->
<div class="p-4 sm:p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-3">
    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
        <span class="text-xs font-extrabold text-white flex items-center space-x-2 tracking-wide uppercase">
            <span class="text-emerald-400 text-sm">🔍</span>
            <span>Spesifikasi &amp; Ciri Fisik Barang</span>
        </span>
        <span class="text-[10.5px] text-emerald-400/90 font-mono font-bold px-2 py-0.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
            Detail Pengenal
        </span>
    </div>

    <!-- Grid Atribut Barang -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
        <!-- Merk / Brand -->
        <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">🏷️ Merk / Brand</span>
            <span class="text-white font-extrabold text-xs block truncate" title="{{ $merk ?: '-' }}">
                {{ $merk ?: '-' }}
            </span>
        </div>

        <!-- Tipe / Model -->
        <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">📐 Tipe / Model</span>
            <span class="text-cyan-300 font-extrabold text-xs block truncate" title="{{ $tipe ?: '-' }}">
                {{ $tipe ?: '-' }}
            </span>
        </div>

        <!-- Ukuran / Dimensi -->
        <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">📏 Ukuran / CC</span>
            <span class="text-white font-extrabold text-xs block truncate">
                {{ $ukuran ? $ukuran : '-' }}
            </span>
        </div>

        <!-- Bahan / Material -->
        <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">🪵 Bahan / Material</span>
            <span class="text-amber-300 font-extrabold text-xs block truncate">
                {{ $bahan ?: '-' }}
            </span>
        </div>

        <!-- Nomor Pabrik / Seri -->
        <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">🔢 No. Pabrik / Seri</span>
            <span class="text-emerald-300 font-mono font-bold text-xs block truncate" title="{{ $noPabrik ?: '-' }}">
                {{ $noPabrik ?: '-' }}
            </span>
        </div>

        <!-- Tahun Masuk / Perolehan -->
        <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">📅 Tahun Pengadaan</span>
            <span class="text-white font-mono font-black text-xs block">
                Tahun {{ $astap->tahun_perolehan ?? '-' }}
            </span>
        </div>

        <!-- Satuan Barang -->
        <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">📦 Satuan Fisik</span>
            <span class="text-purple-300 font-bold text-xs block">
                1 {{ $astap->satuan ?? 'Unit' }}
            </span>
        </div>

        <!-- Klasifikasi 108 -->
        <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">🏛️ Kode Klasifikasi</span>
            <span class="text-slate-300 font-mono text-[11px] font-bold block truncate" title="{{ $astap->kode_barang ?? '-' }}">
                {{ $astap->kode_barang ?? '-' }}
            </span>
        </div>
    </div>

    <!-- Ciri Khusus Tambahan (Jika Kendaraan / Buku / Lisensi ATB) -->
    @if(!empty($noPolisi) || !empty($noRangka) || !empty($noMesin) || !empty($pencipta) || !empty($spesifikasiKhusus))
    <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800/90 text-xs space-y-2 mt-2">
        <span class="text-[10px] font-extrabold text-emerald-400 uppercase tracking-wider block">
            Ciri Tambahan &amp; Keterangan Spesifik:
        </span>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
            @if(!empty($noPolisi))
            <div class="flex items-center justify-between border-b border-slate-800/60 pb-1">
                <span class="text-slate-400">Nomor Polisi (Plat):</span>
                <strong class="text-amber-300 font-mono">{{ $noPolisi }}</strong>
            </div>
            @endif
            @if(!empty($noRangka))
            <div class="flex items-center justify-between border-b border-slate-800/60 pb-1">
                <span class="text-slate-400">Nomor Rangka:</span>
                <strong class="text-slate-200 font-mono">{{ $noRangka }}</strong>
            </div>
            @endif
            @if(!empty($noMesin))
            <div class="flex items-center justify-between border-b border-slate-800/60 pb-1">
                <span class="text-slate-400">Nomor Mesin:</span>
                <strong class="text-slate-200 font-mono">{{ $noMesin }}</strong>
            </div>
            @endif
            @if(!empty($pencipta))
            <div class="flex items-center justify-between border-b border-slate-800/60 pb-1">
                <span class="text-slate-400">Pencipta / Vendor:</span>
                <strong class="text-cyan-300">{{ $pencipta }}</strong>
            </div>
            @endif
            @if(!empty($spesifikasiKhusus))
            <div class="sm:col-span-2 pt-1">
                <span class="text-slate-400 block text-[10px] mb-0.5">Spesifikasi Detail:</span>
                <p class="text-slate-300 text-xs leading-relaxed bg-slate-900/60 p-2.5 rounded-xl border border-slate-800">
                    {{ $spesifikasiKhusus }}
                </p>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
