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
                <a href="{{ route('pengadaan.create') }}" class="px-3.5 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Pengadaan ASTAP Baru</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Metric Summary Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Total Aset (ASTAP)</span>
                <div class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg></div>
            </div>
            <p class="text-2xl font-black text-white">1,428 <span class="text-xs font-normal text-emerald-400">Unit</span></p>
            <p class="text-[11px] text-slate-400 mt-1">8 Kategori ASTAP Terdaftar</p>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Pengadaan ASTAP</span>
                <div class="p-2 rounded-xl bg-cyan-500/10 text-cyan-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg></div>
            </div>
            <p class="text-2xl font-black text-white">84 <span class="text-xs font-normal text-cyan-400">Paket</span></p>
            <p class="text-[11px] text-slate-400 mt-1">APBD, BLUD, DAK, Hibah</p>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Distribusi Barang</span>
                <div class="p-2 rounded-xl bg-teal-500/10 text-teal-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg></div>
            </div>
            <p class="text-2xl font-black text-white">312 <span class="text-xs font-normal text-teal-400">Terdistribusi</span></p>
            <p class="text-[11px] text-slate-400 mt-1">Ke Unit & Ruang RSUD</p>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Berita Acara (BAST)</span>
                <div class="p-2 rounded-xl bg-purple-500/10 text-purple-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
            </div>
            <p class="text-2xl font-black text-white">156 <span class="text-xs font-normal text-purple-400">Dokumen</span></p>
            <p class="text-[11px] text-slate-400 mt-1">Dokumen BAST Resmi</p>
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
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-cyan-500/40 transition-all flex flex-col justify-between">
                <div>
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 font-bold text-lg w-fit mb-3">🛒</div>
                    <h4 class="text-sm font-bold text-white">Pengadaan ASTAP</h4>
                    <p class="text-xs text-slate-400 mt-1">CREATE paket pengadaan baru & kelola anggaran pengadaan barang RSUD.</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('pengadaan.create') }}" class="text-cyan-400 hover:text-cyan-300">+ Input Pengadaan</a>
                    <a href="{{ route('pengadaan.index') }}" class="text-slate-400 hover:text-white">Kelola &rarr;</a>
                </div>
            </div>

            <!-- 2. Distribusi ASTAP (Create, Read, Update, Delete) -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-teal-500/40 transition-all flex flex-col justify-between">
                <div>
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 font-bold text-lg w-fit mb-3">🚚</div>
                    <h4 class="text-sm font-bold text-white">Distribusi ASTAP</h4>
                    <p class="text-xs text-slate-400 mt-1">CREATE & UPDATE alokasi penyerahan barang ke unit/ruang kerja RSUD.</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('distribusi.create') }}" class="text-teal-400 hover:text-teal-300">+ Distribusi Baru</a>
                    <a href="{{ route('distribusi.index') }}" class="text-slate-400 hover:text-white">Kelola &rarr;</a>
                </div>
            </div>

            <!-- 3. Berita Acara BAST (Create, Read, Update, Delete) -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-purple-500/40 transition-all flex flex-col justify-between">
                <div>
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 font-bold text-lg w-fit mb-3">📄</div>
                    <h4 class="text-sm font-bold text-white">Berita Acara (BAST)</h4>
                    <p class="text-xs text-slate-400 mt-1">CREATE dokumen BAST resmi, cetak dokumen, & update data penanggung jawab.</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('bast.create') }}" class="text-purple-400 hover:text-purple-300">+ Buat BAST</a>
                    <a href="{{ route('bast.index') }}" class="text-slate-400 hover:text-white">Kelola &rarr;</a>
                </div>
            </div>

            <!-- 4. Pembuatan Akun User (Create, Update, Delete User) -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-emerald-500/40 transition-all flex flex-col justify-between">
                <div>
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 font-bold text-lg w-fit mb-3">👤</div>
                    <h4 class="text-sm font-bold text-white">Manajemen Akun User</h4>
                    <p class="text-xs text-slate-400 mt-1">CREATE akun sub admin unit/ruangan, UPDATE profil user, & kelola akun.</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('master.users') }}" class="text-emerald-400 hover:text-emerald-300">Tambah Akun</a>
                    <a href="{{ route('master.users') }}" class="text-slate-400 hover:text-white">Kelola &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Data ASTAP Table (READ & UPDATE Status Kerusakan) -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-base font-extrabold text-white">Data ASTAP Full Format (Kelola Status & Kondisi)</h3>
                <p class="text-xs text-slate-400 mt-0.5">Admin berwenang memperbarui status kondisi aset (Baik / Rusak Ringan / Rusak Berat)</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('astap.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-300 border border-slate-700 transition-all flex items-center space-x-1.5">
                    <span>🔍 Lihat Semua ASTAP</span>
                </a>
                <a href="{{ route('astap.create') }}" class="px-3.5 py-1.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah ASTAP Baru</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap w-12">No</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Kode Barang</th>
                        <th class="px-4 py-3.5 text-left min-w-[220px]">Nama ASTAP</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Jenis ASTAP</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Tahun</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Lokasi Unit</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Kondisi Saat Ini</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Aksi Admin (CRUD)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap">1</td>
                        <td class="px-4 py-4 text-center font-mono font-semibold text-cyan-400 whitespace-nowrap">AST-MSN-045</td>
                        <td class="px-4 py-4 font-bold text-white">CT-Scan 128 Slice High Resolution</td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap leading-none bg-purple-500/15 text-purple-300 border border-purple-500/30 shadow-sm select-none">
                                Peralatan & Mesin
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center font-mono text-slate-300 whitespace-nowrap">2022</td>
                        <td class="px-4 py-4 text-center font-semibold text-slate-200 whitespace-nowrap">Instalasi Radiologi</td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap tracking-wide leading-none bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 shadow-sm select-none">
                                Baik
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center space-x-1 whitespace-nowrap">
                            <a href="{{ route('astap.index') }}" class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Ubah</span>
                            </a>
                            <button type="button" class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus</span>
                            </button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap">2</td>
                        <td class="px-4 py-4 text-center font-mono font-semibold text-cyan-400 whitespace-nowrap">AST-JRN-004</td>
                        <td class="px-4 py-4 font-bold text-white">Jaringan Pipa Oksigen Sentral Medis</td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap leading-none bg-teal-500/15 text-teal-300 border border-teal-500/30 shadow-sm select-none">
                                Jalan, Irigasi & Jaringan
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center font-mono text-slate-300 whitespace-nowrap">2020</td>
                        <td class="px-4 py-4 text-center font-semibold text-slate-200 whitespace-nowrap">Seluruh Ruang Rawat</td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap tracking-wide leading-none bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm select-none">
                                Rusak Ringan
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center space-x-1 whitespace-nowrap">
                            <a href="{{ route('astap.index') }}" class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Ubah</span>
                            </a>
                            <button type="button" class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus</span>
                            </button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap">3</td>
                        <td class="px-4 py-4 text-center font-mono font-semibold text-cyan-400 whitespace-nowrap">AST-MED-018</td>
                        <td class="px-4 py-4 font-bold text-white">Bed Patient Electric 3 Crank Acare</td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap leading-none bg-purple-500/15 text-purple-300 border border-purple-500/30 shadow-sm select-none">
                                Peralatan & Mesin
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center font-mono text-slate-300 whitespace-nowrap">2023</td>
                        <td class="px-4 py-4 text-center font-semibold text-slate-200 whitespace-nowrap">Paviliun Graha Amukti</td>
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap tracking-wide leading-none bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 shadow-sm select-none">
                                Baik
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center space-x-1 whitespace-nowrap">
                            <a href="{{ route('astap.index') }}" class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Ubah</span>
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
