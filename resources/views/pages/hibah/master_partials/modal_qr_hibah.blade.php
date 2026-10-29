<!-- ========================================================================= -->
<!-- 1. FRONTEND MODAL: PRATINJAU & DOWNLOAD QR CODE ASET HIBAH                 -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showQrModal" x-cloak @click.self="showQrModal = false"
         class="fixed inset-0 flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
         style="background-color: rgba(2, 6, 23, 0.88); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 99999;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="border border-amber-500/30 rounded-3xl max-w-md w-full p-6 shadow-2xl text-center space-y-5 max-h-[90vh] overflow-y-auto my-auto"
             style="background-color: #0f172a;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="text-lg">🎁</span>
                    <h3 class="text-base font-extrabold text-white">Label QR Code Aset Hibah</h3>
                </div>
                <button type="button" @click.stop="showQrModal = false" class="text-slate-500 hover:text-white text-xl font-bold cursor-pointer p-1">&times;</button>
            </div>

            <template x-if="selectedQrItem">
                <div class="space-y-4">
                    <!-- Gambar QR Code yang Berisi URL Publik (Bisa Di-scan HP Tanpa Login) -->
                    <div class="p-4 bg-white rounded-2xl inline-block shadow-lg border-2 border-amber-500/40 min-w-[230px] min-h-[230px] relative">
                        <template x-if="isGeneratingQr">
                            <div class="w-52 h-52 flex flex-col items-center justify-center text-slate-500 space-y-2.5">
                                <svg class="animate-spin h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-xs font-bold text-slate-700">Membuat QR Code...</span>
                            </div>
                        </template>
                        <template x-if="!isGeneratingQr && qrDataUrl">
                            <img :src="qrDataUrl"
                                 :alt="selectedQrItem.nama_barang"
                                 class="w-52 h-52 mx-auto object-contain rounded-lg" />
                        </template>
                    </div>

                    <!-- Kartu Informasi Detail Barang Sesuai QR Code -->
                    <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800 text-left space-y-2 text-xs">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                            <span class="text-slate-400 font-semibold text-[10.5px]">📦 Nama Barang:</span>
                            <span class="text-white font-bold text-right max-w-[200px] truncate" x-text="selectedQrItem.nama_barang"></span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                            <span class="text-slate-400 font-semibold text-[10.5px]">🏷️ NIBAR / Kode:</span>
                            <span class="text-amber-400 font-bold font-mono text-right text-[11px]" x-text="selectedQrItem.kode_barang"></span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                            <span class="text-slate-400 font-semibold text-[10.5px]">🎁 Pihak Hibah:</span>
                            <span class="text-amber-300 font-bold text-right max-w-[200px] truncate" x-text="selectedQrItem.pihak_hibah || '-'"></span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                            <span class="text-slate-400 font-semibold text-[10.5px]">📍 Lokasi / Ruangan:</span>
                            <span class="text-teal-300 font-bold text-right max-w-[190px] truncate" x-text="selectedQrItem.ruang_pemegang || 'Gudang Aset RSUD'"></span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                            <span class="text-slate-400 font-semibold text-[10.5px]">⚙️ Kondisi Aset:</span>
                            <span class="text-emerald-300 font-extrabold" x-text="selectedQrItem.kondisi"></span>
                        </div>
                        <div class="flex items-start justify-between pt-0.5">
                            <span class="text-slate-400 font-semibold text-[10.5px] shrink-0 mr-2">📜 Legalitas BAST:</span>
                            <span class="text-slate-300 font-mono text-right text-[10.5px] truncate" x-text="selectedQrItem.nomor_bast || '-'"></span>
                        </div>
                    </div>

                    <!-- Box URL Publik Scan -->
                    <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 text-left space-y-1.5">
                        <span class="text-[9.5px] font-bold text-slate-500 uppercase tracking-wider block">🔗 URL Publik Terenkripsi QR Code (Tanpa Login):</span>
                        <a :href="getQrPayloadUrl(selectedQrItem)" target="_blank"
                           class="font-mono text-[10.5px] text-amber-400 hover:underline block truncate" x-text="getQrPayloadUrl(selectedQrItem)"></a>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5 pt-1">
                        <button type="button" @click="downloadQrImage()"
                            class="w-full sm:flex-1 py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-amber-500/20 transition-all flex items-center justify-center space-x-2 active:scale-95 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh QR</span>
                        </button>
                        <a :href="getQrPayloadUrl(selectedQrItem)" target="_blank"
                           class="w-full sm:w-auto py-2.5 px-4 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 font-bold text-xs transition-all flex items-center justify-center space-x-1.5">
                            <span>🌐 Buka Scan</span>
                        </a>
                        <button type="button" @click="showQrModal = false"
                            class="w-full sm:w-auto py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<!-- ========================================================================= -->
