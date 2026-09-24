<!-- ========================================================================= -->
<!-- MODAL CETAK: LEMBAR DOKUMEN BAST PELIMPAHAN BMD (MUTASI EKSTERNAL)       -->
<!-- ========================================================================= -->
<div x-show="showPrintModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak @click.self="showPrintModal = false">
    <div @click.away="showPrintModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
        
        <!-- Action Bar Modal (Hidden when Printed) -->
        <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2">
                <span class="p-2 rounded-xl bg-purple-500/20 text-purple-300 text-sm">🏛️</span>
                <div>
                    <h3 class="text-base font-extrabold text-white">Berita Acara Serah Terima (BAST) Pelimpahan BMD</h3>
                    <p class="text-[11px] text-slate-400 font-mono" x-text="printDoc ? ('Nomor BAST: ' + (printDoc.nomor_bast || printDoc.kode || '-')) : ''"></p>
                </div>
            </div>

            <div class="flex items-center space-x-2 shrink-0">
                <!-- Toggle Form Edit -->
                <button type="button" @click="showEditForm = !showEditForm"
                    class="px-3.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all active:scale-95 cursor-pointer">
                    <span x-text="showEditForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                </button>

                <!-- Toggle TTD BSrE -->
                <button type="button" @click="toggleSign(printDoc)"
                    :class="printDoc && printDoc.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 text-slate-950 font-extrabold shadow-md'"
                    class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all active:scale-95 cursor-pointer">
                    <span x-text="printDoc && printDoc.signed ? '↩️ Batalkan TTD BSrE' : '✍️ TTD BSrE'"></span>
                </button>

                <!-- Tombol Cetak -->
                <button type="button" @click="printCurrent()"
                    class="px-4 py-1.5 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-1.5 active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>🖨️ Cetak Surat</span>
                </button>

                <!-- Tutup Modal -->
                <button type="button" @click="showPrintModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-white font-bold text-lg cursor-pointer">&times;</button>
            </div>
        </div>

        <!-- Formulir Live Edit BAST Mutasi Eksternal (Hidden when Printed) -->
        <template x-if="printDoc">
            <div x-show="showEditForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-purple-500/40 text-xs space-y-3.5 shadow-inner">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="font-bold text-purple-300 text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                        <span>✏️ Live Edit BAST Pelimpahan BMD (Otomatis Berubah Pada Lembar Cetak):</span>
                    </span>
                    <span class="text-[10px] text-emerald-400 font-mono">Teks di kertas cetak otomatis sinkron</span>
                </div>

                <!-- Baris 1: Informasi Dokumen & Tanggal -->
                <div class="grid grid-cols-2 sm:grid-cols-6 gap-2.5">
                    <div class="col-span-2">
                        <label class="block text-slate-400 text-[10px] mb-1">Nomor BAST Pelimpahan</label>
                        <input type="text" x-model="printDoc.nomor_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-purple-300 font-mono font-bold text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Hari</label>
                        <input type="text" x-model="printDoc.hari" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Tgl</label>
                        <input type="text" x-model="printDoc.tgl_angka" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Bulan</label>
                        <input type="text" x-model="printDoc.bulan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Tahun Anggaran</label>
                        <input type="text" x-model="printDoc.tahun_anggaran" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-amber-300 font-bold text-xs">
                    </div>
                </div>

                <!-- Baris 2: Pihak Kesatu (Yang Menyerahkan / OPD Luar) -->
                <div class="pt-2 border-t border-slate-800/80">
                    <span class="text-[10.5px] font-bold text-slate-300 uppercase tracking-wider block mb-2">1. Pihak Kesatu (Yang Menyerahkan / SKPD Pengirim):</span>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5">
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-slate-400 text-[10px] mb-1">Nama Instansi / OPD Pengirim</label>
                            <input type="text" x-model="printDoc.opd_asal" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-cyan-300 font-bold text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nama Pejabat Penyerah</label>
                            <input type="text" x-model="printDoc.pj_asal_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-bold text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">NIP Pejabat Penyerah</label>
                            <input type="text" x-model="printDoc.pj_asal_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-slate-200 font-mono text-xs">
                        </div>
                    </div>
                </div>

                <!-- Baris 3: Pihak Kedua (Yang Menerima / RSUD Koesnandi) & Direktur -->
                <div class="pt-2 border-t border-slate-800/80">
                    <span class="text-[10.5px] font-bold text-slate-300 uppercase tracking-wider block mb-2">2. Pihak Kedua (Yang Menerima / RSUD) & Pejabat Pengesah:</span>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5">
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nama Pengurus Barang (Penerima)</label>
                            <input type="text" x-model="printDoc.pj_tujuan_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-emerald-300 font-bold text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">NIP Pengurus Barang</label>
                            <input type="text" x-model="printDoc.pj_tujuan_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-slate-200 font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nama Direktur RSUD</label>
                            <input type="text" x-model="printDoc.direktur_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-bold text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">NIP Direktur RSUD</label>
                            <input type="text" x-model="printDoc.direktur_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-slate-200 font-mono text-xs">
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- LEMBAR PREVIEW CETAK RESMI (A4 PUTIH) -->
        <template x-if="printDoc">
            <div class="bg-gray-200 p-3 sm:p-6 rounded-2xl border border-slate-700 flex justify-center items-start overflow-y-auto max-h-[75vh] custom-scrollbar shadow-inner">
                <div id="print-area-bast-eksternal" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 10pt; color: #000000 !important; background-color: #ffffff !important; min-height: 100%; box-sizing: border-box;" class="w-full max-w-[760px] shrink-0 bg-white text-black p-8 sm:p-12 md:p-14 shadow-2xl rounded-sm space-y-3.5 select-text print:p-0 print:m-0 print:shadow-none print:max-w-none">
                    
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
                        <h3 class="font-bold text-[11pt] sm:text-[11.5pt] uppercase underline tracking-normal text-black m-0">BERITA ACARA SERAH TERIMA BARANG MILIK DAERAH (BMD)</h3>
                        <p class="text-[9.5pt] sm:text-[10pt] font-semibold text-black mt-1 m-0">
                            Nomor : <span x-text="printDoc.nomor_bast || printDoc.kode"></span>
                        </p>
                    </div>

                    <!-- KLAUSUL PEMBUKA -->
                    <p class="text-justify mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                        Pada hari ini <strong x-text="printDoc.hari"></strong> tanggal <strong x-text="printDoc.tgl_angka"></strong> bulan <strong x-text="printDoc.bulan"></strong> tahun <strong x-text="printDoc.tahun"></strong>, bertempat di Rumah Sakit Umum Daerah dr. H. Koesnandi Kabupaten Bondowoso, kami yang bertanda tangan di bawah ini :
                    </p>

                    <!-- IDENTITAS PIHAK KESATU & PIHAK KEDUA -->
                    <div class="space-y-2 mb-3 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                        <!-- PIHAK KESATU -->
                        <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                            <tr style="border: none !important;">
                                <td style="border: none !important; width: 25px; vertical-align: top; padding: 1px 0;">1.</td>
                                <td style="border: none !important; width: 130px; vertical-align: top; padding: 1px 0;">Nama</td>
                                <td style="border: none !important; width: 15px; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" class="font-bold uppercase" x-text="printDoc.pj_asal_nama"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">NIP</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" class="font-mono" x-text="printDoc.pj_asal_nip || '-'"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">Jabatan</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" x-text="printDoc.pj_asal_jabatan || 'Pengurus Barang / PPK Asal'"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">Instansi / OPD</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" class="font-bold uppercase text-purple-950" x-text="printDoc.opd_asal"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important;"></td>
                                <td colspan="3" style="border: none !important; vertical-align: top; padding: 2px 0 6px 0;" class="italic text-[9pt]">
                                    Selanjutnya disebut sebagai <strong>PIHAK KESATU (Yang Menyerahkan)</strong>.
                                </td>
                            </tr>

                            <!-- PIHAK KEDUA -->
                            <tr style="border: none !important;">
                                <td style="border: none !important; width: 25px; vertical-align: top; padding: 1px 0;">2.</td>
                                <td style="border: none !important; width: 130px; vertical-align: top; padding: 1px 0;">Nama</td>
                                <td style="border: none !important; width: 15px; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" class="font-bold uppercase" x-text="printDoc.pj_tujuan_nama"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">NIP</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" class="font-mono" x-text="printDoc.pj_tujuan_nip"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">Jabatan</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" x-text="printDoc.pj_tujuan_jabatan"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">Instansi / Unit</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1px 0;" class="font-bold">RSUD dr. H. Koesnandi Kabupaten Bondowoso</td>
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
                        PIHAK KESATU menyerahkan kepada PIHAK KEDUA, dan PIHAK KEDUA menerima dari PIHAK KESATU Barang Milik Daerah (BMD) hasil pelimpahan / mutasi eksternal antar-OPD dengan rincian sebagai berikut :
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
                                <template x-for="(sub, idx) in (printDoc.items || [])" :key="idx">
                                    <tr style="border: 1px solid black; background-color: #ffffff !important; color: #000000 !important;">
                                        <td class="px-1.5 py-1 text-center" style="border: 1px solid black;" x-text="idx + 1"></td>
                                        <td class="px-2 py-1 text-left font-bold" style="border: 1px solid black;" x-text="sub.nama_barang || printDoc.nama_barang"></td>
                                        <td class="px-2 py-1 font-mono text-[8.5pt] text-left break-all" style="border: 1px solid black;">
                                            <div x-text="sub.kode_108 || printDoc.kode_108 || '-'"></div>
                                            <div class="text-[8pt] text-slate-700" x-text="sub.nibar ? ('NIBAR: ' + sub.nibar) : ''"></div>
                                        </td>
                                        <td class="px-1 py-1 text-center font-bold" style="border: 1px solid black;" x-text="sub.volume || sub.qty || 1"></td>
                                        <td class="px-1 py-1 text-center" style="border: 1px solid black;" x-text="sub.satuan || printDoc.satuan || 'Unit'"></td>
                                        <td class="px-1 py-1 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(sub.kondisi === 'Baik' || !sub.kondisi) ? '✓' : ''"></td>
                                        <td class="px-1 py-1 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(sub.kondisi === 'Kurang Baik' || sub.kondisi === 'KB') ? '✓' : ''"></td>
                                        <td class="px-1 py-1 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(sub.kondisi === 'Rusak Berat' || sub.kondisi === 'RB' || sub.kondisi === 'Rusak Ringan' || sub.kondisi === 'Rusak') ? '✓' : ''"></td>
                                        <td class="px-2 py-1 text-right font-mono text-[8.5pt]" style="border: 1px solid black;" x-text="formatRupiah(sub.harga_satuan || printDoc.harga_satuan)"></td>
                                        <td class="px-2 py-1 text-right font-mono font-bold text-[8.5pt]" style="border: 1px solid black;" x-text="formatRupiah(sub.nilai_total || (sub.volume ? sub.volume * (sub.harga_satuan || printDoc.harga_satuan) : printDoc.total_realisasi_num || printDoc.nilai_perolehan))"></td>
                                    </tr>
                                </template>

                                <!-- Baris Total -->
                                <tr style="font-weight: 700; border: 1px solid black; background-color: #f8fafc !important; color: #000000 !important;">
                                    <td colspan="3" style="border: 1px solid black; padding: 5px 8px; text-align: right; text-transform: uppercase;">Total Aset Yang Diserahkan</td>
                                    <td style="border: 1px solid black; padding: 5px 4px; text-align: center;" x-text="printDoc.jumlah_volume || printDoc.items?.length || 1"></td>
                                    <td style="border: 1px solid black; padding: 5px 4px; text-align: center;" x-text="printDoc.satuan || 'Unit'"></td>
                                    <td colspan="3" style="border: 1px solid black;"></td>
                                    <td colspan="2" style="border: 1px solid black; padding: 5px 8px; text-align: right; font-family: monospace; font-size: 9.5pt;" x-text="formatRupiah(printDoc.total_realisasi_num || printDoc.nilai_perolehan)"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ALASAN / MAKSUD PELIMPAHAN -->
                    <div class="my-2.5 p-2 rounded border border-black/40 text-[9pt] leading-relaxed" style="background-color: #fafafa !important;">
                        <strong>Maksud / Catatan Pelimpahan:</strong>
                        <span x-text="printDoc.alasan_mutasi || 'Pelimpahan aset barang milik daerah dari SKPD luar ke RSUD dr. H. Koesnandi Kabupaten Bondowoso.'"></span>
                    </div>

                    <!-- KLAUSUL PENUTUP -->
                    <p class="text-justify my-3 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                        Demikian Berita Acara Serah Terima Barang Milik Daerah ini dibuat dengan sebenarnya dalam rangkap secukupnya untuk dipergunakan sebagaimana mestinya.
                    </p>

                    <!-- BLOK TANDA TANGAN RESMI (3 PIHAK: PENYERAH, PENERIMA BSrE, MENGETAHUI DIREKTUR) -->
                    <div class="mt-5 pt-2 space-y-4" style="color: #000000 !important;">
                        <!-- Baris Atas: Pihak Kesatu & Pihak Kedua -->
                        <div class="grid grid-cols-2 gap-8 text-center text-black text-[9.5pt] sm:text-[10pt]" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; color: #000000 !important;">
                            <!-- Kolom Kiri: PIHAK KESATU (Yang Menyerahkan - OPD Luar) -->
                            <div>
                                <p class="m-0">Yang Menyerahkan,</p>
                                <p class="font-bold m-0" x-text="printDoc.opd_asal"></p>
                                
                                <!-- Ruang TTD Basah (Tinggi 55px) -->
                                <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;"></div>

                                <p class="font-bold underline uppercase m-0" x-text="printDoc.pj_asal_nama"></p>
                                <p class="m-0" x-text="printDoc.pj_asal_nip && printDoc.pj_asal_nip !== '-' ? 'NIP. ' + printDoc.pj_asal_nip : ''"></p>
                            </div>

                            <!-- Kolom Kanan: PIHAK KEDUA (Yang Menerima - Pengurus Barang RSUD) -->
                            <div>
                                <p class="m-0">Yang Menerima,</p>
                                <p class="font-bold m-0">Pengurus Barang Aset RSUD</p>
                                
                                <!-- TTD Elektronik BSrE Pengurus Barang (Pak Budi Hartono) -->
                                <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;">
                                    <template x-if="printDoc.signed !== false">
                                        <div style="padding:4px; border:1.5px solid #0d9488; background:#f0fdfa; border-radius:5px; display:inline-flex; align-items:center; gap:6px; text-align:left;">
                                            <img :src="getQrCodeSvg(window.location.origin + '/validasi-tte/' + (printDoc.qr_hash || printDoc.nomor_bast || 'BSRE-PELIMPAHAN-BMD'))" alt="QR TTE" style="width:36px; height:36px; flex-shrink:0;">
                                            <div style="font-size:7.5px; line-height:1.35; color:#1e293b;">
                                                <div style="font-weight:700; color:#134e4a;">DITANDATANGANI ELEKTRONIK</div>
                                                <div style="color:#374151;">Pengurus Barang Aset</div>
                                                <div style="font-size:6.5px; color:#6b7280; font-family:monospace;">Sertifikat BSrE - BSSN</div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="printDoc.signed === false">
                                        <div style="padding:4px 10px; border:1.5px dashed #d97706; background:#fffbeb; border-radius:5px; text-align:center; color:#92400e; display:inline-block;">
                                            <span style="font-size:8px; font-weight:700; font-style:italic;">( Menunggu Pengesahan TTD BSrE )</span>
                                        </div>
                                    </template>
                                </div>

                                <p class="font-bold underline uppercase m-0" x-text="printDoc.pj_tujuan_nama"></p>
                                <p class="m-0" x-text="'NIP. ' + printDoc.pj_tujuan_nip"></p>
                            </div>
                        </div>

                        <!-- Baris Bawah: Mengetahui DIREKTUR RSUD Dr. H. Koesnandi -->
                        <div class="text-center text-black text-[9.5pt] sm:text-[10pt] pt-2" style="color: #000000 !important;">
                            <p class="m-0">Mengetahui,</p>
                            <p class="font-bold m-0">DIREKTUR RSUD dr. H. KOESNANDI</p>
                            <p class="m-0 text-[8.5pt]">KABUPATEN BONDOWOSO</p>
                            
                            <!-- Ruang TTD Direktur (Tinggi 55px) -->
                            <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;"></div>

                            <p class="font-bold underline uppercase m-0" x-text="printDoc.direktur_nama"></p>
                            <p class="m-0" x-text="'NIP. ' + printDoc.direktur_nip"></p>
                        </div>
                    </div>

                </div>
            </div>
        </template>

    </div>
</div>
