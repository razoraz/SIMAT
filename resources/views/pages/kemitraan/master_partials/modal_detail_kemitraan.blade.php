<!-- ========================================================================= -->
<!-- MODAL DETAIL & STATUS KONSESI ASET KEMITRAAN                              -->
<!-- ========================================================================= -->
<div x-show="showDetailModal" x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div @click.away="showDetailModal = false"
         class="w-full max-w-2xl bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-7 shadow-2xl space-y-5 text-left relative max-h-[90vh] overflow-y-auto custom-scrollbar">

        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-lg font-bold shrink-0">
                    🤝
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-extrabold text-white">
                        Rincian Aset Kemitraan (Akun 1.5.2)
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5" x-text="activeDetail.kemitraan?.nomor_pks || '-'"></p>
                </div>
            </div>

            <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-white p-2 rounded-xl hover:bg-slate-800 transition-colors cursor-pointer">
                ✕
            </button>
        </div>

        <!-- 1. Grid Informasi Legalitas PKS -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
            <span class="text-[11px] font-bold text-cyan-400 uppercase tracking-wider block">
                1. Dokumen Perjanjian Kerja Sama (PKS)
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div>
                    <span class="text-slate-500 block text-[10px]">Mitra Pihak Ketiga:</span>
                    <span class="font-bold text-white block mt-0.5" x-text="activeDetail.kemitraan?.mitra_nama || '-'"></span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px]">Skema Kemitraan:</span>
                    <span class="font-bold text-cyan-300 block mt-0.5" x-text="activeDetail.kemitraan?.skema_kemitraan || 'KSO'"></span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px]">Tanggal Penandatanganan PKS:</span>
                    <span class="font-semibold text-slate-300 block mt-0.5" x-text="formatTanggal(activeDetail.kemitraan?.tanggal_pks)"></span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px]">Masa Konsesi / Kerjasama:</span>
                    <span class="font-semibold text-slate-300 block mt-0.5" 
                          x-text="(formatTanggal(activeDetail.kemitraan?.tanggal_mulai) || '?') + ' s.d. ' + (formatTanggal(activeDetail.kemitraan?.tanggal_selesai) || '?')"></span>
                </div>
            </div>
        </div>

        <!-- 2. Grid Rincian Barang & Nilai Aset -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
            <span class="text-[11px] font-bold text-cyan-400 uppercase tracking-wider block">
                2. Identitas Barang &amp; Taksiran Nilai
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="sm:col-span-2">
                    <span class="text-slate-500 block text-[10px]">Nama Barang:</span>
                    <span class="font-bold text-white text-sm block mt-0.5" x-text="activeDetail.astap?.nama_barang || '-'"></span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px]">Klasifikasi Kode 108:</span>
                    <span class="font-mono text-cyan-300 font-bold block mt-0.5" x-text="activeDetail.astap?.kode_108 || '1.5.2.x'"></span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px]">Total Taksiran Nilai Wajar:</span>
                    <span class="font-mono text-emerald-400 font-black text-sm block mt-0.5" x-text="'Rp ' + formatRupiah(activeDetail.kemitraan?.nilai_aset)"></span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px]">Volume &amp; Satuan:</span>
                    <span class="font-bold text-white block mt-0.5" x-text="(activeDetail.kemitraan?.jumlah_volume || 1) + ' ' + (activeDetail.kemitraan?.satuan || 'Unit')"></span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px]">Tahun &amp; Triwulan:</span>
                    <span class="font-semibold text-slate-300 block mt-0.5" x-text="(activeDetail.kemitraan?.tahun || '-') + ' • ' + (activeDetail.kemitraan?.triwulan || '-')"></span>
                </div>
            </div>
        </div>

        <!-- 3. Spesifikasi Fisik & Ruangan Penempatan -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
            <span class="text-[11px] font-bold text-cyan-400 uppercase tracking-wider block">
                3. Spesifikasi Fisik &amp; Penempatan (KIR)
            </span>
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <span class="text-slate-500 block text-[10px]">Merk / Model:</span>
                    <span class="font-semibold text-white block mt-0.5" x-text="(activeDetail.astap?.spesifikasi_json?.merk || '-') + ' / ' + (activeDetail.astap?.spesifikasi_json?.type || '-')"></span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px]">Nomor Seri / Pabrik:</span>
                    <span class="font-mono text-slate-300 block mt-0.5" x-text="activeDetail.astap?.spesifikasi_json?.no_pabrik || '-'"></span>
                </div>
                <div class="col-span-2">
                    <span class="text-slate-500 block text-[10px]">Ruangan Penempatan RSUD:</span>
                    <span class="font-bold text-emerald-300 block mt-0.5" x-text="activeDetail.register?.ruang_pemegang || activeDetail.astap?.alamat_barang || '-'"></span>
                </div>
            </div>
        </div>

        <!-- 4. Form Pembaruan Status Konsesi -->
        <div class="p-4 rounded-2xl bg-slate-950 border border-cyan-500/30 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-cyan-300 uppercase tracking-wider">
                    ⚙️ Perbarui Status Konsesi Kerja Sama
                </span>
                <span class="text-[10px] text-slate-400">Pilih status terkini</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 mb-1">Status Konsesi Saat Ini:</label>
                    <select x-model="statusForm.status_konsesi"
                        class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        <option value="Aktif">🟢 Aktif (Kerjasama Berjalan)</option>
                        <option value="Akan Berakhir">🟡 Akan Berakhir (Sisa &lt; 30 Hari)</option>
                        <option value="Selesai / Reklasifikasi">🔵 Selesai (Siap Reklasifikasi ke Aset Tetap)</option>
                        <option value="Dihentikan">🔴 Dihentikan / Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 mb-1">Catatan Tambahan Status:</label>
                    <input type="text" x-model="statusForm.keterangan"
                        placeholder="Contoh: Masa PKS telah berakhir dan alkes diserahkan ke RSUD..."
                        class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('master.reklasifikasi') }}"
                    class="text-[11px] text-cyan-400 hover:text-cyan-300 transition-colors flex items-center gap-1">
                    <span>Lihat Matriks Reklasifikasi Neraca &rarr;</span>
                </a>

                <button type="button" @click="saveStatusUpdate()" :disabled="isUpdatingStatus"
                    class="px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition-all flex items-center gap-1.5 shadow-md shadow-cyan-500/20 disabled:opacity-50 cursor-pointer">
                    <span x-show="!isUpdatingStatus">Simpan Status</span>
                    <span x-show="isUpdatingStatus">Menyimpan...</span>
                </button>
            </div>
        </div>

    </div>
</div>
