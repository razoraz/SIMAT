<!-- ========================================================================= -->
<!-- MODAL CETAK 4: LEMBAR DOKUMEN BAST HIBAH ASET BMD                         -->
<!-- ========================================================================= -->
<div x-show="showPrintHibahModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak @click.self="closeHibahModal()">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
        
        <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2">
                <span class="p-2 rounded-xl bg-emerald-500/20 text-emerald-300 text-sm">🎁</span>
                <div>
                    <h3 class="text-base font-extrabold text-white">Berita Acara Serah Terima (BAST) Hibah Aset</h3>
                    <p class="text-[11px] text-slate-400" x-text="selectedHibah ? ('Nomor BAST: ' + selectedHibah.nomor_bast) : ''"></p>
                </div>
            </div>

            <div class="flex items-center space-x-2 shrink-0">
                <button type="button" @click="showEditHibahForm = !showEditHibahForm"
                    class="px-3.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all">
                    <span x-text="showEditHibahForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                </button>

                <!-- Toggle Button TTD BSrE -->
                <button type="button" @click="toggleSignHibah(selectedHibah)"
                    :class="selectedHibah && selectedHibah.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 text-slate-950 font-extrabold shadow-md'"
                    class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all active:scale-95">
                    <span x-text="selectedHibah && selectedHibah.signed ? '↩️ Batalkan TTD BSrE' : '✍️ TTD BSrE'"></span>
                </button>

                <button type="button" @click="printCurrent()" class="px-4 py-1.5 rounded-xl bg-emerald-500 text-slate-950 font-bold text-xs shadow-lg">
                    🖨️ Cetak Surat
                </button>
                <button type="button" @click="closeHibahModal()" class="p-1 rounded-lg text-slate-400 hover:text-white font-bold text-lg">&times;</button>
            </div>
        </div>

        <!-- Formulir Edit Live BAST Hibah -->
        <template x-if="selectedHibah">
            <div x-show="showEditHibahForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-emerald-500/40 text-xs space-y-3 shadow-inner">
                <div class="font-bold text-emerald-300 text-[11px] uppercase tracking-wider border-b border-slate-800 pb-2">
                    ✏️ Live Edit Berita Acara Hibah BMD (Otomatis Berubah Pada Lembar Cetak):
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-6 gap-2.5">
                    <div class="col-span-2">
                        <label class="block text-slate-400 text-[10px] mb-1">Nomor Surat BAST</label>
                        <input type="text" x-model="selectedHibah.nomor_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-emerald-300 font-mono font-bold text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Hari</label>
                        <input type="text" x-model="selectedHibah.hari" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Tgl Angka</label>
                        <input type="text" x-model="selectedHibah.tanggal_angka" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Bulan</label>
                        <input type="text" x-model="selectedHibah.bulan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Tahun</label>
                        <input type="text" x-model="selectedHibah.tahun" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-bold text-amber-400 uppercase">👤 Pihak Pemberi / Penyerah</span>
                        <div>
                            <label class="block text-slate-400 text-[9px]">Instansi / Pihak Pemberi</label>
                            <input type="text" x-model="selectedHibah.pemberi" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-white font-bold text-xs">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-slate-400 text-[9px]">Nama Pejabat</label>
                                <input type="text" x-model="selectedHibah.pemberi_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Jabatan</label>
                                <input type="text" x-model="selectedHibah.pemberi_jabatan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                            </div>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-bold text-emerald-400 uppercase">👤 Pihak Penerima Hibah</span>
                        <div>
                            <label class="block text-slate-400 text-[9px]">Nama Lengkap</label>
                            <input type="text" x-model="selectedHibah.penerima_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-white font-bold text-xs">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-slate-400 text-[9px]">NIP</label>
                                <input type="text" x-model="selectedHibah.penerima_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs font-mono">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Jabatan</label>
                                <input type="text" x-model="selectedHibah.penerima_jabatan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Lembar Cetak Dokumen A4 Resmi Hibah -->
        <template x-if="selectedHibah">
            <div id="print-area-hibah" class="print-container bg-white text-black p-8 sm:p-12 shadow-inner rounded-sm text-[10pt] leading-relaxed max-w-4xl mx-auto" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                
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
                    <h3 class="font-bold text-[12pt] uppercase underline tracking-normal m-0 text-black">BERITA ACARA SERAH TERIMA (BAST)</h3>
                    <h4 class="font-bold text-[10.5pt] uppercase tracking-normal m-0 text-black" x-text="selectedHibah.tipe_hibah === 'keluar' ? 'PENYERAHAN / PENGURANGAN HIBAH BARANG MILIK DAERAH (BMD)' : 'PENERIMAAN HIBAH BARANG MILIK DAERAH (BMD)'"></h4>
                    <p class="text-[10pt] font-semibold mt-1 text-black">Nomor : <span class="font-mono font-bold" x-text="selectedHibah.nomor_bast"></span></p>
                </div>

                <!-- Pembuka -->
                <p class="text-justify mb-3 text-[10pt]">
                    Pada hari ini <strong class="capitalize" x-text="selectedHibah.hari"></strong> tanggal <strong x-text="selectedHibah.tanggal_angka + ' ' + selectedHibah.bulan + ' ' + selectedHibah.tahun"></strong>, bertempat di Rumah Sakit Umum Daerah dr. H. Koesnandi Kabupaten Bondowoso, yang bertanda tangan di bawah ini :
                </p>

                <!-- Pihak 1 & 2 -->
                <div class="space-y-3 mb-4 text-[9.5pt]">
                    <table class="w-full">
                        <tr>
                            <td class="w-6 align-top font-bold">1.</td>
                            <td class="w-32 align-top">Nama Instansi / Pihak</td>
                            <td class="w-3 align-top">:</td>
                            <td class="align-top font-bold uppercase" x-text="selectedHibah.pemberi"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="align-top">Penanggung Jawab</td>
                            <td class="align-top">:</td>
                            <td class="align-top" x-text="selectedHibah.pemberi_nama"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="align-top">Kedudukan / Jabatan</td>
                            <td class="align-top">:</td>
                            <td class="align-top" x-text="selectedHibah.pemberi_jabatan"></td>
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
                            <td class="align-top font-bold" x-text="selectedHibah.penerima_nama"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="align-top">NIP</td>
                            <td class="align-top">:</td>
                            <td class="align-top font-mono" x-text="selectedHibah.penerima_nip && selectedHibah.penerima_nip !== '-' ? selectedHibah.penerima_nip : '-'"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="align-top">Jabatan</td>
                            <td class="align-top">:</td>
                            <td class="align-top" x-text="selectedHibah.penerima_jabatan"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="align-top">Instansi Penerima</td>
                            <td class="align-top">:</td>
                            <td class="align-top font-semibold">RSUD dr. H. Koesnandi Kabupaten Bondowoso</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="3" class="pt-0.5 italic text-slate-700">
                                Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong> (Yang Menerima).
                            </td>
                        </tr>
                    </table>
                </div>

                <p class="text-justify mb-3 text-[10pt]">
                    PIHAK PERTAMA menyerahkan kepada PIHAK KEDUA, dan PIHAK KEDUA menyatakan telah menerima dengan baik dan cukup barang hibah milik daerah sebagaimana rincian berikut:
                </p>

                <!-- Tabel Rincian Barang Hibah -->
                <div class="mb-4">
                    <table class="w-full border-collapse border border-black text-[9pt]">
                        <thead>
                            <tr class="bg-gray-100 text-black font-bold text-center">
                                <th class="border border-black px-2 py-1.5 w-8">No</th>
                                <th class="border border-black px-2 py-1.5 text-left">Nama Barang &amp; Kode 108</th>
                                <th class="border border-black px-2 py-1.5 w-24">NIBAR</th>
                                <th class="border border-black px-2 py-1.5 w-16">Kondisi</th>
                                <th class="border border-black px-2 py-1.5 w-16">Vol</th>
                                <th class="border border-black px-2 py-1.5 text-right w-24">Nilai Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(it, idx) in (selectedHibah.items || [])" :key="idx">
                                <tr>
                                    <td class="border border-black px-2 py-1 text-center" x-text="idx + 1"></td>
                                    <td class="border border-black px-2 py-1">
                                        <div class="font-bold" x-text="it.nama_barang"></div>
                                        <div class="text-[8pt] text-gray-700 font-mono" x-text="it.kode_108"></div>
                                    </td>
                                    <td class="border border-black px-2 py-1 text-center font-mono" x-text="it.nibar || '-'"></td>
                                    <td class="border border-black px-2 py-1 text-center" x-text="it.kondisi || 'Baik'"></td>
                                    <td class="border border-black px-2 py-1 text-center font-mono" x-text="(it.volume || 1) + ' ' + (it.satuan || 'Unit')"></td>
                                    <td class="border border-black px-2 py-1 text-right font-mono font-bold" x-text="'Rp ' + Number(it.nilai_total || 0).toLocaleString('id-ID')"></td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr class="font-bold bg-gray-50">
                                <td colspan="4" class="border border-black px-2 py-1.5 text-right uppercase">Total Nilai Perolehan Hibah:</td>
                                <td class="border border-black px-2 py-1.5 text-center font-mono" x-text="selectedHibah.jumlah_volume + ' ' + (selectedHibah.satuan || 'Unit')"></td>
                                <td class="border border-black px-2 py-1.5 text-right font-mono font-bold" x-text="selectedHibah.nilai_aset_format"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Klausul Penutup -->
                <p class="text-justify mb-5 text-[9.5pt]">
                    Demikian Berita Acara Serah Terima Hibah ini dibuat rangkap secukupnya dan ditandatangani untuk dipergunakan sebagaimana mestinya dan dicatatkan ke dalam Buku Inventaris Barang Milik Daerah RSUD dr. H. Koesnandi.
                </p>

                <!-- Tanda Tangan & QR Code BSrE -->
                <div class="grid grid-cols-2 gap-4 text-center text-[9.5pt]">
                    <div>
                        <p class="font-bold mb-1">PIHAK PERTAMA</p>
                        <p class="text-xs text-gray-600 mb-2">Yang Menyerahkan,</p>
                        <div class="h-16 flex items-center justify-center">
                            <span class="text-[8pt] italic text-gray-400 border border-dashed border-gray-300 px-3 py-1 rounded">Tanda Tangan &amp; Stempel</span>
                        </div>
                        <p class="font-bold underline mt-2" x-text="selectedHibah.pemberi_nama"></p>
                        <p class="text-[8.5pt]" x-text="selectedHibah.pemberi_jabatan"></p>
                    </div>

                    <div>
                        <p class="font-bold mb-1">PIHAK KEDUA</p>
                        <p class="text-xs text-gray-600 mb-2">Yang Menerima,</p>
                        <div class="h-16 flex items-center justify-center">
                            <template x-if="selectedHibah.signed">
                                <div class="flex flex-col items-center">
                                    <img :src="getQrCodeSvg(selectedHibah.qr_hash)" class="w-14 h-14 object-contain" alt="QR BSrE">
                                    <span class="text-[7.5pt] font-mono text-emerald-800 font-bold mt-0.5">TERVERIFIKASI BSrE</span>
                                </div>
                            </template>
                            <template x-if="!selectedHibah.signed">
                                <span class="text-[8pt] italic text-gray-400 border border-dashed border-gray-300 px-3 py-1 rounded">Tanda Tangan Elektronik</span>
                            </template>
                        </div>
                        <p class="font-bold underline mt-2" x-text="selectedHibah.penerima_nama"></p>
                        <p class="text-[8.5pt] font-mono" x-text="selectedHibah.penerima_nip && selectedHibah.penerima_nip !== '-' ? ('NIP. ' + selectedHibah.penerima_nip) : selectedHibah.penerima_jabatan"></p>
                    </div>

                    <div class="col-span-2 text-center mt-4">
                        <p class="font-bold mb-1">MENGETAHUI,</p>
                        <p class="text-xs text-gray-600 mb-2">DIREKTUR RSUD DR. H. KOESNADI BONDOWOSO</p>
                        <div class="h-16 flex items-center justify-center">
                            <template x-if="selectedHibah.signed">
                                <div class="flex flex-col items-center">
                                    <img :src="getQrCodeSvg(selectedHibah.qr_hash + '-DIR')" class="w-14 h-14 object-contain" alt="QR BSrE">
                                    <span class="text-[7.5pt] font-mono text-emerald-800 font-bold mt-0.5">TERVERIFIKASI BSrE</span>
                                </div>
                            </template>
                            <template x-if="!selectedHibah.signed">
                                <span class="text-[8pt] italic text-gray-400 border border-dashed border-gray-300 px-3 py-1 rounded">Tanda Tangan &amp; Cap Dinas</span>
                            </template>
                        </div>
                        <p class="font-bold underline mt-2" x-text="selectedHibah.direktur_nama || 'dr. DIAN ARISANDI, M.Kes'"></p>
                        <p class="text-[8.5pt] font-mono" x-text="'NIP. ' + (selectedHibah.direktur_nip || '19730514 200212 2 003')"></p>
                    </div>
                </div>

            </div>
        </template>

    </div>
</div>
