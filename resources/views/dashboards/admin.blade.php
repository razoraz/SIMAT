<x-layout title="Dashboard Admin Operasional - SIMAT-RK">
    @section('page-title', 'Dashboard Admin')
    @section('breadcrumb', 'Beranda / Admin Operasional')

    <!-- Welcome Banner Card -->
    <div class="bg-gradient-to-r from-cyan-500/10 via-slate-900 to-slate-900 border border-cyan-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-8 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-cyan-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative z-10">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-bold mb-3">
                    <span>🛡️ ADMIN OPERASIONAL - OTORISASI OPERASIONAL (CRUD)</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Selamat Datang, {{ Auth::user()->name }}!</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                    Anda berada di Panel Admin Operasional. Wewenang Anda meliputi pembuatan Pengadaan ASTAP, Distribusi ASTAP, Berita Acara (BAST), pembuatan akun User, serta Update/Delete Data ASTAP (status kondisi), Distribusi, User, dan BAST.
                </p>
            </div>
            
            <!-- Admin Fast Create Action -->
            <div class="flex flex-wrap gap-2">
                <button type="button" class="px-3.5 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Pengadaan ASTAP Baru</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Admin CRUD Action Cards -->
    <div class="mb-8">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 flex items-center space-x-2">
            <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
            <span>Akses Fitur Admin Operasional</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Pengadaan ASTAP (Create, Read, Update, Delete) -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-cyan-500/40 transition-all">
                <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 font-bold text-lg w-fit mb-3">🛒</div>
                <h4 class="text-sm font-bold text-white">Pengadaan ASTAP</h4>
                <p class="text-xs text-slate-400 mt-1">CREATE paket pengadaan baru & kelola anggaran pengadaan barang RSUD.</p>
                <button type="button" class="mt-4 w-full py-2 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 font-bold text-xs border border-cyan-500/30 transition-all">
                    + Input Pengadaan ASTAP
                </button>
            </div>

            <!-- 2. Distribusi ASTAP (Create, Read, Update, Delete) -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-teal-500/40 transition-all">
                <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 font-bold text-lg w-fit mb-3">🚚</div>
                <h4 class="text-sm font-bold text-white">Distribusi ASTAP</h4>
                <p class="text-xs text-slate-400 mt-1">CREATE & UPDATE alokasi penyerahan barang ke unit/ruang kerja RSUD.</p>
                <button type="button" class="mt-4 w-full py-2 rounded-xl bg-teal-500/10 hover:bg-teal-500/20 text-teal-300 font-bold text-xs border border-teal-500/30 transition-all">
                    + Form Distribusi ASTAP
                </button>
            </div>

            <!-- 3. Berita Acara BAST (Create, Read, Update, Delete) -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-purple-500/40 transition-all">
                <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 font-bold text-lg w-fit mb-3">📄</div>
                <h4 class="text-sm font-bold text-white">Berita Acara (BAST)</h4>
                <p class="text-xs text-slate-400 mt-1">CREATE dokumen BAST resmi, cetak dokumen, & update data penanggung jawab.</p>
                <button type="button" class="mt-4 w-full py-2 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 text-purple-300 font-bold text-xs border border-purple-500/30 transition-all">
                    + Pembuatan BAST Baru
                </button>
            </div>

            <!-- 4. Pembuatan Akun User (Create, Update, Delete User) -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-emerald-500/40 transition-all">
                <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 font-bold text-lg w-fit mb-3">👤</div>
                <h4 class="text-sm font-bold text-white">Pembuatan Akun User</h4>
                <p class="text-xs text-slate-400 mt-1">CREATE akun pengguna/pegawai, UPDATE data user, & DELETE akun jika diperlukan.</p>
                <button type="button" class="mt-4 w-full py-2 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 font-bold text-xs border border-emerald-500/30 transition-all">
                    + Tambah Akun User
                </button>
            </div>
        </div>
    </div>

    <!-- Data ASTAP Table (READ & UPDATE Status Kerusakan) -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-extrabold text-white">Data ASTAP Full Format (Kelola Status & Kondisi)</h3>
                <p class="text-xs text-slate-400 mt-0.5">Admin berwenang memperbarui status kondisi aset (Baik / Rusak Ringan / Rusak Berat)</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3 text-center">Kode Barang</th>
                        <th class="px-4 py-3 text-center">Nama ASTAP</th>
                        <th class="px-4 py-3 text-center">Jenis ASTAP</th>
                        <th class="px-4 py-3 text-center">Lokasi Unit</th>
                        <th class="px-4 py-3 text-center">Kondisi Saat Ini</th>
                        <th class="px-4 py-3 text-center">Aksi Admin (Update/Delete)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-4 py-3.5 font-mono font-semibold text-cyan-400">AST-MSN-045</td>
                        <td class="px-4 py-3.5 font-bold text-white">CT-Scan 128 Slice High Resolution</td>
                        <td class="px-4 py-3.5"><span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">Peralatan & Mesin</span></td>
                        <td class="px-4 py-3.5">Instalasi Radiologi</td>
                        <td class="px-4 py-3.5"><span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Baik</span></td>
                        <td class="px-4 py-3.5 text-right space-x-2">
                            <button type="button" class="px-2.5 py-1 rounded-lg bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 font-semibold">Ubah Kondisi</button>
                            <button type="button" class="px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 font-semibold">Hapus</button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-4 py-3.5 font-mono font-semibold text-cyan-400">AST-JRN-004</td>
                        <td class="px-4 py-3.5 font-bold text-white">Jaringan Pipa Oksigen Sentral Medis</td>
                        <td class="px-4 py-3.5"><span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30">Jalan & Jaringan</span></td>
                        <td class="px-4 py-3.5">Seluruh Ruang Rawat</td>
                        <td class="px-4 py-3.5"><span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-500/20 text-amber-400 border border-amber-500/30">Rusak Ringan</span></td>
                        <td class="px-4 py-3.5 text-right space-x-2">
                            <button type="button" class="px-2.5 py-1 rounded-lg bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 font-semibold">Ubah Kondisi</button>
                            <button type="button" class="px-2.5 py-1 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 font-semibold">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
