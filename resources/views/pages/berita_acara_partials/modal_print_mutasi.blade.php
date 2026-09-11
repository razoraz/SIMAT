        <!-- ========================================================================= -->
        <!-- MODAL CETAK 3: LEMBAR DOKUMEN BAST MUTASI ASET (ANTAR RUANGAN)             -->
        <!-- ========================================================================= -->
        <div x-show="showPrintMutasiModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak @click.self="closeMutasiModal()">
            <div @click.away="closeMutasiModal()" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-rose-500/20 text-rose-300 text-sm">🔄</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Berita Acara Serah Terima Mutasi Aset</h3>
                            <p class="text-[11px] text-slate-400" x-text="selectedMutasi ? ('Nomor BAST: ' + selectedMutasi.nomor_bast) : ''"></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <button type="button" @click="showEditMutasiForm = !showEditMutasiForm"
                            class="px-3.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all">
                            <span x-text="showEditMutasiForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                        </button>

                        <!-- Toggle Button TTD / Batalkan TTD -->
                        <button type="button" @click="toggleSignMutasi(selectedMutasi)"
                            :class="selectedMutasi && selectedMutasi.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 text-slate-950 font-extrabold shadow-md'"
                            class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all active:scale-95">
                            <span x-text="selectedMutasi && selectedMutasi.signed ? '↩️ Batalkan TTD BSrE' : '✍️ TTD BSrE'"></span>
                        </button>

                        <button type="button" @click="printCurrent()" class="px-4 py-1.5 rounded-xl bg-purple-500 text-slate-950 font-bold text-xs shadow-lg">
                            🖨️ Cetak Surat
                        </button>
                        <button type="button" @click="closeMutasiModal()" class="p-1 rounded-lg text-slate-400 hover:text-white font-bold text-lg">&times;</button>
                    </div>
                </div>

                <!-- Formulir Edit Live BAST Mutasi -->
                <div x-show="showEditMutasiForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-rose-500/40 text-xs space-y-3 shadow-inner">
                    <div class="font-bold text-rose-300 text-[11px] uppercase tracking-wider border-b border-slate-800 pb-2">
                        ✏️ Live Edit Surat BAST Mutasi (Otomatis Berubah Pada Lembar Cetak):
                    </div>
                    <template x-if="selectedMutasi">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor BAST Mutasi</label>
                                <input type="text" x-model="selectedMutasi.nomor_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-purple-300 font-mono font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Hari Surat</label>
                                <input type="text" x-model="selectedMutasi.hari" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tanggal Surat</label>
                                <input type="text" x-model="selectedMutasi.tgl_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-800/60">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nama PJ Asal</label>
                                <input type="text" x-model="selectedMutasi.pj_asal_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">NIP PJ Asal</label>
                                <input type="text" x-model="selectedMutasi.pj_asal_nip" placeholder="NIP..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-slate-200 font-mono text-xs">
                            </div>
                            <div x-show="!((selectedMutasi.jenis_mutasi && selectedMutasi.jenis_mutasi.toLowerCase().includes('pengembalian')) || (selectedMutasi.keterangan && selectedMutasi.keterangan.toLowerCase().includes('pengembalian')))">
                                <label class="block text-slate-400 text-[10px] mb-1">Nama PJ Tujuan</label>
                                <input type="text" x-model="selectedMutasi.pj_tujuan_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-bold text-xs">
                            </div>
                            <div x-show="!((selectedMutasi.jenis_mutasi && selectedMutasi.jenis_mutasi.toLowerCase().includes('pengembalian')) || (selectedMutasi.keterangan && selectedMutasi.keterangan.toLowerCase().includes('pengembalian')))">
                                <label class="block text-slate-400 text-[10px] mb-1">NIP PJ Tujuan</label>
                                <input type="text" x-model="selectedMutasi.pj_tujuan_nip" placeholder="NIP..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-slate-200 font-mono text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nama Pengurus Barang (BSrE)</label>
                                <input type="text" x-model="selectedMutasi.pengurus_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-teal-300 font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">NIP Pengurus Barang</label>
                                <input type="text" x-model="selectedMutasi.pengurus_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-slate-200 font-mono text-xs">
                            </div>
                        </div>
                    </template>
                </div>

                <!-- LEMBAR CETAK BAST MUTASI -->
                <template x-if="selectedMutasi">
                    <div class="bg-gray-200 p-3 sm:p-6 rounded-2xl border border-slate-700 flex justify-center items-start overflow-y-auto max-h-[75vh] custom-scrollbar shadow-inner">
                        <div id="print-area-mutasi" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 10pt; color: #000000 !important; background-color: #ffffff !important; min-height: 100%; box-sizing: border-box;" class="w-full max-w-[760px] shrink-0 bg-white text-black p-8 sm:p-12 md:p-14 shadow-2xl rounded-sm space-y-3.5 select-text print:p-0 print:m-0 print:shadow-none print:max-w-none">
                            
                            <div class="border-b-[2.5px] border-black pb-2 mb-3" style="border-bottom: 2.5px solid #000000;">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                        <img src="{{ asset('img/logo-bondowoso.png') }}" alt="Logo Dinas Bondowoso" class="h-16 w-16 object-contain">
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

                            <div class="text-center text-black mb-3">
                                <h3 class="font-bold text-[11pt] sm:text-[11.5pt] uppercase underline tracking-normal text-black m-0">BERITA ACARA MUTASI BARANG (BAMB)</h3>
                                <p class="text-[9.5pt] sm:text-[10pt] font-semibold text-black mt-1 m-0">Nomor : <span x-text="selectedMutasi.nomor_bast"></span></p>
                            </div>

                            <p class="text-justify mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                                Pada hari ini <strong x-text="selectedMutasi.hari"></strong> tanggal <strong x-text="selectedMutasi.tgl_bast"></strong>, telah dilaksanakan pemindahan/mutasi aset inventaris dari ruangan asal ke ruangan tujuan sebagai berikut :
                            </p>

                            <div class="space-y-1 mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                                <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; width: 140px; vertical-align: top; padding: 1.5px 0;">Ruangan Asal</td>
                                        <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold uppercase" x-text="selectedMutasi.asal"></td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Ruangan Tujuan</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold uppercase text-purple-900" x-text="selectedMutasi.tujuan"></td>
                                    </tr>
                                </table>
                            </div>

                            <!-- TABEL RESMI BARANG MUTASI -->
                            <div class="my-2.5 overflow-x-auto">
                                <table class="w-full text-center border-collapse border border-black text-[9pt] sm:text-[9.5pt]" style="border-collapse: collapse; width: 100%; border: 1px solid black; background-color: #ffffff !important; color: #000000 !important;">
                                    <thead>
                                        <tr style="font-weight:700; border:1px solid black; background-color:#ffffff; color:#000000 !important;">
                                            <th rowspan="2" style="border:1px solid black; padding:6px 6px; text-align:center; width:4%; background-color:#ffffff; font-weight:700; color:#000000;">No</th>
                                            <th rowspan="2" style="border:1px solid black; padding:6px 8px; text-align:left; width:22%; background-color:#ffffff; font-weight:700; color:#000000;">Nama Barang / Aset</th>
                                            <th rowspan="2" style="border:1px solid black; padding:6px 8px; text-align:left; width:18%; background-color:#ffffff; font-weight:700; color:#000000;">Merk / Spesifikasi</th>
                                            <th rowspan="2" style="border:1px solid black; padding:6px 8px; text-align:left; width:20%; background-color:#ffffff; font-weight:700; color:#000000;">Kode Barang / NIBAR</th>
                                            <th rowspan="2" style="border:1px solid black; padding:4px 6px; text-align:center; width:5%; background-color:#ffffff; font-weight:700; color:#000000;">Vol</th>
                                            <th rowspan="2" style="border:1px solid black; padding:4px 6px; text-align:center; width:7%; background-color:#ffffff; font-weight:700; color:#000000;">Satuan</th>
                                            <th colspan="3" style="border:1px solid black; padding:4px; text-align:center; width:10%; background-color:#ffffff; font-weight:700; color:#000000;">Kondisi</th>
                                            <th rowspan="2" style="border:1px solid black; padding:6px 8px; text-align:left; width:14%; background-color:#ffffff; font-weight:700; color:#000000;">Keterangan</th>
                                        </tr>
                                        <tr style="font-weight:700; border:1px solid black; background-color:#ffffff; color:#000000 !important;">
                                            <th style="border:1px solid black; padding:2px 4px; text-align:center; font-size:8pt; background-color:#ffffff; font-weight:700; color:#000000;">Baik</th>
                                            <th style="border:1px solid black; padding:2px 4px; text-align:center; font-size:8pt; background-color:#ffffff; font-weight:700; color:#000000;">KB</th>
                                            <th style="border:1px solid black; padding:2px 4px; text-align:center; font-size:8pt; background-color:#ffffff; font-weight:700; color:#000000;">RB</th>
                                        </tr>
                                    </thead>
                                    <tbody style="background-color: #ffffff !important; color: #000000 !important;">
                                        <template x-for="(sub, idx) in (selectedMutasi.items || [])" :key="idx">
                                            <tr class="border border-black" style="border: 1px solid black; background-color: #ffffff !important; color: #000000 !important;">
                                                <td class="border border-black px-1.5 py-1 text-center" style="border: 1px solid black;" x-text="idx + 1"></td>
                                                <td class="border border-black px-2 py-1 text-left font-bold" style="border: 1px solid black;" x-text="sub.nama_barang"></td>
                                                <td class="border border-black px-2 py-1 text-left text-[9pt]" style="border: 1px solid black;" x-text="sub.spesifikasi || sub.merk_type || sub.merk || '-'"></td>
                                                <td class="border border-black px-2 py-1 font-mono text-[8.5pt] text-left break-all" style="border: 1px solid black;" x-text="sub.nibar || sub.kode_barang || '-'"></td>
                                                <td class="border border-black px-1 py-1 text-center font-bold" style="border: 1px solid black;" x-text="sub.qty || sub.vol || 1"></td>
                                                <td class="border border-black px-1.5 py-1 text-center" style="border: 1px solid black;" x-text="sub.satuan || 'Unit'"></td>
                                                <td class="border border-black px-1 py-1 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(sub.kondisi === 'Baik' || !sub.kondisi) ? '✓' : ''"></td>
                                                <td class="border border-black px-1 py-1 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(sub.kondisi === 'Kurang Baik' || sub.kondisi === 'KB') ? '✓' : ''"></td>
                                                <td class="border border-black px-1 py-1 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(sub.kondisi === 'Rusak Berat' || sub.kondisi === 'RB' || sub.kondisi === 'Rusak Ringan' || sub.kondisi === 'Rusak') ? '✓' : ''"></td>
                                                <td class="border border-black px-2 py-1 text-left text-[9pt]" style="border: 1px solid black;" x-text="sub.keterangan || selectedMutasi.keterangan || '-'"></td>
                                            </tr>
                                        </template>
                                        <template x-if="!selectedMutasi.items || selectedMutasi.items.length === 0">
                                            <tr class="border border-black" style="border: 1px solid black; background-color: #ffffff !important; color: #000000 !important;">
                                                <td class="border border-black px-1.5 py-1 text-center" style="border: 1px solid black;">1</td>
                                                <td class="border border-black px-2 py-1 text-left font-bold" style="border: 1px solid black;" x-text="selectedMutasi.nama_barang || selectedMutasi.nama"></td>
                                                <td class="border border-black px-2 py-1 text-left text-[9pt]" style="border: 1px solid black;" x-text="selectedMutasi.spesifikasi || selectedMutasi.merk || '-'"></td>
                                                <td class="border border-black px-2 py-1 font-mono text-[8.5pt] text-left break-all" style="border: 1px solid black;" x-text="selectedMutasi.nibar || selectedMutasi.kode_barang || '-'"></td>
                                                <td class="border border-black px-1 py-1 text-center font-bold" style="border: 1px solid black;" x-text="selectedMutasi.qty || 1"></td>
                                                <td class="border border-black px-1.5 py-1 text-center" style="border: 1px solid black;" x-text="selectedMutasi.satuan || 'Unit'"></td>
                                                <td class="border border-black px-1 py-1 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(selectedMutasi.kondisi === 'Baik' || !selectedMutasi.kondisi) ? '✓' : ''"></td>
                                                <td class="border border-black px-1 py-1 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(selectedMutasi.kondisi === 'Kurang Baik' || selectedMutasi.kondisi === 'KB') ? '✓' : ''"></td>
                                                <td class="border border-black px-1 py-1 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(selectedMutasi.kondisi === 'Rusak Berat' || selectedMutasi.kondisi === 'RB' || selectedMutasi.kondisi === 'Rusak Ringan' || selectedMutasi.kondisi === 'Rusak') ? '✓' : ''"></td>
                                                <td class="border border-black px-2 py-1 text-left text-[9pt]" style="border: 1px solid black;" x-text="selectedMutasi.keterangan || '-'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <p class="text-justify my-3 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black" style="color: #000000 !important;">
                                Demikian Berita Acara Mutasi Barang ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.
                            </p>

                            <!-- TANDA TANGAN BAST MUTASI -->
                            <!-- KONDISI 1: JIKA TIPE MUTASI PENGEMBALIAN (2 TTD: Kiri Yang Menyerahkan TTD Basah, Kanan Yang Menerima Pengurus Barang Pak Budi TTD BSrE) -->
                            <template x-if="(selectedMutasi.jenis_mutasi && selectedMutasi.jenis_mutasi.toLowerCase().includes('pengembalian')) || (selectedMutasi.keterangan && selectedMutasi.keterangan.toLowerCase().includes('pengembalian'))">
                                <div class="grid grid-cols-2 gap-8 text-center text-black text-[9.5pt] sm:text-[10pt] mt-6 pt-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; color: #000000 !important;">
                                    <!-- Kolom Kiri: Yang Menyerahkan (PJ Ruangan yang mengembalikan - TTD Basah) -->
                                    <div>
                                        <p class="m-0" style="color: #000000 !important;">Yang Menyerahkan,</p>
                                        <p class="font-bold m-0" style="color: #000000 !important;" x-text="'PJ Ruangan ' + (selectedMutasi.asal || 'Asal')"></p>
                                        
                                        <!-- Ruang Tanda Tangan Basah Manual (Tinggi 55px) -->
                                        <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;"></div>
                                        
                                        <p class="font-bold underline uppercase m-0" style="color: #000000 !important;" x-text="selectedMutasi.pj_asal_nama"></p>
                                        <p class="m-0" style="color: #000000 !important;" x-text="selectedMutasi.pj_asal_nip ? 'NIP. ' + selectedMutasi.pj_asal_nip : ''"></p>
                                    </div>

                                    <!-- Kolom Kanan: Yang Menerima (Pengurus Barang Aset - Pak Budi Hartono - TTD Digital BSrE) -->
                                    <div>
                                        <p class="m-0" style="color: #000000 !important;">Yang Menerima,</p>
                                        <p class="font-bold m-0" style="color: #000000 !important;">Pengurus Barang Aset</p>
                                        
                                        <!-- TTD Elektronik BSrE Pengurus Barang (Pak Budi) -->
                                        <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;">
                                            <template x-if="selectedMutasi.signed !== false">
                                                <div style="padding:4px; border:1.5px solid #0d9488; background:#f0fdfa; border-radius:5px; display:flex; align-items:center; gap:6px; text-align:left;">
                                                    <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(window.location.origin + '/validasi-tte/' + encodeURIComponent(selectedMutasi.nomor_bast || 'BSRE-MUTASI-PENGURUS'))" style="width:36px; height:36px; flex-shrink:0;">
                                                    <div style="font-size:7.5px; line-height:1.35; color:#1e293b;">
                                                        <div style="font-weight:700; color:#134e4a;">DITANDATANGANI ELEKTRONIK</div>
                                                        <div style="color:#374151;">Pengurus Barang Aset</div>
                                                        <div style="font-size:6.5px; color:#6b7280; font-family:monospace;">Sertifikat BSrE - BSSN</div>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="selectedMutasi.signed === false">
                                                <div style="padding:4px 8px; border:1.5px dashed #d97706; background:#fffbeb; border-radius:5px; text-align:center; color:#92400e;">
                                                    <span style="font-size:8px; font-weight:700; font-style:italic;">( Menunggu Pengesahan TTD BSrE )</span>
                                                </div>
                                            </template>
                                        </div>

                                        <p class="font-bold underline uppercase m-0" style="color: #000000 !important;" x-text="selectedMutasi.pengurus_nama || 'BUDI HARTONO, S.Sos'"></p>
                                        <p class="m-0" style="color: #000000 !important;" x-text="'NIP. ' + (selectedMutasi.pengurus_nip || '19760229 200801 1 010')"></p>
                                    </div>
                                </div>
                            </template>

                            <!-- KONDISI 2: JIKA TIPE MUTASI BIASA / BUKAN PENGEMBALIAN (3 TTD: 2 TTD BASAH OLEH PJ ASAL & PJ TUJUAN, BAWAH TENGAH MENGETAHUI PENGURUS BARANG PAK BUDI TTD DIGITAL BSrE) -->
                            <template x-if="!((selectedMutasi.jenis_mutasi && selectedMutasi.jenis_mutasi.toLowerCase().includes('pengembalian')) || (selectedMutasi.keterangan && selectedMutasi.keterangan.toLowerCase().includes('pengembalian')))">
                                <div class="mt-6 pt-2 space-y-4" style="color: #000000 !important;">
                                    <!-- Baris Atas: 2 TTD Basah (Yang Menyerahkan & Yang Menerima) -->
                                    <div class="grid grid-cols-2 gap-8 text-center text-black text-[9.5pt] sm:text-[10pt]" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; color: #000000 !important;">
                                        <!-- Kiri: Yang Menyerahkan (PJ Ruangan Asal - TTD Basah) -->
                                        <div>
                                            <p class="m-0" style="color: #000000 !important;">Yang Menyerahkan,</p>
                                            <p class="font-bold m-0" style="color: #000000 !important;">PJ Ruangan Asal</p>
                                            
                                            <!-- Ruang TTD Basah (Tinggi 55px) -->
                                            <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;"></div>

                                            <p class="font-bold underline uppercase m-0" style="color: #000000 !important;" x-text="selectedMutasi.pj_asal_nama"></p>
                                            <p class="m-0" style="color: #000000 !important;" x-text="selectedMutasi.pj_asal_nip ? 'NIP. ' + selectedMutasi.pj_asal_nip : ''"></p>
                                        </div>

                                        <!-- Kanan: Yang Menerima (PJ Ruangan Tujuan - TTD Basah) -->
                                        <div>
                                            <p class="m-0" style="color: #000000 !important;">Yang Menerima,</p>
                                            <p class="font-bold m-0" style="color: #000000 !important;">PJ Ruangan Tujuan</p>
                                            
                                            <!-- Ruang TTD Basah (Tinggi 55px) -->
                                            <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;"></div>

                                            <p class="font-bold underline uppercase m-0" style="color: #000000 !important;" x-text="selectedMutasi.pj_tujuan_nama"></p>
                                            <p class="m-0" style="color: #000000 !important;" x-text="selectedMutasi.pj_tujuan_nip ? 'NIP. ' + selectedMutasi.pj_tujuan_nip : ''"></p>
                                        </div>
                                    </div>

                                    <!-- Baris Bawah: Mengetahui Pengurus Barang Aset (Pak Budi Hartono - TTD Digital BSrE) -->
                                    <div class="text-center text-black text-[9.5pt] sm:text-[10pt] pt-2" style="color: #000000 !important;">
                                        <p class="m-0" style="color: #000000 !important;">Mengetahui,</p>
                                        <p class="font-bold m-0" style="color: #000000 !important;">Pengurus Barang Aset</p>
                                        
                                        <!-- TTD Elektronik BSrE Pengurus Barang (Pak Budi) -->
                                        <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;">
                                            <template x-if="selectedMutasi.signed !== false">
                                                <div style="padding:4px; border:1.5px solid #0d9488; background:#f0fdfa; border-radius:5px; display:inline-flex; align-items:center; gap:6px; text-align:left;">
                                                    <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(window.location.origin + '/validasi-tte/' + encodeURIComponent(selectedMutasi.nomor_bast || 'BSRE-MUTASI-PENGURUS'))" style="width:36px; height:36px; flex-shrink:0;">
                                                    <div style="font-size:7.5px; line-height:1.35; color:#1e293b;">
                                                        <div style="font-weight:700; color:#134e4a;">DITANDATANGANI ELEKTRONIK</div>
                                                        <div style="color:#374151;">Pengurus Barang Aset</div>
                                                        <div style="font-size:6.5px; color:#6b7280; font-family:monospace;">Sertifikat BSrE - BSSN</div>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="selectedMutasi.signed === false">
                                                <div style="padding:4px 12px; border:1.5px dashed #d97706; background:#fffbeb; border-radius:5px; text-align:center; color:#92400e; display:inline-block;">
                                                    <span style="font-size:8px; font-weight:700; font-style:italic;">( Menunggu Pengesahan TTD BSrE )</span>
                                                </div>
                                            </template>
                                        </div>

                                        <p class="font-bold underline uppercase m-0" style="color: #000000 !important;" x-text="selectedMutasi.pengurus_nama || 'BUDI HARTONO, S.Sos'"></p>
                                        <p class="m-0" style="color: #000000 !important;" x-text="'NIP. ' + (selectedMutasi.pengurus_nip || '19760229 200801 1 010')"></p>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </div>
                </template>

            </div>
        </div>
