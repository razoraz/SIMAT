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
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-base font-extrabold text-white">Data Distribusi ASTAP Saya</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar alokasi barang aset khusus yang diajukan atau diterima oleh akun unit kerja Anda</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('distribusi.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-300 border border-slate-700 transition-all flex items-center space-x-1.5">
                    <span>🔍 Riwayat Distribusi</span>
                </a>
                <a href="{{ route('distribusi.create') }}" class="px-3.5 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Distribusi Baru</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap w-12">No</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">No. Pengajuan</th>
                        <th class="px-4 py-3.5 text-left min-w-[220px]">Nama Barang / ASTAP</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Tujuan Unit</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Tanggal</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Status Pengajuan</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Aksi (Update/Delete)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap">1</td>
                        <td class="px-4 py-4 text-center font-mono font-semibold text-emerald-400 whitespace-nowrap">DST-2026-089</td>
                        <td class="px-4 py-4 font-bold text-white">Printer Thermal Lab & Patient Monitor</td>
                        <td class="px-4 py-4 text-center font-semibold text-slate-200 whitespace-nowrap">Unit IGD Utama</td>
                        <td class="px-4 py-4 text-center font-mono text-slate-300 whitespace-nowrap">14 Ags 2026</td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap tracking-wide leading-none bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 shadow-sm select-none">
                                Disetujui Admin
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center space-x-1 whitespace-nowrap">
                            <a href="{{ route('distribusi.index') }}" class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Edit</span>
                            </a>
                            <button type="button" class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus</span>
                            </button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap">2</td>
                        <td class="px-4 py-4 text-center font-mono font-semibold text-emerald-400 whitespace-nowrap">DST-2026-092</td>
                        <td class="px-4 py-4 font-bold text-white">Laptop Operasional SIMAT-RK IT Unit</td>
                        <td class="px-4 py-4 text-center font-semibold text-slate-200 whitespace-nowrap">Ruang IT Server</td>
                        <td class="px-4 py-4 text-center font-mono text-slate-300 whitespace-nowrap">16 Ags 2026</td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap tracking-wide leading-none bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm select-none">
                                Menunggu Verifikasi
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center space-x-1 whitespace-nowrap">
                            <a href="{{ route('distribusi.index') }}" class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Edit</span>
                            </a>
                            <button type="button" class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
