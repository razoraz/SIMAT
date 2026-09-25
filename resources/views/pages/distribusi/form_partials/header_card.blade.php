        <!-- Top Navigation Bar -->
        <div class="flex items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <a href="{{ route('distribusi.index') }}" 
                   class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] font-bold mb-1">
                        <span x-text="isSubAdmin ? '📋 PENGAJUAN PERMINTAAN ASTAP RUANGAN' : (isEdit ? '✏️ UBAH DISTRIBUSI BARANG' : '🚚 INPUT DISTRIBUSI MULTI-BARANG')"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight"
                        x-text="isSubAdmin ? 'Form Input Pengajuan Baru' : (isEdit ? 'Form Ubah Distribusi ASTAP' : 'Form Distribusi & Penyerahan ASTAP')"></h1>
                    <p class="text-xs text-slate-400 mt-0.5"
                       x-text="isSubAdmin ? 'Pengajuan kebutuhan barang untuk unit ruangan Anda — penentuan NIBAR & verifikasi fisik diproses oleh Admin.' : 'Dapat memasukkan beberapa barang berbeda sekaligus dalam satu transaksi penyerahan ke ruangan.'"></p>
                </div>
            </div>
        </div>

