<!-- ========================================================================= -->
<!-- MODAL EDIT MUTASI EKSTERNAL KELUAR & SELEKSI REGISTER NIBAR              -->
<!-- Desain: Bespoke Modern Dark Dashboard (SIMAT-RK Standard)                -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showEditKeluarModal" 
         x-cloak 
         class="fixed inset-0 z-[999] flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
         @keydown.escape.window="if(!isSavingKeluar) showEditKeluarModal = false">
        
        <!-- Backdrop Glassmorphism -->
        <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" 
             @click="if(!isSavingKeluar) showEditKeluarModal = false"></div>

        <!-- Modal Dialog Box -->
        <div class="relative w-full max-w-4xl bg-slate-900 border border-slate-800 rounded-3xl shadow-2xl overflow-hidden flex flex-col my-auto max-h-[92vh] z-10 transition-all">
            
            <!-- HEADER MODAL -->
            <div class="px-6 py-4.5 border-b border-slate-800/80 flex items-center justify-between bg-slate-950/60 shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 shadow-inner">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="text-base font-extrabold text-white tracking-wide">Ubah Mutasi Keluar & Alokasi NIBAR</h3>
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/40">Koreksi Data</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5 flex items-center gap-1.5 flex-wrap">
                            <span class="font-medium text-slate-300" x-text="editingKeluarItem?.nama_murni || editingKeluarItem?.nama_barang || 'Barang Mutasi'"></span>
                            <span class="text-slate-600">•</span>
                            <span class="font-mono text-cyan-400 text-[11px]" x-text="'Kode 108: ' + (editingKeluarItem?.kode_108 || '-')"></span>
                        </p>
                    </div>
                </div>
                <button type="button" 
                        @click="showEditKeluarModal = false"
                        :disabled="isSavingKeluar"
                        class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/80 transition-colors disabled:opacity-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- BODY MODAL (SCROLLABLE) -->
            <form @submit.prevent="saveEditKeluar()" class="overflow-y-auto custom-scrollbar p-6 space-y-6 flex-1 text-xs">
                
                <!-- SEKSI 1: DATA DOKUMEN BAST KELUAR & PEJABAT -->
                <div class="bg-slate-950/50 border border-slate-800/80 rounded-2xl p-5 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800/60">
                        <div class="flex items-center space-x-2">
                            <span class="text-cyan-400 font-bold text-sm">📋</span>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200">1. Informasi Dokumen BAST & Pihak Serah Terima</h4>
                        </div>
                        <span class="text-[11px] text-slate-400">RSUD Dr. H. Koesnadi &rarr; OPD Penerima</span>
                    </div>

                    <!-- Baris 1: Nomor BAST & Tanggal Penyerahan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                                Nomor BAST Keluar <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" 
                                   x-model="editKeluarForm.nomor_bamb" 
                                   required
                                   placeholder="Contoh: 000.2.3.2/BAST-KLR-001/430.10.7/2026"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700/80 text-white font-mono text-xs focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all placeholder:text-slate-600">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                                Tanggal Penyerahan BAST <span class="text-rose-400">*</span>
                            </label>
                            <input type="date" 
                                   x-model="editKeluarForm.tanggal_mutasi" 
                                   required
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700/80 text-white font-mono text-xs focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                        </div>
                    </div>

                    <!-- Baris 2: OPD Tujuan & Nomor SK Dasar -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                                SKPD / OPD Penerima <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" 
                                   x-model="editKeluarForm.opd_tujuan" 
                                   required
                                   placeholder="Contoh: Dinas Kesehatan Kab. Bondowoso"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-xs focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all placeholder:text-slate-600">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                                Dasar Pemindahtanganan / Nomor SK
                            </label>
                            <input type="text" 
                                   x-model="editKeluarForm.nomor_sk_dasar" 
                                   placeholder="Nomor SK Bupati / Surat Tugas / Nota Dinas"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-xs focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all placeholder:text-slate-600">
                        </div>
                    </div>

                    <!-- Baris 3: Alasan Pemindahtanganan -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Alasan Pemindahtanganan / Catatan BAST
                        </label>
                        <textarea x-model="editKeluarForm.alasan_mutasi" 
                                  rows="2"
                                  placeholder="Keterangan pendukung pemindahtanganan / transfer aset ke OPD..."
                                  class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700/80 text-white text-xs focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all placeholder:text-slate-600 resize-none"></textarea>
                    </div>

                    <!-- Baris 4: Pejabat Penyerah & Penerima (Side-by-Side Cards) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <!-- Pihak Pertama (RSUD Dr. H. Koesnadi) -->
                        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-3.5 space-y-3">
                            <div class="flex items-center space-x-2 text-rose-400 font-bold text-[11px]">
                                <span>🏥</span>
                                <span>PIHAK PERTAMA (PENYERAH RSUD)</span>
                            </div>
                            <div class="space-y-2">
                                <div>
                                    <label class="text-[10px] text-slate-400 block font-medium">Nama Pejabat Penyerah</label>
                                    <input type="text" x-model="editKeluarForm.pj_asal_nama" placeholder="BUDI HARTONO, S.Sos"
                                           class="w-full px-2.5 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-400">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-[10px] text-slate-400 block font-medium">NIP Penyerah</label>
                                        <input type="text" x-model="editKeluarForm.pj_asal_nip" placeholder="19760229 200801 1 010"
                                               class="w-full px-2.5 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 font-mono text-[11px] focus:border-rose-400">
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-slate-400 block font-medium">Jabatan Penyerah</label>
                                        <input type="text" x-model="editKeluarForm.pj_asal_jabatan" placeholder="Pengurus Barang Pengguna"
                                               class="w-full px-2.5 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-rose-400">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pihak Kedua (SKPD / OPD Penerima) -->
                        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-3.5 space-y-3">
                            <div class="flex items-center space-x-2 text-cyan-400 font-bold text-[11px]">
                                <span>🏢</span>
                                <span>PIHAK KEDUA (PENERIMA OPD)</span>
                            </div>
                            <div class="space-y-2">
                                <div>
                                    <label class="text-[10px] text-slate-400 block font-medium">Nama Pejabat Penerima</label>
                                    <input type="text" x-model="editKeluarForm.pj_tujuan_nama" placeholder="Nama Pejabat Penerima OPD"
                                           class="w-full px-2.5 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-cyan-400">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-[10px] text-slate-400 block font-medium">NIP Penerima</label>
                                        <input type="text" x-model="editKeluarForm.pj_tujuan_nip" placeholder="NIP Pejabat Penerima"
                                               class="w-full px-2.5 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 font-mono text-[11px] focus:border-cyan-400">
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-slate-400 block font-medium">Jabatan Penerima</label>
                                        <input type="text" x-model="editKeluarForm.pj_tujuan_jabatan" placeholder="Jabatan Pejabat OPD"
                                               class="w-full px-2.5 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 text-xs focus:border-cyan-400">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEKSI 2: SELEKSI & KOREKSI UNIT REGISTER NIBAR -->
                <div class="bg-slate-950/50 border border-slate-800/80 rounded-2xl p-5 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800/60">
                        <div class="flex items-center space-x-2">
                            <span class="text-amber-400 font-bold text-sm">🏷️</span>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200">2. Alokasi Unit Register NIBAR</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Centang unit NIBAR yang ditransfer pada BAST keluar ini</p>
                            </div>
                        </div>

                        <!-- Counter Pill & Quick Actions -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-extrabold border select-none transition-all shadow-sm"
                                  :class="editKeluarForm.selected_register_ids.length > 0 
                                      ? 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30' 
                                      : 'bg-rose-500/15 text-rose-300 border-rose-500/30'">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="editKeluarForm.selected_register_ids.length > 0 ? 'bg-cyan-400 animate-pulse' : 'bg-rose-400'"></span>
                                <span>Terpilih: <strong x-text="editKeluarForm.selected_register_ids.length"></strong> dari <strong x-text="editKeluarAllRegisters.length"></strong> Unit</span>
                            </span>

                            <button type="button" 
                                    @click="selectAllAvailableKeluar()"
                                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-[11px] font-semibold transition-all">
                                Pilih Semua Tersedia
                            </button>
                            <button type="button" 
                                    @click="deselectAllKeluar()"
                                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-rose-300 border border-slate-700 text-[11px] font-semibold transition-all">
                                Lepas Semua
                            </button>
                        </div>
                    </div>

                    <!-- Filter Pencarian NIBAR Cepat -->
                    <div class="relative" x-show="editKeluarAllRegisters.length > 5">
                        <input type="text" 
                               x-model="editKeluarSearchNibar" 
                               placeholder="Cari NIBAR, Nomor Register, atau Lokasi Unit..." 
                               class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white text-xs placeholder:text-slate-500 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                        <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <!-- TABEL SELEKSI REGISTER NIBAR -->
                    <div class="border border-slate-800/80 rounded-xl overflow-hidden bg-slate-900/60">
                        <div class="max-h-64 overflow-y-auto custom-scrollbar">
                            <table class="w-full text-left text-xs text-slate-300 border-collapse">
                                <thead class="text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-800 bg-slate-950/80 sticky top-0 z-10 backdrop-blur-sm">
                                    <tr>
                                        <th class="px-3 py-2.5 text-center w-12">Pilih</th>
                                        <th class="px-3.5 py-2.5 text-left min-w-[140px]">NIBAR / No. Register</th>
                                        <th class="px-3 py-2.5 text-center w-24">Kondisi</th>
                                        <th class="px-3.5 py-2.5 text-left min-w-[140px]">Lokasi Terakhir</th>
                                        <th class="px-3 py-2.5 text-center w-36">Status Alokasi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 font-medium">
                                    <template x-for="reg in filteredEditKeluarRegisters" :key="reg.id">
                                        <tr class="transition-colors cursor-pointer"
                                            :class="{
                                                'bg-cyan-500/10 hover:bg-cyan-500/15 border-l-2 border-l-cyan-400': editKeluarForm.selected_register_ids.includes(reg.id),
                                                'hover:bg-slate-800/40': !editKeluarForm.selected_register_ids.includes(reg.id) && reg.is_available,
                                                'opacity-40 bg-slate-950/40 cursor-not-allowed': !reg.is_available && !editKeluarForm.selected_register_ids.includes(reg.id)
                                            }"
                                            @click="if(reg.is_available || editKeluarForm.selected_register_ids.includes(reg.id)) toggleEditKeluarRegister(reg.id)">
                                            
                                            <!-- Checkbox Kolom -->
                                            <td class="px-3 py-2.5 text-center" @click.stop>
                                                <input type="checkbox" 
                                                       :value="reg.id"
                                                       :disabled="!reg.is_available && !editKeluarForm.selected_register_ids.includes(reg.id)"
                                                       :checked="editKeluarForm.selected_register_ids.includes(reg.id)"
                                                       @change="toggleEditKeluarRegister(reg.id)"
                                                       class="w-4 h-4 rounded text-cyan-500 bg-slate-950 border-slate-700 focus:ring-cyan-500 focus:ring-offset-slate-900 cursor-pointer disabled:cursor-not-allowed">
                                            </td>

                                            <!-- NIBAR & Nomor Register -->
                                            <td class="px-3.5 py-2.5 font-mono">
                                                <div class="font-bold text-white text-xs tracking-wide" x-text="reg.nibar || '-'"></div>
                                                <div class="text-[10px] text-slate-500" x-text="'No. Reg: ' + (reg.nomor_register || '-')"></div>
                                            </td>

                                            <!-- Kondisi Barang -->
                                            <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold border"
                                                      :class="{
                                                          'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': (reg.kondisi || 'Baik') === 'Baik',
                                                          'bg-amber-500/15 text-amber-300 border-amber-500/30': reg.kondisi === 'Kurang Baik',
                                                          'bg-rose-500/15 text-rose-300 border-rose-500/30': reg.kondisi === 'Rusak Berat'
                                                      }">
                                                    <span x-text="reg.kondisi || 'Baik'"></span>
                                                </span>
                                            </td>

                                            <!-- Lokasi Terakhir -->
                                            <td class="px-3.5 py-2.5 text-slate-300 truncate max-w-[160px]" :title="reg.ruang_pemegang">
                                                <span class="inline-flex items-center gap-1">
                                                    <span class="text-slate-500 text-[10px]">📍</span>
                                                    <span x-text="reg.ruang_pemegang || 'Gudang Utama RSUD'"></span>
                                                </span>
                                            </td>

                                            <!-- Status Alokasi -->
                                            <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                                <template x-if="editKeluarForm.selected_register_ids.includes(reg.id)">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40">
                                                        <span>✓</span> <span>Terpilih BAST</span>
                                                    </span>
                                                </template>
                                                <template x-if="!editKeluarForm.selected_register_ids.includes(reg.id) && reg.is_available">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                        <span>•</span> <span>Tersedia di RSUD</span>
                                                    </span>
                                                </template>
                                                <template x-if="!editKeluarForm.selected_register_ids.includes(reg.id) && !reg.is_available">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-800 text-slate-500 border border-slate-700" title="Terkunci di transaksi mutasi lain atau tidak aktif">
                                                        <span>🔒</span> <span>Terkunci Lain</span>
                                                    </span>
                                                </template>
                                            </td>
                                        </tr>
                                    </template>

                                    <!-- Empty State jika aset tidak memiliki register sama sekali -->
                                    <template x-if="editKeluarAllRegisters.length === 0">
                                        <tr>
                                            <td colspan="5" class="py-8 text-center text-slate-500">
                                                <p class="font-medium">Aset ini dicatat tanpa register satuan individual (kuantitas global).</p>
                                                <p class="text-[11px] text-slate-600 mt-0.5">Seluruh volume mutasi dikelola melalui dokumen BAST ini.</p>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Notice / Alert Dampak Perubahan -->
                    <div class="flex items-start space-x-2.5 p-3 rounded-xl bg-cyan-950/30 border border-cyan-800/40 text-cyan-300 text-[11px] leading-relaxed">
                        <span class="text-base shrink-0 mt-0.5">💡</span>
                        <div>
                            <strong class="font-semibold text-cyan-200">Sinkronisasi Otomatis Inventaris RSUD:</strong> Unit NIBAR yang dicentang akan tercatat resmi keluar ke <span class="font-bold underline" x-text="editKeluarForm.opd_tujuan || 'OPD Tujuan'"></span>. Unit NIBAR yang tidak dicentang akan otomatis kembali aktif ke inventaris RSUD Dr. H. Koesnadi.
                        </div>
                    </div>
                </div>

                <!-- FOOTER MODAL & TOMBOL AKSI -->
                <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between gap-3">
                    <div>
                        <template x-if="editKeluarAllRegisters.length > 0 && editKeluarForm.selected_register_ids.length === 0">
                            <span class="text-rose-400 font-bold text-[11px] flex items-center gap-1">
                                <span>⚠️</span>
                                <span>Wajib memilih minimal 1 unit NIBAR</span>
                            </span>
                        </template>
                    </div>

                    <div class="flex items-center space-x-2.5">
                        <button type="button" 
                                @click="showEditKeluarModal = false"
                                :disabled="isSavingKeluar"
                                class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 font-bold text-xs transition-all disabled:opacity-50">
                            Batal
                        </button>
                        
                        <button type="submit" 
                                :disabled="isSavingKeluar || (editKeluarAllRegisters.length > 0 && editKeluarForm.selected_register_ids.length === 0)"
                                class="inline-flex items-center space-x-2 px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-extrabold text-xs shadow-lg shadow-amber-500/20 active:scale-95 transition-all disabled:opacity-50 disabled:pointer-events-none">
                            <template x-if="!isSavingKeluar">
                                <span class="flex items-center space-x-1.5">
                                    <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Simpan Perubahan Mutasi</span>
                                </span>
                            </template>
                            <template x-if="isSavingKeluar">
                                <span class="flex items-center space-x-1.5">
                                    <svg class="animate-spin w-4 h-4 text-slate-950" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    <span>Menyimpan...</span>
                                </span>
                            </template>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</template>

