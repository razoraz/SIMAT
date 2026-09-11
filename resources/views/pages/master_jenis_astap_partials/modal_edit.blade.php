<!-- ========================================================================= -->
<!-- MODAL EDIT KLASIFIKASI KODE 108 BMD                                       -->
<!-- ========================================================================= -->
<div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
    <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-5 relative">
        <button type="button" @click="showEditModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-sm font-bold transition-all">&times;</button>

        <div class="text-center pb-3 border-b border-slate-800">
            <h3 class="text-base font-extrabold text-white">Edit Klasifikasi Kode 108 BMD</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Perbarui Data Klasifikasi Kode 108</p>
        </div>

        <form :action="'/master-data/jenis-astap/' + editFormData.id" method="POST" class="space-y-4 text-xs" @submit="confirmEditForm($event)">
            @csrf
            @method('PUT')
            
            <div class="p-3.5 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 space-y-2">
                <span class="font-bold text-emerald-400 uppercase tracking-wider text-[10px] block">1. Jenis Utama</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">Kode Jenis</label>
                        <select name="jenis" x-model="editFormData.jenis" @change="onJenisChangeEdit()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-emerald-400 font-mono font-bold focus:border-emerald-500">
                            <option value="1.3.1">1.3.1 (Tanah)</option>
                            <option value="1.3.2">1.3.2 (Peralatan &amp; Mesin)</option>
                            <option value="1.3.3">1.3.3 (Gedung &amp; Bangunan)</option>
                            <option value="1.3.4">1.3.4 (Jalan, Irigasi &amp; Jaringan)</option>
                            <option value="1.3.5">1.3.5 (Aset Tetap Lainnya)</option>
                            <option value="1.3.6">1.3.6 (KDP)</option>
                            <option value="1.5.3">1.5.3 (Aset Tidak Berwujud)</option>
                            <option value="1.3.7">1.3.7 (Aset Dalam Renovasi)</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-slate-400 mb-1">Nama Jenis</label>
                        <input type="text" name="nama_jenis" x-model="editFormData.nama_jenis" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-emerald-500">
                    </div>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-amber-950/20 border border-amber-500/30 space-y-2">
                <span class="font-bold text-amber-400 uppercase tracking-wider text-[10px] block">2. Sub Rincian Objek</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">Kode Sub Rincian</label>
                        <input type="text" name="sub_rincian_objek" x-model="editFormData.sub_rincian_objek" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-400 font-mono font-bold focus:border-amber-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-slate-400 mb-1">Uraian Sub Rincian</label>
                        <input type="text" name="uraian_sub_rincian" x-model="editFormData.uraian_sub_rincian" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-amber-500">
                    </div>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-purple-950/20 border border-purple-500/30 space-y-2">
                <span class="font-bold text-purple-400 uppercase tracking-wider text-[10px] block">3. Sub - Sub Rincian Objek (Spesifik)</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">Kode Sub-Sub Rincian</label>
                        <input type="text" name="sub_sub_rincian_objek" x-model="editFormData.sub_sub_rincian_objek" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-purple-400 font-mono font-bold focus:border-purple-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-slate-400 mb-1">Uraian Sub-Sub Rincian (Nama Barang)</label>
                        <input type="text" name="uraian_sub_sub_rincian" x-model="editFormData.uraian_sub_sub_rincian" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-purple-500">
                    </div>
                </div>
            </div>

            <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 text-slate-300 font-bold text-xs">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-cyan-500/20 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
