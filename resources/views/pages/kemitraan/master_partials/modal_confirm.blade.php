<!-- ========================================================================= -->
<!-- MODAL KONFIRMASI HAPUS / BATALKAN ASET KEMITRAAN (AKUN 1.5.2)             -->
<!-- ========================================================================= -->
<div x-show="showDeleteModal" x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div @click.away="if (!isDeleting) showDeleteModal = false"
         class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl space-y-4 text-center relative">

        <!-- Icon Warning -->
        <div class="w-14 h-14 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-400 flex items-center justify-center text-2xl mx-auto shadow-lg shadow-rose-500/10">
            🗑️
        </div>

        <div>
            <h3 class="text-base font-extrabold text-white">
                Hapus Catatan Aset Kemitraan?
            </h3>
            <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
                Anda akan menghapus data kemitraan untuk barang:
            </p>
            <p class="text-xs font-bold text-cyan-300 mt-1 font-mono bg-slate-950/80 px-3 py-1.5 rounded-xl border border-slate-800 mx-auto inline-block max-w-full truncate"
               x-text="deleteItem.nama || 'Aset Kemitraan'">
            </p>
            <p class="text-[11px] text-rose-400/80 mt-2 bg-rose-500/10 p-2.5 rounded-xl border border-rose-500/20 text-left">
                ⚠️ <strong>Perhatian:</strong> Penghapusan ini akan menghapus catatan kemitraan dan nomor register barang terkait di SIMAT-RK.
            </p>
        </div>

        <div class="flex items-center justify-center gap-3 pt-2">
            <button type="button" @click="showDeleteModal = false" :disabled="isDeleting"
                class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all disabled:opacity-50 cursor-pointer">
                Batal
            </button>
            <button type="button" @click="executeDelete()" :disabled="isDeleting"
                class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-extrabold shadow-lg shadow-rose-600/30 transition-all flex items-center gap-1.5 disabled:opacity-50 cursor-pointer">
                <span x-show="!isDeleting">Ya, Hapus Data</span>
                <span x-show="isDeleting">Menghapus...</span>
            </button>
        </div>
    </div>
</div>
