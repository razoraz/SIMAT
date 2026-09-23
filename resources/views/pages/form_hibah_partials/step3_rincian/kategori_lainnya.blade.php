<!-- ===================================================================== -->
<!-- KONDISI LAINNYA: FALLBACK RINCIAN ASET HIBAH LAINNYA                 -->
<!-- ===================================================================== -->
<template x-if="!isTanah && !isMesin && !isGedung && !isJaringan && !isAsetLainnya && !isAtb && !isKdp">
    <div class="space-y-6">
        <div class="p-5 rounded-3xl bg-slate-950/90 border border-slate-700 space-y-4 shadow-xl">
            <div class="flex items-center space-x-2 border-b border-slate-800 pb-3">
                <span class="p-2 rounded-xl bg-cyan-500/20 text-cyan-400 text-sm">📦</span>
                <div>
                    <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">Rincian Fisik Barang Hibah</h3>
                    <p class="text-[11px] text-slate-400">Pengisian rincian umum untuk klasifikasi aset hibah yang dipilih</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-3">
                    <div>
                        <label class="block text-slate-400 text-xs font-semibold mb-1">Nama Barang / Aset</label>
                        <input type="text" x-model="formData.nama_barang" placeholder="Nama barang..."
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-semibold focus:border-cyan-500">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-xs font-semibold mb-1">Spesifikasi / Keterangan Fisik</label>
                        <textarea rows="3" x-model="formData.spesifikasi_barang" placeholder="Deskripsi spesifikasi fisik barang hibah..."
                                  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:border-cyan-500 resize-none"></textarea>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-slate-400 text-xs font-semibold mb-1">Jumlah (Volume)</label>
                            <input type="number" min="1" x-model.number="formData.jumlah_volume" placeholder="1"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-xs font-semibold mb-1">Satuan</label>
                            <input type="text" x-model="formData.satuan" placeholder="Unit / Buah"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-400 text-xs font-semibold mb-1">Taksiran Nilai Hibah (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-500 text-xs font-bold">Rp</span>
                            <input type="text"
                                   :value="formData.total_realisasi ? Number(formData.total_realisasi).toLocaleString('id-ID') : ''"
                                   @input="
                                       let raw = $event.target.value.replace(/\D/g, '');
                                       formData.total_realisasi = raw ? parseInt(raw, 10) : 0;
                                       $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                   "
                                   placeholder="0"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-9 pr-3.5 py-2.5 text-xs text-emerald-400 font-mono font-bold focus:border-emerald-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-400 text-xs font-semibold mb-1">Kondisi Barang</label>
                        <select x-model="formData.keadaan_barang" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-cyan-500">
                            <option value="Baik">Baik (B)</option>
                            <option value="Kurang Baik">Kurang Baik (KB)</option>
                            <option value="Rusak Berat">Rusak Berat (RB)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
