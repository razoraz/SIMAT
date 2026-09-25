        <!-- ========================================================================= -->
        <!-- MODAL CETAK 2: LEMBAR DOKUMEN BAST DISTRIBUSI BARANG ASET                 -->
        <!-- ========================================================================= -->
        <div x-show="showPrintDistribusiModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak @click.self="closeDistribusiModal()">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-teal-500/20 text-teal-300 text-sm">🚚</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Berita Acara Serah Terima Distribusi Barang</h3>
                            <p class="text-[11px] text-slate-400" x-text="selectedDistribusi ? ('Nomor BAST: ' + selectedDistribusi.nomor_bast) : ''"></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <button type="button" @click="showEditDistribusiForm = !showEditDistribusiForm"
                            class="px-3.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all">
                            <span x-text="showEditDistribusiForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                        </button>

                        <!-- Toggle Button TTD / Batalkan TTD -->
                        <button type="button" @click="toggleSignDistribusi(selectedDistribusi)"
                            :class="selectedDistribusi && selectedDistribusi.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 text-slate-950 font-extrabold shadow-md'"
                            class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all active:scale-95">
                            <span x-text="selectedDistribusi && selectedDistribusi.signed ? '↩️ Batalkan TTD BSrE' : '✍️ TTD BSrE'"></span>
                        </button>

                        <button type="button" @click="printCurrent()" class="px-4 py-1.5 rounded-xl bg-purple-500 text-slate-950 font-bold text-xs shadow-lg">
                            🖨️ Cetak Surat
                        </button>
                        <button type="button" @click="closeDistribusiModal()" class="p-1 rounded-lg text-slate-400 hover:text-white font-bold text-lg">&times;</button>
                    </div>
                </div>

                <!-- Formulir Edit Live BAST Distribusi -->
                <template x-if="selectedDistribusi">
                    <div x-show="showEditDistribusiForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-teal-500/40 text-xs space-y-3 shadow-inner">
                        <div class="font-bold text-teal-300 text-[11px] uppercase tracking-wider border-b border-slate-800 pb-2">
                            ✏️ Live Edit Berita Acara Penyerahan Barang (Otomatis Berubah Pada Lembar Cetak):
                        </div>
                        <!-- Info Surat, Tanggal & SK Bupati (1 grid gabungan) -->
                        <div class="grid grid-cols-2 sm:grid-cols-7 gap-2.5">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor Surat</label>
                                <input type="text" x-model="selectedDistribusi.nomor_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-teal-300 font-mono font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Hari</label>
                                <input type="text" x-model="selectedDistribusi.hari" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tgl</label>
                                <input type="text" x-model="selectedDistribusi.tanggal_angka" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Bulan</label>
                                <input type="text" x-model="selectedDistribusi.bulan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tahun</label>
                                <input type="text" x-model="selectedDistribusi.tahun" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tahun Anggaran</label>
                                <input type="text" x-model="selectedDistribusi.tahun_anggaran" placeholder="2026" class="w-full bg-slate-900 border border-amber-700/50 rounded-lg px-2.5 py-1 text-amber-300 font-bold text-xs">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor SK Bupati</label>
                                <input type="text" x-model="selectedDistribusi.sk_bupati_nomor" placeholder="188.45/430.10.7/2026" class="w-full bg-slate-900 border border-amber-700/50 rounded-lg px-2.5 py-1 text-amber-200 font-mono text-xs">
                            </div>
                        </div>
                        <!-- Tanggal SK terpisah agar label tidak terpotong -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div class="sm:col-span-1">
                                <label class="block text-slate-400 text-[10px] mb-1">Tanggal SK Bupati</label>
                                <input type="text" x-model="selectedDistribusi.sk_bupati_tanggal" placeholder="02 Januari 2026" class="w-full bg-slate-900 border border-amber-700/50 rounded-lg px-2.5 py-1 text-amber-200 text-xs">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                            <div class="space-y-1.5">
                                <span class="text-[10px] font-bold text-teal-400 uppercase">👤 Pihak Kesatu (Pengurus Barang)</span>
                                <div>
                                    <label class="block text-slate-400 text-[9px]">Nama Lengkap</label>
                                    <input type="text" x-model="selectedDistribusi.pengurus_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-white font-bold text-xs">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">NIP</label>
                                        <input type="text" x-model="selectedDistribusi.pengurus_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Ruangan</label>
                                        <input type="text" x-model="selectedDistribusi.pengurus_ruangan" placeholder="Gudang Perbekalan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <span class="text-[10px] font-bold text-emerald-400 uppercase">👤 Pihak Kedua (Penerima Barang)</span>
                                <div>
                                    <label class="block text-slate-400 text-[9px]">Nama Lengkap & Gelar</label>
                                    <input type="text" x-model="selectedDistribusi.pj_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-emerald-300 font-bold text-xs">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">NIP</label>
                                        <input type="text" x-model="selectedDistribusi.pj_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Jabatan / Ruangan</label>
                                        <input type="text" x-model="selectedDistribusi.pj_jabatan" placeholder="Supervisor Front Office" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- LEMBAR CETAK BAST DISTRIBUSI (BERITA ACARA PENYERAHAN BARANG SESUAI DOKUMEN RESMI) -->
                <template x-if="selectedDistribusi">
                    <div class="bg-gray-200 p-3 sm:p-6 rounded-2xl border border-slate-700 flex justify-center items-start overflow-y-auto max-h-[75vh] custom-scrollbar shadow-inner">
                        <div id="print-area-distribusi" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 10pt; color: #000000 !important; background-color: #ffffff !important; min-height: 100%; box-sizing: border-box;" class="w-full max-w-[760px] shrink-0 bg-white text-black p-8 sm:p-12 md:p-14 shadow-2xl rounded-sm space-y-3.5 select-text print:p-0 print:m-0 print:shadow-none print:max-w-none">
                            
                            <!-- KOP SURAT RESMI -->
                            <div class="border-b-[2.5px] border-black pb-2 mb-1" style="border-bottom: 2.5px solid #000000;">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                        <img src="{{ asset('img/logo-bondowoso.png') }}" alt="Logo Dinas Bondowoso" class="h-16 w-16 object-contain">
                                    </div>
                                    <div class="flex-1 text-center text-black" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                        <h4 class="font-bold text-[11pt] sm:text-[12pt] uppercase tracking-normal leading-tight text-black m-0 p-0">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                        <h3 class="font-bold text-[12.5pt] sm:text-[13.5pt] uppercase tracking-normal leading-tight text-black mt-0.5 mb-0 p-0">RUMAH SAKIT UMUM DAERAH dr. H. KOESNADI</h3>
                                        <p class="text-[8.5pt] sm:text-[9pt] italic leading-tight text-black mt-0.5 mb-0 p-0">Jl. Kapten Pierre Tendean No. 3 Telepon (0332) 421974. Fax.0332 422311</p>
                                        <p class="text-[8.5pt] sm:text-[9pt] leading-tight text-black m-0 p-0">Website: rsudrkoesnadi.go.id, Email: rsu.koesnadi@gmail.com</p>
                                        <p class="font-bold text-[10pt] sm:text-[10.5pt] uppercase text-black mt-1 mb-0 p-0" style="letter-spacing: 0.35em;">B O N D O W O S O</p>
                                    </div>
                                    <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                        <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="h-16 w-16 object-contain">
                                    </div>
                                </div>
                            </div>
                            <div class="text-right text-[8pt] text-black pr-1 mb-2 font-normal">
                                Kode Pos: 68214
                            </div>

                            <!-- JUDUL SURAT & NOMOR -->
                            <div class="text-center text-black mb-3">
                                <h3 class="font-bold text-[11pt] sm:text-[11.5pt] uppercase underline tracking-normal text-black m-0">BERITA ACARA PENYERAHAN BARANG</h3>
                                <p class="text-[9.5pt] sm:text-[10pt] font-semibold text-black mt-1 m-0">Nomor : <span x-text="selectedDistribusi.nomor_bast || '032 / 034 / 430.10.7 / 2026'"></span></p>
                            </div>

                            <!-- PEMBUKA -->
                            <p class="text-justify mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                                Pada hari ini <strong x-text="selectedDistribusi.hari || 'Kamis'"></strong> tanggal <strong x-text="selectedDistribusi.tanggal_angka || '13'"></strong> bulan <strong x-text="selectedDistribusi.bulan || 'Agustus'"></strong> tahun <strong x-text="selectedDistribusi.tahun || '2026'"></strong>, yang bertanda tangan di bawah ini :
                            </p>

                            <!-- PIHAK KESATU (PENGURUS BARANG) -->
                            <div class="space-y-1 mb-2 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                                <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; width: 85px; vertical-align: top; padding: 1.5px 0;">Nama</td>
                                        <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold" x-text="selectedDistribusi.pengurus_nama || 'BUDI HARTONO,S.Sos'">BUDI HARTONO,S.Sos</td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">NIP</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pengurus_nip || '19760229 200801 1 010'">19760229 200801 1 010</td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Jabatan</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pengurus_jabatan || 'Pengurus Barang'">Pengurus Barang</td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Ruangan</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pengurus_ruangan || 'Gudang Perbekalan'">Gudang Perbekalan</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- PARAGRAF PENGESAHAN SK BUPATI -->
                            <p class="text-justify my-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                                Dalam hal ini selaku Pengurus Barang Aset Tahun Anggaran <span x-text="selectedDistribusi.tahun_anggaran || selectedDistribusi.tahun || '2025'"></span> Rumah Sakit Umum Dr. H. Koesnadi Bondowoso, berdasarkan Surat Keputusan Bupati Kabupaten Bondowoso sesuai Nomor : <span x-text="selectedDistribusi.sk_bupati_nomor || '188.45/969/430.4.2/2024'"></span> tanggal <span x-text="selectedDistribusi.sk_bupati_tanggal || '02 Januari 2025'"></span>
                            </p>

                            <!-- PIHAK KEDUA (PENERIMA BARANG) -->
                            <p class="text-[9.5pt] sm:text-[10pt] mb-1 font-normal text-black">
                                Dengan ini menyerahkan barang kepada :
                            </p>
                            <div class="space-y-1 mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                                <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; width: 85px; vertical-align: top; padding: 1.5px 0;">Nama</td>
                                        <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold" x-text="selectedDistribusi.pj_nama"></td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">NIP</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pj_nip"></td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Jabatan</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pj_jabatan || ('Supervisor ' + selectedDistribusi.unit_nama)"></td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Ruangan</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.unit_nama"></td>
                                    </tr>
                                </table>
                            </div>

                            <!-- TABEL RESMI BARANG DISTRIBUSI -->
                            <div class="my-2.5 overflow-x-auto">
                                <table class="w-full text-black border-collapse border border-black text-[9pt] sm:text-[9.5pt]" style="border-collapse: collapse; width: 100%; border: 1px solid black;">
                                    <thead>
                                        <tr style="background-color:#ffffff; font-weight:700; border:1px solid black; color:#000000;">
                                            <th style="border:1px solid black; padding:6px 8px; text-align:center; width:5%; background-color:#ffffff; font-weight:700;">No</th>
                                            <th style="border:1px solid black; padding:6px 12px; text-align:left; width:26%; background-color:#ffffff; font-weight:700;">Uraian Barang</th>
                                            <th style="border:1px solid black; padding:6px 12px; text-align:left; width:25%; background-color:#ffffff; font-weight:700;">Merk / Type</th>
                                            <th style="border:1px solid black; padding:6px 8px; text-align:center; width:6%; background-color:#ffffff; font-weight:700;">Vol</th>
                                            <th style="border:1px solid black; padding:6px 8px; text-align:center; width:8%; background-color:#ffffff; font-weight:700;">Satuan</th>
                                            <th style="border:1px solid black; padding:6px 8px; text-align:center; width:10%; background-color:#ffffff; font-weight:700;">Kondisi</th>
                                            <th style="border:1px solid black; padding:6px 12px; text-align:left; width:20%; background-color:#ffffff; font-weight:700;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(sub, idx) in (selectedDistribusi.items || []).filter(i => (i.qty_acc === null ? true : i.qty_acc > 0))" :key="idx">
                                            <tr style="border:1px solid black; background-color:#ffffff; color:#000000;">
                                                <td style="border:1px solid black; padding:6px 8px; text-align:center;" x-text="idx + 1"></td>
                                                <td style="border:1px solid black; padding:6px 12px; text-align:left; font-weight:700;" x-text="sub.nama_barang"></td>
                                                <td style="border:1px solid black; padding:6px 12px; text-align:left;" x-text="sub.spesifikasi || sub.merk_type || '-'"></td>
                                                <td style="border:1px solid black; padding:6px 8px; text-align:center; font-weight:700;"
                                                    :title="sub.qty_acc !== null ? ('Volume Di-ACC: ' + sub.qty_acc + ' | Volume Diajukan: ' + sub.qty) : ('Volume Diajukan: ' + sub.qty + ' | Belum Di-ACC Admin')"
                                                    x-text="(sub.qty_acc !== null && sub.qty_acc !== undefined) ? sub.qty_acc : (sub.vol_bast !== undefined ? sub.vol_bast : '-')"></td>
                                                <td style="border:1px solid black; padding:6px 8px; text-align:center;" x-text="sub.satuan"></td>
                                                <td style="border:1px solid black; padding:6px 8px; text-align:center;" x-text="sub.kondisi || 'Baik'"></td>
                                                <td style="border:1px solid black; padding:6px 12px; text-align:left;" x-text="sub.keterangan || selectedDistribusi.keterangan || '-'"></td>
                                            </tr>
                                        </template>
                                        <template x-if="!selectedDistribusi.items || selectedDistribusi.items.length === 0">
                                            <tr style="border:1px solid black; background-color:#ffffff; color:#000000;">
                                                <td style="border:1px solid black; padding:6px 8px; text-align:center;">1</td>
                                                <td style="border:1px solid black; padding:6px 12px; text-align:left; font-weight:700;" x-text="selectedDistribusi.barang_nama"></td>
                                                <td style="border:1px solid black; padding:6px 12px; text-align:left;" x-text="(selectedDistribusi.merk ? (selectedDistribusi.merk + ' ' + (selectedDistribusi.type || '')) : '-')"></td>
                                                <td style="border:1px solid black; padding:6px 8px; text-align:center; font-weight:700;" x-text="selectedDistribusi.volume"></td>
                                                <td style="border:1px solid black; padding:6px 8px; text-align:center;" x-text="selectedDistribusi.satuan"></td>
                                                <td style="border:1px solid black; padding:6px 8px; text-align:center;">Baik</td>
                                                <td style="border:1px solid black; padding:6px 12px; text-align:left;" x-text="selectedDistribusi.keterangan || '-'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <!-- PENUTUP -->
                            <p class="text-justify my-3 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                                Demikian Berita Acara Penyerahan Barang ini dibuat rangkap secukupnya untuk dipergunakan sebagaimana mestinya.
                            </p>

                            <!-- TANDA TANGAN (KIRI: TTD ELEKTRONIK PENGURUS BARANG, KANAN: TTD BASAH KEPALA RUANGAN/PJ) -->
                            <div class="grid grid-cols-2 gap-8 text-center text-black text-[9.5pt] sm:text-[10pt] mt-6 pt-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                                <div>
                                    <p class="m-0">Yang Menyerahkan</p>
                                    <p class="font-bold m-0">Pengurus Barang Aset</p>
                                    
                                    <!-- TTD Elektronik BSrE Pengurus Barang (Hanya Tampil Jika Status Sudah Ditandatangani) -->
                                    <div class="my-1 flex items-center justify-center" style="height: 52px; min-height: 52px;">
                                        <template x-if="selectedDistribusi.signed">
                                            <div style="padding:4px; border:1.5px solid #0d9488; background:#f0fdfa; border-radius:5px; display:flex; align-items:center; gap:6px; text-align:left;">
                                                <img :src="getQrCodeSvg(window.location.origin + '/validasi-tte/' + (selectedDistribusi.nomor_bast || 'BSRE-DISTRIBUSI'))" alt="QR TTE" style="width:36px; height:36px; flex-shrink:0;">
                                                <div style="font-size:7.5px; line-height:1.35; color:#1e293b;">
                                                    <div style="font-weight:700; color:#134e4a;">DITANDATANGANI ELEKTRONIK</div>
                                                    <div style="color:#374151;">Pengurus Barang Aset</div>
                                                    <div style="font-size:6.5px; color:#6b7280; font-family:monospace;">Sertifikat BSrE - BSSN</div>
                                                    <div style="font-size:6.5px; color:#0f766e; font-family:monospace; font-weight:700;" x-show="selectedDistribusi.tgl_signed && selectedDistribusi.tgl_signed !== '-'" x-text="'Tgl: ' + selectedDistribusi.tgl_signed"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <p class="font-bold underline uppercase m-0" x-text="selectedDistribusi.pengurus_nama || 'BUDI HARTONO,S.Sos'"></p>
                                    <p class="m-0" x-text="'NIP. ' + (selectedDistribusi.pengurus_nip || '19760229 200801 1 010')"></p>
                                </div>

                                <div>
                                    <p class="m-0">Yang Menerima</p>
                                    <p class="font-bold m-0" x-text="'Kepala Ruangan ' + (selectedDistribusi.unit_nama || 'Unit')"></p>
                                    
                                    <!-- Ruang Tanda Tangan Basah Manual (Tinggi presisi 52px agar nama sejajar horizontal sempurna) -->
                                    <div class="my-1 flex items-center justify-center" style="height: 52px; min-height: 52px;"></div>

                                    <p class="font-bold underline uppercase m-0" x-text="selectedDistribusi.pj_nama"></p>
                                    <p class="m-0" x-text="'NIP. ' + selectedDistribusi.pj_nip"></p>
                                </div>
                            </div>

                        </div>
                    </div>
                </template>

            </div>
        </div>
