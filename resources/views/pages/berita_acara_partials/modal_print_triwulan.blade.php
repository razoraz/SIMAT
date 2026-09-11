        <!-- ========================================================================= -->
        <!-- MODAL CETAK 1: LEMBAR DOKUMEN BAST PENAMBAHAN ASTAP (TRIWULAN PENGADAAN)  -->
        <!-- ========================================================================= -->
        <div x-show="showPrintTriwulanModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.away="showPrintTriwulanModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <!-- Action Bar Modal -->
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-purple-500/20 text-purple-300 text-sm">🖨️</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Cetak Berita Acara Serah Terima Barang (Triwulan)</h3>
                            <p class="text-[11px] text-slate-400">Dokumen Resmi Pengesahan Hasil Belanja Modal RSUD Dr. H. Koesnandi</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <button type="button" @click="showEditTriwulanForm = !showEditTriwulanForm"
                            class="px-3.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all">
                            <span x-text="showEditTriwulanForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                        </button>

                        <!-- Toggle Button TTD / Batalkan TTD -->
                        <button type="button" @click="toggleSignTriwulan(selectedTriwulanKey)"
                            :class="currentTriwulanDoc.pihak2_signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 text-slate-950 font-extrabold shadow-md'"
                            class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all active:scale-95">
                            <span x-text="currentTriwulanDoc.pihak2_signed ? '↩️ Batalkan TTD BSrE' : '✍️ TTD BSrE'"></span>
                        </button>

                        <button type="button" @click="printCurrent()" class="px-4 py-1.5 rounded-xl bg-purple-500 text-slate-950 font-bold text-xs shadow-lg">
                            🖨️ Cetak Surat
                        </button>
                        <button type="button" @click="showPrintTriwulanModal = false" class="p-1 rounded-lg text-slate-400 hover:text-white font-bold text-lg">&times;</button>
                    </div>
                </div>

                <!-- Formulir Edit Live BAST Triwulan -->
                <div x-show="showEditTriwulanForm" class="no-print bg-slate-950 p-5 rounded-2xl border border-purple-500/40 text-xs space-y-4 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                        <div class="flex items-center space-x-2">
                            <span class="p-1.5 rounded-lg bg-purple-500/20 text-purple-300">✏️</span>
                            <div class="font-bold text-purple-300 text-xs uppercase tracking-wider">
                                Live Edit Surat BAST Serah Terima Barang (Otomatis Berubah Pada Lembar Cetak):
                            </div>
                        </div>
                        <button type="button" @click="saveTriwulanEdit(selectedTriwulanKey)"
                            class="px-4 py-1.5 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-md transition-all active:scale-95 flex items-center space-x-1.5">
                            <span>💾 Simpan ke Database</span>
                        </button>
                    </div>

                    <!-- Section 1: Informasi Dokumen & Lokasi -->
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">📄 Informasi Dokumen & Lokasi Pelaksanaan</span>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor Surat BAST</label>
                                <input type="text" x-model="currentTriwulanDoc.nomor_surat" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-purple-300 font-mono font-bold text-xs focus:border-purple-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Hari & Tanggal Surat</label>
                                <input type="text" x-model="currentTriwulanDoc.hari_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs focus:border-purple-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Lokasi Pelaksanaan</label>
                                <input type="text" x-model="currentTriwulanDoc.lokasi" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs focus:border-purple-400 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Pihak I & Pihak II -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-800">
                        <!-- Pihak I: Pejabat Pembuat Komitmen (PPK) -->
                        <div class="p-3.5 bg-slate-900/90 rounded-2xl border border-purple-500/20 space-y-2.5">
                            <span class="text-[10px] font-bold text-purple-300 uppercase tracking-wider block flex items-center space-x-1.5">
                                <span>👤 PIHAK I (YANG MENYERAHKAN / PPK)</span>
                            </span>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-0.5">Nama Lengkap & Gelar</label>
                                <input type="text" x-model="currentTriwulanDoc.pihak1_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white font-bold text-xs focus:border-purple-400 focus:outline-none">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-0.5">NIP</label>
                                    <input type="text" x-model="currentTriwulanDoc.pihak1_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 font-mono text-xs focus:border-purple-400 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-0.5">Jabatan</label>
                                    <input type="text" x-model="currentTriwulanDoc.pihak1_jabatan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 text-xs focus:border-purple-400 focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Pihak II: Pengurus Barang Aset -->
                        <div class="p-3.5 bg-slate-900/90 rounded-2xl border border-emerald-500/20 space-y-2.5">
                            <span class="text-[10px] font-bold text-emerald-300 uppercase tracking-wider block flex items-center space-x-1.5">
                                <span>👤 PIHAK II (YANG MENERIMA / PENGURUS BARANG)</span>
                            </span>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-0.5">Nama Lengkap & Gelar</label>
                                <input type="text" x-model="currentTriwulanDoc.pihak2_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-emerald-300 font-bold text-xs focus:border-emerald-400 focus:outline-none">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-0.5">NIP</label>
                                    <input type="text" x-model="currentTriwulanDoc.pihak2_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 font-mono text-xs focus:border-emerald-400 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-0.5">Jabatan</label>
                                    <input type="text" x-model="currentTriwulanDoc.pihak2_jabatan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 text-xs focus:border-emerald-400 focus:outline-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LEMBAR CETAK DOKUMEN TRIWULAN (PREVIEW PERSIS LEMBAR KERTAS FISIK A4/F4) -->
                <div class="bg-gray-200 p-3 sm:p-6 rounded-2xl border border-slate-700 flex justify-center items-start overflow-y-auto max-h-[75vh] custom-scrollbar shadow-inner">
                    <div id="print-area-triwulan" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 10pt; color: #000000 !important; background-color: #ffffff !important; min-height: 100%; box-sizing: border-box;" class="w-full max-w-[760px] shrink-0 bg-white text-black p-8 sm:p-12 md:p-14 shadow-2xl rounded-sm space-y-3.5 select-text print:p-0 print:m-0 print:shadow-none print:max-w-none">
                        
                        <!-- KOP SURAT RESMI DENGAN DUA LOGO RESMI (KABUPATEN & RSUD) -->
                        <div class="border-b-[2.5px] border-black pb-2 mb-3" style="border-bottom: 2.5px solid #000000;">
                            <div class="flex items-center justify-between gap-3">
                                <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/logo-bondowoso.png') }}" alt="Logo Kabupaten Bondowoso" class="h-16 w-16 object-contain">
                                </div>
                                <div class="flex-1 text-center text-black" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                    <h4 class="font-bold text-[11pt] sm:text-[12pt] uppercase tracking-normal leading-tight text-black m-0 p-0">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                    <h3 class="font-bold text-[12.5pt] sm:text-[13.5pt] uppercase tracking-normal leading-tight text-black mt-0.5 mb-0 p-0">RUMAH SAKIT UMUM DAERAH DR. H. KOESNADI</h3>
                                    <p class="text-[8.5pt] sm:text-[9pt] italic leading-tight text-black mt-0.5 mb-0 p-0">Jl. Kapten Piere Tendean No. 3 Telp. (0332) 421974 Fax. (0332) 422311</p>
                                    <p class="text-[8.5pt] sm:text-[9pt] leading-tight text-black m-0 p-0">e-mail: rsu.koesnadi@gmail.com, Website: rsudrkoesnadi.go.id</p>
                                    <p class="font-bold text-[10pt] sm:text-[10.5pt] uppercase text-black mt-1 mb-0 p-0" style="letter-spacing: 0.35em;">B O N D O W O S O</p>
                                </div>
                                <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD Koesnandi" class="h-16 w-16 object-contain">
                                </div>
                            </div>
                        </div>

                        <!-- JUDUL SURAT & NOMOR -->
                        <div class="text-center text-black mb-3">
                            <h3 class="font-bold text-[11pt] sm:text-[11.5pt] uppercase underline tracking-normal text-black m-0">BERITA ACARA SERAH TERIMA BARANG</h3>
                            <p class="text-[9.5pt] sm:text-[10pt] font-semibold text-black mt-1 m-0">Nomor: <span x-text="currentTriwulanDoc.nomor_surat || '000.2.3.2/759/430.10.7/2025'"></span></p>
                        </div>

                        <!-- PEMBUKA -->
                        <p class="text-justify mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                            Pada hari ini <strong x-text="currentTriwulanDoc.hari_tanggal || 'Rabu tanggal 31 Desember 2025'"></strong> bertempat di Rumah Sakit Umum Daerah dr. H. Koesnandi Kabupaten Bondowoso, yang bertanda tangan di bawah ini:
                        </p>

                        <!-- PIHAK 1 (PPK) -->
                        <div class="space-y-1 mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                            <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                                <tr style="border: none !important;">
                                    <td style="border: none !important; width: 22px; vertical-align: top; padding: 1.5px 0;" class="font-bold">1.</td>
                                    <td style="border: none !important; width: 75px; vertical-align: top; padding: 1.5px 0;">Nama</td>
                                    <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold" x-text="currentTriwulanDoc.pihak1_nama">dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.</td>
                                </tr>
                                <tr style="border: none !important;">
                                    <td style="border: none !important; padding: 1.5px 0;"></td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">NIP</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="currentTriwulanDoc.pihak1_nip">19771002 200604 1 006</td>
                                </tr>
                                <tr style="border: none !important;">
                                    <td style="border: none !important; padding: 1.5px 0;"></td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Jabatan</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="currentTriwulanDoc.pihak1_jabatan">Pejabat Pembuat Komitmen / Penerima Barang Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso</td>
                                </tr>
                                <tr style="border: none !important;">
                                    <td style="border: none !important; padding: 1.5px 0;"></td>
                                    <td colspan="3" style="border: none !important; padding: 2px 0;" class="italic">
                                        Dalam hal ini disebut <strong class="not-italic">PIHAK KESATU</strong>.
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- PIHAK 2 (PENGURUS BARANG) -->
                        <div class="space-y-1 mb-3 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                            <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                                <tr style="border: none !important;">
                                    <td style="border: none !important; width: 22px; vertical-align: top; padding: 1.5px 0;" class="font-bold">2.</td>
                                    <td style="border: none !important; width: 75px; vertical-align: top; padding: 1.5px 0;">Nama</td>
                                    <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold" x-text="currentTriwulanDoc.pihak2_nama">BUDI HARTONO,S.Sos</td>
                                </tr>
                                <tr style="border: none !important;">
                                    <td style="border: none !important; padding: 1.5px 0;"></td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">NIP</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="currentTriwulanDoc.pihak2_nip">19760229 200801 1 010</td>
                                </tr>
                                <tr style="border: none !important;">
                                    <td style="border: none !important; padding: 1.5px 0;"></td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Jabatan</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="currentTriwulanDoc.pihak2_jabatan">Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso</td>
                                </tr>
                                <tr style="border: none !important;">
                                    <td style="border: none !important; padding: 1.5px 0;"></td>
                                    <td colspan="3" style="border: none !important; padding: 2px 0;" class="italic">
                                        Dalam hal ini disebut <strong class="not-italic">PIHAK KEDUA</strong>.
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- PERNYATAAN PENYERAHAN -->
                        <p class="text-justify mb-2 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                            Bersama ini <strong>PIHAK KESATU</strong> menyerahkan Barang dari Hasil Pengadaan Belanja Modal <span x-text="currentTriwulanDoc.triwulan_label || ('Triwulan ' + (selectedTriwulanKey === 'TW1' ? 'I' : (selectedTriwulanKey === 'TW2' ? 'II' : (selectedTriwulanKey === 'TW3' ? 'III' : 'IV'))) + ' Tahun ' + selectedTahun)"></span> Rumah Sakit Umum Daerah dr. H. Koesnandi Kabupaten Bondowoso kepada <strong>PIHAK KEDUA</strong> sebagai berikut:
                        </p>

                        <p class="text-[9.5pt] sm:text-[10pt] mb-2 font-normal text-black">
                            1. PIHAK KESATU menyerahkan barang kepada PIHAK KEDUA berupa:
                        </p>

                        <!-- TABEL 8 GOLONGAN BARANG PERSIS DOKUMEN RESMI -->
                        <div class="my-2.5 overflow-x-auto">
                            <table class="w-full text-black border-collapse border border-black text-[9pt] sm:text-[9.5pt]" style="border-collapse: collapse; width: 100%; border: 1px solid black;">
                                <thead>
                                    <tr style="font-weight:700; color:#000000; border:1px solid black; background-color:#ffffff;">
                                        <th style="border:1px solid black; padding:6px 8px; text-align:center; width:7%; background-color:#ffffff; font-weight:700;">NO</th>
                                        <th style="border:1px solid black; padding:6px 12px; text-align:center; background-color:#ffffff; font-weight:700;">NAMA GOLONGAN BARANG</th>
                                        <th style="border:1px solid black; padding:6px 12px; text-align:center; width:22%; background-color:#ffffff; font-weight:700;">JUMLAH BARANG</th>
                                        <th style="border:1px solid black; padding:6px 12px; text-align:center; width:34%; background-color:#ffffff; font-weight:700;">NILAI PEROLEHAN (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="item in currentTriwulanDoc.rekapItems" :key="item.no">
                                        <tr style="border:1px solid black; background-color:#ffffff; color:#000000;">
                                            <td style="border:1px solid black; padding:6px 8px; text-align:center;" x-text="item.no"></td>
                                            <td style="border:1px solid black; padding:6px 12px; text-align:left;" x-text="item.nama"></td>
                                            <td style="border:1px solid black; padding:6px 12px; text-align:center;" x-text="item.qty.toLocaleString('id-ID')"></td>
                                            <td style="border:1px solid black; padding:6px 12px; text-align:right; font-weight:700;" x-text="item.nilai.toLocaleString('id-ID')"></td>
                                        </tr>
                                    </template>
                                    <!-- BARIS TOTAL JUMLAH -->
                                    <tr style="border:1px solid black; font-weight:700; background-color:#ffffff; color:#000000;">
                                        <td colspan="2" style="border:1px solid black; padding:6px 12px; text-align:center; font-weight:700;">JUMLAH</td>
                                        <td style="border:1px solid black; padding:6px 12px; text-align:center; font-weight:700;" x-text="currentTriwulanTotalQty.toLocaleString('id-ID')"></td>
                                        <td style="border:1px solid black; padding:6px 12px; text-align:right; font-weight:700;" x-text="currentTriwulanTotalNilai.toLocaleString('id-ID')"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- PENUTUP -->
                        <p class="text-justify my-3 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                            Demikian Berita Acara Serah Terima Barang ini dibuat untuk dipergunakan sebagaimana mestinya.
                        </p>

                        <!-- TANDA TANGAN DUA PIHAK DENGAN TTD DIGITAL BSR-E -->
                        <div class="grid grid-cols-2 gap-8 text-center text-black text-[9.5pt] sm:text-[10pt] mt-6 pt-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                            <div>
                                <p class="m-0">Yang Menerima,</p>
                                <p class="font-bold m-0">PIHAK KEDUA</p>
                                
                                <!-- TTD Digital BSrE / Space Pihak II -->
                                <div class="h-20 flex items-center justify-center my-1">
                                    <template x-if="currentTriwulanDoc.pihak2_signed">
                                        <div style="padding:4px; border:1.5px solid #16a34a; background:#f0fdf4; border-radius:5px; display:flex; align-items:center; gap:6px; text-align:left;">
                                            <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(window.location.origin + '/validasi-tte/' + (currentTriwulanDoc.pihak2_qr_hash || encodeURIComponent(currentTriwulanDoc.nomor_surat) || 'PENGURUS-KOESNANDI'))" style="width:40px; height:40px; flex-shrink:0;">
                                            <div style="font-size:7.5px; line-height:1.35; color:#1e293b;">
                                                <div style="font-weight:700; color:#14532d;">DITANDATANGANI ELEKTRONIK</div>
                                                <div style="color:#374151;">Pengurus Barang Aset</div>
                                                <div style="font-size:6.5px; color:#6b7280; font-family:monospace;">Sertifikat BSrE - BSSN</div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!currentTriwulanDoc.pihak2_signed">
                                        <div style="padding:4px 8px; border:1.5px dashed #d97706; background:#fffbeb; border-radius:5px; text-align:center; color:#92400e;">
                                            <span style="font-size:8px; font-weight:700; font-style:italic;">( Menunggu Pengesahan TTD BSrE )</span>
                                        </div>
                                    </template>
                                </div>

                                <p class="font-bold underline uppercase m-0" x-text="currentTriwulanDoc.pihak2_nama">BUDI HARTONO,S.Sos</p>
                                <p class="m-0" x-text="'NIP. ' + currentTriwulanDoc.pihak2_nip">NIP. 19760229 200801 1 010</p>
                            </div>

                            <div>
                                <p class="m-0">Yang Menyerahkan,</p>
                                <p class="font-bold m-0">PIHAK KESATU</p>
                                
                                <!-- Ruang Tanda Tangan Basah Manual Pihak I (Direktur / PPK) -->
                                <div class="h-20 my-1" style="height:80px; margin:4px 0;"></div>

                                <p class="font-bold underline uppercase m-0" x-text="currentTriwulanDoc.pihak1_nama">dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.</p>
                                <p class="m-0" x-text="'NIP. ' + currentTriwulanDoc.pihak1_nip">NIP. 19771002 200604 1 006</p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