<!-- 2. FRONTEND MODAL: UBAH KONDISI UNIT BARANG HIBAH                          -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showEditKondisiModal" x-cloak @click.self="showEditKondisiModal = false"
         class="fixed inset-0 flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
         style="background-color: rgba(2, 6, 23, 0.88); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 99999;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="border border-amber-500/30 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 my-auto"
             style="background-color: #0f172a;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="text-lg">⚙️</span>
                    <h3 class="text-base font-extrabold text-white">Ubah Kondisi Unit Barang</h3>
                </div>
                <button type="button" @click.stop="showEditKondisiModal = false" class="text-slate-500 hover:text-white text-xl font-bold cursor-pointer p-1">&times;</button>
            </div>

            <template x-if="editingRegisterItem">
                <div class="space-y-4 text-xs">
                    <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block">TARGET UNIT NIBAR:</span>
                        <p class="text-amber-400 font-mono font-bold text-sm" x-text="editingRegisterItem.nibar || editingRegisterItem.no_register"></p>
                        <p class="text-slate-300 font-semibold text-[11px]" x-text="selectedDetail ? (selectedDetail.astap?.nama_barang || selectedDetail.nama_barang) : ''"></p>
                    </div>

                    <!-- Pilihan Kondisi -->
                    <div class="space-y-2">
                        <label class="font-bold text-slate-300 text-xs">Pilih Kondisi Terkini Unit:</label>
                        <div class="space-y-2">
                            <label class="flex items-center space-x-3 p-3 rounded-2xl border cursor-pointer transition-all"
                                :class="newKondisiValue === 'Baik' ? 'bg-emerald-500/15 border-emerald-500/50 text-emerald-300' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:bg-slate-800/40'">
                                <input type="radio" value="Baik" x-model="newKondisiValue" class="text-emerald-500 focus:ring-0">
                                <div>
                                    <span class="font-extrabold text-xs block text-emerald-300">🟢 Baik (B)</span>
                                    <span class="text-[10px] text-slate-400 block">Unit berfungsi sempurna dan siap digunakan.</span>
                                </div>
                            </label>

                            <label class="flex items-center space-x-3 p-3 rounded-2xl border cursor-pointer transition-all"
                                :class="newKondisiValue === 'Kurang Baik' ? 'bg-amber-500/15 border-amber-500/50 text-amber-300' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:bg-slate-800/40'">
                                <input type="radio" value="Kurang Baik" x-model="newKondisiValue" class="text-amber-500 focus:ring-0">
                                <div>
                                    <span class="font-extrabold text-xs block text-amber-300">🟡 Kurang Baik (KB)</span>
                                    <span class="text-[10px] text-slate-400 block">Ada kendala kecil / penurunan performa namun masih dapat difungsikan.</span>
                                </div>
                            </label>

                            <label class="flex items-center space-x-3 p-3 rounded-2xl border cursor-pointer transition-all"
                                :class="newKondisiValue === 'Rusak Berat' ? 'bg-rose-500/15 border-rose-500/50 text-rose-300' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:bg-slate-800/40'">
                                <input type="radio" value="Rusak Berat" x-model="newKondisiValue" class="text-rose-500 focus:ring-0">
                                <div>
                                    <span class="font-extrabold text-xs block text-rose-300">🔴 Rusak Berat (RB)</span>
                                    <span class="text-[10px] text-slate-400 block">Unit rusak parah / tidak dapat dipakai (siap usul hapus).</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2">
                        <button type="button" @click.stop="showEditKondisiModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold hover:bg-slate-700 cursor-pointer">Batal</button>
                        <button type="button" @click.stop="saveKondisiChange()" :disabled="isSavingKondisi" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold shadow-lg shadow-amber-500/20 active:scale-95 disabled:opacity-50 cursor-pointer">
                            <span x-text="isSavingKondisi ? 'Menyimpan...' : 'Simpan Kondisi'"></span>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<!-- ========================================================================= -->
