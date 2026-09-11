            <!-- Tombol Aksi Batal & Simpan (Hanya di Bagian Bawah Form Sesuai Permintaan) -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-end space-x-3">
                <a href="{{ route('distribusi.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()" :disabled="isSaving" class="px-6 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-1.5 active:scale-95 disabled:opacity-50 cursor-pointer">
                    <svg x-show="!isSaving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="isSaving ? 'Menyimpan...' : (isSubAdmin ? 'Kirim Pengajuan Distribusi' : (isEdit ? 'Simpan Perubahan' : 'Simpan Distribusi Baru'))"></span>
                </button>
            </div>
