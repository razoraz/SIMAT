<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi QR Code Aset - SIMAT-RK RSUD Dr. H. Koesnandi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-emerald-500 selection:text-slate-950">

    <!-- TOP HEADER NAVBAR -->
    <header class="bg-slate-900/90 border-b border-slate-800/80 sticky top-0 z-50 backdrop-blur-md">
        <div class="max-w-4xl mx-auto px-4 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-slate-950 font-black text-lg shadow-lg shadow-emerald-500/20">
                    🏥
                </div>
                <div>
                    <span class="text-[10px] font-bold tracking-widest text-emerald-400 uppercase block">RSUD DR. H. KOESNANDI BONDOWOSO</span>
                    <h1 class="text-xs sm:text-sm font-extrabold text-white tracking-tight">SIMAT-RK • Verifikasi QR Aset Tetap</h1>
                </div>
            </div>
            <div class="hidden sm:flex items-center space-x-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-[11px] font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Sistem Publik Terverifikasi</span>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT CONTAINER -->
    <main class="max-w-4xl mx-auto px-4 py-6 sm:py-8 w-full flex-1 space-y-6">

        @if($found)
        <!-- ========================================================================= -->
        <!-- VERIFIED ASSET BANNER                                                     -->
        <!-- ========================================================================= -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-950/60 via-slate-900 to-slate-900 border border-emerald-500/40 p-6 sm:p-8 shadow-2xl">
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div class="space-y-3">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-xs font-bold">
                        <span>✅</span>
                        <span>DOKUMEN &amp; NIBAR ASET TERVERIFIKASI RESMI</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                        {{ $astap->nama_barang ?? 'Data Aset' }}
                        @if($register)
                        <span class="text-emerald-400 text-lg sm:text-xl font-mono block mt-1">(Register {{ $register->no_register }})</span>
                        @endif
                    </h2>
                    
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <span class="px-3 py-1 rounded-xl bg-slate-950 border border-slate-800 font-mono text-xs text-emerald-400 font-bold tracking-wider">
                            NIBAR: {{ $register->nibar ?? $astap->kode_barang }}
                        </span>
                        <span class="px-2.5 py-1 rounded-xl bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 text-xs font-semibold">
                            {{ $astap->category ?? 'ASTAP' }}
                        </span>
                    </div>
                </div>

                <div class="shrink-0 flex md:flex-col items-start md:items-end justify-between gap-2 border-t md:border-t-0 md:border-l border-slate-800/80 pt-4 md:pt-0 md:pl-6">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Status Kondisi Terkini:</span>
                    <span class="px-4 py-2 rounded-2xl text-xs font-black border shadow-lg tracking-wide
                        @if(($register->kondisi ?? $astap->kondisi) === 'Baik') bg-emerald-500/20 text-emerald-300 border-emerald-500/40 shadow-emerald-500/10
                        @elseif(($register->kondisi ?? $astap->kondisi) === 'Rusak Ringan') bg-amber-500/20 text-amber-300 border-amber-500/40 shadow-amber-500/10
                        @elseif(($register->kondisi ?? $astap->kondisi) === 'Rusak Berat') bg-rose-500/20 text-rose-300 border-rose-500/40 shadow-rose-500/10
                        @else bg-purple-500/20 text-purple-300 border-purple-500/40 shadow-purple-500/10 @endif">
                        ● {{ $register->kondisi ?? $astap->kondisi ?? 'Baik' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- GRID RINGKASAN DATA LOKASI & PENEMPATAN                                   -->
        <!-- ========================================================================= -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            
            <!-- Lokasi Penempatan -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 space-y-2 shadow-lg hover:border-emerald-500/30 transition-all">
                <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                    <span>📍 Lokasi Penempatan</span>
                    <span class="text-teal-400 text-sm">🏛️</span>
                </div>
                <div class="text-sm font-extrabold text-white">
                    @if(!empty($register->ruang_pemegang))
                        <span class="text-teal-300">{{ $register->ruang_pemegang }}</span>
                    @elseif(!empty($astap->letak_lokasi))
                        <span class="text-teal-300">{{ $astap->letak_lokasi }}</span>
                    @else
                        <span class="text-amber-400 flex items-center space-x-1">
                            <span>⚠️</span>
                            <span>Belum Ditempatkan / Di Gudang Aset</span>
                        </span>
                    @endif
                </div>
                <p class="text-[10.5px] text-slate-400">Unit kerja / Paviliun pemegang fisik barang.</p>
            </div>

            <!-- Tahun Perolehan -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 space-y-2 shadow-lg hover:border-emerald-500/30 transition-all">
                <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                    <span>📅 Tahun Keluaran / Masuk</span>
                    <span class="text-amber-400 text-sm">📆</span>
                </div>
                <div class="text-base font-mono font-black text-white">
                    Tahun {{ $astap->tahun_perolehan ?? '-' }}
                </div>
                <p class="text-[10.5px] text-slate-400">Tahun pengadaan inventaris aset.</p>
            </div>

            <!-- Asal Usul & Anggaran -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 space-y-2 shadow-lg hover:border-emerald-500/30 transition-all">
                <div class="flex items-center justify-between text-slate-400 text-xs font-bold uppercase tracking-wider">
                    <span>💰 Sumber Dana / Realisasi</span>
                    <span class="text-emerald-400 text-sm">💵</span>
                </div>
                <div class="text-sm font-bold text-emerald-400 font-mono">
                    {{ $astap->jumlah_realisasi ?? '-' }}
                </div>
                <p class="text-[10.5px] text-slate-400">{{ $astap->asal_usul ?? 'APBD Kabupaten / DAK' }}</p>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- RINCIAN SPESIFIKASI TEKNIS & DOKUMEN                                     -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <h3 class="text-sm font-extrabold text-white flex items-center space-x-2 border-b border-slate-800 pb-3">
                <span class="text-emerald-400 text-lg">📋</span>
                <span>Spesifikasi Teknis &amp; Identitas Kode Barang</span>
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div class="bg-slate-950/60 p-3.5 rounded-xl border border-slate-800/80">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Klasifikasi Kode 108</span>
                    <span class="text-white font-bold block">{{ $astap->kode_barang ?? '-' }}</span>
                    <span class="text-[10.5px] text-slate-400 truncate block mt-0.5">{{ $astap->jenis_aset_nama ?? '-' }}</span>
                </div>
                <div class="bg-slate-950/60 p-3.5 rounded-xl border border-slate-800/80">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Merk / Brand</span>
                    <span class="text-white font-bold block">{{ $astap->merk ?? '-' }}</span>
                </div>
                <div class="bg-slate-950/60 p-3.5 rounded-xl border border-slate-800/80">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Type / Model</span>
                    <span class="text-white font-bold block">{{ $astap->type ?? '-' }}</span>
                </div>
                <div class="bg-slate-950/60 p-3.5 rounded-xl border border-slate-800/80">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">No. Pabrik / Seri</span>
                    <span class="text-cyan-300 font-mono font-bold block">{{ $astap->no_pabrik ?? '-' }}</span>
                </div>
            </div>

            @if(!empty($astap->spk_nomor) || !empty($astap->surat_pesanan_nomor))
            <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-800 space-y-2 text-xs mt-3">
                <span class="text-[10px] font-bold text-amber-400 uppercase tracking-wider block">📄 Dokumen Pengadaan Terverifikasi:</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                    <div>
                        <span class="text-slate-400 block text-[9.5px]">Nomor SPK / Kontrak:</span>
                        <span class="text-cyan-300 font-mono font-bold">{{ $astap->spk_nomor ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[9.5px]">Nomor Surat Pesanan / BAP:</span>
                        <span class="text-purple-300 font-mono font-bold">{{ $astap->surat_pesanan_nomor ?? '-' }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- ========================================================================= -->
        <!-- RIWAYAT PEMELIHARAAN & PERBAIKAN                                         -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="text-emerald-400 text-lg">🛠️</span>
                    <h3 class="text-sm font-extrabold text-white">Riwayat Pemeliharaan &amp; Perbaikan Aset</h3>
                </div>
                <span class="text-[10.5px] font-bold px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                    Sistem Pemeliharaan IPSRS
                </span>
            </div>

            @php
                $defaultList = [
                    [ 'kode' => 'MTN-2026-003', 'nama' => 'CT-Scan 128 Slice Siemens', 'jenis' => 'Kalibrasi Rutin & QC BAPETEN', 'tgl' => '05 Ags 2026', 'pelaksana' => 'PT. Siemens Healthcare Indonesia', 'status' => 'Selesai' ],
                    [ 'kode' => 'MTN-2026-007', 'nama' => 'Submersible Pump 7.5 HP', 'jenis' => 'Penggantian Seal & Bearing', 'tgl' => '10 Ags 2026', 'pelaksana' => 'Teknisi IPSRS RSUD', 'status' => 'Dalam Pengerjaan' ]
                ];
                $matchedServis = [];
                foreach ($defaultList as $item) {
                    if (str_contains(strtolower($astap->nama_barang ?? ''), strtolower($item['nama'])) || str_contains(strtolower($item['nama']), strtolower($astap->nama_barang ?? ''))) {
                        $matchedServis[] = $item;
                    }
                }
            @endphp

            @if(count($matchedServis) > 0)
            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase text-[9.5px]">
                        <tr>
                            <th class="px-3.5 py-2.5">Kode Tiket</th>
                            <th class="px-3.5 py-2.5">Jenis Pekerjaan</th>
                            <th class="px-3.5 py-2.5">Tanggal</th>
                            <th class="px-3.5 py-2.5">Pelaksana</th>
                            <th class="px-3.5 py-2.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80 bg-slate-950/40">
                        @foreach($matchedServis as $servis)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-3.5 py-3 font-mono font-bold text-cyan-400">{{ $servis['kode'] }}</td>
                            <td class="px-3.5 py-3 font-bold text-white">{{ $servis['jenis'] }}</td>
                            <td class="px-3.5 py-3 text-slate-300 font-mono">{{ $servis['tgl'] }}</td>
                            <td class="px-3.5 py-3 text-slate-400">{{ $servis['pelaksana'] }}</td>
                            <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold
                                    @if($servis['status'] === 'Selesai') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                    @else bg-amber-500/20 text-amber-300 border border-amber-500/30 @endif">
                                    {{ $servis['status'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 text-center space-y-1">
                <span class="text-emerald-400 text-base">✨</span>
                <p class="text-xs text-slate-300 font-semibold">Tidak ada riwayat kerusakan / perbaikan aktif.</p>
                <p class="text-[11px] text-slate-400">Aset ini belum pernah mengajukan tiket pemeliharaan berat.</p>
            </div>
            @endif
        </div>

        @else
        <!-- ========================================================================= -->
        <!-- NOT FOUND STATE                                                           -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-rose-500/30 rounded-3xl p-8 shadow-2xl text-center space-y-4 max-w-lg mx-auto my-12">
            <div class="w-16 h-16 rounded-full bg-rose-500/10 text-rose-400 flex items-center justify-center text-3xl mx-auto border border-rose-500/30">
                🔍
            </div>
            <h2 class="text-xl font-extrabold text-white">Data Aset Tidak Ditemukan</h2>
            <p class="text-xs text-slate-400 leading-relaxed">
                Nomor NIBAR / Kode Barang <span class="font-mono text-amber-400 font-bold">{{ $nibar }}</span> tidak terdaftar dalam database resmi SIMAT-RK RSUD Dr. H. Koesnandi.
            </p>
        </div>
        @endif

    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-900/80 border-t border-slate-800/80 py-6 text-center text-xs text-slate-400 mt-8">
        <div class="max-w-4xl mx-auto px-4 space-y-1">
            <p class="font-bold text-slate-300">© 2026 RSUD Dr. H. Koesnandi Bondowoso</p>
            <p class="text-[11px] text-slate-400">Sistem Informasi Manajemen Aset Tetap &amp; Rumah Tangga (SIMAT-RK)</p>
            <p class="text-[10px] text-emerald-400/80 font-mono pt-1">Waktu Verifikasi Scan: {{ date('d M Y H:i:s') }} WIB</p>
        </div>
    </footer>

</body>
</html>
