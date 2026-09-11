<!-- Admin CRUD Action Cards (Posisi Ketiga) -->
<div class="mb-8">
    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 flex items-center space-x-2">
        <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
        <span>Akses Fitur Admin Operasional</span>
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- 1. Manajemen Akun Sub Admin -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-amber-500/40 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 font-bold text-lg">👥</div>
                <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
            </div>
            <h4 class="text-sm font-bold text-white group-hover:text-amber-400 transition-colors">Manajemen Akun Sub Admin</h4>
            <p class="text-xs text-slate-400 mt-1">Register akun baru, reset password, &amp; hapus akun.</p>
            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                <a href="{{ route('master.users') }}" class="text-amber-400 hover:text-amber-300">+ Create Akun</a>
                <a href="{{ route('master.users') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
            </div>
        </div>

        <!-- 2. Mutasi Aset -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-cyan-500/40 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 font-bold text-lg">🔄</div>
                <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
            </div>
            <h4 class="text-sm font-bold text-white group-hover:text-cyan-400 transition-colors">Mutasi Aset</h4>
            <p class="text-xs text-slate-400 mt-1">Form perpindahan lokasi barang antar unit/ruangan RSUD &amp; riwayat penanggung jawab.</p>
            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                <a href="{{ route('mutasi.create') }}" class="text-cyan-400 hover:text-cyan-300">+ Mutasi Baru</a>
                <a href="{{ route('mutasi.index') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
            </div>
        </div>

        <!-- 3. Distribusi ASTAP -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-teal-500/40 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 font-bold text-lg">🚚</div>
                <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
            </div>
            <h4 class="text-sm font-bold text-white group-hover:text-teal-400 transition-colors">Distribusi ASTAP</h4>
            <p class="text-xs text-slate-400 mt-1">Form alokasi distribusi barang ke unit RSUD, perbarui lokasi barang, &amp; hapus data distribusi.</p>
            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                <a href="{{ route('distribusi.create') }}" class="text-teal-400 hover:text-teal-300">+ Distribusi Baru</a>
                <a href="{{ route('distribusi.index') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
            </div>
        </div>

        <!-- 4. Pembuatan Berita Acara (BAST) -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-purple-500/40 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 font-bold text-lg">📄</div>
                <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
            </div>
            <h4 class="text-sm font-bold text-white group-hover:text-purple-400 transition-colors">Berita Acara (BAST)</h4>
            <p class="text-xs text-slate-400 mt-1">Cetak &amp; buat dokumen BAST penyerahan aset, update penanggung jawab, &amp; hapus dokumen.</p>
            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                <a href="{{ route('bast.index') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
            </div>
        </div>

        <!-- 5. Unit & Paviliun -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-indigo-500/40 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-400 font-bold text-lg">🏥</div>
                <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
            </div>
            <h4 class="text-sm font-bold text-white group-hover:text-indigo-400 transition-colors">Unit &amp; Paviliun</h4>
            <p class="text-xs text-slate-400 mt-1">Manajemen gedung paviliun, unit ruangan kerja, instalasi RSUD, &amp; kepala penanggung jawab.</p>
            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                <a href="{{ route('unit.create') }}" class="text-indigo-400 hover:text-indigo-300">+ Tambah Unit</a>
                <a href="{{ route('unit.index') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
            </div>
        </div>

        <!-- 6. Data ASTAP (Aset Tetap) -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-emerald-500/40 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 font-bold text-lg">📦</div>
                <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
            </div>
            <h4 class="text-sm font-bold text-white group-hover:text-emerald-400 transition-colors">Data ASTAP (Aset Tetap)</h4>
            <p class="text-xs text-slate-400 mt-1">Buku induk inventaris ASTAP, nomor registrasi NIBAR, cetak barcode QR, &amp; rincian perolehan.</p>
            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                <a href="{{ route('astap.create') }}" class="text-emerald-400 hover:text-emerald-300">+ Tambah ASTAP</a>
                <a href="{{ route('astap.index') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
            </div>
        </div>

        <!-- 7. Master Jenis ASTAP -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-emerald-500/40 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 font-bold text-lg">🏷️</div>
                <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
            </div>
            <h4 class="text-sm font-bold text-white group-hover:text-emerald-400 transition-colors">Master Jenis ASTAP</h4>
            <p class="text-xs text-slate-400 mt-1">Kelola 8 kategori utama (Tanah, Bangunan, Peralatan/Mesin, Irigasi, Tetap Lainnya, Tidak Berwujud).</p>
            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                <a href="{{ route('master.jenis_astap') }}" class="text-emerald-400 hover:text-emerald-300">+ Tambah Jenis</a>
                <a href="{{ route('master.jenis_astap') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
            </div>
        </div>

        <!-- 8. Master Jenis Pengadaan -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-blue-500/40 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-400 font-bold text-lg">📋</div>
                <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
            </div>
            <h4 class="text-sm font-bold text-white group-hover:text-blue-400 transition-colors">Master Jenis Pengadaan</h4>
            <p class="text-xs text-slate-400 mt-1">Kelola sumber dana pengadaan (APBD Kabupaten, DAK Kesehatan, BLUD RSUD, Hibah Pemerintah).</p>
            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                <a href="{{ route('master.jenis_pengadaan') }}" class="text-blue-400 hover:text-blue-300">+ Tambah Sumber</a>
                <a href="{{ route('master.jenis_pengadaan') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
            </div>
        </div>

        <!-- 9. Rekening Belanja SIPD -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-violet-500/40 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2.5 rounded-xl bg-violet-500/10 text-violet-400 font-bold text-lg">💳</div>
                <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
            </div>
            <h4 class="text-sm font-bold text-white group-hover:text-violet-400 transition-colors">Rekening Belanja SIPD</h4>
            <p class="text-xs text-slate-400 mt-1">Kelola kode akun rekening belanja aset, klasifikasi belanja modal, &amp; sinkronisasi SIPD.</p>
            <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                <a href="{{ route('master.rekening_belanja') }}" class="text-violet-400 hover:text-violet-300">+ Tambah Akun</a>
                <a href="{{ route('master.rekening_belanja') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
            </div>
        </div>
    </div>
</div>
