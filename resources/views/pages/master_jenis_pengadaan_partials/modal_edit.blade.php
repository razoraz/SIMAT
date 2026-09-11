<!-- ========================================================================= -->
<!-- MODAL UBAH JENIS PENGADAAN SIPD                                           -->
<!-- ========================================================================= -->
<div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
    <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl relative">
        <!-- Tombol Close Corner -->
        <button type="button" @click="showEditModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center text-sm font-bold transition-all">&times;</button>

        <!-- Header Center -->
        <div class="text-center pb-3 border-b border-slate-800 mb-4">
            <h3 class="text-base font-extrabold text-white">Ubah Data Pengadaan SIPD</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Perbarui struktur 3 tingkatan (Program, Kegiatan, Sub Kegiatan)</p>
        </div>

        <form method="POST" :action="editActionUrl" class="space-y-3.5 text-xs" @submit="confirmEditForm($event)">
            @csrf
            @method('PUT')

            <!-- Blok 1: Program -->
            <div class="p-3.5 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 space-y-2">
                <span class="font-bold text-emerald-400 uppercase tracking-wider text-[10px] block">1. Program Pengadaan SIPD</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">Kode Program</label>
                        <input type="text" inputmode="numeric" @input="editFormData.program_kode = $event.target.value = $event.target.value.replace(/[^0-9.]/g, '')" name="program_kode" x-model="editFormData.program_kode" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-emerald-400 font-mono font-bold focus:border-emerald-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-slate-400 mb-1">Nama Program</label>
                        <input type="text" name="program_nama" x-model="editFormData.program_nama" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Blok 2: Kegiatan -->
            <div class="p-3.5 rounded-2xl bg-amber-950/20 border border-amber-500/30 space-y-2">
                <span class="font-bold text-amber-400 uppercase tracking-wider text-[10px] block">2. Kegiatan Pengadaan SIPD</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">Kode Kegiatan</label>
                        <input type="text" inputmode="numeric" @input="editFormData.kegiatan_kode = $event.target.value = $event.target.value.replace(/[^0-9.]/g, '')" name="kegiatan_kode" x-model="editFormData.kegiatan_kode" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-400 font-mono font-bold focus:border-amber-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-slate-400 mb-1">Nama Kegiatan Pengadaan</label>
                        <input type="text" name="kegiatan_nama" x-model="editFormData.kegiatan_nama" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-amber-500">
                    </div>
                </div>
            </div>

            <!-- Blok 3: Sub Kegiatan -->
            <div class="p-3.5 rounded-2xl bg-purple-950/20 border border-purple-500/30 space-y-2">
                <span class="font-bold text-purple-400 uppercase tracking-wider text-[10px] block">3. Sub Kegiatan Pengadaan SIPD</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">Kode Sub Kegiatan</label>
                        <input type="text" inputmode="numeric" @input="editFormData.sub_kegiatan_kode = $event.target.value = $event.target.value.replace(/[^0-9.]/g, '')" name="sub_kegiatan_kode" x-model="editFormData.sub_kegiatan_kode" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-purple-400 font-mono font-bold focus:border-purple-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-slate-400 mb-1">Nama Sub Kegiatan Pengadaan</label>
                        <input type="text" name="sub_kegiatan_nama" x-model="editFormData.sub_kegiatan_nama" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-purple-500">
                    </div>
                </div>
            </div>
            <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-slate-700 hover:border-rose-500/30 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>Batal</span>
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
