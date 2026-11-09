<!-- ========================================================================= -->
<!-- MODAL CETAK: LEMBAR DOKUMEN BAST BELANJA BARANG RESMI                       -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showModalPrintBast" 
         x-cloak 
         @click.self="showModalPrintBast = false"
         class="fixed inset-0 flex items-center justify-center p-2 sm:p-4 md:p-6 overflow-y-auto w-screen h-screen min-h-screen"
         style="background-color: rgba(2, 6, 23, 0.90); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 9500;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative max-h-[92vh] overflow-y-auto custom-scrollbar">
        
            <!-- Action Bar Modal (Hidden when Printed) -->
            <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                <div class="flex items-center space-x-2.5">
                    <span class="p-2 rounded-xl text-sm bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        🏪
                    </span>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="text-base font-extrabold text-white">Berita Acara Serah Terima (BAST) Belanja Barang</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-indigo-500/20 text-indigo-300 border border-indigo-500/40">
                                AKUN 5.1.02
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400 font-mono" x-text="printBastDoc ? ('Faktur: ' + (printBastDoc.nomor_faktur || '-') + ' · ' + (printBastDoc.toko_penyedia || '')) : ''"></p>
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
                        <a :href="'/master-data/belanja-barang/' + printBastDoc.id + '/cetak'" target="_blank"
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

            <!-- Formulir Live Edit BAST Belanja Barang (Hidden when Printed) -->
            <template x-if="printBastDoc">
                <div x-show="showEditBastForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-slate-800 text-xs space-y-3.5 shadow-inner">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="font-bold text-indigo-300 text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                            <span>✏️ Live Edit Dokumen BAST Belanja Barang:</span>
                        </span>
                        <span class="text-[10px] text-emerald-400 font-mono">Perubahan otomatis tertera pada lembar cetak</span>
                    </div>

                    <!-- Baris 1: Informasi Surat & Tanggal -->
                    <div class="grid grid-cols-2 sm:grid-cols-6 gap-2.5">
                        <div class="col-span-2">
                            <label class="block text-slate-400 text-[10px] mb-1">Nomor BAST</label>
                            <input type="text" x-model="printBastDoc.nomor_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-cyan-300 font-mono font-bold text-xs">
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
                            <label class="block text-slate-400 text-[10px] mb-1">Tahun</label>
                            <input type="text" x-model="printBastDoc.tahun" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-amber-300 font-bold text-xs">
                        </div>
                    </div>

                    <!-- Baris 2: Data Pihak Penyedia & Penerima -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-800/80">
                        <!-- Pihak 1 (Penyedia) -->
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nama Toko Rekanan (Pihak Pertama)</label>
                            <input type="text" x-model="printBastDoc.pihak_1_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-bold text-xs mb-2">
                            <label class="block text-slate-400 text-[10px] mb-1">Jabatan / Kedudukan Pihak Pertama</label>
                            <input type="text" x-model="printBastDoc.pihak_1_jabatan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-cyan-300 text-xs">
                        </div>

                        <!-- Pihak 2 (RSUD) -->
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nama Pengurus Barang (Pihak Kedua)</label>
                            <input type="text" x-model="printBastDoc.pihak_2_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-bold text-xs mb-1">
                            <div class="grid grid-cols-2 gap-2 mt-1">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-0.5">NIP Pengurus Barang</label>
                                    <input type="text" x-model="printBastDoc.pihak_2_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-slate-300 font-mono text-xs">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-0.5">Jabatan</label>
                                    <input type="text" x-model="printBastDoc.pihak_2_jabatan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-slate-300 text-xs">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- LEMBAR PREVIEW CETAK RESMI (A4 PUTIH) -->
            <template x-if="printBastDoc">
                <div class="bg-gray-200 p-3 sm:p-6 rounded-2xl border border-slate-700 flex justify-center items-start overflow-y-auto max-h-[75vh] custom-scrollbar shadow-inner">
                    <div id="print-area-bast-belanja" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 10pt; color: #000000 !important; background-color: #ffffff !important; min-height: 100%; box-sizing: border-box;" class="w-full max-w-[760px] shrink-0 bg-white text-black p-8 sm:p-12 md:p-14 shadow-2xl rounded-sm space-y-3.5 select-text print:p-0 print:m-0 print:shadow-none print:max-w-none">
                        
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
                            <h4 class="font-bold text-[10pt] sm:text-[10.5pt] uppercase tracking-normal text-black m-0 mt-0.5">
                                PENERIMAAN BARANG HASIL BELANJA PERBEKALAN / BARANG (AKUN 5.1.02)
                            </h4>
                            <p class="text-[9.5pt] sm:text-[10pt] font-semibold text-black mt-1 m-0">
                                Nomor : <span class="font-mono font-bold" x-text="printBastDoc.nomor_bast"></span>
                            </p>
                        </div>

                        <!-- KLAUSUL PEMBUKA -->
                        <p class="text-justify mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                            Pada hari ini <strong x-text="printBastDoc.hari"></strong> tanggal <strong x-text="printBastDoc.tgl_angka"></strong> bulan <strong x-text="printBastDoc.bulan"></strong> tahun <strong x-text="printBastDoc.tahun"></strong>, bertempat di Rumah Sakit Umum Daerah dr. H. Koesnandi Kabupaten Bondowoso, yang bertanda tangan di bawah ini :
                        </p>

                        <!-- IDENTITAS PIHAK KESATU & PIHAK KEDUA -->
                        <div class="space-y-2 mb-3 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                            <!-- PIHAK KESATU -->
                            <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                                <tr style="border: none !important;">
                                    <td style="border: none !important; width: 25px; vertical-align: top; padding: 1px 0;">1.</td>
                                    <td style="border: none !important; width: 150px; vertical-align: top; padding: 1px 0;">Nama Toko / Penyedia</td>
                                    <td style="border: none !important; width: 15px; vertical-align: top; padding: 1px 0;">:</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1px 0;" class="font-bold uppercase" x-text="printBastDoc.pihak_1_nama"></td>
                                </tr>
                                <tr style="border: none !important;">
                                    <td style="border: none !important;"></td>
                                    <td style="border: none !important; vertical-align: top; padding: 1px 0;">Kedudukan / Jabatan</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1px 0;">:</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1px 0;" x-text="printBastDoc.pihak_1_jabatan"></td>
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
                                    <td style="border: none !important; width: 150px; vertical-align: top; padding: 1px 0;">Nama</td>
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
                                    <td style="border: none !important; vertical-align: top; padding: 1px 0;">Instansi Penerima</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1px 0;">:</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1px 0;" class="font-semibold" x-text="printBastDoc.pihak_2_instansi"></td>
                                </tr>
                                <tr style="border: none !important;">
                                    <td style="border: none !important;"></td>
                                    <td colspan="3" style="border: none !important; vertical-align: top; padding: 2px 0 6px 0;" class="italic text-[9pt]">
                                        Selanjutnya disebut sebagai <strong>PIHAK KEDUA (Yang Menerima)</strong>.
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- KLAUSUL TRANSAKSI -->
                        <p class="text-justify mb-2.5 text-[9.5pt] leading-[1.5] text-black">
                            Berdasarkan <strong>Faktur / Nota Pembelian Nomor: <span x-text="printBastDoc.nomor_faktur"></span></strong> tanggal <strong x-text="printBastDoc.tanggal_faktur"></strong>, 
                            <strong>PIHAK PERTAMA</strong> telah menyerahkan barang perbekalan operasional kepada <strong>PIHAK KEDUA</strong>, dan <strong>PIHAK KEDUA</strong> telah menerima, memeriksa fisik, dan menguji kelayakan seluruh barang belanja tersebut dalam keadaan lengkap, baru, dan berfungsi baik dengan rincian sebagai berikut:
                        </p>

                        <!-- TABEL DAFTAR BARANG -->
                        <div class="mb-3">
                            <table class="w-full border-collapse border border-black text-[9pt]" style="border: 1px solid #000000; width: 100%;">
                                <thead>
                                    <tr class="bg-gray-100 text-center font-bold" style="background-color: #f3f4f6;">
                                        <th class="border border-black p-1 w-8">No</th>
                                        <th class="border border-black p-1 text-left">Nama Barang &amp; Kode 108</th>
                                        <th class="border border-black p-1 w-14">Vol</th>
                                        <th class="border border-black p-1 w-14">Satuan</th>
                                        <th class="border border-black p-1 w-24 text-right">Harga (Rp)</th>
                                        <th class="border border-black p-1 w-28 text-right">Subtotal (Rp)</th>
                                        <th class="border border-black p-1 w-28 text-center">Penempatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(it, idx) in (printBastDoc.items || [])" :key="idx">
                                        <tr>
                                            <td class="border border-black p-1 text-center align-top" x-text="idx + 1"></td>
                                            <td class="border border-black p-1 align-top">
                                                <div class="font-bold" x-text="it.nama_barang"></div>
                                                <div class="font-mono text-[7.5pt] text-gray-700" x-text="'Kode: ' + (it.kode_108 || '-')"></div>
                                                <template x-if="it.registers && it.registers.length > 0">
                                                    <div class="mt-0.5 pt-0.5 border-t border-gray-200 text-[7pt] font-mono text-gray-800">
                                                        <span class="font-bold">NIBAR:</span>
                                                        <span x-text="it.registers.slice(0, 3).map(r => r.nibar || r.no_register).join(', ')"></span>
                                                        <template x-if="it.registers.length > 3">
                                                            <span class="italic text-gray-600" x-text="' (+' + (it.registers.length - 3) + ' lainnya)'"></span>
                                                        </template>
                                                    </div>
                                                </template>
                                            </td>
                                            <td class="border border-black p-1 text-center font-bold align-top" x-text="it.jumlah_volume"></td>
                                            <td class="border border-black p-1 text-center align-top" x-text="it.satuan || 'Unit'"></td>
                                            <td class="border border-black p-1 text-right font-mono align-top" x-text="Number(it.harga_satuan || 0).toLocaleString('id-ID')"></td>
                                            <td class="border border-black p-1 text-right font-mono font-bold align-top" x-text="Number(it.subtotal || (it.jumlah_volume * it.harga_satuan) || 0).toLocaleString('id-ID')"></td>
                                            <td class="border border-black p-1 text-center text-[8pt] align-top" x-text="it.ruangan || 'Belum Ditempatkan'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-100 font-bold" style="background-color: #f3f4f6;">
                                        <td colspan="2" class="border border-black p-1 text-center uppercase tracking-wider">TOTAL BELANJA</td>
                                        <td class="border border-black p-1 text-center font-mono" x-text="printBastDoc.total_volume || 0"></td>
                                        <td class="border border-black p-1 text-center">Unit</td>
                                        <td class="border border-black p-1 text-center">-</td>
                                        <td class="border border-black p-1 text-right font-mono font-black text-[9.5pt]" x-text="'Rp ' + Number(printBastDoc.total_pembelian || 0).toLocaleString('id-ID')"></td>
                                        <td class="border border-black p-1 text-center text-[7.5pt] text-gray-700" x-text="printBastDoc.status_alokasi === 'ruangan' ? 'Ruangan Unit' : 'Belum Ditempatkan'"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- TERBILANG TOTAL NILAI RUPIAH -->
                        <div class="mb-2.5 p-2 bg-gray-50 border border-gray-400 text-[8.5pt] rounded-sm" style="background-color: #f9fafb; border: 1px solid #9ca3af;">
                            <span class="font-bold">Terbilang :</span>
                            <span class="italic font-serif font-bold text-gray-900" x-text="'&quot;' + (printBastDoc.terbilang || 'Nol Rupiah') + '&quot;'"></span>
                        </div>

                        <!-- CATATAN -->
                        <template x-if="printBastDoc.keterangan && printBastDoc.keterangan !== '-'">
                            <div class="mb-2 text-[8.5pt] text-gray-800">
                                <span class="font-bold">Catatan Pembelian :</span>
                                <span x-text="printBastDoc.keterangan"></span>
                            </div>
                        </template>

                        <!-- KLAUSUL PENUTUP -->
                        <p class="text-justify mb-3 text-[9pt] leading-[1.4] text-black">
                            Demikian Berita Acara Serah Terima ini dibuat dengan sebenarnya dalam rangkap secukupnya untuk dapat dipergunakan sebagaimana mestinya dan menjadi bukti pencatatan resmi Buku Inventaris Barang Milik Daerah (BMD) RSUD dr. H. Koesnandi Kabupaten Bondowoso.
                        </p>

                        <!-- BLOK TANDA TANGAN (3 PIHAK) -->
                        <div class="mt-4 pt-1">
                            <table class="w-full text-center border-none text-[9pt] leading-normal" style="border: none !important; width: 100%;">
                                <tr style="border: none !important;">
                                    <td class="w-1/2 align-top pb-2" style="border: none !important;">
                                        <p class="font-bold m-0">PIHAK PERTAMA</p>
                                        <p class="text-[8.5pt] text-gray-700 m-0">Yang Menyerahkan,</p>
                                        <p class="font-bold uppercase text-[8.5pt] m-0" x-text="printBastDoc.pihak_1_nama"></p>
                                        <div class="h-16 flex items-center justify-center">
                                            <span class="text-gray-400 text-[7.5pt] italic">(Tanda Tangan &amp; Cap Toko)</span>
                                        </div>
                                        <p class="font-bold underline uppercase m-0" x-text="printBastDoc.pihak_1_nama"></p>
                                        <p class="text-[8pt] text-gray-700 m-0" x-text="printBastDoc.pihak_1_jabatan"></p>
                                    </td>
                                    <td class="w-1/2 align-top pb-2" style="border: none !important;">
                                        <p class="font-bold m-0">PIHAK KEDUA</p>
                                        <p class="text-[8.5pt] text-gray-700 m-0">Yang Menerima,</p>
                                        <p class="font-bold text-[8.5pt] m-0" x-text="printBastDoc.pihak_2_instansi"></p>
                                        <div class="h-16 flex items-center justify-center">
                                            <template x-if="printBastDoc.signed">
                                                <div class="border border-emerald-600 bg-emerald-50/60 p-1 rounded flex items-center space-x-1.5 text-left" style="border: 1px solid #059669; background-color: #ecfdf5;">
                                                    <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=50x50&data=' + encodeURIComponent('{{ url('/validasi-tte/') }}/' + printBastDoc.nomor_bast)" alt="TTE BSrE" class="w-10 h-10">
                                                    <div class="text-[6.5pt] text-emerald-800 leading-tight">
                                                        <div class="font-bold text-[7pt]">DITANDATANGANI ELEKTRONIK</div>
                                                        <div>Balai Sertifikasi Elektronik (BSrE)</div>
                                                        <div class="font-mono text-[6pt] text-emerald-600 mt-0.5" x-text="'ID: ' + (printBastDoc.nomor_bast || '').replace(/[^a-zA-Z0-9]/g, '').slice(-8)"></div>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="!printBastDoc.signed">
                                                <span class="text-gray-400 text-[7.5pt] italic">(Tanda Tangan Pejabat)</span>
                                            </template>
                                        </div>
                                        <p class="font-bold underline uppercase m-0" x-text="printBastDoc.pihak_2_nama"></p>
                                        <p class="text-[8pt] text-gray-700 font-mono m-0" x-text="'NIP. ' + (printBastDoc.pihak_2_nip || '-')"></p>
                                        <p class="text-[8pt] text-gray-700 m-0" x-text="printBastDoc.pihak_2_jabatan"></p>
                                    </td>
                                </tr>
                                <tr style="border: none !important;">
                                    <td colspan="2" class="pt-3 align-top" style="border: none !important;">
                                        <p class="font-bold m-0">Mengetahui / Mengesahkan,</p>
                                        <p class="text-[8.5pt] text-gray-700 m-0">Pejabat Pembuat Komitmen (PPK)</p>
                                        <p class="font-bold text-[8.5pt] m-0">RSUD dr. H. Koesnandi Kabupaten Bondowoso</p>
                                        <div class="h-14 flex items-center justify-center">
                                            <span class="text-gray-400 text-[7.5pt] italic">(Tanda Tangan &amp; Stempel Basah)</span>
                                        </div>
                                        <p class="font-bold underline uppercase m-0" x-text="printBastDoc.ppk_nama"></p>
                                        <p class="text-[8pt] text-gray-700 font-mono m-0" x-text="'NIP. ' + (printBastDoc.ppk_nip || '-')"></p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>
            </template>

            <!-- Modal Footer -->
            <div class="no-print pt-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span class="flex items-center gap-1.5">
                    <span>💡</span> Dokumen BAST ini sah dan terintegrasi dengan kode rekening 108 serta register NIBAR SIMAT-RK.
                </span>
                <button type="button" @click="showModalPrintBast = false"
                    class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold transition-all cursor-pointer">
                    Tutup Preview
                </button>
            </div>

        </div>
    </div>
</template>

