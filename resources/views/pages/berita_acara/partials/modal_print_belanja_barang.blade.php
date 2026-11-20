<!-- ========================================================================= -->
<!-- MODAL CETAK 6: LEMBAR DOKUMEN BAST BELANJA MODAL / BARANG (FAKTUR)        -->
<!-- ========================================================================= -->
<div x-show="showPrintBelanjaBarangModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak @click.self="closeBelanjaBarangModal()">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
        
        <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2">
                <span class="p-2 rounded-xl bg-sky-500/20 text-sky-300 text-sm">🏪</span>
                <div>
                    <h3 class="text-base font-extrabold text-white">Berita Acara Penerimaan Pengadaan Barang/Jasa</h3>
                    <p class="text-[11px] text-slate-400" x-text="selectedBelanjaBarang ? ('No. Faktur: ' + selectedBelanjaBarang.nomor_faktur + ' | Toko: ' + selectedBelanjaBarang.toko_penyedia) : ''"></p>
                </div>
            </div>

            <div class="flex items-center space-x-2 shrink-0">
                <button type="button" @click="showEditBelanjaBarangForm = !showEditBelanjaBarangForm"
                    class="px-3.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all">
                    <span x-text="showEditBelanjaBarangForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                </button>

                <button type="button" @click="printCurrent()" class="px-4 py-1.5 rounded-xl bg-sky-500 text-slate-950 font-bold text-xs shadow-lg">
                    🖨️ Cetak Surat
                </button>
                <button type="button" @click="closeBelanjaBarangModal()" class="p-1 rounded-lg text-slate-400 hover:text-white font-bold text-lg">&times;</button>
            </div>
        </div>

        <!-- Formulir Edit Live BAST Belanja Barang -->
        <template x-if="selectedBelanjaBarang">
            <div x-show="showEditBelanjaBarangForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-sky-500/40 text-xs space-y-3 shadow-inner">
                <div class="font-bold text-sky-300 text-[11px] uppercase tracking-wider border-b border-slate-800 pb-2">
                    ✏️ Live Edit Berita Acara Penerimaan Barang (Otomatis Berubah Pada Lembar Cetak):
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-6 gap-2.5">
                    <div class="col-span-2">
                        <label class="block text-slate-400 text-[10px] mb-1">Nomor BAST</label>
                        <input type="text" x-model="selectedBelanjaBarang.nomor_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-sky-300 font-mono font-bold text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Nomor Faktur</label>
                        <input type="text" x-model="selectedBelanjaBarang.nomor_faktur" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-mono text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Nomor SPK</label>
                        <input type="text" x-model="selectedBelanjaBarang.spk_nomor" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-mono text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Hari</label>
                        <input type="text" x-model="selectedBelanjaBarang.hari" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Tgl Angka</label>
                        <input type="text" x-model="selectedBelanjaBarang.tanggal_angka" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-800">
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-bold text-amber-400 uppercase">🏢 Rekanan Toko / Penyedia</span>
                        <div>
                            <label class="block text-slate-400 text-[9px]">Nama Toko / Perusahaan</label>
                            <input type="text" x-model="selectedBelanjaBarang.toko_penyedia" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-white font-bold text-xs">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-bold text-sky-400 uppercase">👤 Pengurus Barang RSUD</span>
                        <div>
                            <label class="block text-slate-400 text-[9px]">Nama Lengkap</label>
                            <input type="text" x-model="selectedBelanjaBarang.pengurus_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-white font-bold text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[9px]">NIP</label>
                            <input type="text" x-model="selectedBelanjaBarang.pengurus_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs font-mono">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-bold text-emerald-400 uppercase">👤 Pejabat Pembuat Komitmen (PPK)</span>
                        <div>
                            <label class="block text-slate-400 text-[9px]">Nama Lengkap</label>
                            <input type="text" x-model="selectedBelanjaBarang.ppk_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-white font-bold text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[9px]">NIP</label>
                            <input type="text" x-model="selectedBelanjaBarang.ppk_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs font-mono">
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Lembar Cetak Dokumen A4 Resmi Belanja Barang -->
        <template x-if="selectedBelanjaBarang">
            <div id="print-area-belanja-barang" class="print-container bg-white text-black p-8 sm:p-12 shadow-inner rounded-sm text-[10pt] leading-relaxed max-w-4xl mx-auto" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                
                <!-- Kop Surat Resmi -->
                <div class="border-b-[2.5px] border-black pb-3 mb-4" style="border-bottom: 2.5px solid #000000;">
                    <div class="flex items-center justify-between gap-4">
                        <div class="w-20 shrink-0 flex justify-center">
                            <img src="{{ asset('img/logo-bondowoso.png') }}" alt="Logo Bondowoso" class="h-16 w-16 object-contain">
                        </div>
                        <div class="flex-1 text-center text-black">
                            <h4 class="font-bold text-[11pt] sm:text-[12pt] uppercase tracking-normal leading-tight text-black m-0 p-0">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                            <h3 class="font-bold text-[12.5pt] sm:text-[13.5pt] uppercase tracking-normal leading-tight text-black mt-0.5 mb-0 p-0">RUMAH SAKIT UMUM DAERAH DR. H. KOESNADI</h3>
                            <p class="text-[8.5pt] sm:text-[9pt] italic leading-tight text-black mt-0.5 mb-0 p-0">Jl. Kapten Piere Tendean No. 3 Telp. (0332) 421974 Fax. (0332) 422311</p>
                            <p class="text-[8.5pt] sm:text-[9pt] leading-tight text-black m-0 p-0">e-mail: rsu.koesnadi@gmail.com, Website: rsudrkoesnadi.go.id</p>
                            <p class="font-bold text-[10pt] sm:text-[10.5pt] uppercase text-black mt-1 mb-0 p-0" style="letter-spacing: 0.35em;">B O N D O W O S O</p>
                        </div>
                        <div class="w-20 shrink-0 flex justify-center">
                            <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="h-16 w-16 object-contain">
                        </div>
                    </div>
                </div>

                <!-- Judul BAST -->
                <div class="text-center mb-4">
                    <h3 class="font-bold text-[12pt] uppercase underline tracking-normal m-0 text-black">BERITA ACARA SERAH TERIMA HASIL PEKERJAAN PENGADAAN</h3>
                    <h4 class="font-bold text-[10.5pt] uppercase tracking-normal m-0 text-black">PENERIMAAN BELANJA MODAL / BARANG OPERASIONAL RSUD</h4>
                    <p class="text-[10pt] font-semibold mt-1 text-black">Nomor : <span class="font-mono font-bold" x-text="selectedBelanjaBarang.nomor_bast"></span></p>
                </div>

                <!-- Pembuka -->
                <p class="text-justify mb-3 text-[10pt]">
                    Pada hari ini <strong class="capitalize" x-text="selectedBelanjaBarang.hari"></strong> tanggal <strong x-text="selectedBelanjaBarang.tanggal_angka + ' ' + selectedBelanjaBarang.bulan + ' ' + selectedBelanjaBarang.tahun"></strong>, bertempat di Kantor Pengurus Barang RSUD dr. H. Koesnandi Kabupaten Bondowoso, berdasarkan Faktur Pembelian Nomor: <span class="font-mono font-bold" x-text="selectedBelanjaBarang.nomor_faktur"></span> dan SPK: <span class="font-mono font-bold" x-text="selectedBelanjaBarang.spk_nomor"></span>, kami yang bertanda tangan di bawah ini:
                </p>

                <!-- Pihak 1 & 2 -->
                <div class="space-y-3 mb-4 text-[9.5pt]">
                    <table class="w-full">
                        <tr>
                            <td class="w-6 align-top font-bold">1.</td>
                            <td class="w-32 align-top">Nama Rekanan / Toko</td>
                            <td class="w-3 align-top">:</td>
                            <td class="align-top font-bold uppercase" x-text="selectedBelanjaBarang.toko_penyedia"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="align-top">Kedudukan</td>
                            <td class="align-top">:</td>
                            <td class="align-top">Penyedia Barang / Jasa Rekanan RSUD</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="3" class="pt-0.5 italic text-slate-700">
                                Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong> (Yang Menyerahkan).
                            </td>
                        </tr>
                    </table>

                    <table class="w-full mt-2">
                        <tr>
                            <td class="w-6 align-top font-bold">2.</td>
                            <td class="w-32 align-top">Nama</td>
                            <td class="w-3 align-top">:</td>
                            <td class="align-top font-bold" x-text="selectedBelanjaBarang.pengurus_nama || 'BUDI HARTONO, S.Sos'"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="align-top">NIP</td>
                            <td class="align-top">:</td>
                            <td class="align-top font-mono" x-text="selectedBelanjaBarang.pengurus_nip || '19760229 200801 1 010'"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="align-top">Jabatan</td>
                            <td class="align-top">:</td>
                            <td class="align-top font-semibold">Pengurus Barang Pengguna RSUD dr. H. Koesnandi</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="3" class="pt-0.5 italic text-slate-700">
                                Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong> (Yang Menerima dan Memeriksa Hasil Pekerjaan).
                            </td>
                        </tr>
                    </table>
                </div>

                <p class="text-justify mb-3 text-[10pt]">
                    Menyatakan bahwa PIHAK PERTAMA telah menyerahkan hasil pengadaan barang dan PIHAK KEDUA telah memeriksa serta menerima barang tersebut dalam keadaan 100% baru, baik, dan lengkap sesuai spesifikasi faktur pembelian:
                </p>

                <!-- Tabel Rincian Barang Faktur -->
                <div class="mb-4">
                    <table class="w-full border-collapse border border-black text-[9pt]">
                        <thead>
                            <tr class="bg-gray-100 text-black font-bold text-center">
                                <th class="border border-black px-2 py-1.5 w-8">No</th>
                                <th class="border border-black px-2 py-1.5 text-left">Nama Barang &amp; Kode 108</th>
                                <th class="border border-black px-2 py-1.5 text-left">Spesifikasi / Jenis</th>
                                <th class="border border-black px-2 py-1.5 w-16">Vol</th>
                                <th class="border border-black px-2 py-1.5 text-right w-24">Harga Satuan</th>
                                <th class="border border-black px-2 py-1.5 text-right w-28">Nilai Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(it, idx) in (selectedBelanjaBarang.items || [])" :key="idx">
                                <tr>
                                    <td class="border border-black px-2 py-1 text-center" x-text="idx + 1"></td>
                                    <td class="border border-black px-2 py-1">
                                        <div class="font-bold" x-text="it.nama_barang"></div>
                                        <div class="text-[8pt] text-gray-700 font-mono" x-text="it.kode_108"></div>
                                    </td>
                                    <td class="border border-black px-2 py-1">
                                        <div x-text="it.spesifikasi || '-'"></div>
                                        <div class="text-[8pt] text-gray-700" x-text="it.jenis_nama"></div>
                                    </td>
                                    <td class="border border-black px-2 py-1 text-center font-mono" x-text="(it.volume || 1) + ' ' + (it.satuan || 'Unit')"></td>
                                    <td class="border border-black px-2 py-1 text-right font-mono" x-text="'Rp ' + Number(it.harga_satuan || 0).toLocaleString('id-ID')"></td>
                                    <td class="border border-black px-2 py-1 text-right font-mono font-bold" x-text="'Rp ' + Number(it.nilai_total || 0).toLocaleString('id-ID')"></td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr class="font-bold bg-gray-50">
                                <td colspan="5" class="border border-black px-2 py-1.5 text-right uppercase">Total Nilai Pembelian Faktur:</td>
                                <td class="border border-black px-2 py-1.5 text-right font-mono font-bold" x-text="selectedBelanjaBarang.total_pembelian_format"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Terbilang Box -->
                <div class="p-2.5 border border-black bg-gray-50 rounded-sm mb-4 text-[9pt]">
                    <span class="font-bold uppercase tracking-wider text-[8pt] text-gray-700 block">Terbilang:</span>
                    <p class="font-bold italic mt-0.5 text-black" x-text="'# ' + selectedBelanjaBarang.terbilang + ' #'"></p>
                </div>

                <!-- Klausul Penutup -->
                <p class="text-justify mb-5 text-[9.5pt]">
                    Demikian Berita Acara Penerimaan Hasil Pengadaan Barang/Jasa ini dibuat dalam rangkap yang cukup untuk digunakan sebagai kelengkapan dokumen Surat Pertanggungjawaban (SPJ) dan pencatatan penatausahaan aset pada RSUD dr. H. Koesnandi.
                </p>

                <!-- Tanda Tangan 3 Kolom & QR Code BSrE -->
                <div class="grid grid-cols-3 gap-3 text-center text-[9pt]">
                    <div>
                        <p class="font-bold mb-1">PIHAK PERTAMA</p>
                        <p class="text-[8pt] text-gray-600 mb-2" x-text="selectedBelanjaBarang.toko_penyedia"></p>
                        <div class="h-16 flex items-center justify-center">
                            <span class="text-[8pt] italic text-gray-400 border border-dashed border-gray-300 px-3 py-1 rounded">Tanda Tangan &amp; Stempel</span>
                        </div>
                        <p class="font-bold underline mt-2" x-text="selectedBelanjaBarang.toko_penyedia"></p>
                        <p class="text-[8pt]">Penyedia Rekanan</p>
                    </div>

                    <div>
                        <p class="font-bold mb-1">PIHAK KEDUA</p>
                        <p class="text-[8pt] text-gray-600 mb-2">Pengurus Barang RSUD,</p>
                        <div class="h-16 flex items-center justify-center">
                            <div class="flex flex-col items-center">
                                <img :src="getQrCodeSvg(selectedBelanjaBarang.qr_hash)" class="w-14 h-14 object-contain" alt="QR BSrE">
                                <span class="text-[7.5pt] font-mono text-emerald-800 font-bold mt-0.5">TERVERIFIKASI BSrE</span>
                            </div>
                        </div>
                        <p class="font-bold underline mt-2" x-text="selectedBelanjaBarang.pengurus_nama || 'BUDI HARTONO, S.Sos'"></p>
                        <p class="text-[8pt] font-mono" x-text="'NIP. ' + (selectedBelanjaBarang.pengurus_nip || '19760229 200801 1 010')"></p>
                    </div>

                    <div>
                        <p class="font-bold mb-1">MENGETAHUI,</p>
                        <p class="text-[8pt] text-gray-600 mb-2">Pejabat Pembuat Komitmen (PPK)</p>
                        <div class="h-16 flex items-center justify-center">
                            <div class="flex flex-col items-center">
                                <img :src="getQrCodeSvg(selectedBelanjaBarang.qr_hash + '-PPK')" class="w-14 h-14 object-contain" alt="QR BSrE">
                                <span class="text-[7.5pt] font-mono text-emerald-800 font-bold mt-0.5">TERVERIFIKASI BSrE</span>
                            </div>
                        </div>
                        <p class="font-bold underline mt-2" x-text="selectedBelanjaBarang.ppk_nama || 'dr. YUS PRIYATNA ADRYANTO, Sp.P, FISR.'"></p>
                        <p class="text-[8pt] font-mono" x-text="'NIP. ' + (selectedBelanjaBarang.ppk_nip || '19771002 200604 1 006')"></p>
                    </div>
                </div>

            </div>
        </template>

    </div>
</div>
