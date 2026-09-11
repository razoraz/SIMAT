<!-- ========================================================================= -->
<!-- MODAL UBAH REKENING BELANJA                                               -->
<!-- ========================================================================= -->
<div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
    <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative">
        <!-- Tombol Close Corner -->
        <button type="button" @click="showEditModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center text-sm font-bold transition-all">&times;</button>

        <!-- Header Center -->
        <div class="text-center pb-3 border-b border-slate-800 mb-4">
            <h3 class="text-base font-extrabold text-white">Ubah Rekening Belanja SIPD</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Perbarui Kode Rekening & Nama Belanja</p>
        </div>

        <form method="POST" :action="editActionUrl" class="space-y-4 text-xs" @submit="confirmEditForm($event)">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-slate-400 mb-1.5 font-semibold">Kelompok Belanja Modal</label>
                <select name="kelompok" x-model="editFormData.kelompok" @change="onKelompokChangeEdit()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-blue-400 font-bold focus:border-blue-500">
                    <option value="5.2.01">5.2.01 - Belanja Modal Tanah</option>
                    <option value="5.2.02">5.2.02 - Belanja Modal Peralatan dan Mesin</option>
                    <option value="5.2.03">5.2.03 - Belanja Modal Gedung dan Bangunan</option>
                    <option value="5.2.04">5.2.04 - Belanja Modal Jalan, Jaringan & Irigasi</option>
                    <option value="5.2.05">5.2.05 - Belanja Modal Aset Tetap Lainnya</option>
                    <option value="5.2.06">5.2.06 - Belanja Modal Aset Tidak Berwujud</option>
                </select>
            </div>

            <div>
                <label class="block text-slate-400 mb-1.5 font-semibold">Kode Rek (8)</label>
                <input type="text" name="kode_rek" x-model="editFormData.kode_rek" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-blue-400 font-mono font-bold focus:border-blue-500">
            </div>

            <div>
                <label class="block text-slate-400 mb-1.5 font-semibold">Nama Belanja Pengadaan (9)</label>
                <input type="text" name="nama_belanja" x-model="editFormData.nama_belanja" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white font-semibold focus:border-blue-500">
            </div>

            <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-slate-700 hover:border-rose-500/30 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>Batal</span>
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
