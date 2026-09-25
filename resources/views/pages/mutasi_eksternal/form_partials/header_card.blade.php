<!-- Top Header & Back -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="flex items-center space-x-3">
        <a href="{{ $backUrl }}"
            class="p-2.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-purple-500/50 text-slate-400 hover:text-purple-400 transition-all shadow-lg shadow-black/20 group">
            <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-white tracking-tight" x-text="isEdit ? ('Ubah Data Mutasi Eksternal: ' + (formData.nama_barang || 'Aset')) : 'Pencatatan Mutasi Eksternal (Antar-OPD / Pelimpahan SKPD)'">
                    Pencatatan Mutasi Eksternal (Antar-OPD / Pelimpahan SKPD)
                </h1>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-500/15 text-purple-300 border border-purple-500/30" x-text="isEdit ? '✏️ MODE EDIT MUTASI EKSTERNAL' : '🔄 MUTASI EKSTERNAL / PELIMPAHAN SKPD'">
                    🔄 MUTASI EKSTERNAL / PELIMPAHAN SKPD
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-0.5" x-text="isEdit ? 'Perbarui informasi dokumen Berita Acara (BAMB/BAST), kode rekening 108, atau ruangan penempatan aset mutasi eksternal.' : 'Pendaftaran aset mutasi eksternal dari SKPD/Dinas luar berdasarkan Berita Acara resmi (BAP/BAMB) atau SK Kepala Daerah.'">
                Pendaftaran aset mutasi eksternal dari SKPD/Dinas luar berdasarkan Berita Acara resmi (BAP/BAMB) atau SK Kepala Daerah.
            </p>
        </div>
    </div>
</div>
