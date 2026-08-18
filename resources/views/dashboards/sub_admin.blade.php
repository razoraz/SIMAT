<x-layout title="Dashboard Sub Admin / User Master - SIMAT-RK">
    @section('page-title', 'Dashboard Sub Admin')
    @section('breadcrumb', 'Beranda / Sub Admin')

    <!-- Welcome Banner Card -->
    <div class="bg-gradient-to-r from-emerald-500/10 via-slate-900 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-8 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative z-10">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold mb-3">
                    <span>👤 SUB ADMIN / USER MASTER - AKSES PENGGUNA KHUSUS</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Selamat Datang, {{ Auth::user()->name }}!</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                    Anda berada di Panel Sub Admin (Perwakilan Unit/Paviliun). Wewenang Anda meliputi melihat Katalog Data ASTAP, memantau Aset Ruangan & Cetak KIR Unit Anda, melihat riwayat Pemeliharaan, serta pengajuan Distribusi Barang Aset.
                </p>
            </div>

            <!-- Sub Admin Create Action -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('distribusi.create') }}" class="px-3.5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Pengajuan Distribusi ASTAP</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Sub Admin Main Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- 1. Form Input Distribusi ASTAP (Create) -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-emerald-500/40 transition-all">
            <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 font-bold text-lg w-fit mb-3">🚚</div>
            <h4 class="text-sm font-bold text-white">Input Distribusi ASTAP</h4>
            <p class="text-xs text-slate-400 mt-1">Buat permohonan pengalokasian atau penerimaan barang aset baru untuk unit kerja Anda.</p>
            <a href="{{ route('distribusi.create') }}" class="mt-4 block text-center w-full py-2 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 font-bold text-xs border border-emerald-500/30 transition-all">
                + Input Distribusi Baru
            </a>
        </div>

        <!-- 2. Katalog Data ASTAP (Read Only) -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-cyan-500/40 transition-all">
            <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 font-bold text-lg w-fit mb-3">📁</div>
            <h4 class="text-sm font-bold text-white">Katalog Data ASTAP</h4>
            <p class="text-xs text-slate-400 mt-1">Lihat daftar lengkap katalog aset RSUD (Tanah, Peralatan Medis, Gedung, Sarana, dsb).</p>
            <a href="{{ route('astap.index') }}" class="mt-4 block text-center w-full py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all">
                Lihat Katalog ASTAP &rarr;
            </a>
        </div>

        <!-- 3. Unit & Paviliun (KIR Ruangan) -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-blue-500/40 transition-all">
            <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-400 font-bold text-lg w-fit mb-3">🏥</div>
            <h4 class="text-sm font-bold text-white">Unit & Lembar KIR</h4>
            <p class="text-xs text-slate-400 mt-1">Lihat daftar aset inventaris ruangan unit kerja Anda dan cetak Lembar Kartu Inventaris Ruangan (KIR).</p>
            <a href="{{ route('unit.index') }}" class="mt-4 block text-center w-full py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all">
                Buka Unit & KIR &rarr;
            </a>
        </div>
    </div>

    <!-- Data Distribusi Khusus User Ini (READ, UPDATE, DELETE) -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-extrabold text-white">Data Distribusi ASTAP Saya</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar alokasi barang aset khusus yang diajukan atau diterima oleh akun Anda (Read, Update, Delete)</p>
            </div>
            <button type="button" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md">
                + Tambah Distribusi Baru
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3 text-center">No. Pengajuan</th>
                        <th class="px-4 py-3 text-center">Nama Barang / ASTAP</th>
                        <th class="px-4 py-3 text-center">Tujuan Unit</th>
                        <th class="px-4 py-3 text-center">Tanggal</th>
                        <th class="px-4 py-3 text-center">Status Pengajuan</th>
                        <th class="px-4 py-3 text-center">Aksi User (Update/Delete)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-4 py-3.5 font-mono font-semibold text-emerald-400">DST-2026-089</td>
                        <td class="px-4 py-3.5 font-bold text-white">Printer Thermal Lab & Patient Monitor</td>
                        <td class="px-4 py-3.5">Unit IGD Utama</td>
                        <td class="px-4 py-3.5">14 Ags 2026</td>
                        <td class="px-4 py-3.5"><span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Disetujui Admin</span></td>
                        <td class="px-4 py-3.5 text-right space-x-2">
                            <button type="button" class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 font-semibold">Edit</button>
                            <button type="button" class="px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 font-semibold">Hapus</button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-4 py-3.5 font-mono font-semibold text-emerald-400">DST-2026-092</td>
                        <td class="px-4 py-3.5 font-bold text-white">Laptop Operasional SIMAT-RK IT Unit</td>
                        <td class="px-4 py-3.5">Ruang IT Server</td>
                        <td class="px-4 py-3.5">16 Ags 2026</td>
                        <td class="px-4 py-3.5"><span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-500/20 text-amber-400 border border-amber-500/30">Menunggu Verifikasi</span></td>
                        <td class="px-4 py-3.5 text-right space-x-2">
                            <button type="button" class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 font-semibold">Edit</button>
                            <button type="button" class="px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 font-semibold">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
