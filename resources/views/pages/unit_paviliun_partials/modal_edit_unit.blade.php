        <!-- MODAL UBAH UNIT -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-white">✏️ Ubah Data Unit</h3>
                    <button type="button" @click="showEditModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <form @submit.prevent="showEditModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Unit</label>
                        <input type="text" x-model="selectedUnit ? selectedUnit.nama : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Kepala Ruangan</label>
                        <input type="text" x-model="selectedUnit ? selectedUnit.kepala : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div class="pt-4 flex items-center justify-end space-x-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-500 text-slate-950 font-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
