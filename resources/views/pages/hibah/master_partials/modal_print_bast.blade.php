<!-- ========================================================================= -->
<!-- MODAL CETAK: LEMBAR DOKUMEN BAST HIBAH ASET (MASUK & KELUAR)              -->
<!-- ========================================================================= -->
<div x-show="showModalPrintBast" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak @click.self="showModalPrintBast = false">
    <div @click.away="showModalPrintBast = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
        
        <!-- Action Bar Modal (Hidden when Printed) -->
        <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2.5">
                <span class="p-2 rounded-xl text-sm"
                    :class="printBastDoc && printBastDoc.tipe_hibah === 'masuk' ? 'bg-amber-400/20 text-amber-300' : 'bg-rose-500/20 text-rose-300'">
                    <span x-text="printBastDoc && printBastDoc.tipe_hibah === 'masuk' ? '🎁' : '📤'"></span>
                </span>
                <div>
                    <div class="flex items-center space-x-2">
                        <h3 class="text-base font-extrabold text-white">Berita Acara Serah Terima (BAST) Hibah Aset</h3>
                        <template x-if="printBastDoc">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black"
                                :class="printBastDoc.tipe_hibah === 'masuk' ? 'bg-amber-400/20 text-amber-300 border border-amber-400/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30'"
                                x-text="printBastDoc.tipe_hibah === 'masuk' ? 'HIBAH MASUK' : 'HIBAH KELUAR'"></span>
                        </template>
                    </div>
                    <p class="text-[11px] text-slate-400 font-mono" x-text="printBastDoc ? ('Nomor BAST: ' + (printBastDoc.nomor_bast || '-')) : ''"></p>
                </div>
            </div>

            <div class="flex items-center flex-wrap gap-2 shrink-0">
                <!-- Toggle Form Edit -->
                <button type="button" @click="showEditBastForm = !showEditBastForm"
                    class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition-all active:scale-95 cursor-pointer">
                    <span x-text="showEditBastForm ? '✕ Tutup Edit' : '✏️ Edit Data'"></span>
                </button>

                <!-- Toggle TTD BSrE -->
                <button type="button" @click="toggleSignBast()"
                    :class="printBastDoc && printBastDoc.signed ? 'bg-teal-500/20 text-teal-300 border border-teal-500/40 hover:bg-teal-500/30' : 'bg-slate-800 text-slate-300 border border-slate-700 hover:bg-slate-700'"
                    class="px-3 py-1.5 rounded-xl font-bold text-xs transition-all active:scale-95 cursor-pointer flex items-center space-x-1">
                    <span x-text="printBastDoc && printBastDoc.signed ? '✓ TTE BSrE Aktif' : '✍️ TTE BSrE'"></span>
                </button>

                <!-- Buka Tab Cetak Penuh -->
                <template x-if="printBastDoc">
                    <a :href="'/master-data/hibah/' + printBastDoc.id + '/cetak'" target="_blank"
                        class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-300 border border-cyan-500/30 font-bold text-xs transition-all flex items-center space-x-1 cursor-pointer">
                        <span>↗ Tab Baru</span>
                    </a>
                </template>

                <!-- Tombol Cetak Langsung -->
                <button type="button" @click="printCurrentBast()"
                    class="px-4 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5 active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>🖨️ Cetak Surat</span>
                </button>

                <!-- Tutup Modal -->
                <button type="button" @click="showModalPrintBast = false" class="p-1.5 rounded-lg text-slate-400 hover:text-white font-bold text-lg cursor-pointer">&times;</button>
            </div>
        </div>

        <!-- Formulir Live Edit BAST Hibah (Hidden when Printed) -->
        <template x-if="printBastDoc">
            <div x-show="showEditBastForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-slate-800 text-xs space-y-3.5 shadow-inner">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="font-bold text-amber-300 text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                        <span>✏️ Live Edit Dokumen BAST Hibah:</span>
                    </span>
                    <span class="text-[10px] text-emerald-400 font-mono">Perubahan otomatis tertera pada lembar cetak</span>
                </div>

                <!-- Baris 1: Informasi Surat & Tanggal -->
                <div class="grid grid-cols-2 sm:grid-cols-6 gap-2.5">
                    <div class="col-span-2">
                        <label class="block text-slate-400 text-[10px] mb-1">Nomor BAST Hibah</label>
                        <input type="text" x-model="printBastDoc.nomor_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-amber-300 font-mono font-bold text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Hari</label>
                        <input type="text" x-model="printBastDoc.hari" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Tanggal</label>
                        <input type="text" x-model="printBastDoc.tgl_angka" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Bulan</label>
                        <input type="text" x-model="printBastDoc.bulan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Tahun Anggaran</label>
                        <input type="text" x-model="printBastDoc.tahun" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-amber-300 font-bold text-xs">
                    </div>
                </div>

                <!-- Baris 2: Data Pihak Pemberi & Penerima -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-800/80">
                    <!-- Pihak 1 -->
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Nama Pihak Pertama (Yang Menyerahkan)</label>
                        <input type="text" x-model="printBastDoc.pihak_1_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-bold text-xs mb-2">
                        <label class="block text-slate-400 text-[10px] mb-1">Instansi Pihak Pertama</label>
                        <input type="text" x-model="printBastDoc.pihak_1_instansi" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-cyan-300 text-xs">
                    </div>

                    <!-- Pihak 2 -->
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Nama Pihak Kedua (Yang Menerima)</label>
                        <input type="text" x-model="printBastDoc.pihak_2_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-bold text-xs mb-2">
                        <label class="block text-slate-400 text-[10px] mb-1">Instansi Pihak Kedua</label>
                        <input type="text" x-model="printBastDoc.pihak_2_instansi" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-cyan-300 text-xs">
                    </div>
                </div>
            </div>
        </template>

        <!-- LEMBAR PREVIEW CETAK RESMI (A4 PUTIH) -->
        <template x-if="printBastDoc">
            <div class="bg-gray-200 p-3 sm:p-6 rounded-2xl border border-slate-700 flex justify-center items-start overflow-y-auto max-h-[75vh] custom-scrollbar shadow-inner">
                <div id="print-area-bast-hibah" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 10pt; color: #000000 !important; background-color: #ffffff !important; min-height: 100%; box-sizing: border-box;" class="w-full max-w-[760px] shrink-0 bg-white text-black p-8 sm:p-12 md:p-14 shadow-2xl rounded-sm space-y-3.5 select-text print:p-0 print:m-0 print:shadow-none print:max-w-none">
                    
                    <!-- KOP SURAT PEMKAB & RSUD -->
                    <div class="border-b-[2.5px] border-black pb-2 mb-3" style="border-bottom: 2.5px solid #000000;">
                        <div class="flex items-center justify-between gap-3">
                            <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                <img src="{{ asset('img/logo-bondowoso.png') }}" alt="Logo Bondowoso" class="h-16 w-16 object-contain">
                            </div>
                            <div class="flex-1 text-center text-black" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                <h4 class="font-bold text-[11pt] sm:text-[12pt] uppercase tracking-normal leading-tight text-black m-0 p-0">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                <h3 class="font-bold text-[12.5pt] sm:text-[13.5pt] uppercase tracking-normal leading-tight text-black mt-0.5 mb-0 p-0">RUMAH SAKIT UMUM DAERAH DR. H. KOESNADI</h3>
                                <p class="text-[8.5pt] sm:text-[9pt] italic leading-tight text-black mt-0.5 mb-0 p-0">Jl. Kapten Piere Tendean No. 3 Telp. (0332) 421974 Fax. (0332) 422311</p>
                                <p class="text-[8.5pt] sm:text-[9pt] leading-tight text-black m-0 p-0">e-mail: rsu.koesnadi@gmail.com, Website: rsudrkoesnadi.go.id</p>
                                <p class="font-bold text-[10pt] sm:text-[10.5pt] uppercase text-black mt-1 mb-0 p-0" style="letter-spacing: 0.35em;">B O N D O W O S O</p>
                            </div>
                            <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="h-16 w-16 object-contain">
                            </div>
                        </div>
                    </div>

                    <!-- JUDUL SURAT RESMI -->
                    <div class="text-center text-black mb-3">
                        <h3 class="font-bold text-[11pt] sm:text-[11.5pt] uppercase underline tracking-normal text-black m-0">BERITA ACARA SERAH TERIMA (BAST)</h3>
                        <h4 class="font-bold text-[10pt] sm:text-[10.5pt] uppercase tracking-normal text-black m-0 mt-0.5"
                            x-text="printBastDoc.tipe_hibah === 'masuk' ? 'PENERIMAAN HIBAH BARANG MILIK DAERAH (BMD)' : 'PENYERAHAN / PENGURANGAN HIBAH BARANG MILIK DAERAH (BMD)'"></h4>
                        <p class="text-[9.5pt] sm:text-[10pt] font-semibold text-black mt-1 m-0">
                            Nomor : <span class="font-mono font-bold" x-text="printBastDoc.nomor_bast"></span>
                        </p>
                    </div>

                    <!-- KLAUSUL PEMBUKA -->
                    <p class="text-justify mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                        Pada hari ini <strong x-text="printBastDoc.hari"></strong> tanggal <strong x-text="printBastDoc.tgl_angka"></strong> bulan <strong x-text="printBastDoc.bulan"></strong> tahun <strong x-text="printBastDoc.tahun"></strong>, bertempat di Rumah Sakit Umum Daerah dr. H. Koesnadi Kabupaten Bondowoso, yang bertanda tangan di bawah ini :
                    </p>

                    <!-- IDENTITAS PIHAK KESATU & PIHAK KEDUA -->
                    <div class="space-y-2 mb-3 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                        <!-- PIHAK KESATU -->
                        <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                            <tr style="border: none !important;">
                                <td style="border: none !important; width: 25px; vertical-align: top; padding: 1px 0;">1.</td>
                                <td style="border: none !important; width: 130px; vertical-align: top; padding: 1px 0;">Nama</td>
                                <td style="border: none !important; width: 15px; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" class="font-bold uppercase" x-text="printBastDoc.pihak_1_nama"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">NIP</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" class="font-mono" x-text="printBastDoc.pihak_1_nip || '-'"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">Jabatan</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" x-text="printBastDoc.pihak_1_jabatan"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">Instansi / Lembaga</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" class="font-bold uppercase" x-text="printBastDoc.pihak_1_instansi"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important;"></td>
                                <td colspan="3" style="border: none !important; vertical-align: top; padding: 2px 0 6px 0;" class="italic text-[9pt]">
                                    Selanjutnya disebut sebagai <strong>PIHAK PERTAMA (Yang Menyerahkan)</strong>.
                                </td>
                            </tr>

                            <!-- PIHAK KEDUA -->
                            <tr style="border: none !important;">
                                <td style="border: none !important; width: 25px; vertical-align: top; padding: 1px 0;">2.</td>
                                <td style="border: none !important; width: 130px; vertical-align: top; padding: 1px 0;">Nama</td>
                                <td style="border: none !important; width: 15px; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" class="font-bold uppercase" x-text="printBastDoc.pihak_2_nama"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">NIP</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" class="font-mono" x-text="printBastDoc.pihak_2_nip || '-'"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">Jabatan</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" x-text="printBastDoc.pihak_2_jabatan"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">Instansi / Lembaga</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" class="font-bold uppercase" x-text="printBastDoc.pihak_2_instansi"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important;"></td>
                                <td colspan="3" style="border: none !important; vertical-align: top; padding: 2px 0 4px 0;" class="italic text-[9pt]">
                                    Selanjutnya disebut sebagai <strong>PIHAK KEDUA (Yang Menerima)</strong>.
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- KLAUSUL PERNYATAAN PENYERAHAN -->
                    <p class="text-justify mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                        <template x-if="printBastDoc.tipe_hibah === 'masuk'">
                            <span>
                                PIHAK PERTAMA menyerahkan kepada PIHAK KEDUA, dan PIHAK KEDUA menerima dari PIHAK PERTAMA Barang Milik Daerah (BMD) hasil penerimaan hibah barang dalam keadaan baik dan lengkap untuk dicatat serta dipergunakan pada RSUD dr. H. Koesnadi Kabupaten Bondowoso dengan rincian sebagai berikut :
                            </span>
                        </template>
                        <template x-if="printBastDoc.tipe_hibah === 'keluar'">
                            <span>
                                PIHAK PERTAMA menyerahkan kepada PIHAK KEDUA, dan PIHAK KEDUA menerima dari PIHAK PERTAMA Barang Milik Daerah (BMD) sebagai hibah barang dalam keadaan baik dan lengkap sesuai ketentuan perundang-undangan dengan rincian sebagai berikut :
                            </span>
                        </template>
                    </p>

                    <!-- TABEL RESMI BARANG MILIK DAERAH (BMD) -->
                    <div class="my-2.5 overflow-x-auto">
                        <table class="w-full text-center border-collapse border border-black text-[9pt] sm:text-[9.5pt]" style="border-collapse: collapse; width: 100%; border: 1px solid black; background-color: #ffffff !important; color: #000000 !important;">
                            <thead>
                                <tr style="font-weight:700; border:1px solid black; background-color:#ffffff; color:#000000 !important;">
                                    <th rowspan="2" style="border:1px solid black; padding:5px 4px; text-align:center; width:4%; font-weight:700; color:#000000;">No</th>
                                    <th rowspan="2" style="border:1px solid black; padding:5px 6px; text-align:left; width:22%; font-weight:700; color:#000000;">Nama Barang / Aset</th>
                                    <th rowspan="2" style="border:1px solid black; padding:5px 6px; text-align:left; width:18%; font-weight:700; color:#000000;">Kode 108 / NIBAR</th>
                                    <th rowspan="2" style="border:1px solid black; padding:5px 4px; text-align:center; width:6%; font-weight:700; color:#000000;">Vol</th>
                                    <th rowspan="2" style="border:1px solid black; padding:5px 4px; text-align:center; width:6%; font-weight:700; color:#000000;">Sat</th>
                                    <th colspan="3" style="border:1px solid black; padding:3px; text-align:center; width:11%; font-weight:700; color:#000000;">Kondisi</th>
                                    <th rowspan="2" style="border:1px solid black; padding:5px 6px; text-align:right; width:15%; font-weight:700; color:#000000;">Harga Satuan (Rp)</th>
                                    <th rowspan="2" style="border:1px solid black; padding:5px 6px; text-align:right; width:18%; font-weight:700; color:#000000;">Nilai Total (Rp)</th>
                                </tr>
                                <tr style="font-weight:700; border:1px solid black; background-color:#ffffff; color:#000000 !important;">
                                    <th style="border:1px solid black; padding:2px 3px; text-align:center; font-size:7.5pt; font-weight:700; color:#000000;">Baik</th>
                                    <th style="border:1px solid black; padding:2px 3px; text-align:center; font-size:7.5pt; font-weight:700; color:#000000;">KB</th>
                                    <th style="border:1px solid black; padding:2px 3px; text-align:center; font-size:7.5pt; font-weight:700; color:#000000;">RB</th>
                                </tr>
                            </thead>
                            <tbody style="background-color: #ffffff !important; color: #000000 !important;">
                                <template x-for="(sub, idx) in (printBastDoc.items || [])" :key="idx">
                                    <tr style="border: 1px solid black; background-color: #ffffff !important; color: #000000 !important;">
                                        <td class="px-1.5 py-1 text-center" style="border: 1px solid black;" x-text="idx + 1"></td>
                                        <td class="px-2 py-1 text-left font-bold" style="border: 1px solid black;" x-text="sub.nama"></td>
                                        <td class="px-2 py-1 font-mono text-[8.5pt] text-left break-all" style="border: 1px solid black;">
                                            <div x-text="sub.kode_108 || '-'"></div>
                                            <div class="text-[8pt] text-slate-700" x-text="sub.nibar && sub.nibar !== '-' ? ('NIBAR: ' + sub.nibar) : ''"></div>
                                        </td>
                                        <td class="px-1 py-1 text-center font-bold" style="border: 1px solid black;" x-text="sub.volume || 1"></td>
                                        <td class="px-1 py-1 text-center" style="border: 1px solid black;" x-text="sub.satuan || 'Unit'"></td>
                                        <td class="px-1 py-1 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(sub.kondisi === 'Baik' || !sub.kondisi) ? '✓' : ''"></td>
                                        <td class="px-1 py-1 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(sub.kondisi === 'Kurang Baik' || sub.kondisi === 'KB') ? '✓' : ''"></td>
                                        <td class="px-1 py-1 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(sub.kondisi === 'Rusak Berat' || sub.kondisi === 'RB' || sub.kondisi === 'Rusak Ringan' || sub.kondisi === 'Rusak') ? '✓' : ''"></td>
                                        <td class="px-2 py-1 text-right font-mono text-[8.5pt]" style="border: 1px solid black;" x-text="formatRupiah(sub.harga_satuan)"></td>
                                        <td class="px-2 py-1 text-right font-mono font-bold text-[8.5pt]" style="border: 1px solid black;" x-text="formatRupiah(sub.nilai_total)"></td>
                                    </tr>
                                </template>

                                <!-- Baris Total -->
                                <tr style="font-weight: 700; border: 1px solid black; background-color: #f8fafc !important; color: #000000 !important;">
                                    <td colspan="3" style="border: 1px solid black; padding: 5px 8px; text-align: right; text-transform: uppercase;">Total Nilai Hibah BMD</td>
                                    <td style="border: 1px solid black; padding: 5px 4px; text-align: center;" x-text="printBastDoc.jumlah_volume || 1"></td>
                                    <td style="border: 1px solid black; padding: 5px 4px; text-align: center;" x-text="printBastDoc.satuan || 'Unit'"></td>
                                    <td colspan="3" style="border: 1px solid black;"></td>
                                    <td colspan="2" style="border: 1px solid black; padding: 5px 8px; text-align: right; font-family: monospace; font-size: 9.5pt;" x-text="formatRupiah(printBastDoc.nilai_aset)"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- CATATAN / KETERANGAN HIBAH -->
                    <div class="my-2.5 p-2 rounded border border-black/40 text-[9pt] leading-relaxed" style="background-color: #fafafa !important;" x-show="printBastDoc.keterangan">
                        <strong>Catatan / Keterangan:</strong>
                        <span x-text="printBastDoc.keterangan || '-'"></span>
                    </div>

                    <!-- KLAUSUL PENUTUP -->
                    <p class="text-justify my-3 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                        Demikian Berita Acara Serah Terima (BAST) ini dibuat dan ditandatangani oleh kedua belah pihak dengan itikad baik dan sebenarnya dalam rangkap secukupnya untuk dipergunakan sebagaimana mestinya.
                    </p>

                    <!-- BLOK TANDA TANGAN RESMI (3 PIHAK: PENYERAH, PENERIMA, MENGETAHUI DIREKTUR) -->
                    <div class="mt-5 pt-2 space-y-4" style="color: #000000 !important;">
                        <!-- Baris Atas: Pihak Kesatu & Pihak Kedua -->
                        <div class="grid grid-cols-2 gap-8 text-center text-black text-[9.5pt] sm:text-[10pt]" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; color: #000000 !important;">
                            <!-- Kolom Kiri: PIHAK KESATU (Yang Menyerahkan) -->
                            <div>
                                <p class="m-0 font-bold">PIHAK KESATU</p>
                                <p class="m-0 text-[8.5pt] text-slate-700">Yang Menyerahkan,</p>
                                
                                <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;">
                                    <template x-if="printBastDoc.tipe_hibah === 'keluar' && printBastDoc.signed">
                                        <div style="padding:4px; border:1.5px solid #0d9488; background:#f0fdfa; border-radius:5px; display:inline-flex; align-items:center; gap:6px; text-align:left;">
                                            <img :src="getQrCodeSvg(window.location.origin + '/validasi-tte/' + (printBastDoc.qr_hash || 'BSRE-HIBAH-KOESNANDI'))" alt="QR TTE" style="width:36px; height:36px; flex-shrink:0;">
                                            <div style="font-size:7.5px; line-height:1.35; color:#1e293b;">
                                                <div style="font-weight:700; color:#134e4a;">DITANDATANGANI ELEKTRONIK</div>
                                                <div style="color:#374151;">Pengurus Barang Aset</div>
                                                <div style="font-size:6.5px; color:#6b7280; font-family:monospace;">Sertifikat BSrE - BSSN</div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <p class="font-bold underline uppercase m-0" x-text="printBastDoc.pihak_1_nama"></p>
                                <p class="m-0 font-mono text-[8.5pt]" x-text="printBastDoc.pihak_1_nip && printBastDoc.pihak_1_nip !== '-' ? ('NIP. ' + printBastDoc.pihak_1_nip) : printBastDoc.pihak_1_jabatan"></p>
                            </div>

                            <!-- Kolom Kanan: PIHAK KEDUA (Yang Menerima) -->
                            <div>
                                <p class="m-0 font-bold">PIHAK KEDUA</p>
                                <p class="m-0 text-[8.5pt] text-slate-700">Yang Menerima,</p>
                                
                                <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;">
                                    <template x-if="printBastDoc.tipe_hibah === 'masuk' && printBastDoc.signed">
                                        <div style="padding:4px; border:1.5px solid #0d9488; background:#f0fdfa; border-radius:5px; display:inline-flex; align-items:center; gap:6px; text-align:left;">
                                            <img :src="getQrCodeSvg(window.location.origin + '/validasi-tte/' + (printBastDoc.qr_hash || 'BSRE-HIBAH-KOESNANDI'))" alt="QR TTE" style="width:36px; height:36px; flex-shrink:0;">
                                            <div style="font-size:7.5px; line-height:1.35; color:#1e293b;">
                                                <div style="font-weight:700; color:#134e4a;">DITANDATANGANI ELEKTRONIK</div>
                                                <div style="color:#374151;">Pengurus Barang Aset</div>
                                                <div style="font-size:6.5px; color:#6b7280; font-family:monospace;">Sertifikat BSrE - BSSN</div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <p class="font-bold underline uppercase m-0" x-text="printBastDoc.pihak_2_nama"></p>
                                <p class="m-0 font-mono text-[8.5pt]" x-text="printBastDoc.pihak_2_nip && printBastDoc.pihak_2_nip !== '-' ? ('NIP. ' + printBastDoc.pihak_2_nip) : printBastDoc.pihak_2_jabatan"></p>
                            </div>
                        </div>

                        <!-- Baris Bawah: Mengetahui DIREKTUR RSUD Dr. H. Koesnandi -->
                        <div class="text-center text-black text-[9.5pt] sm:text-[10pt] pt-2" style="color: #000000 !important;">
                            <p class="m-0">Mengetahui,</p>
                            <p class="font-bold m-0">DIREKTUR RSUD dr. H. KOESNADI</p>
                            <p class="m-0 text-[8.5pt]">KABUPATEN BONDOWOSO</p>
                            
                            <!-- Ruang TTD Direktur (Tinggi 55px) -->
                            <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;"></div>

                            <p class="font-bold underline uppercase m-0" x-text="printBastDoc.direktur_nama || 'dr. DIAN ARISANDI, M.Kes'"></p>
                            <p class="m-0 font-mono text-[8.5pt]" x-text="'NIP. ' + (printBastDoc.direktur_nip || '19730514 200212 2 003')"></p>
                        </div>
                    </div>

                </div>
            </div>
        </template>

    </div>
</div>
