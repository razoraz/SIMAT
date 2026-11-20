<!-- ========================================================================= -->
<!-- MODAL CETAK 5: LEMBAR DOKUMEN BAST PEMANFAATAN KEMITRAAN (AKUN 1.5.2)      -->
<!-- ========================================================================= -->
<div x-show="showPrintKemitraanModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak @click.self="closeKemitraanModal()">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
        
        <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2">
                <span class="p-2 rounded-xl bg-amber-500/20 text-amber-300 text-sm">🤝</span>
                <div>
                    <h3 class="text-base font-extrabold text-white">Berita Acara Pemanfaatan Kemitraan (Akun 1.5.2)</h3>
                    <p class="text-[11px] text-slate-400" x-text="selectedKemitraan ? ('No. BAST: ' + selectedKemitraan.nomor_bast + ' | PKS: ' + selectedKemitraan.nomor_pks) : ''"></p>
                </div>
            </div>

            <div class="flex items-center space-x-2 shrink-0">
                <button type="button" @click="showEditKemitraanForm = !showEditKemitraanForm"
                    class="px-3.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all">
                    <span x-text="showEditKemitraanForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                </button>

                <!-- Toggle Button TTD BSrE -->
                <button type="button" @click="toggleSignKemitraan(selectedKemitraan)"
                    :class="selectedKemitraan && selectedKemitraan.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-amber-500 text-slate-950 font-extrabold shadow-md'"
                    class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all active:scale-95">
                    <span x-text="selectedKemitraan && selectedKemitraan.signed ? '↩️ Batalkan TTD BSrE' : '✍️ TTD BSrE'"></span>
                </button>

                <button type="button" @click="printCurrent()" class="px-4 py-1.5 rounded-xl bg-amber-500 text-slate-950 font-bold text-xs shadow-lg">
                    🖨️ Cetak Surat
                </button>
                <button type="button" @click="closeKemitraanModal()" class="p-1 rounded-lg text-slate-400 hover:text-white font-bold text-lg">&times;</button>
            </div>
        </div>

        <!-- Formulir Edit Live BAST Kemitraan -->
        <template x-if="selectedKemitraan">
            <div x-show="showEditKemitraanForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-amber-500/40 text-xs space-y-3 shadow-inner">
                <div class="font-bold text-amber-300 text-[11px] uppercase tracking-wider border-b border-slate-800 pb-2">
                    ✏️ Live Edit Berita Acara Kemitraan (Otomatis Berubah Pada Lembar Cetak):
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-6 gap-2.5">
                    <div class="col-span-2">
                        <label class="block text-slate-400 text-[10px] mb-1">Nomor Surat BAST</label>
                        <input type="text" x-model="selectedKemitraan.nomor_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-amber-300 font-mono font-bold text-xs">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-slate-400 text-[10px] mb-1">Nomor Perjanjian Kerja Sama (PKS)</label>
                        <input type="text" x-model="selectedKemitraan.nomor_pks" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-mono text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Hari</label>
                        <input type="text" x-model="selectedKemitraan.hari" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1">Tgl Angka</label>
                        <input type="text" x-model="selectedKemitraan.tanggal_angka" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-bold text-cyan-400 uppercase">👤 Pihak Pertama (RSUD Koesnandi)</span>
                        <div>
                            <label class="block text-slate-400 text-[9px]">Direktur RSUD</label>
                            <input type="text" x-model="selectedKemitraan.direktur_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-white font-bold text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[9px]">NIP Direktur</label>
                            <input type="text" x-model="selectedKemitraan.direktur_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs font-mono">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-bold text-amber-400 uppercase">👤 Pihak Kedua (Mitra Kerja Sama)</span>
                        <div>
                            <label class="block text-slate-400 text-[9px]">Nama Perusahaan / Rekanan</label>
                            <input type="text" x-model="selectedKemitraan.mitra_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-white font-bold text-xs">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-slate-400 text-[9px]">Pimpinan / Kuasa</label>
                                <input type="text" x-model="selectedKemitraan.mitra_pimpinan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Alamat Mitra</label>
                                <input type="text" x-model="selectedKemitraan.mitra_alamat" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Lembar Cetak Dokumen A4 Resmi Kemitraan -->
        <template x-if="selectedKemitraan">
            <div id="print-area-kemitraan" class="print-container bg-white text-black p-8 sm:p-12 shadow-inner rounded-sm text-[10pt] leading-relaxed max-w-4xl mx-auto" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                
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
                    <h3 class="font-bold text-[12pt] uppercase underline tracking-normal m-0 text-black">BERITA ACARA SERAH TERIMA PEMANFAATAN BMD</h3>
                    <h4 class="font-bold text-[10.5pt] uppercase tracking-normal m-0 text-black" x-text="'KERJA SAMA ' + (selectedKemitraan.tipe_label || 'SEWA BMD') + ' (AKUN 1.5.2)'"></h4>
                    <p class="text-[10pt] font-semibold mt-1 text-black">Nomor : <span class="font-mono font-bold" x-text="selectedKemitraan.nomor_bast"></span></p>
                </div>

                <!-- Pembuka -->
                <p class="text-justify mb-3 text-[10pt]">
                    Pada hari ini <strong class="capitalize" x-text="selectedKemitraan.hari"></strong> tanggal <strong x-text="selectedKemitraan.tanggal_angka + ' ' + selectedKemitraan.bulan + ' ' + selectedKemitraan.tahun"></strong>, bertempat di RSUD dr. H. Koesnandi Kabupaten Bondowoso, berdasarkan Perjanjian Kerja Sama Nomor: <span class="font-mono font-bold" x-text="selectedKemitraan.nomor_pks"></span>, yang bertanda tangan di bawah ini :
                </p>

                <!-- Pihak 1 & 2 -->
                <div class="space-y-3 mb-4 text-[9.5pt]">
                    <table class="w-full">
                        <tr>
                            <td class="w-6 align-top font-bold">1.</td>
                            <td class="w-32 align-top">Nama</td>
                            <td class="w-3 align-top">:</td>
                            <td class="align-top font-bold" x-text="selectedKemitraan.direktur_nama || 'dr. DIAN ARISANDI, M.Kes'"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="align-top">NIP</td>
                            <td class="align-top">:</td>
                            <td class="align-top font-mono" x-text="selectedKemitraan.direktur_nip || '19730514 200212 2 003'"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="align-top">Jabatan</td>
                            <td class="align-top">:</td>
                            <td class="align-top font-semibold">Direktur RSUD dr. H. Koesnandi Kabupaten Bondowoso</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="3" class="pt-0.5 italic text-slate-700">
                                Bertindak untuk dan atas nama Pemerintah Daerah Kabupaten Bondowoso (RSUD dr. H. Koesnandi), selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.
                            </td>
                        </tr>
                    </table>

                    <table class="w-full mt-2">
                        <tr>
                            <td class="w-6 align-top font-bold">2.</td>
                            <td class="w-32 align-top">Nama Mitra</td>
                            <td class="w-3 align-top">:</td>
                            <td class="align-top font-bold uppercase" x-text="selectedKemitraan.mitra_nama"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="align-top">Pimpinan / Kuasa</td>
                            <td class="align-top">:</td>
                            <td class="align-top" x-text="selectedKemitraan.mitra_pimpinan"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="align-top">Alamat Domisili</td>
                            <td class="align-top">:</td>
                            <td class="align-top" x-text="selectedKemitraan.mitra_alamat"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="3" class="pt-0.5 italic text-slate-700">
                                Bertindak untuk dan atas nama mitra kerja sama pihak ketiga, selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.
                            </td>
                        </tr>
                    </table>
                </div>

                <p class="text-justify mb-3 text-[10pt]">
                    PIHAK PERTAMA menyerahkan kepada PIHAK KEDUA hak pemanfaatan Barang Milik Daerah (BMD) dan PIHAK KEDUA menerima dengan rincian objek pemanfaatan sebagai berikut:
                </p>

                <!-- Tabel Objek Kemitraan -->
                <div class="mb-4">
                    <table class="w-full border-collapse border border-black text-[9pt]">
                        <thead>
                            <tr class="bg-gray-100 text-black font-bold text-center">
                                <th class="border border-black px-2 py-1.5 w-8">No</th>
                                <th class="border border-black px-2 py-1.5 text-left">Objek BMD / Aset Kemitraan</th>
                                <th class="border border-black px-2 py-1.5 w-32">Kode 108 / Akun</th>
                                <th class="border border-black px-2 py-1.5 text-left">Lokasi &amp; Luas</th>
                                <th class="border border-black px-2 py-1.5 w-32">Periode Pemanfaatan</th>
                                <th class="border border-black px-2 py-1.5 text-right w-28">Nilai Aset</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-black px-2 py-2 text-center">1</td>
                                <td class="border border-black px-2 py-2">
                                    <div class="font-bold text-[9.5pt]" x-text="selectedKemitraan.objek_nama"></div>
                                    <div class="text-[8pt] text-gray-700" x-text="'Bentuk: ' + selectedKemitraan.tipe_label"></div>
                                </td>
                                <td class="border border-black px-2 py-2 text-center font-mono font-semibold" x-text="selectedKemitraan.objek_kode || '1.5.2.01.01'"></td>
                                <td class="border border-black px-2 py-2">
                                    <div x-text="selectedKemitraan.objek_lokasi"></div>
                                    <div class="text-[8pt] text-gray-700" x-text="'Luas: ' + selectedKemitraan.objek_luas"></div>
                                </td>
                                <td class="border border-black px-2 py-2 text-center font-mono text-[8.5pt]" x-text="selectedKemitraan.tanggal_mulai + ' s/d ' + selectedKemitraan.tanggal_selesai"></td>
                                <td class="border border-black px-2 py-2 text-right font-mono font-bold" x-text="selectedKemitraan.nilai_aset_format"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Klausul Penutup -->
                <p class="text-justify mb-5 text-[9.5pt]">
                    Demikian Berita Acara Serah Terima Pemanfaatan Barang Milik Daerah ini dibuat dan ditandatangani untuk menjadi landasan pencatatan reklasifikasi aset ke Akun 1.5.2 (Aset Kemitraan dengan Pihak Ketiga) pada neraca RSUD dr. H. Koesnandi.
                </p>

                <!-- Tanda Tangan & QR Code BSrE -->
                <div class="grid grid-cols-2 gap-4 text-center text-[9.5pt]">
                    <div>
                        <p class="font-bold mb-1">PIHAK PERTAMA</p>
                        <p class="text-xs text-gray-600 mb-2">Direktur RSUD dr. H. Koesnandi,</p>
                        <div class="h-16 flex items-center justify-center">
                            <template x-if="selectedKemitraan.signed">
                                <div class="flex flex-col items-center">
                                    <img :src="getQrCodeSvg(selectedKemitraan.qr_hash)" class="w-14 h-14 object-contain" alt="QR BSrE">
                                    <span class="text-[7.5pt] font-mono text-emerald-800 font-bold mt-0.5">TERVERIFIKASI BSrE</span>
                                </div>
                            </template>
                            <template x-if="!selectedKemitraan.signed">
                                <span class="text-[8pt] italic text-gray-400 border border-dashed border-gray-300 px-3 py-1 rounded">Tanda Tangan Elektronik</span>
                            </template>
                        </div>
                        <p class="font-bold underline mt-2" x-text="selectedKemitraan.direktur_nama || 'dr. DIAN ARISANDI, M.Kes'"></p>
                        <p class="text-[8.5pt] font-mono" x-text="'NIP. ' + (selectedKemitraan.direktur_nip || '19730514 200212 2 003')"></p>
                    </div>

                    <div>
                        <p class="font-bold mb-1">PIHAK KEDUA</p>
                        <p class="text-xs text-gray-600 mb-2" x-text="selectedKemitraan.mitra_nama"></p>
                        <div class="h-16 flex items-center justify-center">
                            <span class="text-[8pt] italic text-gray-400 border border-dashed border-gray-300 px-3 py-1 rounded">Tanda Tangan &amp; Stempel Mitra</span>
                        </div>
                        <p class="font-bold underline mt-2" x-text="selectedKemitraan.mitra_pimpinan"></p>
                        <p class="text-[8.5pt]" x-text="'Pimpinan / Kuasa Mitra'"></p>
                    </div>
                </div>

            </div>
        </template>

    </div>
</div>
