        <!-- 5. MODAL PRATINJAU & CETAK DOKUMEN KIR RESMI (KERTAS PUTIH STANDAR PEMERINTAH) -->
        <div x-show="showPrintModal" x-cloak
            class="flex items-center justify-center p-3 sm:p-6 overflow-y-auto"
            style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 99999 !important; background-color: rgba(2, 6, 23, 0.88) !important; backdrop-filter: blur(14px) !important; -webkit-backdrop-filter: blur(14px) !important;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            
            <div @click.away="showPrintModal = false"
                class="bg-slate-900 border border-slate-800 rounded-3xl max-w-5xl w-full p-4 sm:p-6 shadow-2xl relative overflow-hidden flex flex-col max-h-[92vh]">
                
                <!-- Modal Top Action Bar -->
                <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-3 shrink-0">
                    <div class="flex items-center space-x-2">
                        <span class="text-xl">🖨️</span>
                        <h3 class="text-base font-extrabold text-white">Pratinjau Cetak Lembar Kartu Inventaris Ruangan (KIR)</h3>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="button" @click="printKir()"
                            class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5">
                            <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak Sekarang (Print)</span>
                        </button>
                        <button type="button" @click="showPrintModal = false" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <!-- LEMBAR DOKUMEN CETAK KERTAS PUTIH (PRINT READY) -->
                <div class="flex-1 overflow-y-auto rounded-2xl bg-white text-black p-6 sm:p-8 font-serif text-[11px] leading-relaxed shadow-inner">
                    
                    <!-- KOP SURAT RESMI RSUD -->
                    <div class="border-b-[3px] border-black pb-1 mb-0.5">
                        <div class="flex items-center justify-between gap-4">
                            <div class="w-20 shrink-0 flex justify-center">
                                <img src="{{ asset('img/logo-bondowoso.png') }}" alt="Logo Bondowoso" class="h-16 w-16 object-contain">
                            </div>

                            <div class="flex-1 text-center font-sans text-black">
                                <h4 class="font-bold text-xs sm:text-sm uppercase tracking-wide leading-tight">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                <h3 class="font-black text-sm sm:text-base uppercase tracking-tight leading-tight">RUMAH SAKIT UMUM DAERAH dr. H. KOESNANDI</h3>
                                <p class="text-[10px] leading-tight mt-0.5">Jl. Kapten Piere Tendean No. 3 Telp. (0332) 421974 Fax. (0332) 422311</p>
                                <p class="text-[10px] leading-tight">e-mail : rsu.koesnadi@gmail.com · Website : rsudrkoesnadi.go.id</p>
                                <h4 class="font-bold text-xs tracking-[0.3em] uppercase mt-0.5">B O N D O W O S O</h4>
                            </div>

                            <div class="w-20 shrink-0 flex justify-center">
                                <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="h-16 w-16 object-contain">
                            </div>
                        </div>
                    </div>
                    <div class="border-b border-black mb-3"></div>

                    <!-- JUDUL LEMBAR KIR -->
                    <div class="text-center font-sans mb-3">
                        <h3 class="font-black text-xs sm:text-sm uppercase underline tracking-wider">KARTU INVENTARIS RUANGAN (KIR)</h3>
                        <p class="text-[10px] font-semibold">Nomor Registrasi : <span x-text="kirDoc.nomor_surat"></span></p>
                    </div>

                    <!-- ATRIBUT IDENTITAS RUANGAN -->
                    <div class="grid grid-cols-2 gap-x-6 gap-y-1 font-sans text-[10px] mb-3 border p-2.5 bg-gray-50 border-gray-300 rounded">
                        <div class="flex">
                            <span class="w-36 font-bold">SKPD / Unit Kerja</span>
                            <span class="w-3">:</span>
                            <span class="font-semibold text-black">RSUD dr. H. KOESNANDI BONDOWOSO</span>
                        </div>
                        <div class="flex">
                            <span class="w-36 font-bold">KABUPATEN / PROVINSI</span>
                            <span class="w-3">:</span>
                            <span class="font-semibold text-black">BONDOWOSO / JAWA TIMUR</span>
                        </div>
                        <div class="flex">
                            <span class="w-36 font-bold">NAMA RUANGAN / UNIT</span>
                            <span class="w-3">:</span>
                            <span class="font-bold text-black uppercase">{{ $unitNama }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-36 font-bold">KODE RUANGAN / LOKASI</span>
                            <span class="w-3">:</span>
                            <span class="font-mono font-bold text-black">{{ $unitKode }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-36 font-bold">PENANGGUNG JAWAB</span>
                            <span class="w-3">:</span>
                            <span class="font-semibold text-black">{{ $unitKepala }} (NIP. {{ $unitNip }})</span>
                        </div>
                        <div class="flex">
                            <span class="w-36 font-bold">TAHUN ANGGARAN</span>
                            <span class="w-3">:</span>
                            <span class="font-bold text-black">2026</span>
                        </div>
                    </div>

                    <!-- TABEL RESMI FORMAT 12 KOLOM PERMENDAGRI BMD UNTUK LEMBAR KIR -->
                    <div class="mb-4 overflow-x-auto">
                        <table class="w-full border-collapse border border-black text-[9.5px]">
                            <thead class="bg-gray-100 font-sans text-center font-bold">
                                <tr>
                                    <th rowspan="2" class="border border-black px-1 py-1.5 w-6">No</th>
                                    <th rowspan="2" class="border border-black px-2 py-1.5">Jenis Barang / Nama Barang</th>
                                    <th rowspan="2" class="border border-black px-2 py-1.5">Merk / Type / Spesifikasi</th>
                                    <th rowspan="2" class="border border-black px-2 py-1.5">No. Pabrik / No. Seri</th>
                                    <th rowspan="2" class="border border-black px-2 py-1.5 w-12">Tahun</th>
                                    <th rowspan="2" class="border border-black px-2 py-1.5">Kode 108 / Register NIBAR</th>
                                    <th rowspan="2" class="border border-black px-1 py-1.5 w-8">Jml</th>
                                    <th rowspan="2" class="border border-black px-2 py-1.5 text-right">Harga Beli / Nilai (Rp)</th>
                                    <th colspan="3" class="border border-black px-1 py-1">Keadaan Barang</th>
                                    <th rowspan="2" class="border border-black px-2 py-1.5">Keterangan</th>
                                </tr>
                                <tr>
                                    <th class="border border-black px-1 py-0.5 w-7">B</th>
                                    <th class="border border-black px-1 py-0.5 w-7">KB</th>
                                    <th class="border border-black px-1 py-0.5 w-7">RB</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(ast, idx) in assets" :key="idx">
                                    <tr>
                                        <td class="border border-black px-1 py-1 text-center font-mono" x-text="idx + 1"></td>
                                        <td class="border border-black px-2 py-1 font-sans font-bold" x-text="ast.nama"></td>
                                        <td class="border border-black px-2 py-1 font-sans" x-text="ast.merk"></td>
                                        <td class="border border-black px-2 py-1 font-mono text-[9px]" x-text="ast.no_seri"></td>
                                        <td class="border border-black px-1 py-1 text-center font-mono" x-text="ast.tahun"></td>
                                        <td class="border border-black px-2 py-1 font-mono text-center text-[8.5px]" x-text="ast.nibar || ast.kode"></td>
                                        <td class="border border-black px-1 py-1 text-center font-mono font-bold">1</td>
                                        <td class="border border-black px-2 py-1 text-right font-mono font-semibold" x-text="formatRupiah(ast.harga || 0)"></td>
                                        
                                        <!-- Keadaan Barang: B, KB, RB -->
                                        <td class="border border-black px-1 py-1 text-center font-bold font-sans">
                                            <span x-text="ast.kondisi === 'Baik' ? '✓' : ''"></span>
                                        </td>
                                        <td class="border border-black px-1 py-1 text-center font-bold font-sans text-amber-700">
                                            <span x-text="ast.kondisi === 'Kurang Baik' || ast.kondisi === 'Rusak Ringan' ? '✓' : ''"></span>
                                        </td>
                                        <td class="border border-black px-1 py-1 text-center font-bold font-sans text-red-700">
                                            <span x-text="ast.kondisi === 'Rusak Berat' || ast.kondisi === 'Rusak' ? '✓' : ''"></span>
                                        </td>
                                        <td class="border border-black px-2 py-1 text-[8.5px] font-sans" x-text="ast.kategori"></td>
                                    </tr>
                                </template>

                                <!-- Baris Total Akumulasi -->
                                <tr class="bg-gray-100 font-sans font-bold">
                                    <td colspan="6" class="border border-black px-2 py-1.5 text-right uppercase">TOTAL ASET RUANGAN :</td>
                                    <td class="border border-black px-1 py-1.5 text-center font-mono" x-text="assets.length"></td>
                                    <td class="border border-black px-2 py-1.5 text-right font-mono" x-text="'{{ $totalNilaiFmt }}'"></td>
                                    <td class="border border-black px-1 py-1.5 text-center font-mono" x-text="countBaik"></td>
                                    <td class="border border-black px-1 py-1.5 text-center font-mono" x-text="countKurangBaik + countRusakRingan"></td>
                                    <td class="border border-black px-1 py-1.5 text-center font-mono" x-text="countRusakBerat"></td>
                                    <td class="border border-black px-1 py-1.5 text-center text-[8.5px]">Lengkap</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- TANDA TANGAN PENGESAHAN DOKUMEN KIR -->
                    <div class="grid grid-cols-3 gap-4 text-center font-sans text-[10px] mt-6 pt-3 border-t border-gray-300">
                        <!-- Kolom 1: Direktur RSUD (TTD Basah Manual) -->
                        <div class="space-y-1">
                            <p class="font-bold">Mengetahui / Menyetujui,</p>
                            <p class="font-black uppercase text-[9.5px]">DIREKTUR RSUD dr.H.KOESNANDI</p>
                            
                            <!-- Ruang Tanda Tangan Basah Manual -->
                            <div class="h-14 my-1"></div>

                            <p class="font-bold underline text-[10.5px]" x-text="kirDoc.direktur_nama"></p>
                            <p class="font-mono text-[9px]" x-text="'NIP. ' + kirDoc.direktur_nip"></p>
                        </div>

                        <!-- Kolom 2: Pengurus Barang Aset (TTD Digital BSrE) -->
                        <div class="space-y-1">
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
                        <div class="space-y-1">
                            <p class="font-semibold text-[9.5px]" x-text="kirDoc.kota_tanggal"></p>
                            <p class="font-bold uppercase text-[9.5px]">PENANGGUNG JAWAB RUANGAN,</p>
                            
                            <!-- Ruang Tanda Tangan Basah Manual -->
                            <div class="h-14 my-1"></div>

                            <p class="font-bold underline text-[10.5px]" x-text="kirDoc.pj_nama"></p>
                            <p class="font-mono text-[9px]" x-text="'NIP. ' + kirDoc.pj_nip"></p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
