<!-- ========================================================================= -->
<!-- MODAL CETAK: LEMBAR DOKUMEN BAST PEMANFAATAN KEMITRAAN (AKUN 1.5.2)       -->
<!-- Standar Desain & Layout Persis BAST Master Utama (Berita Acara SIMAT)     -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showModalPrintBast"
         class="fixed inset-0 z-[99999] flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto custom-scrollbar"
         x-cloak
         @click.self="showModalPrintBast = false"
         @keydown.escape.window="if(showModalPrintBast) showModalPrintBast = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="showModalPrintBast = false"
             class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
        
        <!-- Action Bar Modal (Disembunyikan saat cetak) -->
        <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2.5">
                <span class="p-2 rounded-xl bg-purple-500/20 text-purple-300 text-sm flex items-center justify-center shrink-0">
                    🖨️
                </span>
                <div>
                    <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                        <span>Cetak Berita Acara Serah Terima Barang (Kemitraan)</span>
                        <span x-show="isBastModified" class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/40" style="display: none;">
                            💾 Tersimpan
                        </span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Dokumen Resmi Pengesahan Pemanfaatan BMD RSUD Dr. H. Koesnandi</p>
                </div>
            </div>

            <div class="flex items-center space-x-2 shrink-0 flex-wrap">
                <!-- 1. Tombol Toggle Live Edit -->
                <button type="button" @click="showEditBastForm = !showEditBastForm"
                    class="px-3.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all active:scale-95 cursor-pointer">
                    <span x-text="showEditBastForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'">✏️ Edit Data & Pejabat BAST</span>
                </button>

                <!-- 2. Tombol Reset -->
                <button type="button" @click="resetBastData()"
                    title="Kembalikan data ke nilai awal sistem"
                    class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold text-xs transition-all active:scale-95 cursor-pointer">
                    <span>🔄 Reset</span>
                </button>

                <!-- 3. Tombol Cetak Surat -->
                <button type="button" @click="printCurrentBast()"
                    class="px-4 py-1.5 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-bold text-xs shadow-lg shadow-purple-500/20 transition-all active:scale-95 cursor-pointer flex items-center space-x-1.5">
                    <span>🖨️</span>
                    <span>Cetak Surat</span>
                </button>

                <!-- 4. Tutup Modal -->
                <button type="button" @click="showModalPrintBast = false"
                    class="p-1 rounded-lg text-slate-400 hover:text-white font-bold text-lg leading-none cursor-pointer">
                    &times;
                </button>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- FORMULIR LIVE EDIT SURAT BAST (ACCORDION COLLAPSIBLE)                    -->
        <!-- ========================================================================= -->
        <template x-if="bastDoc">
            <div x-show="showEditBastForm" x-transition class="no-print bg-slate-950 p-5 rounded-2xl border border-purple-500/40 text-xs space-y-4 shadow-2xl" style="display: none;">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                    <div class="flex items-center space-x-2">
                        <span class="p-1.5 rounded-lg bg-purple-500/20 text-purple-300">✏️</span>
                        <div class="font-bold text-purple-300 text-xs uppercase tracking-wider">
                            Live Edit Surat BAST Pemanfaatan BMD (Otomatis Berubah Pada Lembar Cetak):
                        </div>
                    </div>
                    <button type="button" @click="saveBastData()"
                        class="px-4 py-1.5 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-md transition-all active:scale-95 flex items-center space-x-1.5 cursor-pointer">
                        <span>💾 Simpan Perubahan</span>
                    </button>
                </div>

                <!-- Section 1: Informasi Dokumen & Waktu -->
                <div class="space-y-1.5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">📄 Informasi Dokumen &amp; Perjanjian (PKS)</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nomor Surat BAST</label>
                            <input type="text" x-model="bastDoc.nomor_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-purple-300 font-mono font-bold text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Hari &amp; Tanggal BAST</label>
                            <input type="text" x-model="bastDoc.hari_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Lokasi Pelaksanaan</label>
                            <input type="text" x-model="bastDoc.lokasi" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nomor Surat PKS</label>
                            <input type="text" x-model="bastDoc.nomor_pks" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white font-mono text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Tanggal Surat PKS</label>
                            <input type="text" x-model="bastDoc.tanggal_pks" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Skema Kemitraan</label>
                            <input type="text" x-model="bastDoc.skema_kemitraan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Pihak I & Pihak II -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-800">
                    <!-- Pihak I -->
                    <div class="p-3.5 bg-slate-900/90 rounded-2xl border border-purple-500/20 space-y-2.5">
                        <span class="text-[10px] font-bold text-purple-300 uppercase tracking-wider block">🏛️ PIHAK I (RSUD / KUASA PENGGUNA BARANG)</span>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-0.5">Nama Lengkap &amp; Gelar</label>
                            <input type="text" x-model="bastDoc.p1_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white font-bold text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-0.5">NIP</label>
                                <input type="text" x-model="bastDoc.p1_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 font-mono text-xs focus:border-purple-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-0.5">Pangkat / Golongan</label>
                                <input type="text" x-model="bastDoc.p1_pangkat" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 text-xs focus:border-purple-400 focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-0.5">Jabatan Resmi</label>
                            <input type="text" x-model="bastDoc.p1_jabatan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                    </div>

                    <!-- Pihak II -->
                    <div class="p-3.5 bg-slate-900/90 rounded-2xl border border-emerald-500/20 space-y-2.5">
                        <span class="text-[10px] font-bold text-emerald-300 uppercase tracking-wider block">🏢 PIHAK II (MITRA KERJA SAMA)</span>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-0.5">Nama Perusahaan / Mitra</label>
                            <input type="text" x-model="bastDoc.p2_perusahaan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-emerald-300 font-bold text-xs focus:border-emerald-400 focus:outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-0.5">Nama Pimpinan / Direktur</label>
                                <input type="text" x-model="bastDoc.p2_pimpinan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white font-bold text-xs focus:border-emerald-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-0.5">Jabatan Pimpinan</label>
                                <input type="text" x-model="bastDoc.p2_jabatan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 text-xs focus:border-emerald-400 focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-0.5">Alamat Domisili Mitra</label>
                            <input type="text" x-model="bastDoc.p2_alamat" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 text-xs focus:border-emerald-400 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Objek Barang & Nilai -->
                <div class="space-y-1.5 pt-2 border-t border-slate-800">
                    <span class="text-[10px] font-bold text-purple-300 uppercase tracking-wider block">📦 Rincian Objek Barang Milik Daerah (BMD)</span>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-slate-400 text-[10px] mb-1">Nama Barang / Spesifikasi Objek</label>
                            <input type="text" x-model="bastDoc.aset_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white font-bold text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Kode 108</label>
                            <input type="text" x-model="bastDoc.aset_kode108" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 font-mono text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">NIBAR</label>
                            <input type="text" x-model="bastDoc.aset_nibar" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 font-mono text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Jumlah / Volume / Luas</label>
                            <input type="text" x-model="bastDoc.aset_volume" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Kondisi</label>
                            <input type="text" x-model="bastDoc.aset_kondisi" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Masa Konsesi / Keterangan</label>
                            <input type="text" x-model="bastDoc.aset_keterangan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Taksiran Nilai (Rp)</label>
                            <input type="text" x-model="bastDoc.aset_nilai" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-emerald-400 font-mono font-bold text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Section 4: Pengurus Barang Pengguna -->
                <div class="space-y-1.5 pt-2 border-t border-slate-800">
                    <span class="text-[10px] font-bold text-amber-400 uppercase tracking-wider block">✍️ Pengurus Barang Pengguna (Mengetahui / Mengesahkan)</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nama Pengurus Barang</label>
                            <input type="text" x-model="bastDoc.pb_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white font-bold text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">NIP Pengurus Barang</label>
                            <input type="text" x-model="bastDoc.pb_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 font-mono text-xs focus:border-purple-400 focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- ========================================================================= -->
        <!-- LEMBAR PREVIEW DOKUMEN CETAK (PERSIS MODAL BAST STANDAR RSUD)            -->
        <!-- Frame gray-200 dengan max-w-[760px] proporsional kertas A4 fisik         -->
        <!-- ========================================================================= -->
        <template x-if="bastDoc">
            <div class="bg-gray-200 p-3 sm:p-6 rounded-2xl border border-slate-700 flex justify-center items-start overflow-y-auto max-h-[75vh] custom-scrollbar shadow-inner">
                <div id="print-area-bast-kemitraan"
                     style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 10pt; color: #000000 !important; background-color: #ffffff !important; min-height: 100%; box-sizing: border-box;"
                     class="w-full max-w-[760px] shrink-0 bg-white text-black p-6 sm:p-10 md:p-12 shadow-2xl rounded-sm space-y-3.5 select-text print:p-0 print:m-0 print:shadow-none print:max-w-none">
                    
                    <!-- KOP SURAT RESMI DENGAN DUA LOGO RESMI (KABUPATEN & RSUD) -->
                    <div class="border-b-[2.5px] border-black pb-2 mb-3" style="border-bottom: 2.5px solid #000000;">
                        <div class="flex items-center justify-between gap-3">
                            <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                <img src="{{ asset('img/logo-bondowoso.png') }}" alt="Logo Kabupaten Bondowoso" class="h-16 w-16 object-contain">
                            </div>
                            <div class="flex-1 text-center text-black" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                <h4 class="font-bold text-[11pt] sm:text-[12pt] uppercase tracking-normal leading-tight text-black m-0 p-0">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                <h3 class="font-bold text-[12.5pt] sm:text-[13.5pt] uppercase tracking-normal leading-tight text-black mt-0.5 mb-0 p-0">RUMAH SAKIT UMUM DAERAH DR. H. KOESNANDI</h3>
                                <p class="text-[8.5pt] sm:text-[9pt] italic leading-tight text-black mt-0.5 mb-0 p-0">Jl. Kapten Piere Tendean No. 3 Telp. (0332) 421974 Fax. (0332) 422311</p>
                                <p class="text-[8.5pt] sm:text-[9pt] leading-tight text-black m-0 p-0">e-mail: rsu.koesnadi@gmail.com, Website: rsudrkoesnadi.go.id</p>
                                <p class="font-bold text-[10pt] sm:text-[10.5pt] uppercase text-black mt-1 mb-0 p-0" style="letter-spacing: 0.35em;">B O N D O W O S O</p>
                            </div>
                            <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD Koesnandi" class="h-16 w-16 object-contain">
                            </div>
                        </div>
                    </div>

                    <!-- JUDUL SURAT & NOMOR (PERSIS STANDAR BAST UTAMA) -->
                    <div class="text-center text-black mb-3">
                        <h3 class="font-bold text-[11pt] sm:text-[11.5pt] uppercase underline tracking-normal text-black m-0">BERITA ACARA SERAH TERIMA BARANG</h3>
                        <p class="text-[9.5pt] sm:text-[10pt] font-semibold text-black mt-1 m-0">
                            Nomor: <span class="font-mono font-bold" x-text="bastDoc.nomor_bast"></span>
                        </p>
                    </div>

                    <!-- PEMBUKA RESMI -->
                    <p class="text-justify mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                        Pada hari ini <strong x-text="bastDoc.hari_tanggal"></strong> bertempat di Rumah Sakit Umum Daerah dr. H. Koesnandi Kabupaten Bondowoso, yang bertanda tangan di bawah ini:
                    </p>

                    <!-- PIHAK 1 (RSUD DR. H. KOESNANDI) -->
                    <div class="space-y-1 mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                        <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                            <tr style="border: none !important;">
                                <td style="border: none !important; width: 22px; vertical-align: top; padding: 1.5px 0;" class="font-bold">1.</td>
                                <td style="border: none !important; width: 85px; vertical-align: top; padding: 1.5px 0;">Nama</td>
                                <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold uppercase" x-text="bastDoc.p1_nama"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important; padding: 1.5px 0;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">NIP</td>
                                <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-mono" x-text="bastDoc.p1_nip"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important; padding: 1.5px 0;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Jabatan</td>
                                <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-semibold" x-text="bastDoc.p1_jabatan"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important; padding: 1.5px 0;"></td>
                                <td colspan="3" style="border: none !important; padding: 2px 0;" class="italic">
                                    Dalam hal ini bertindak untuk dan atas nama RSUD Dr. H. Koesnandi selaku <strong class="not-italic">PIHAK KESATU</strong>.
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- PIHAK 2 (MITRA KERJA SAMA) -->
                    <div class="space-y-1 mb-3 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                        <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                            <tr style="border: none !important;">
                                <td style="border: none !important; width: 22px; vertical-align: top; padding: 1.5px 0;" class="font-bold">2.</td>
                                <td style="border: none !important; width: 85px; vertical-align: top; padding: 1.5px 0;">Nama</td>
                                <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold uppercase" x-text="bastDoc.p2_pimpinan"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important; padding: 1.5px 0;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Perusahaan</td>
                                <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold uppercase" x-text="bastDoc.p2_perusahaan"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important; padding: 1.5px 0;"></td>
                                <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Jabatan</td>
                                <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="bastDoc.p2_jabatan"></td>
                            </tr>
                            <tr style="border: none !important;">
                                <td style="border: none !important; padding: 1.5px 0;"></td>
                                <td colspan="3" style="border: none !important; padding: 2px 0;" class="italic">
                                    Dalam hal ini bertindak untuk dan atas nama <span class="font-semibold not-italic" x-text="bastDoc.p2_perusahaan"></span> selaku <strong class="not-italic">PIHAK KEDUA</strong>.
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- PERNYATAAN PENYERAHAN & DASAR PKS -->
                    <p class="text-justify mb-2 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                        Berdasarkan Surat Perjanjian Kerja Sama (PKS) Nomor: <strong class="font-mono" x-text="bastDoc.nomor_pks"></strong> Tanggal <strong x-text="bastDoc.tanggal_pks"></strong> perihal Kerja Sama Pemanfaatan Barang Milik Daerah skema <strong x-text="bastDoc.skema_kemitraan"></strong>, <strong>PIHAK KESATU</strong> menyerahkan kepada <strong>PIHAK KEDUA</strong>, dan <strong>PIHAK KEDUA</strong> menerima penyerahan objek Barang Milik Daerah (BMD) milik Pemerintah Kabupaten Bondowoso yang tercatat pada RSUD Dr. H. Koesnandi dalam keadaan baik dan lengkap untuk dimanfaatkan sesuai ketentuan kerja sama, dengan rincian sebagai berikut:
                    </p>

                    <!-- TABEL RINCIAN OBJEK BARANG MILIK DAERAH (STANDAR KEDINASAN RESMI) -->
                    <div class="my-2.5 overflow-x-auto">
                        <table class="w-full text-black border-collapse border border-black text-[9pt] sm:text-[9.5pt]" style="border-collapse: collapse; width: 100%; border: 1px solid black;">
                            <thead>
                                <tr style="font-weight:700; color:#000000; border:1px solid black; background-color:#ffffff;">
                                    <th style="border:1px solid black; padding:6px 6px; text-align:center; width:6%; background-color:#ffffff; font-weight:700;">NO</th>
                                    <th style="border:1px solid black; padding:6px 10px; text-align:left; background-color:#ffffff; font-weight:700;">NAMA BARANG / SPESIFIKASI</th>
                                    <th style="border:1px solid black; padding:6px 8px; text-align:center; width:17%; background-color:#ffffff; font-weight:700;">KODE 108</th>
                                    <th style="border:1px solid black; padding:6px 8px; text-align:center; width:13%; background-color:#ffffff; font-weight:700;">NIBAR</th>
                                    <th style="border:1px solid black; padding:6px 8px; text-align:center; width:12%; background-color:#ffffff; font-weight:700;">VOL</th>
                                    <th style="border:1px solid black; padding:6px 8px; text-align:center; width:10%; background-color:#ffffff; font-weight:700;">KONDISI</th>
                                    <th style="border:1px solid black; padding:6px 10px; text-align:right; width:18%; background-color:#ffffff; font-weight:700;">NILAI (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="border:1px solid black; background-color:#ffffff; color:#000000;">
                                    <td style="border:1px solid black; padding:6px 6px; text-align:center; font-weight:700; vertical-align:top;">1</td>
                                    <td style="border:1px solid black; padding:6px 10px; text-align:left; vertical-align:top;">
                                        <strong class="block" x-text="bastDoc.aset_nama"></strong>
                                        <span class="text-[8pt] text-slate-700 block mt-0.5">
                                            Lokasi: Kompleks RSUD Dr. H. Koesnandi Bondowoso
                                        </span>
                                        <span class="text-[8pt] text-slate-700 block italic mt-0.5" x-text="'Masa Konsesi: ' + bastDoc.aset_keterangan"></span>
                                    </td>
                                    <td style="border:1px solid black; padding:6px 8px; text-align:center; font-family:monospace; font-size:8.5pt; vertical-align:top;" x-text="bastDoc.aset_kode108"></td>
                                    <td style="border:1px solid black; padding:6px 8px; text-align:center; font-family:monospace; font-size:8.5pt; vertical-align:top;" x-text="bastDoc.aset_nibar"></td>
                                    <td style="border:1px solid black; padding:6px 8px; text-align:center; vertical-align:top;" x-text="bastDoc.aset_volume"></td>
                                    <td style="border:1px solid black; padding:6px 8px; text-align:center; vertical-align:top;" x-text="bastDoc.aset_kondisi"></td>
                                    <td style="border:1px solid black; padding:6px 10px; text-align:right; font-weight:700; font-family:monospace; vertical-align:top;" x-text="bastDoc.aset_nilai"></td>
                                </tr>
                                <!-- BARIS TOTAL JUMLAH -->
                                <tr style="border:1px solid black; font-weight:700; background-color:#ffffff; color:#000000;">
                                    <td colspan="4" style="border:1px solid black; padding:6px 10px; text-align:center; font-weight:700;">
                                        TOTAL TAKSIRAN NILAI PEMANFAATAN BMD
                                    </td>
                                    <td colspan="2" style="border:1px solid black; padding:6px 8px; text-align:center; font-weight:700;" x-text="bastDoc.aset_volume"></td>
                                    <td style="border:1px solid black; padding:6px 10px; text-align:right; font-weight:700; font-family:monospace;" x-text="'Rp ' + bastDoc.aset_nilai"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- KETENTUAN UMUM PEMANFAATAN (NETRAL, TANPA PASAL KAKU / TANPA ASUMSI BANGUN GEDUNG) -->
                    <p class="text-justify my-2 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                        Objek Barang Milik Daerah tersebut dipergunakan oleh PIHAK KEDUA semata-mata untuk penyelenggaraan operasional kegiatan kemitraan sesuai hak dan kewajiban yang telah disepakati dalam Surat Perjanjian Kerja Sama (PKS). Selama masa kerja sama berlangsung, PIHAK KEDUA berkewajiban memelihara, merawat, dan menjaga keamanan serta kebersihan barang tersebut dengan sebaik-baiknya.
                    </p>

                    <!-- PENUTUP -->
                    <p class="text-justify my-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                        Demikian Berita Acara Serah Terima (BAST) ini dibuat dan ditandatangani oleh PARA PIHAK pada hari dan tanggal tersebut di atas dalam rangkap 2 (dua) bermeterai cukup, serta memiliki kekuatan hukum yang sama bagi masing-masing pihak untuk dipergunakan sebagaimana mestinya.
                    </p>

                    <!-- TANDA TANGAN 2 PIHAK KANAN KIRI -->
                    <div class="grid grid-cols-2 gap-8 text-center text-black text-[9.5pt] sm:text-[10pt] mt-5 pt-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div>
                            <p class="m-0 font-bold uppercase">PIHAK KESATU</p>
                            <p class="text-[8.5pt] text-slate-700 m-0">RSUD Dr. H. Koesnandi Bondowoso</p>
                            <p class="text-[8pt] text-slate-600 font-semibold m-0">Kuasa Pengguna Barang</p>
                            
                            <!-- Ruang Bersih TTD Direktur & Cap Dinas -->
                            <div class="h-20 my-1" style="height:75px; margin:4px 0;"></div>

                            <p class="font-bold underline uppercase m-0" x-text="bastDoc.p1_nama"></p>
                            <p class="m-0 font-mono text-[9pt]" x-text="'NIP. ' + bastDoc.p1_nip"></p>
                        </div>

                        <div>
                            <p class="m-0 font-bold uppercase">PIHAK KEDUA</p>
                            <p class="text-[8.5pt] text-slate-700 font-bold uppercase m-0" x-text="bastDoc.p2_perusahaan"></p>
                            <p class="text-[8pt] text-slate-600 font-semibold m-0">Mitra Kerja Sama</p>
                            
                            <!-- Ruang Meterai Pihak II -->
                            <div class="h-20 flex items-center justify-center my-1" style="height:75px; margin:4px 0;">
                                <span style="font-size:7.5pt; border:1px dashed #94a3b8; padding:3px 8px; border-radius:4px; color:#64748b;">
                                    Meterai Rp 10.000,- &amp; Cap Basah
                                </span>
                            </div>

                            <p class="font-bold underline uppercase m-0" x-text="bastDoc.p2_pimpinan"></p>
                            <p class="text-[8.5pt] text-slate-700 m-0" x-text="bastDoc.p2_jabatan"></p>
                        </div>
                    </div>

                    <!-- MENGETAHUI / MENGESAHKAN: PENGURUS BARANG PENGGUNA RSUD -->
                    <div class="mt-5 text-center text-black text-[9.5pt] sm:text-[10pt]">
                        <p class="m-0 font-bold uppercase">MENGETAHUI / MENGESAHKAN:</p>
                        <p class="font-bold text-[9pt] text-slate-800 uppercase m-0">PENGURUS BARANG PENGGUNA RSUD DR. H. KOESNANDI</p>
                        <div class="h-16 my-1" style="height:65px; margin:4px 0;">
                            <!-- Ruang Bersih TTD Pengurus Barang Pengguna -->
                        </div>
                        <p class="font-bold underline uppercase m-0" x-text="bastDoc.pb_nama"></p>
                        <p class="m-0 font-mono text-[9pt]" x-text="'NIP. ' + bastDoc.pb_nip"></p>
                    </div>

                </div>
            </div>
        </template>

        </div>
    </div>
</template>
