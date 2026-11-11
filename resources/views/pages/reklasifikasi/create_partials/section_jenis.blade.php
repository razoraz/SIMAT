<!-- SEKSI 2: JENIS & KATEGORI REKLASIFIKASI -->
<div class="p-6 sm:p-7 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-5">
    <div class="flex items-center gap-3 border-b border-slate-800/80 pb-4">
        <span class="w-8 h-8 rounded-xl bg-violet-500/10 border border-violet-500/20 text-violet-400 flex items-center justify-center font-black text-xs font-mono">
            02
        </span>
        <div>
            <h2 class="text-sm sm:text-base font-bold text-white tracking-wide">
                Jenis &amp; Sifat Reklasifikasi (PMDN 108)
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Tentukan dasar perlakuan akuntansi mutasi buku atau penyesuaian status aset daerah.
            </p>
        </div>
    </div>

    <!-- Pilihan Kartu Radio Interaktif -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
        <!-- 1. Koreksi Rekening / Pindah KIB -->
        <label class="group relative flex flex-col justify-between p-4 rounded-2xl border transition-all duration-200 cursor-pointer select-none"
               :class="formData.jenis_reklas === 'KOREKSI_REKENING' ? 'bg-indigo-950/30 border-indigo-500 ring-1 ring-indigo-500/40 shadow-lg shadow-indigo-950/50' : 'bg-slate-950/70 border-slate-800 hover:border-slate-700 hover:bg-slate-950'">
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center text-sm">
                        🔄
                    </span>
                    <span class="text-xs font-bold text-white group-hover:text-indigo-300 transition-colors">
                        Koreksi Rekening / Pindah KIB
                    </span>
                </div>
                <input type="radio" x-model="formData.jenis_reklas" value="KOREKSI_REKENING" @change="onJenisReklasChange()" class="text-indigo-600 focus:ring-0 cursor-pointer mt-1">
            </div>
            <p class="text-[11px] text-slate-400 leading-relaxed">
                Koreksi salah kamar klasifikasi KIB (A s/d F, ATB, Kemitraan) atau penyesuaian hierarki kode rekening Permendagri 108.
            </p>
        </label>

        <!-- 2. KDP Selesai ➔ Definitif -->
        <label class="group relative flex flex-col justify-between p-4 rounded-2xl border transition-all duration-200 cursor-pointer select-none"
               :class="formData.jenis_reklas === 'KDP_TO_DEFINITIF' ? 'bg-emerald-950/30 border-emerald-500 ring-1 ring-emerald-500/40 shadow-lg shadow-emerald-950/50' : 'bg-slate-950/70 border-slate-800 hover:border-slate-700 hover:bg-slate-950'">
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm">
                        🏗️
                    </span>
                    <span class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors">
                        KDP Selesai ➔ Definitif
                    </span>
                </div>
                <input type="radio" x-model="formData.jenis_reklas" value="KDP_TO_DEFINITIF" @change="onJenisReklasChange()" class="text-emerald-600 focus:ring-0 cursor-pointer mt-1">
            </div>
            <p class="text-[11px] text-slate-400 leading-relaxed">
                Pekerjaan konstruksi fisik (KIB F) telah selesai 100% dan siap dimutasikan menjadi aset definitif (Gedung KIB C / Jaringan KIB D).
            </p>
        </label>

        <!-- 3. Ekstrakomptabel (≤ Rp 300rb) -->
        <label class="group relative flex flex-col justify-between p-4 rounded-2xl border transition-all duration-200 cursor-pointer select-none"
               :class="formData.jenis_reklas === 'EKSTRAKOMPTABEL' ? 'bg-rose-950/30 border-rose-500 ring-1 ring-rose-500/40 shadow-lg shadow-rose-950/50' : 'bg-slate-950/70 border-slate-800 hover:border-slate-700 hover:bg-slate-950'">
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center text-sm">
                        🔻
                    </span>
                    <span class="text-xs font-bold text-white group-hover:text-rose-300 transition-colors">
                        Ekstrakomptabel (≤ Rp 300rb)
                    </span>
                </div>
                <input type="radio" x-model="formData.jenis_reklas" value="EKSTRAKOMPTABEL" @change="onJenisReklasChange()" class="text-rose-600 focus:ring-0 cursor-pointer mt-1">
            </div>
            <p class="text-[11px] text-slate-400 leading-relaxed">
                Nilai perolehan satuan barang di bawah batas kapitalisasi (≤ Rp 300.000). Dikeluarkan dari neraca aset tetap ke ekstrakomptabel.
            </p>
        </label>

        <!-- 4. Kapitalisasi Intrakomptabel -->
        <label class="group relative flex flex-col justify-between p-4 rounded-2xl border transition-all duration-200 cursor-pointer select-none"
               :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM' ? 'bg-teal-950/30 border-teal-500 ring-1 ring-teal-500/40 shadow-lg shadow-teal-950/50' : 'bg-slate-950/70 border-slate-800 hover:border-slate-700 hover:bg-slate-950'">
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center text-sm">
                        🔺
                    </span>
                    <span class="text-xs font-bold text-white group-hover:text-teal-300 transition-colors">
                        Kapitalisasi Intrakomptabel
                    </span>
                </div>
                <input type="radio" x-model="formData.jenis_reklas" value="KAPITALISASI_INTRAKOM" @change="onJenisReklasChange()" class="text-teal-600 focus:ring-0 cursor-pointer mt-1">
            </div>
            <p class="text-[11px] text-slate-400 leading-relaxed">
                Menaikkan aset dari daftar ekstrakomptabel ke neraca Aset Tetap Intrakomptabel karena nilai perolehan di atas batas kapitalisasi (> Rp 300.000).
            </p>
        </label>

        <!-- 5. Bantuan Hibah Keluar -->
        <label class="group relative flex flex-col justify-between p-4 rounded-2xl border transition-all duration-200 cursor-pointer select-none"
               :class="formData.jenis_reklas === 'HIBAH_KELUAR' ? 'bg-purple-950/30 border-purple-500 ring-1 ring-purple-500/40 shadow-lg shadow-purple-950/50' : 'bg-slate-950/70 border-slate-800 hover:border-slate-700 hover:bg-slate-950'">
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center text-sm">
                        📤
                    </span>
                    <span class="text-xs font-bold text-white group-hover:text-purple-300 transition-colors">
                        Hibah Keluar (Dihibahkan)
                    </span>
                </div>
                <input type="radio" x-model="formData.jenis_reklas" value="HIBAH_KELUAR" @change="onJenisReklasChange()" class="text-purple-600 focus:ring-0 cursor-pointer mt-1">
            </div>
            <p class="text-[11px] text-slate-400 leading-relaxed">
                Penyerahan aset milik RSUD secara cuma-cuma kepada yayasan, pemerintah desa, atau pihak ketiga (Koreksi Baris 40 / Kolom 12).
            </p>
        </label>

        <!-- 6. Mutasi Eksternal (Antar-OPD) -->
        <label class="group relative flex flex-col justify-between p-4 rounded-2xl border transition-all duration-200 cursor-pointer select-none"
               :class="formData.jenis_reklas === 'MUTASI_EKSTERNAL' ? 'bg-cyan-950/30 border-cyan-500 ring-1 ring-cyan-500/40 shadow-lg shadow-cyan-950/50' : 'bg-slate-950/70 border-slate-800 hover:border-slate-700 hover:bg-slate-950'">
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center text-sm">
                        🏛️
                    </span>
                    <span class="text-xs font-bold text-white group-hover:text-cyan-300 transition-colors">
                        Mutasi Antar-OPD
                    </span>
                </div>
                <input type="radio" x-model="formData.jenis_reklas" value="MUTASI_EKSTERNAL" @change="onJenisReklasChange()" class="text-cyan-600 focus:ring-0 cursor-pointer mt-1">
            </div>
            <p class="text-[11px] text-slate-400 leading-relaxed">
                Pemindahan penguasaan aset ke SKPD/OPD lain di lingkungan Pemkab Bondowoso berdasarkan BAST resmi (Koreksi Baris 42 / Kolom 13).
            </p>
        </label>

        <!-- 7. Koreksi Nilai Realisasi / BPK / Manset (Full Span) -->
        <label class="group relative flex flex-col justify-between p-4 rounded-2xl border transition-all duration-200 cursor-pointer select-none sm:col-span-2 lg:col-span-3"
               :class="formData.jenis_reklas === 'KOREKSI_LAIN' ? 'bg-amber-950/30 border-amber-500 ring-1 ring-amber-500/40 shadow-lg shadow-amber-950/50' : 'bg-slate-950/70 border-slate-800 hover:border-slate-700 hover:bg-slate-950'">
            <div class="flex items-start justify-between gap-3 mb-1">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-sm">
                        ⚖️
                    </span>
                    <div>
                        <span class="text-xs font-bold text-white group-hover:text-amber-300 transition-colors">
                            Koreksi Nilai Realisasi / Audit BPK / Kapitalisasi Susulan
                        </span>
                        <span class="ml-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            Penyesuaian Saldo Buku
                        </span>
                    </div>
                </div>
                <input type="radio" x-model="formData.jenis_reklas" value="KOREKSI_LAIN" @change="onJenisReklasChange()" class="text-amber-500 focus:ring-0 cursor-pointer mt-1">
            </div>
            <p class="text-[11px] text-slate-400 leading-relaxed">
                Penyesuaian nilai nominal perolehan belanja modal aset bertambah (+) atau berkurang (-) akibat temuan pemeriksaan BPK-RI, koreksi audit internal, atau administrasi aset manset.
            </p>
        </label>
    </div>
</div>

