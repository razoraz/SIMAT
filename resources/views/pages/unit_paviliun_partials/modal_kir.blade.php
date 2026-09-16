        <!-- MODAL CETAK LEMBAR KARTU INVENTARIS RUANGAN (KIR) RESMI KEDINASAN BMD     -->
        <!-- ========================================================================= -->
        <div x-show="showPrintKIRModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.away="showPrintKIRModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-5xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <!-- Action Bar Modal -->
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-blue-500/20 text-blue-300 text-sm">🖨️</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Lembar Cetak Kartu Inventaris Ruangan (KIR)</h3>
                            <p class="text-[11px] text-slate-400" x-text="selectedUnit ? (selectedUnit.nama + ' • Kode: ' + selectedUnit.kode) : ''"></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                        <button type="button" @click="showEditKIRForm = !showEditKIRForm"
                            class="px-3.5 py-2 rounded-xl bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 border border-blue-500/40 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95">
                            <span x-text="showEditKIRForm ? '✕ Tutup Form Edit' : '✏️ Edit Data Pejabat / Tanggal KIR'"></span>
                        </button>
                        <button type="button" @click="printCurrentKIR()"
                            class="px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak (Print / PDF)</span>
                        </button>
                        <button type="button" @click="showPrintKIRModal = false" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all active:scale-95">
                            Tutup
                        </button>
                    </div>
                </div>

                <!-- Formulir Cepat Edit Pejabat KIR (Hidden when Printed) -->
                <div x-show="showEditKIRForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-blue-500/40 text-xs space-y-3 shadow-inner">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="font-bold text-blue-300 text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                            <span>✏️ Sesuaikan Data Nomor KIR, Tanggal & Identitas Pejabat Pengesah:</span>
                        </span>
                        <span class="text-[10px] text-emerald-400 font-mono">Teks di lembar KIR otomatis berganti live</span>
                    </div>

                    <!-- Nomor & Tanggal -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nomor Registrasi KIR</label>
                            <input type="text" x-model="kirDoc.nomor_surat" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-blue-300 font-mono font-bold text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Tanggal Pengesahan (Kota, Tanggal Bulan Tahun)</label>
                            <input type="text" x-model="kirDoc.tanggal_pengesahan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs">
                        </div>
                    </div>

                    <!-- 3 Pejabat: Penanggung Jawab Ruangan, Pengurus Barang, Direktur -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2 border-t border-slate-800/80">
                        <!-- PJ Ruangan -->
                        <div class="p-3 rounded-xl bg-slate-900/80 border border-blue-500/30 space-y-2">
                            <span class="text-[10px] font-bold text-blue-400 block uppercase">1. Penanggung Jawab Ruangan:</span>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Nama PJ Ruangan / Gelar</label>
                                <input type="text" x-model="kirDoc.pj_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-blue-300 font-semibold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">NIP PJ Ruangan</label>
                                <input type="text" x-model="kirDoc.pj_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                            </div>
                        </div>

                        <!-- Pengurus Barang -->
                        <div class="p-3 rounded-xl bg-slate-900/80 border border-emerald-500/30 space-y-2">
                            <span class="text-[10px] font-bold text-emerald-400 block uppercase">2. Pengurus Barang RSUD:</span>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Nama Pengurus Barang</label>
                                <input type="text" x-model="kirDoc.pengurus_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-emerald-400 font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">NIP Pengurus Barang</label>
                                <input type="text" x-model="kirDoc.pengurus_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                            </div>
                        </div>

                        <!-- Direktur RSUD -->
                        <div class="p-3 rounded-xl bg-slate-900/80 border border-purple-500/30 space-y-2">
                            <span class="text-[10px] font-bold text-purple-400 block uppercase">3. Mengetahui (Direktur RSUD):</span>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Nama Direktur & Gelar</label>
                                <input type="text" x-model="kirDoc.direktur_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">NIP Direktur</label>
                                <input type="text" x-model="kirDoc.direktur_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LEMBAR CETAK ASLI DOKUMEN KARTU INVENTARIS RUANGAN (KIR) KERTAS PUTIH -->
                <template x-if="selectedUnit">
                    <div id="print-area-kir" class="bg-white text-black p-6 sm:p-8 rounded-2xl shadow-xl max-h-[70vh] overflow-y-auto font-serif text-[11px] leading-relaxed select-text print:max-h-none print:overflow-visible print:p-0 print:m-0 print:shadow-none print:rounded-none">
                        
                        <!-- KOP SURAT RESMI -->
                        <div class="border-b-[3px] border-black pb-1 mb-0.5">
                            <div class="flex items-center justify-between gap-4">
                                <div class="w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/logo-bondowoso.png') }}" alt="Logo Dinas Bondowoso" class="h-16 w-16 object-contain">
                                </div>

                                <div class="flex-1 text-center font-sans text-black">
                                    <h4 class="font-bold text-xs sm:text-sm uppercase tracking-wide leading-tight">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                    <h3 class="font-black text-sm sm:text-base uppercase tracking-tight leading-tight">RUMAH SAKIT UMUM DAERAH dr.H.KOESNADI</h3>
                                    <p class="text-[10px] leading-tight mt-0.5">Jl. Kapten Piere Tendean No. 3 Telp. (0332) 421974 Fax.0332 422311</p>
                                    <p class="text-[10px] leading-tight">e-mail : rsu.koesnadi@gmail.com, Website : rsudrkoesnadi.go.id</p>
                                    <h4 class="font-bold text-xs tracking-[0.3em] uppercase mt-0.5">B O N D O W O S O</h4>
                                </div>

                                <div class="w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="h-16 w-16 object-contain">
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-black mb-4"></div>

                        <!-- JUDUL LEMBAR KIR -->
                        <div class="text-center font-sans mb-3">
                            <h3 class="font-black text-xs sm:text-sm uppercase underline tracking-wider">KARTU INVENTARIS RUANGAN (KIR)</h3>
                            <p class="text-[10px] font-semibold">Nomor Registrasi : <span x-text="kirDoc.nomor_surat"></span></p>
                        </div>

                        <!-- ATRIBUT DATA RUANGAN -->
                        <div class="grid grid-cols-2 gap-x-6 gap-y-1 font-sans text-[10px] mb-3 border p-2.5 bg-gray-50 border-gray-300 rounded">
                            <div class="flex">
                                <span class="w-32 font-bold">SKPD / Unit Kerja</span>
                                <span class="w-3">:</span>
                                <span class="font-semibold text-black">RSUD dr. H. KOESNANDI</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-bold">KABUPATEN / PROV</span>
                                <span class="w-3">:</span>
                                <span class="font-semibold text-black">BONDOWOSO / JAWA TIMUR</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-bold">NAMA RUANGAN / UNIT</span>
                                <span class="w-3">:</span>
                                <span class="font-bold text-black uppercase" x-text="selectedUnit.nama"></span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-bold">KODE RUANGAN / LOKASI</span>
                                <span class="w-3">:</span>
                                <span class="font-mono font-bold text-black" x-text="selectedUnit.kode"></span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-bold">PENANGGUNG JAWAB</span>
                                <span class="w-3">:</span>
                                <span class="font-semibold text-black" x-text="kirDoc.pj_nama"></span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-bold">TAHUN ANGGARAN</span>
                                <span class="w-3">:</span>
                                <span class="font-bold text-black">2026</span>
                            </div>
                        </div>

                        <!-- TABEL STANDAR PERMENDAGRI BMD UNTUK KARTU INVENTARIS RUANGAN -->
                        <div class="mb-4">
                            <table class="w-full border-collapse border border-black text-[9.5px]">
                                <thead class="bg-gray-100 font-sans text-center font-bold">
                                    <tr>
                                        <th rowspan="2" class="border border-black px-1.5 py-2 w-7">No</th>
                                        <th rowspan="2" class="border border-black px-2 py-2">Jenis Barang / Nama Barang</th>
                                        <th rowspan="2" class="border border-black px-2 py-2">Merk / Type / Spesifikasi</th>
                                        <th rowspan="2" class="border border-black px-2 py-2">No. Pabrik / No. Seri</th>
                                        <th rowspan="2" class="border border-black px-2 py-2 w-12">Tahun</th>
                                        <th rowspan="2" class="border border-black px-2 py-2">Kode 108 / Register</th>
                                        <th rowspan="2" class="border border-black px-1.5 py-2 w-10">Jml</th>
                                        <th rowspan="2" class="border border-black px-2 py-2 text-right">Harga Beli / Nilai (Rp)</th>
                                        <th colspan="3" class="border border-black px-1 py-1">Keadaan Barang</th>
                                        <th rowspan="2" class="border border-black px-2 py-2">Keterangan</th>
                                    </tr>
                                    <tr>
                                        <th class="border border-black px-1 py-1 w-8">B</th>
                                        <th class="border border-black px-1 py-1 w-8">RR</th>
                                        <th class="border border-black px-1 py-1 w-8">RB</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(ast, idx) in (selectedUnit.assets || [])" :key="idx">
                                        <tr>
                                            <td class="border border-black px-1 py-1 text-center font-mono" x-text="idx + 1"></td>
                                            <td class="border border-black px-2 py-1 font-sans font-bold" x-text="ast.nama"></td>
                                            <td class="border border-black px-2 py-1 font-sans" x-text="ast.merk"></td>
                                            <td class="border border-black px-2 py-1 font-mono text-[9px]" x-text="ast.no_seri"></td>
                                            <td class="border border-black px-1 py-1 text-center font-mono" x-text="ast.tahun"></td>
                                            <td class="border border-black px-2 py-1 font-mono text-center text-[9px]" x-text="ast.kode"></td>
                                            <td class="border border-black px-1 py-1 text-center font-mono font-bold">1</td>
                                            <td class="border border-black px-2 py-1 text-right font-mono font-semibold" x-text="formatRupiah(ast.nilai ?? ast.harga ?? 0)"></td>
                                            
                                            <!-- Keadaan Barang: Baik (B), Rusak Ringan (RR), Rusak Berat (RB) -->
                                            <td class="border border-black px-1 py-1 text-center font-bold font-sans">
                                                <span x-text="ast.kondisi === 'Baik' ? '✓' : ''"></span>
                                            </td>
                                            <td class="border border-black px-1 py-1 text-center font-bold font-sans">
                                                <span x-text="ast.kondisi === 'Rusak Ringan' ? '✓' : ''"></span>
                                            </td>
                                            <td class="border border-black px-1 py-1 text-center font-bold font-sans">
                                                <span x-text="ast.kondisi === 'Rusak Berat' ? '✓' : ''"></span>
                                            </td>

                                            <td class="border border-black px-2 py-1 font-sans text-[8.5px]" x-text="ast.status"></td>
                                        </tr>
                                    </template>
                                    
                                    <template x-if="!selectedUnit.assets || selectedUnit.assets.length === 0">
                                        <tr>
                                            <td colspan="12" class="border border-black px-2 py-3 text-center italic text-gray-500">
                                                (Belum ada data barang/aset yang terdata di ruangan ini)
                                            </td>
                                        </tr>
                                    </template>

                                    <!-- Baris Total -->
                                    <tr class="bg-gray-100 font-bold font-sans">
                                        <td colspan="6" class="border border-black px-2 py-1.5 text-right uppercase">JUMLAH TOTAL DI RUANGAN:</td>
                                        <td class="border border-black px-1 py-1.5 text-center font-mono" x-text="(selectedUnit.assets ? selectedUnit.assets.length : 0) + ' Unit'"></td>
                                        <td class="border border-black px-2 py-1.5 text-right font-mono font-bold" x-text="selectedUnit.total_nilai"></td>
                                        <td colspan="4" class="border border-black px-2 py-1.5 text-center text-[8.5px] italic text-gray-700">Terinventarisasi Lengkap</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- CATATAN KETENTUAN KIR -->
                        <div class="text-[8.5px] text-gray-700 mb-4 leading-tight font-sans">
                            <p class="font-bold">Keterangan & Ketentuan Pengelolaan Ruangan:</p>
                            <p>1. Barang inventaris yang tercatat dalam daftar ini berada di bawah pengawasan & tanggung jawab Kepala Ruangan.</p>
                            <p>2. Dilarang memindahkan barang inventaris dari ruangan ini ke ruangan lain tanpa izin resmi & Berita Acara Mutasi.</p>
                            <p>3. Apabila terjadi kerusakan/kehilangan segera melapor kepada Pengurus Barang / IPSRS RSUD dr. H. Koesnandi Bondowoso.</p>
                        </div>

                        <!-- 3 KOLOM TANDA TANGAN PENGESAHAN -->
                        <div class="grid grid-cols-3 gap-3 text-center font-sans text-[10px] pt-1">
                            
                            <!-- Kolom 1: Direktur RSUD (TTD Basah Manual) -->
                            <div>
                                <p class="font-bold">Mengetahui / Menyetujui,</p>
                                <p class="font-black uppercase text-[9.5px]">DIREKTUR RSUD dr.H.KOESNANDI</p>
                                
                                <!-- Ruang Tanda Tangan Basah Manual -->
                                <div class="h-14 my-1"></div>

                                <p class="font-bold underline text-[10.5px]" x-text="kirDoc.direktur_nama"></p>
                                <p class="font-mono text-[9px]" x-text="'NIP. ' + kirDoc.direktur_nip"></p>
                            </div>

                            <!-- Kolom 2: Pengurus Barang Aset (TTD Digital BSrE) -->
                            <div>
                                <p class="font-bold">Pengurus Barang Pengelola Aset,</p>
                                <p class="font-black uppercase text-[9.5px]">RSUD dr.H.KOESNANDI</p>
                                
                                <div class="h-14 flex items-center justify-center my-1">
                                    <div class="flex items-center space-x-1.5 p-1 border border-black bg-gray-50 rounded">
                                        <div class="w-8 h-8 bg-white border border-black p-0.5">
                                            <img :src="typeof getQrCodeSvg === 'function' ? getQrCodeSvg('BSRE-KIR-PENGURUS-BARANG') : 'data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2027%2027%22%20shape-rendering%3D%22crispEdges%22%3E%3Cpath%20fill%3D%22%23ffffff%22%20d%3D%22M0%200h27v27H0z%22%2F%3E%3Cpath%20stroke%3D%22%23000000%22%20d%3D%22M1%201.5h7m3%200h1m1%200h1m1%200h3m1%200h7M1%202.5h1m5%200h1m1%200h1m1%200h1m2%200h2m3%200h1m5%200h1M1%203.5h1m1%200h3m1%200h1m1%200h2m2%200h2m1%200h1m2%200h1m1%200h3m1%200h1M1%204.5h1m1%200h3m1%200h1m1%200h1m1%200h2m3%200h1m2%200h1m1%200h3m1%200h1M1%205.5h1m1%200h3m1%200h1m3%200h2m3%200h2m1%200h1m1%200h3m1%200h1M1%206.5h1m5%200h1m2%200h1m2%200h1m2%200h1m2%200h1m5%200h1M1%207.5h7m1%200h1m1%200h1m1%200h1m1%200h1m1%200h1m1%200h7M9%208.5h5m1%200h1M1%209.5h1m5%200h1m1%200h1m1%200h1m4%200h1m1%200h2m2%200h3M4%2010.5h2m2%200h3m1%200h3m1%200h1m1%200h1m5%200h2M1%2011.5h1m1%200h1m2%200h2m1%200h3m1%200h4m3%200h2m1%200h1M1%2012.5h6m2%200h2m1%200h1m1%200h1m1%200h1m4%200h5M1%2013.5h3m2%200h3m1%200h1m1%200h3m1%200h1m2%200h2m2%200h2M1%2014.5h1m3%200h2m2%200h1m6%200h1m4%200h1m2%200h1M1%2015.5h1m2%200h2m1%200h1m3%200h3m2%200h3m1%200h1m1%200h2M1%2016.5h1m1%200h1m1%200h2m3%200h3m3%200h2m3%200h3m1%200h1M1%2017.5h1m2%200h4m2%200h3m3%200h6m3%200h1M9%2018.5h1m1%200h1m1%200h5m3%200h1m1%200h1M1%2019.5h7m4%200h1m4%200h1m1%200h1m1%200h2m1%200h2M1%2020.5h1m5%200h1m3%200h1m1%200h2m1%200h2m3%200h1m3%200h1M1%2021.5h1m1%200h3m1%200h1m5%200h1m1%200h1m1%200h8M1%2022.5h1m1%200h3m1%200h1m2%200h1m4%200h2m1%200h1m1%200h3m2%200h1M1%2023.5h1m1%200h3m1%200h1m2%200h1m1%200h2m2%200h1m1%200h3m2%200h1m1%200h1M1%2024.5h1m5%200h1m4%200h1m2%200h2m2%200h1m1%200h2M1%2025.5h7m1%200h3m2%200h2m1%200h1m1%200h1m2%200h4%22%2F%3E%3C%2Fsvg%3E'"
                                             src="data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2027%2027%22%20shape-rendering%3D%22crispEdges%22%3E%3Cpath%20fill%3D%22%23ffffff%22%20d%3D%22M0%200h27v27H0z%22%2F%3E%3Cpath%20stroke%3D%22%23000000%22%20d%3D%22M1%201.5h7m3%200h1m1%200h1m1%200h3m1%200h7M1%202.5h1m5%200h1m1%200h1m1%200h1m2%200h2m3%200h1m5%200h1M1%203.5h1m1%200h3m1%200h1m1%200h2m2%200h2m1%200h1m2%200h1m1%200h3m1%200h1M1%204.5h1m1%200h3m1%200h1m1%200h1m1%200h2m3%200h1m2%200h1m1%200h3m1%200h1M1%205.5h1m1%200h3m1%200h1m3%200h2m3%200h2m1%200h1m1%200h3m1%200h1M1%206.5h1m5%200h1m2%200h1m2%200h1m2%200h1m2%200h1m5%200h1M1%207.5h7m1%200h1m1%200h1m1%200h1m1%200h1m1%200h1m1%200h7M9%208.5h5m1%200h1M1%209.5h1m5%200h1m1%200h1m1%200h1m4%200h1m1%200h2m2%200h3M4%2010.5h2m2%200h3m1%200h3m1%200h1m1%200h1m5%200h2M1%2011.5h1m1%200h1m2%200h2m1%200h3m1%200h4m3%200h2m1%200h1M1%2012.5h6m2%200h2m1%200h1m1%200h1m1%200h1m4%200h5M1%2013.5h3m2%200h3m1%200h1m1%200h3m1%200h1m2%200h2m2%200h2M1%2014.5h1m3%200h2m2%200h1m6%200h1m4%200h1m2%200h1M1%2015.5h1m2%200h2m1%200h1m3%200h3m2%200h3m1%200h1m1%200h2M1%2016.5h1m1%200h1m1%200h2m3%200h3m3%200h2m3%200h3m1%200h1M1%2017.5h1m2%200h4m2%200h3m3%200h6m3%200h1M9%2018.5h1m1%200h1m1%200h5m3%200h1m1%200h1M1%2019.5h7m4%200h1m4%200h1m1%200h1m1%200h2m1%200h2M1%2020.5h1m5%200h1m3%200h1m1%200h2m1%200h2m3%200h1m3%200h1M1%2021.5h1m1%200h3m1%200h1m5%200h1m1%200h1m1%200h8M1%2022.5h1m1%200h3m1%200h1m2%200h1m4%200h2m1%200h1m1%200h3m2%200h1M1%2023.5h1m1%200h3m1%200h1m2%200h1m1%200h2m2%200h1m1%200h3m2%200h1m1%200h1M1%2024.5h1m5%200h1m4%200h1m2%200h2m2%200h1m1%200h2M1%2025.5h7m1%200h3m2%200h2m1%200h1m1%200h1m2%200h4%22%2F%3E%3C%2Fsvg%3E"
                                             alt="QR BSrE KIR" class="w-full h-full object-contain">
                                        </div>
                                        <div class="text-left text-[6.5px] leading-tight text-black">
                                            <div class="font-bold">PENGURUS BARANG</div>
                                            <div>Tervalidasi BSrE</div>
                                        </div>
                                    </div>
                                </div>

                                <p class="font-bold underline text-[10.5px]" x-text="kirDoc.pengurus_nama"></p>
                                <p class="font-mono text-[9px]" x-text="'NIP. ' + kirDoc.pengurus_nip"></p>
                            </div>

                            <!-- Kolom 3: Penanggung Jawab Ruangan (TTD Basah Manual) -->
                            <div>
                                <p class="font-semibold text-[9.5px]" x-text="kirDoc.tanggal_pengesahan"></p>
                                <p class="font-bold uppercase text-[9.5px]">PENANGGUNG JAWAB RUANGAN,</p>
                                
                                <!-- Ruang Tanda Tangan Basah Manual -->
                                <div class="h-14 my-1"></div>

                                <p class="font-bold underline text-[10.5px]" x-text="kirDoc.pj_nama"></p>
                                <p class="font-mono text-[9px]" x-text="'NIP. ' + kirDoc.pj_nip"></p>
                            </div>

                        </div>

                    </div>
                </template>

            </div>
        </div>