<!-- 3. FRONTEND MODAL: RIWAYAT MUTASI LENGKAP UNIT HIBAH                       -->
<!--    Mewadahi: Mutasi Internal, Mutasi Eksternal (Antar-OPD), & Reklas      -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showRiwayatModal" x-cloak @click.self="showRiwayatModal = false"
         class="fixed inset-0 flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
         style="background-color: rgba(2, 6, 23, 0.88); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 99999;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="border border-purple-500/40 rounded-3xl max-w-2xl w-full p-5 sm:p-6 shadow-2xl space-y-4 max-h-[88vh] overflow-y-auto my-auto"
             style="background-color: #0f172a;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-500/20 border border-purple-500/40 flex items-center justify-center text-purple-400 shrink-0 shadow-lg shadow-purple-500/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white leading-tight">Riwayat Mutasi &amp; Status Unit Barang</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Histori komprehensif: Mutasi Internal Ruangan, Mutasi Antar-OPD, &amp; Reklasifikasi Aset</p>
                    </div>
                </div>
                <button type="button" @click.stop="showRiwayatModal = false" class="text-slate-500 hover:text-white text-xl font-bold p-1 cursor-pointer">&times;</button>
            </div>

            <template x-if="selectedRiwayatRegister">
                <div class="space-y-4 text-xs">
                    <!-- Info Register Card -->
                    <div class="p-3.5 rounded-2xl border border-slate-800 flex items-center justify-between gap-3 shadow-inner" style="background-color: #020617;">
                        <div class="space-y-0.5 min-w-0 flex-1">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block truncate" x-text="selectedDetail ? (selectedDetail.astap?.nama_barang || selectedDetail.nama_barang) : 'Unit Barang'"></span>
                            <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                                <span class="text-purple-300 font-mono font-bold text-xs" x-text="selectedRiwayatRegister.nibar || selectedRiwayatRegister.no_register"></span>
                                <span class="text-slate-500 text-[11px]">•</span>
                                <span class="text-amber-400 text-[11px] font-semibold truncate" x-text="selectedRiwayatRegister.ruang_pemegang || (selectedDetail?.astap?.unit?.nama || 'Gudang Aset RSUD')"></span>
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            <span class="text-[9px] uppercase font-bold text-slate-500 block mb-0.5">Kondisi Sekarang</span>
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold border inline-block"
                                  :class="{
                                      'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': selectedRiwayatRegister.kondisi === 'Baik',
                                      'bg-amber-500/20 text-amber-300 border-amber-500/30': selectedRiwayatRegister.kondisi === 'Kurang Baik',
                                      'bg-rose-500/20 text-rose-300 border-rose-500/30': selectedRiwayatRegister.kondisi === 'Rusak Berat'
                                  }" x-text="selectedRiwayatRegister.kondisi"></span>
                        </div>
                    </div>

                    <!-- Modern Pills Tab Switcher: Semua, Internal, Eksternal, Reklasifikasi -->
                    <div class="flex items-center gap-1.5 p-1 rounded-2xl bg-slate-950 border border-slate-800/80 overflow-x-auto custom-scrollbar">
                        <button type="button" @click="riwayatActiveTab = 'semua'"
                            class="flex-1 min-w-[100px] py-1.5 px-3 rounded-xl font-bold text-[11px] transition-all flex items-center justify-center space-x-1.5 cursor-pointer"
                            :class="riwayatActiveTab === 'semua'
                                ? 'bg-purple-600 text-white shadow-md shadow-purple-600/30 font-extrabold'
                                : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900'">
                            <span>🌐 Semua Histori</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[9px] font-mono"
                                  :class="riwayatActiveTab === 'semua' ? 'bg-purple-900/80 text-white' : 'bg-slate-800 text-slate-400'"
                                  x-text="selectedRiwayatCounts.total || 0"></span>
                        </button>

                        <button type="button" @click="riwayatActiveTab = 'internal'"
                            class="flex-1 min-w-[110px] py-1.5 px-3 rounded-xl font-bold text-[11px] transition-all flex items-center justify-center space-x-1.5 cursor-pointer"
                            :class="riwayatActiveTab === 'internal'
                                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-extrabold'
                                : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900'">
                            <span>🏢 Mutasi Internal</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[9px] font-mono"
                                  :class="riwayatActiveTab === 'internal' ? 'bg-indigo-900/80 text-white' : 'bg-slate-800 text-slate-400'"
                                  x-text="selectedRiwayatCounts.internal || 0"></span>
                        </button>

                        <button type="button" @click="riwayatActiveTab = 'eksternal'"
                            class="flex-1 min-w-[110px] py-1.5 px-3 rounded-xl font-bold text-[11px] transition-all flex items-center justify-center space-x-1.5 cursor-pointer"
                            :class="riwayatActiveTab === 'eksternal'
                                ? 'bg-cyan-600 text-white shadow-md shadow-cyan-600/30 font-extrabold'
                                : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900'">
                            <span>🏛️ Mutasi Eksternal</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[9px] font-mono"
                                  :class="riwayatActiveTab === 'eksternal' ? 'bg-cyan-900/80 text-white' : 'bg-slate-800 text-slate-400'"
                                  x-text="selectedRiwayatCounts.eksternal || 0"></span>
                        </button>

                        <button type="button" @click="riwayatActiveTab = 'reklas'"
                            class="flex-1 min-w-[110px] py-1.5 px-3 rounded-xl font-bold text-[11px] transition-all flex items-center justify-center space-x-1.5 cursor-pointer"
                            :class="riwayatActiveTab === 'reklas'
                                ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30 font-extrabold'
                                : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900'">
                            <span>🔄 Reklasifikasi</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[9px] font-mono"
                                  :class="riwayatActiveTab === 'reklas' ? 'bg-emerald-900/80 text-white' : 'bg-slate-800 text-slate-400'"
                                  x-text="selectedRiwayatCounts.reklas || 0"></span>
                        </button>
                    </div>

                    <!-- Loading State -->
                    <template x-if="isLoadingRiwayat">
                        <div class="py-12 text-center space-y-3">
                            <div class="inline-block w-7 h-7 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
                            <p class="text-xs text-slate-400 font-medium">Memuat histori mutasi internal, eksternal &amp; reklasifikasi...</p>
                        </div>
                    </template>

                    <!-- TAB 1: SEMUA HISTORI -->
                    <div x-show="!isLoadingRiwayat && riwayatActiveTab === 'semua'" class="space-y-3">
                        <template x-if="!selectedRiwayatTimeline || selectedRiwayatTimeline.length === 0">
                            <div class="p-8 text-center rounded-2xl border border-dashed border-slate-800 space-y-2" style="background-color: #020617;">
                                <span class="text-3xl block">📦</span>
                                <h4 class="text-sm font-bold text-slate-200">Belum Ada Riwayat Tercatat</h4>
                                <p class="text-[11px] text-slate-400 max-w-sm mx-auto">Unit aset ini belum pernah mengalami pemindahan ruangan (KIR), mutasi eksternal antar-OPD, maupun reklasifikasi akun.</p>
                            </div>
                        </template>

                        <template x-if="selectedRiwayatTimeline && selectedRiwayatTimeline.length > 0">
                            <div class="relative pl-6 space-y-4 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-800">
                                <template x-for="(ev, eIdx) in selectedRiwayatTimeline" :key="eIdx">
                                    <div class="relative space-y-1">
                                        <div class="absolute -left-6 top-1.5 w-3 h-3 rounded-full border-2 border-slate-900"
                                             :class="{
                                                 'bg-indigo-400': ev.type === 'internal',
                                                 'bg-cyan-400': ev.type === 'eksternal',
                                                 'bg-emerald-400': ev.type === 'reklas'
                                             }"></div>
                                        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800 space-y-1">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-bold uppercase tracking-wider"
                                                      :class="{
                                                          'text-indigo-400': ev.type === 'internal',
                                                          'text-cyan-400': ev.type === 'eksternal',
                                                          'text-emerald-400': ev.type === 'reklas'
                                                      }" x-text="ev.title"></span>
                                                <span class="text-[10px] text-slate-500 font-mono" x-text="formatTanggalIndo(ev.date)"></span>
                                            </div>
                                            <p class="text-[11px] text-slate-300" x-text="ev.description"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- TAB 2: MUTASI INTERNAL -->
                    <div x-show="!isLoadingRiwayat && riwayatActiveTab === 'internal'" class="space-y-3">
                        <template x-if="!selectedRiwayatMutasisInternal || selectedRiwayatMutasisInternal.length === 0">
                            <div class="p-8 text-center rounded-2xl border border-dashed border-slate-800 space-y-2" style="background-color: #020617;">
                                <span class="text-3xl block">🏢</span>
                                <h4 class="text-sm font-bold text-slate-200">Belum Ada Mutasi Ruangan</h4>
                                <p class="text-[11px] text-slate-400 max-w-sm mx-auto">Aset ini belum pernah dipindahkan ke unit/ruangan lain dalam internal RSUD.</p>
                            </div>
                        </template>
                        <template x-if="selectedRiwayatMutasisInternal && selectedRiwayatMutasisInternal.length > 0">
                            <div class="space-y-2">
                                <template x-for="(m, mIdx) in selectedRiwayatMutasisInternal" :key="mIdx">
                                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 space-y-1.5">
                                        <div class="flex items-center justify-between text-[10.5px]">
                                            <span class="font-bold text-indigo-300 font-mono" x-text="m.no_bast_mutasi || 'BAST-MUTASI'"></span>
                                            <span class="text-slate-400 font-mono" x-text="formatTanggalIndo(m.tanggal_mutasi || m.created_at)"></span>
                                        </div>
                                        <div class="flex items-center space-x-2 text-xs">
                                            <span class="text-slate-400" x-text="m.unit_asal?.nama || m.ruang_asal || 'Gudang'"></span>
                                            <span class="text-indigo-400 font-bold">→</span>
                                            <span class="text-white font-bold" x-text="m.unit_tujuan?.nama || m.ruang_tujuan || '-'"></span>
                                        </div>
                                        <template x-if="m.keterangan">
                                            <p class="text-[11px] text-slate-400 italic pt-0.5" x-text="m.keterangan"></p>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- TAB 3: MUTASI EKSTERNAL -->
                    <div x-show="!isLoadingRiwayat && riwayatActiveTab === 'eksternal'" class="space-y-3">
                        <template x-if="!selectedRiwayatMutasisEksternal || selectedRiwayatMutasisEksternal.length === 0">
                            <div class="p-8 text-center rounded-2xl border border-dashed border-slate-800 space-y-2" style="background-color: #020617;">
                                <span class="text-3xl block">🏛️</span>
                                <h4 class="text-sm font-bold text-slate-200">Tidak Ada Mutasi Eksternal</h4>
                                <p class="text-[11px] text-slate-400 max-w-sm mx-auto">Unit aset ini belum pernah dimutasi keluar ke OPD atau instansi pemerintah lain.</p>
                            </div>
                        </template>
                    </div>

                    <!-- TAB 4: REKLASIFIKASI -->
                    <div x-show="!isLoadingRiwayat && riwayatActiveTab === 'reklas'" class="space-y-3">
                        <template x-if="!selectedRiwayatReklas || selectedRiwayatReklas.length === 0">
                            <div class="p-8 text-center rounded-2xl border border-dashed border-slate-800 space-y-2" style="background-color: #020617;">
                                <span class="text-3xl block">🔄</span>
                                <h4 class="text-sm font-bold text-slate-200">Tidak Ada Reklasifikasi</h4>
                                <p class="text-[11px] text-slate-400 max-w-sm mx-auto">Unit aset ini belum pernah direklasifikasi akun aset tetapnya.</p>
                            </div>
                        </template>
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex justify-end">
                        <button type="button" @click="showRiwayatModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs cursor-pointer transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

