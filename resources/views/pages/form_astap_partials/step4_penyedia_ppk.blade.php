            <!-- ========================================================================= -->
            <!-- LANGKAH 4: PIHAK PENYEDIA, PPK & KETERANGAN (SESUAI GAMBAR USER)          -->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 4" class="space-y-6">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold mb-2">
                        <span>🏢 DETAIL PENYEDIA, PPK & PENGESAHAN</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-amber-500/10 text-amber-400 text-sm">🏢</span>
                        <span>Langkah 4: Pihak Penyedia, Pejabat Pembuat Komitmen & Catatan Pengadaan</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Lengkapi identitas perusahaan rekanan, rekening bank rekanan, PPK dan catatan pengadaan:</p>
                </div>

                <!-- Formulir Input Sesuai Tabel Excel Gambar User -->
                <div class="space-y-5">

                    <!-- 1. Pihak Penyedia (Nama, Pemilik, Rekening Nama & Nomor, Alamat) -->
                    <div class="p-5 rounded-2xl bg-slate-950/80 border border-amber-500/30 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                            <span class="text-xs font-bold text-white uppercase tracking-wider flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                <span>1. PIHAK PENYEDIA (Rekanan / Vendor)</span>
                            </span>
                            <span class="text-[10px] text-amber-400 font-mono font-bold">Data Rekanan & Rekening Bank</span>
                        </div>

                        <div :class="(formData.is_extracomtable || isAtb) ? 'grid grid-cols-1 md:grid-cols-3 gap-4' : 'grid grid-cols-1 md:grid-cols-2 gap-4'">
                            <div>
                                <label class="block text-slate-300 text-xs mb-1 font-semibold">Nama Penyedia (Perusahaan / Badan Usaha)</label>
                                <input type="text" x-model="formData.penyedia_nama" placeholder="Contoh: PT. Medika Sarana Utama"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold focus:outline-none focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-slate-300 text-xs mb-1 font-semibold">Pemilik Penyedia (Direktur / Penanggung Jawab)</label>
                                <input type="text" x-model="formData.penyedia_pemilik" placeholder="Contoh: Ir. H. Budi Santoso, M.T."
                                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
                            </div>
                            <template x-if="formData.is_extracomtable || isAtb">
                                <div>
                                    <label class="block text-amber-400 text-xs mb-1 font-semibold flex items-center space-x-1.5">
                                        <span>No Hp / Wa Yang Aktif</span>
                                        <span class="text-[10px] text-amber-400/80 font-normal">(Khusus Extracom & ATB)</span>
                                    </label>
                                    <input type="text" x-model="formData.penyedia_telepon" placeholder="Contoh: 0812-3456-7890 / 0852-9876-5432"
                                           class="w-full bg-slate-900 border border-amber-500/50 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400">
                                </div>
                            </template>
                        </div>

                        <!-- Rekening Bank Penyedia (Nama Rek & Nomor Rek) -->
                        <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Rekening: Nama Rekening (Atas Nama)</label>
                                <input type="text" x-model="formData.penyedia_rekening_nama" placeholder="Contoh: PT. Medika Sarana Utama"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Rekening: Nomor Rekening & Nama Bank</label>
                                <input type="text" x-model="formData.penyedia_rekening_nomor" placeholder="Contoh: 143-00-9876543-2 (Bank Jatim Cab. Bondowoso)"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-300 font-mono font-bold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-slate-300 text-xs mb-1 font-semibold">Alamat Penyedia</label>
                            <input type="text" x-model="formData.penyedia_alamat" placeholder="Contoh: Jl. Raya Darmo No. 45, Wonokromo, Kota Surabaya, Jawa Timur"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
                        </div>
                    </div>

                    <!-- 2. Pejabat Pembuat Komitmen (PPK) -->
                    <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                            <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
                                <span>2. PEJABAT PEMBUAT KOMITMEN (PPK)</span>
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-300 text-xs mb-1 font-semibold">Nama Pejabat Pembuat Komitmen (PPK)</label>
                                <input type="text" x-model="formData.ppk_nama" placeholder="Contoh: dr. Slamet Widodo, M.Kes"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold focus:outline-none focus:border-cyan-500">
                            </div>
                            <div>
                                <label class="block text-slate-300 text-xs mb-1 font-semibold">NIP PPK</label>
                                <input type="text" x-model="formData.ppk_nip" placeholder="Contoh: 19760229 200801 1 010"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-cyan-300 font-mono font-bold focus:outline-none focus:border-cyan-500">
                            </div>
                        </div>
                    </div>

                    <!-- 3. Keterangan (KET.) -->
                    <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
                        <label class="block text-slate-300 font-bold text-xs uppercase tracking-wider">
                            <span>📝 3. Keterangan Tambahan (KET.)</span>
                        </label>
                        <textarea x-model="formData.keterangan_tambahan" rows="2" placeholder="Catatan atau keterangan penting terkait pengadaan..."
                                  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-emerald-500"></textarea>
                    </div>

                </div>

                <!-- ========================================================================= -->
                <!-- LIVE PREVIEW TABEL EXCEL PERSIS SEPERTI GAMBAR SCREENSHOT USER (LANGKAH 4)-->
                <!-- ========================================================================= -->
                <div class="space-y-2 pt-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                            <span>📄 Live Preview Tabel Rekanan Penyedia & PPK (Format Excel Laporan):</span>
                        </span>
                        <span class="text-[10px] text-amber-400 font-mono">Format Excel Sesuai Kolom SPK/Invoice</span>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                        <table class="w-full text-center text-xs border-collapse font-sans min-w-[750px]">
                            <!-- Header Excel Peach/Krem Sesuai Gambar -->
                            <thead>
                                <!-- Header Baris 1 -->
                                <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-600 text-[11px]">
                                    <th :colspan="(formData.is_extracomtable || isAtb) ? 6 : 5" class="py-2.5 border border-slate-600 bg-[#fde9d9] uppercase tracking-wider">
                                        PIHAK PENYEDIA
                                    </th>
                                    <th colspan="2" class="py-2.5 border border-slate-600 bg-[#fde9d9] uppercase tracking-wider">
                                        Pejabat Pembuat Komitmen
                                    </th>
                                    <th rowspan="3" class="px-3 py-3 border border-slate-600 align-middle w-48 bg-[#fde9d9]">
                                        KET.
                                    </th>
                                </tr>
                                <!-- Header Baris 2 -->
                                <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-600 text-[10px]">
                                    <th rowspan="2" class="px-3 py-1.5 border border-slate-600 align-middle">Nama Penyedia</th>
                                    <th rowspan="2" class="px-3 py-1.5 border border-slate-600 align-middle">Pemilik Penyedia</th>
                                    <template x-if="formData.is_extracomtable || isAtb">
                                        <th rowspan="2" class="px-3 py-1.5 border border-slate-600 align-middle">No Hp / wa<br>Yang Aktif</th>
                                    </template>
                                    <th colspan="2" class="py-1 border border-slate-600">Rekening</th>
                                    <th rowspan="2" class="px-4 py-1.5 border border-slate-600 align-middle">Alamat Penyedia</th>
                                    <th rowspan="2" class="px-3 py-1.5 border border-slate-600 align-middle">Nama</th>
                                    <th rowspan="2" class="px-3 py-1.5 border border-slate-600 align-middle">NIP</th>
                                </tr>
                                <!-- Header Baris 3 (Nama Rek & Nomor Rek) -->
                                <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b-2 border-slate-700 text-[10px]">
                                    <th class="px-2.5 py-1 border border-slate-600">Nama Rek</th>
                                    <th class="px-2.5 py-1 border border-slate-600">Nomor Rek</th>
                                </tr>
                            </thead>
                            <!-- Baris Data Live Sesuai Input User -->
                            <tbody class="bg-white text-slate-950 font-medium text-[11px]">
                                <tr>
                                    <td class="px-3 py-3 border border-slate-400 font-semibold" x-text="formData.penyedia_nama || '-'"></td>
                                    <td class="px-3 py-3 border border-slate-400" x-text="formData.penyedia_pemilik || '-'"></td>
                                    <template x-if="formData.is_extracomtable || isAtb">
                                        <td class="px-3 py-3 border border-slate-400 font-mono font-bold text-amber-900" x-text="formData.penyedia_telepon || '-'"></td>
                                    </template>
                                    <td class="px-2.5 py-3 border border-slate-400" x-text="formData.penyedia_rekening_nama || '-'"></td>
                                    <td class="px-2.5 py-3 border border-slate-400 font-mono font-bold text-amber-900" x-text="formData.penyedia_rekening_nomor || '-'"></td>
                                    <td class="px-4 py-3 border border-slate-400 text-left" x-text="formData.penyedia_alamat || '-'"></td>
                                    <td class="px-3 py-3 border border-slate-400 font-bold" x-text="formData.ppk_nama || '-'"></td>
                                    <td class="px-3 py-3 border border-slate-400 font-mono font-bold" x-text="formData.ppk_nip || '-'"></td>
                                    <td class="px-3 py-3 border border-slate-400 text-left text-[10px]" x-text="formData.keterangan_tambahan || '-'"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
