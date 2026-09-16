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
                            <!-- Nama Penyedia dengan Autocomplete Dropdown & Auto-fill -->
                            <div class="relative flex flex-col justify-end" @click.away="isPenyediaDropdownOpen = false">
                                <label class="text-slate-300 text-xs font-semibold mb-1.5 min-h-[34px] flex items-end justify-between gap-2">
                                    <span class="leading-tight">Nama Penyedia <span class="text-slate-400 font-normal text-[11px]">(Perusahaan / Rekanan)</span></span>
                                    <template x-if="masterPenyedias.length > 0">
                                        <span class="text-[10px] text-amber-400 font-mono font-normal flex items-center gap-1 shrink-0 bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20 leading-none">
                                            <span>⚡</span>
                                            <span>Riwayat</span>
                                        </span>
                                    </template>
                                </label>
                                <div class="relative flex items-center">
                                    <input type="text" x-model="formData.penyedia_nama" 
                                           @focus="isPenyediaDropdownOpen = true"
                                           @input="onPenyediaInput()"
                                           placeholder="Contoh: PT. Medika Sarana Utama"
                                           class="w-full h-11 bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 pr-8 text-xs text-white font-bold focus:outline-none focus:border-amber-500 transition-colors">
                                    <template x-if="formData.penyedia_nama">
                                        <button type="button" @click="clearPenyedia()" 
                                                title="Kosongkan nama penyedia"
                                                class="absolute right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 rounded-md bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center text-xs transition-colors">
                                            ✕
                                        </button>
                                    </template>
                                </div>

                                <!-- Floating Dropdown Rekomendasi Penyedia -->
                                <div x-show="isPenyediaDropdownOpen && masterPenyedias.length > 0"
                                     x-transition 
                                     class="absolute top-full left-0 right-0 z-50 mt-1 bg-slate-900 border border-amber-500/40 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto divide-y divide-slate-800">
                                    
                                    <div class="px-4 py-2 bg-slate-950/90 text-[10px] font-bold text-amber-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                        <span>Pilih Riwayat Rekanan / Vendor</span>
                                        <span class="font-mono text-slate-400" x-text="filteredPenyediaList.length + ' data'"></span>
                                    </div>

                                    <template x-for="(p, pIdx) in filteredPenyediaList" :key="pIdx">
                                        <div @click="selectPenyedia(p)"
                                             class="px-4 py-2.5 hover:bg-amber-500/15 cursor-pointer transition-colors group flex items-start justify-between gap-3 text-left">
                                            <div class="space-y-0.5 min-w-0">
                                                <p class="font-bold text-xs text-white group-hover:text-amber-300 truncate" x-text="p.nama"></p>
                                                <p class="text-[10px] text-slate-400 truncate" x-text="(p.pemilik ? 'Direktur: ' + p.pemilik : '') + (p.rekening_nomor ? ' • Rek: ' + p.rekening_nomor : '')"></p>
                                                <template x-if="p.alamat">
                                                    <p class="text-[9.5px] text-slate-500 truncate" x-text="p.alamat"></p>
                                                </template>
                                            </div>
                                            <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/25 font-bold shrink-0 mt-0.5 group-hover:bg-amber-500/20">
                                                Auto-fill ↵
                                            </span>
                                        </div>
                                    </template>

                                    <!-- Indikator jika tidak ada yang persis cocok / Rekanan Baru -->
                                    <template x-if="formData.penyedia_nama && filteredPenyediaList.length === 0">
                                        <div class="p-3 text-center text-xs text-slate-400">
                                            <p class="text-amber-300 font-semibold" x-text="'➕ Gunakan Rekanan Baru: &quot;' + formData.penyedia_nama + '&quot;'"></p>
                                            <p class="text-[10px] text-slate-500 mt-0.5">Silakan lengkapi pemilik, rekening bank, dan alamat di bawah (akan tersimpan otomatis)</p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="flex flex-col justify-end">
                                <label class="text-slate-300 text-xs font-semibold mb-1.5 min-h-[34px] flex items-end">
                                    <span class="leading-tight">Pemilik Penyedia <span class="text-slate-400 font-normal text-[11px]">(Direktur / Pimpinan)</span></span>
                                </label>
                                <div class="relative flex items-center">
                                    <input type="text" x-model="formData.penyedia_pemilik" placeholder="Contoh: Ir. H. Budi Santoso, M.T."
                                           class="w-full h-11 bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500 transition-colors">
                                </div>
                            </div>
                            <template x-if="formData.is_extracomtable || isAtb">
                                <div class="flex flex-col justify-end">
                                    <label class="text-slate-300 text-xs font-semibold mb-1.5 min-h-[34px] flex items-end justify-between gap-1.5">
                                        <span class="leading-tight text-amber-300">No. HP / WA Aktif</span>
                                        <span class="text-[10px] text-amber-400/90 font-mono font-normal bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20 shrink-0 leading-none">
                                            Khusus Extracom & ATB
                                        </span>
                                    </label>
                                    <div class="relative flex items-center">
                                        <input type="text" x-model="formData.penyedia_telepon" placeholder="Contoh: 0812-3456-7890 / 0852-9876-5432"
                                               class="w-full h-11 bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition-colors">
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Rekening Bank Penyedia (Nama Rek & Nomor Rek) -->
                        <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex flex-col justify-end">
                                <label class="block text-slate-400 text-[11px] mb-1 font-medium">Rekening: Nama Rekening (Atas Nama)</label>
                                <input type="text" x-model="formData.penyedia_rekening_nama" placeholder="Contoh: PT. Medika Sarana Utama"
                                       class="w-full h-10 bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500 transition-colors">
                            </div>
                            <div class="flex flex-col justify-end">
                                <label class="block text-slate-400 text-[11px] mb-1 font-medium">Rekening: Nomor Rekening & Nama Bank</label>
                                <input type="text" x-model="formData.penyedia_rekening_nomor" placeholder="Contoh: 143-00-9876543-2 (Bank Jatim Cab. Bondowoso)"
                                       class="w-full h-10 bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-300 font-mono font-bold focus:outline-none focus:border-amber-500 transition-colors">
                            </div>
                        </div>

                        <div>
                            <label class="block text-slate-300 text-xs mb-1 font-semibold">Alamat Penyedia</label>
                            <input type="text" x-model="formData.penyedia_alamat" placeholder="Contoh: Jl. Raya Darmo No. 45, Wonokromo, Kota Surabaya, Jawa Timur"
                                   class="w-full h-11 bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500 transition-colors">
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
                            <!-- Nama PPK dengan Autocomplete Dropdown & Auto-fill -->
                            <div class="relative flex flex-col justify-end" @click.away="isPpkDropdownOpen = false">
                                <label class="text-slate-300 text-xs font-semibold mb-1.5 min-h-[34px] flex items-end justify-between gap-2">
                                    <span class="leading-tight">Nama Pejabat Pembuat Komitmen <span class="text-slate-400 font-normal text-[11px]">(PPK)</span></span>
                                    <template x-if="masterPejabats.length > 0">
                                        <span class="text-[10px] text-cyan-400 font-mono font-normal flex items-center gap-1 shrink-0 bg-cyan-500/10 px-1.5 py-0.5 rounded border border-cyan-500/20 leading-none">
                                            <span>⚡</span>
                                            <span>Riwayat</span>
                                        </span>
                                    </template>
                                </label>
                                <div class="relative flex items-center">
                                    <input type="text" x-model="formData.ppk_nama" 
                                           @focus="isPpkDropdownOpen = true"
                                           @input="onPpkInput()"
                                           placeholder="Contoh: dr. Slamet Widodo, M.Kes"
                                           class="w-full h-11 bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 pr-8 text-xs text-white font-bold focus:outline-none focus:border-cyan-500 transition-colors">
                                    <template x-if="formData.ppk_nama">
                                        <button type="button" @click="clearPpk()" 
                                                title="Kosongkan nama PPK"
                                                class="absolute right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 rounded-md bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center text-xs transition-colors">
                                            ✕
                                        </button>
                                    </template>
                                </div>

                                <!-- Floating Dropdown Rekomendasi PPK -->
                                <div x-show="isPpkDropdownOpen && masterPejabats.length > 0"
                                     x-transition 
                                     class="absolute top-full left-0 right-0 z-50 mt-1 bg-slate-900 border border-cyan-500/40 rounded-2xl shadow-2xl overflow-hidden max-h-56 overflow-y-auto divide-y divide-slate-800">
                                    
                                    <div class="px-4 py-2 bg-slate-950/90 text-[10px] font-bold text-cyan-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                        <span>Pilih Riwayat Pejabat (PPK)</span>
                                        <span class="font-mono text-slate-400" x-text="filteredPpkList.length + ' data'"></span>
                                    </div>

                                    <template x-for="(k, kIdx) in filteredPpkList" :key="kIdx">
                                        <div @click="selectPpk(k)"
                                             class="px-4 py-2.5 hover:bg-cyan-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3 text-left">
                                            <div class="space-y-0.5 min-w-0">
                                                <p class="font-bold text-xs text-white group-hover:text-cyan-300 truncate" x-text="k.nama"></p>
                                                <p class="text-[10px] font-mono text-cyan-400/90" x-text="k.nip ? ('NIP: ' + k.nip) : 'NIP belum terdata'"></p>
                                            </div>
                                            <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/25 font-bold shrink-0 group-hover:bg-cyan-500/20">
                                                Auto-fill ↵
                                            </span>
                                        </div>
                                    </template>

                                    <!-- Indikator jika tidak ada yang persis cocok / PPK Baru -->
                                    <template x-if="formData.ppk_nama && filteredPpkList.length === 0">
                                        <div class="p-3 text-center text-xs text-slate-400">
                                            <p class="text-cyan-300 font-semibold" x-text="'➕ Gunakan Pejabat Baru: &quot;' + formData.ppk_nama + '&quot;'"></p>
                                            <p class="text-[10px] text-slate-500 mt-0.5">Silakan isi NIP di samping (akan tersimpan otomatis)</p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="flex flex-col justify-end">
                                <label class="text-slate-300 text-xs font-semibold mb-1.5 min-h-[34px] flex items-end">
                                    <span class="leading-tight">NIP PPK</span>
                                </label>
                                <div class="relative flex items-center">
                                    <input type="text" x-model="formData.ppk_nip" placeholder="Contoh: 19760229 200801 1 010"
                                           class="w-full h-11 bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-cyan-300 font-mono font-bold focus:outline-none focus:border-cyan-500 transition-colors">
                                </div>
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
