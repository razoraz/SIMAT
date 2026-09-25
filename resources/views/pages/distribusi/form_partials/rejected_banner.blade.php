            <!-- Banner Peringatan Status Ditolak -->
            <div x-show="formData.status === 'Ditolak'" 
                 x-transition
                 class="p-4 sm:p-5 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex items-start sm:items-center space-x-3.5 text-rose-300 shadow-lg">
                <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center justify-center text-lg shrink-0 font-bold">
                    🚫
                </div>
                <div class="space-y-0.5 min-w-0 flex-1">
                    <h4 class="font-extrabold text-white text-xs sm:text-sm flex items-center space-x-2">
                        <span>Status Distribusi: Ditolak</span>
                        <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 text-[10px] font-bold border border-rose-500/30">Terkunci</span>
                    </h4>
                    <p class="text-[11px] sm:text-xs text-rose-200/90 leading-relaxed">
                        Formulir distribusi ini berstatus <strong>Ditolak</strong> sehingga seluruh isian data terkunci dan tidak dapat diedit. Untuk dapat mengisi atau memperbarui data formulir, silakan ubah <strong>Status Distribusi</strong> ke status lain.
                    </p>
                </div>
            </div>
