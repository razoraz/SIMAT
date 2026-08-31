<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tanda Tangan Elektronik BSrE - RSUD Dr. H. Koesnandi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-purple-500 selection:text-slate-950">

    <!-- TOP HEADER NAVBAR -->
    <header class="bg-slate-900/90 border-b border-slate-800 sticky top-0 z-50 backdrop-blur-md">
        <div class="max-w-4xl mx-auto px-4 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-500 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-purple-500/25">
                    🛡️
                </div>
                <div>
                    <span class="text-[10px] font-bold tracking-widest text-purple-400 uppercase block">BALAI SERTIFIKASI ELEKTRONIK (BSrE) • BSSN</span>
                    <h1 class="text-xs sm:text-sm font-extrabold text-white tracking-tight">Verifikasi Keaslian Tanda Tangan Elektronik</h1>
                </div>
            </div>
            <div class="flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/30 text-[11px] font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Sertifikat Digital Aktif</span>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="max-w-4xl mx-auto px-4 py-6 sm:py-8 w-full flex-1 space-y-6">

        <!-- STATUS BANNER SAH & RESMI -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-950/60 via-slate-900 to-slate-900 border border-purple-500/40 p-6 sm:p-8 shadow-2xl">
            <div class="absolute -right-10 -top-10 w-44 h-44 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div class="space-y-3">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-xs font-bold">
                        <span>✅</span>
                        <span>DOKUMEN ASLI &amp; TANDA TANGAN ELEKTRONIK TERVERIFIKASI RESMI</span>
                    </div>
                    
                    <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                        {{ $judulDokumen ?? 'Berita Acara Serah Terima Barang' }}
                    </h2>
                    
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <span class="px-3 py-1 rounded-xl bg-slate-950 border border-purple-500/40 font-mono text-xs text-purple-300 font-bold tracking-wider">
                            Nomor Surat: {{ $nomorSurat ?? '000.2.3.2/224/430.10.7/2026' }}
                        </span>
                        <span class="px-2.5 py-1 rounded-xl bg-teal-500/10 text-teal-300 border border-teal-500/30 text-xs font-semibold">
                            Instansi: RSUD dr. H. Koesnandi Bondowoso
                        </span>
                    </div>
                </div>

                <div class="shrink-0 flex md:flex-col items-start md:items-end justify-between gap-2 border-t md:border-t-0 md:border-l border-slate-800 pt-4 md:pt-0 md:pl-6">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Integritas Dokumen:</span>
                    <span class="px-4 py-2 rounded-2xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-xs font-black shadow-lg shadow-emerald-500/10">
                        ● VALID &amp; BELUM PERNAH DIUBAH
                    </span>
                </div>
            </div>
        </div>

        <!-- GRID DATA PENANDATANGAN ELEKTRONIK -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            
            <!-- Profil Pejabat Penandatangan -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 space-y-4 shadow-xl">
                <div class="flex items-center space-x-2 border-b border-slate-800 pb-3">
                    <span class="p-2 rounded-xl bg-purple-500/20 text-purple-300 text-sm">👤</span>
                    <div>
                        <h3 class="text-sm font-extrabold text-white">Identitas Penandatangan</h3>
                        <p class="text-[11px] text-slate-400">Pemilik sertifikat elektronik BSrE</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Lengkap &amp; Gelar</span>
                        <p class="text-sm font-extrabold text-white mt-0.5">{{ $signerNama ?? 'BUDI HARTONO, S.Sos' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nomor Induk Pegawai (NIP)</span>
                        <p class="font-mono text-slate-200 text-xs font-bold mt-0.5">{{ $signerNip ?? '19760229 200801 1 010' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jabatan Kedinasan</span>
                        <p class="text-slate-300 mt-0.5 leading-relaxed">{{ $signerJabatan ?? 'Pengurus Barang Aset Pada RSUD dr. H. Koesnandi' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Instansi Pemerintah Daerah</span>
                        <p class="text-slate-300 mt-0.5">Pemerintah Kabupaten Bondowoso / RSUD dr. H. Koesnandi</p>
                    </div>
                </div>
            </div>

            <!-- Detail Sertifikat Digital BSrE & Timestamp -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 space-y-4 shadow-xl">
                <div class="flex items-center space-x-2 border-b border-slate-800 pb-3">
                    <span class="p-2 rounded-xl bg-teal-500/20 text-teal-300 text-sm">🔒</span>
                    <div>
                        <h3 class="text-sm font-extrabold text-white">Sertifikat Otoritas &amp; Keamanan</h3>
                        <p class="text-[11px] text-slate-400">Parameter Kriptografi Balai Sertifikasi Elektronik</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Otoritas Sertifikat (CA)</span>
                        <p class="text-slate-200 font-bold mt-0.5">Balai Sertifikasi Elektronik (BSrE) - BSSN RI</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Waktu Penandatanganan (Timestamp)</span>
                        <p class="font-mono text-emerald-400 font-bold mt-0.5">{{ $tglSigned ?? '30/06/2026 14:32 WIB' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kode Enkripsi Signature Hash</span>
                        <p class="font-mono text-[10px] text-purple-300 bg-slate-950 p-2 rounded-xl border border-slate-800 break-all mt-1">
                            {{ $qrHash ?? 'BSRE-KOESNANDI-BAST-TW2-2026-8942' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Dasar Hukum Keabsahan</span>
                        <p class="text-[11px] text-slate-400 mt-0.5">UU ITE No. 11 Tahun 2008 &amp; PP No. 71 Tahun 2019 (Sah &amp; Mengikat)</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- FOOTER INFO BOX -->
        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 text-center text-xs text-slate-400 space-y-1">
            <p>Dokumen ini ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan oleh Balai Sertifikasi Elektronik (BSrE), Badan Siber dan Sandi Negara (BSSN).</p>
            <p class="text-[11px] text-slate-500">Sistem Informasi Manajemen Aset Tetap RSUD dr. H. Koesnandi Kabupaten Bondowoso (SIMAT-RK)</p>
        </div>

    </main>

    <!-- FOOTER COPYRIGHT -->
    <footer class="border-t border-slate-800/80 bg-slate-950/80 py-4 text-center text-xs text-slate-400">
        <p>&copy; {{ date('Y') }} RSUD dr. H. Koesnandi Kabupaten Bondowoso • Layanan TTE BSrE Terverifikasi</p>
    </footer>

</body>
</html>
