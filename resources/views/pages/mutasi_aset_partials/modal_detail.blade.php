        <!-- MODAL DETAIL MUTASI -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 50;" @click.self="showDetailModal = false" x-cloak>
            <div class="border border-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto my-auto" style="background-color: #0f172a;">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="text-indigo-400 font-bold text-lg">🔄</span>
                        <h3 class="text-base font-extrabold text-white">Detail Berita Acara Mutasi</h3>
                    </div>
                    <button type="button" @click.stop="showDetailModal = false" class="text-slate-500 hover:text-white text-xl font-bold cursor-pointer">&times;</button>
                </div>
                
                <template x-if="selectedMutasi">
                    <div class="space-y-3.5 text-xs">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-slate-400 text-[10.5px] uppercase font-bold">Nomor BAMB:</span>
                                <p class="font-mono font-bold text-cyan-300 text-sm" x-text="selectedMutasi.kode"></p>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-400 text-[10.5px] uppercase font-bold">Jenis Mutasi:</span>
                                <p class="font-bold text-white" x-text="selectedMutasi.jenis"></p>
                            </div>
                        </div>

                        <!-- Daftar Rincian Barang yang Dimutasi (Multi-Item Table) -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 text-[10.5px] font-bold uppercase tracking-wider">
                                    Daftar Barang yang Dimutasi (<span x-text="(selectedMutasi.items ? selectedMutasi.items.length : 1) + ' Unit'"></span>):
                                </span>
                            </div>
                            <div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden shadow-inner">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-900/90 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
                                        <tr>
                                            <th class="px-3 py-2 text-center w-8">No</th>
                                            <th class="px-3 py-2">Nama Barang / Aset</th>
                                            <th class="px-3 py-2 font-mono">NIBAR</th>
                                            <th class="px-3 py-2 text-center whitespace-nowrap">Kondisi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/60">
                                        <template x-for="(it, idx) in (selectedMutasi.items && selectedMutasi.items.length > 0 ? selectedMutasi.items : [{no: 1, nama_barang: selectedMutasi.nama, nibar: selectedMutasi.kode_barang, kondisi: selectedMutasi.kondisi}])" :key="idx">
                                            <tr class="hover:bg-slate-900/40">
                                                <td class="px-3 py-2 text-center font-bold text-slate-500" x-text="idx + 1"></td>
                                                <td class="px-3 py-2 font-bold text-white" x-text="it.nama_barang"></td>
                                                <td class="px-3 py-2 font-mono text-cyan-400 text-[11px]" x-text="it.nibar"></td>
                                                <td class="px-3 py-2 text-center whitespace-nowrap">
                                                    <span class="px-2 py-0.5 rounded text-[9.5px] font-black border whitespace-nowrap inline-block"
                                                          :class="{
                                                              'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': it.kondisi === 'Baik',
                                                              'bg-amber-500/20 text-amber-300 border-amber-500/30': it.kondisi === 'Rusak Ringan' || it.kondisi === 'Kurang Baik',
                                                              'bg-rose-500/20 text-rose-300 border-rose-500/30': it.kondisi === 'Rusak Berat'
                                                          }" x-text="it.kondisi || 'Baik'"></span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                            <div>
                                <span class="text-slate-400 text-[10.5px]">Ruangan Asal (Pengirim):</span>
                                <p class="font-semibold text-slate-300 mt-0.5" x-text="selectedMutasi.asal"></p>
                                <p class="text-[10px] text-slate-500 mt-0.5" x-text="'PJ: ' + (selectedMutasi.pemohon || '-')"></p>
                            </div>
                            <div>
                                <span class="text-indigo-400 text-[10.5px] font-semibold">Ruangan Tujuan (Penerima):</span>
                                <p class="font-semibold text-indigo-300 mt-0.5" x-text="selectedMutasi.tujuan"></p>
                                <p class="text-[10px] text-slate-500 mt-0.5" x-text="'PJ: ' + (selectedMutasi.penerima_pj || '-')"></p>
                            </div>
                            <div>
                                <span class="text-slate-400 text-[10.5px]">Tanggal Pengajuan:</span>
                                <p class="font-semibold text-slate-300 mt-0.5" x-text="selectedMutasi.tgl"></p>
                            </div>
                            <div>
                                <span class="text-slate-400 text-[10.5px]">Status Otorisasi:</span>
                                <p class="font-semibold mt-0.5"
                                    :class="{
                                        'text-emerald-400': selectedMutasi.status === 'Disetujui Admin (Selesai)',
                                        'text-cyan-400':    selectedMutasi.status === 'Disetujui 2 Pihak (Menunggu Admin)',
                                        'text-amber-400':   selectedMutasi.status === 'Menunggu Persetujuan Penerima',
                                        'text-rose-400':    selectedMutasi.status === 'Ditolak'
                                    }"
                                    x-text="selectedMutasi.status">
                                </p>
                            </div>
                        </div>

                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800" x-show="selectedMutasi.keterangan">
                            <span class="text-slate-500 block mb-1 font-semibold text-[10px]">Alasan / Urgensi Mutasi:</span>
                            <p class="text-slate-300 leading-relaxed" x-text="selectedMutasi.keterangan"></p>
                        </div>

                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800" x-show="selectedMutasi.catatan_penerima">
                            <span class="text-slate-500 block mb-1 font-semibold text-[10px]">Catatan Tambahan:</span>
                            <p class="text-slate-300 leading-relaxed" x-text="selectedMutasi.catatan_penerima"></p>
                        </div>

                        <div class="p-3 bg-rose-950/30 rounded-xl border border-rose-500/30" x-show="selectedMutasi.alasan_penolakan">
                            <span class="text-rose-400 block mb-1 font-bold text-[10px]">Alasan Penolakan:</span>
                            <p class="text-rose-200 leading-relaxed" x-text="selectedMutasi.alasan_penolakan"></p>
                        </div>

                        {{-- Notifikasi Khusus Sub Admin: Wajib TTD Basah di Ruang Instalasi Perbekalan --}}
                        <div class="p-3.5 bg-gradient-to-r from-amber-500/15 via-amber-500/10 to-slate-950 border border-amber-500/40 rounded-2xl flex items-start space-x-3 text-amber-200 shadow-lg"
                             x-show="userRole === 'sub_admin' && isMutasiSelesai(selectedMutasi)">
                            <div class="p-2 rounded-xl bg-amber-500/20 text-amber-300 text-lg shrink-0 flex items-center justify-center">
                                ✍️
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-extrabold text-xs text-amber-300 uppercase tracking-wide">Pemberitahuan Tanda Tangan Basah</span>
                                    <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-[9px] font-black uppercase">Penting</span>
                                </div>
                                <p class="text-[11.5px] text-amber-100/90 leading-relaxed">
                                    Pengajuan mutasi aset ini telah <strong>disetujui lengkap oleh semua pihak</strong>. Dokumen fisik Berita Acara Mutasi Barang (BAMB) telah diterbitkan oleh Admin.
                                    Dimohon Kepala Ruangan / Penanggung Jawab terkait untuk <strong>segera melakukan tanda tangan basah</strong> di <strong>Ruang Instalasi Perbekalan</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="pt-4 mt-4 border-t border-slate-800 flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        {{-- 1. Tombol Cetak BAMB → HANYA TAMPIL PADA AKUN ADMIN & MASTER ADMIN --}}
                        <template x-if="selectedMutasi && canPrint(selectedMutasi)">
                            <a :href="selectedMutasi ? ('/berita-acara?tab=mutasi&id=' + selectedMutasi.id + '&returnTo=' + encodeURIComponent('/mutasi-aset?openDetail=' + selectedMutasi.id)) : '#'"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shadow-sm active:scale-95 bg-purple-500 hover:bg-purple-400 text-slate-950 shadow-purple-500/20 no-underline">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2h6z"/></svg>
                                <span>🖨️ Cetak / Edit BAMB</span>
                            </a>
                        </template>

                        {{-- Info Badge untuk Sub Admin di Baris Tombol Aksi --}}
                        <template x-if="userRole === 'sub_admin' && isMutasiSelesai(selectedMutasi)">
                            <div class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-300 text-xs font-bold shadow-sm">
                                <span>✍️ Segera Lakukan TTD Basah di Ruang Instalasi Perbekalan</span>
                            </div>
                        </template>

                        {{-- 2. Tombol Setujui Pengirim (HANYA DITAMPILKAN PADA AKUN SUB ADMIN RUANGAN ASAL) --}}
                        <template x-if="canApprovePengirim(selectedMutasi)">
                            <button type="button" @click="approvePengirim(selectedMutasi)" class="px-3.5 py-2 rounded-xl bg-blue-500/15 text-blue-300 border border-blue-500/30 hover:bg-blue-500/25 text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shadow-sm active:scale-95">
                                <span>✓ Setujui (Pengirim)</span>
                            </button>
                        </template>

                        {{-- 3. Tombol Setujui Penerima (HANYA DITAMPILKAN PADA AKUN SUB ADMIN RUANGAN TUJUAN) --}}
                        <template x-if="canApprovePenerima(selectedMutasi)">
                            <button type="button" @click="approvePenerima(selectedMutasi)" class="px-3.5 py-2 rounded-xl bg-teal-500/15 text-teal-300 border border-teal-500/30 hover:bg-teal-500/25 text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shadow-sm active:scale-95">
                                <span>✓ Setujui (Penerima)</span>
                            </button>
                        </template>

                        {{-- 3. Tombol Setujui Admin (HANYA DITAMPILKAN PADA AKUN ADMIN & MASTER ADMIN - BISA LANGSUNG) --}}
                        <template x-if="canApproveAdmin(selectedMutasi)">
                            <button type="button" @click="approveAdmin(selectedMutasi)" class="px-3.5 py-2 rounded-xl bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/25 text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shadow-sm active:scale-95">
                                <span>✓ Setujui (Admin)</span>
                            </button>
                        </template>

                        {{-- 4. Tombol Tolak Mutasi (Selalu tampil jika status belum ditolak & belum selesai) --}}
                        <template x-if="selectedMutasi && selectedMutasi.status !== 'Ditolak' && selectedMutasi.status !== 'Disetujui Admin (Selesai)'">
                            <button type="button" @click="openRejectModal(selectedMutasi)" class="px-3.5 py-2 rounded-xl bg-rose-500/15 text-rose-300 border border-rose-500/30 hover:bg-rose-500/25 text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shadow-sm active:scale-95">
                                <span>✕ Tolak Mutasi</span>
                            </button>
                        </template>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all cursor-pointer">Tutup</button>
                </div>
            </div>
        </div>
