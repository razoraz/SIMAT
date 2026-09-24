<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak BAST Pelimpahan BMD - {{ $mutasi->nomor_bamb }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            @page {
                size: A4 portrait;
                margin: 15mm 15mm 15mm 15mm;
            }
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center">

    <!-- Action Toolbar (Hidden when printing) -->
    <div class="no-print w-full max-w-4xl bg-slate-950/90 border border-slate-800 rounded-2xl p-4 mb-6 flex items-center justify-between shadow-xl">
        <div class="flex items-center space-x-3">
            <a href="{{ route('mutasi.eksternal') }}"
               class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer">
                <span>&larr;</span>
                <span>Kembali ke Katalog</span>
            </a>
            <div>
                <h2 class="text-sm font-bold text-white">Lembar Cetak Resmi BAST Pelimpahan BMD</h2>
                <p class="text-[11px] text-slate-400 font-mono">No: {{ $mutasi->nomor_bamb }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            @if($mutasi->dokumen_lampiran)
            <a href="{{ asset('storage/' . $mutasi->dokumen_lampiran) }}" target="_blank"
               class="px-4 py-2 rounded-xl bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-300 border border-indigo-500/40 text-xs font-bold transition-all flex items-center space-x-1.5">
                <span>📄</span>
                <span>Scan BAST Asli</span>
            </a>
            @endif
            <button type="button" onclick="window.print()"
                    class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Official Paper Document -->
    @php
        \Carbon\Carbon::setLocale('id');
        $tglObj = $mutasi->tanggal_mutasi ?: ($mutasi->astap?->created_at ?: now());
        $hariTgl = \Carbon\Carbon::parse($tglObj)->isoFormat('dddd');
        $tglFormatted = \Carbon\Carbon::parse($tglObj)->isoFormat('D MMMM Y');
        $astap = $mutasi->astap;
        $registers = $astap?->registers ?? collect();
        $totalVol = (int) ($mutasi->jumlah_volume ?: ($registers->count() ?: 1));
        $nilaiTotal = (float) ($mutasi->nilai_perolehan ?: ($astap?->total_realisasi ?: 0));
        $hargaSatuan = $totalVol > 0 ? ($nilaiTotal / $totalVol) : $nilaiTotal;
        $kode108 = $astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: ($astap?->jenisAstap?->jenis ?: '1.3.2.00.00.00'));
    @endphp

    <div class="print-container bg-white text-black max-w-4xl w-full p-8 sm:p-12 shadow-2xl rounded-sm text-[10pt] leading-relaxed"
         style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
        
        <!-- KOP SURAT PEMKAB & RSUD -->
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

        <!-- JUDUL SURAT -->
        <div class="text-center mb-4">
            <h3 class="font-bold text-[12pt] uppercase underline tracking-normal m-0 text-black">BERITA ACARA SERAH TERIMA (BAST)</h3>
            <h4 class="font-bold text-[10.5pt] uppercase tracking-normal m-0 text-black">PELIMPAHAN BARANG MILIK DAERAH (BMD)</h4>
            <p class="text-[10pt] font-semibold mt-1 text-black">Nomor : <span class="font-mono font-bold">{{ $mutasi->nomor_bamb }}</span></p>
        </div>

        <!-- PARAGRAF PEMBUKA -->
        <p class="text-justify mb-3 text-[10pt]">
            Pada hari ini <strong class="capitalize">{{ $hariTgl }}</strong> tanggal <strong>{{ $tglFormatted }}</strong>, bertempat di RSUD Dr. H. Koesnadi Kabupaten Bondowoso, yang bertanda tangan di bawah ini :
        </p>

        <!-- PIHAK PERTAMA & KEDUA -->
        <div class="space-y-3 mb-4 text-[9.5pt]">
            <!-- Pihak Pertama -->
            <table class="w-full">
                <tr>
                    <td class="w-6 align-top font-bold">1.</td>
                    <td class="w-32 align-top">Nama</td>
                    <td class="w-3 align-top">:</td>
                    <td class="align-top font-bold">{{ $mutasi->pj_asal_nama ?: 'Pejabat Penyerah OPD Pengirim' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">NIP</td>
                    <td class="align-top">:</td>
                    <td class="align-top font-mono">{{ $mutasi->pj_asal_nip ?: '-' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">Jabatan</td>
                    <td class="align-top">:</td>
                    <td class="align-top">{{ $mutasi->pj_asal_jabatan ?: 'Pengurus Barang / PPK Asal' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">Instansi / SKPD</td>
                    <td class="align-top">:</td>
                    <td class="align-top font-semibold">{{ $mutasi->opd_asal }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3" class="pt-0.5 italic text-slate-700">
                        Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong> (Yang Menyerahkan).
                    </td>
                </tr>
            </table>

            <!-- Pihak Kedua -->
            <table class="w-full mt-2">
                <tr>
                    <td class="w-6 align-top font-bold">2.</td>
                    <td class="w-32 align-top">Nama</td>
                    <td class="w-3 align-top">:</td>
                    <td class="align-top font-bold">{{ $mutasi->pj_tujuan_nama ?: 'dr. H. Yus Priyatna, Sp.P' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">NIP</td>
                    <td class="align-top">:</td>
                    <td class="align-top font-mono">{{ $mutasi->pj_tujuan_nip ?: '196904121999031004' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">Jabatan</td>
                    <td class="align-top">:</td>
                    <td class="align-top">{{ $mutasi->pj_tujuan_jabatan ?: 'Pengurus Barang / PPK RSUD Dr. H. Koesnadi' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">Instansi Penerima</td>
                    <td class="align-top">:</td>
                    <td class="align-top font-semibold">RSUD dr. H. Koesnadi Bondowoso (Unit: {{ $mutasi->ruangan_tujuan ?: 'RSUD' }})</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3" class="pt-0.5 italic text-slate-700">
                        Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong> (Yang Menerima).
                    </td>
                </tr>
            </table>
        </div>

        <!-- DASAR HUKUM / PELIMPAHAN -->
        <p class="text-justify mb-2 text-[10pt]">
            Berdasarkan <strong>{{ $mutasi->nomor_sk_dasar ? 'Surat Keputusan / Penetapan Nomor: ' . $mutasi->nomor_sk_dasar : 'Dokumen Pelimpahan BMD Nomor: ' . $mutasi->nomor_bamb }}</strong>, 
            PIHAK PERTAMA menyerahkan Barang Milik Daerah (BMD) kepada PIHAK KEDUA, dan PIHAK KEDUA menerima penyerahan barang tersebut dalam keadaan baik dan lengkap untuk dipergunakan pada unit pelayanan RSUD dr. H. Koesnadi Kabupaten Bondowoso, dengan rincian sebagai berikut:
        </p>

        <!-- TABEL RINCIAN BARANG BMD -->
        <div class="my-3">
            <table class="w-full border-collapse border border-black text-[9pt]">
                <thead>
                    <tr class="bg-gray-100 font-bold text-center">
                        <th class="border border-black px-2 py-1.5 w-8">No</th>
                        <th class="border border-black px-2 py-1.5 text-left">Nama Barang / Spesifikasi</th>
                        <th class="border border-black px-2 py-1.5 font-mono">Kode 108</th>
                        <th class="border border-black px-2 py-1.5 font-mono">NIBAR</th>
                        <th class="border border-black px-2 py-1.5 w-14">Tahun</th>
                        <th class="border border-black px-2 py-1.5 w-14">Kondisi</th>
                        <th class="border border-black px-2 py-1.5 text-right w-28">Nilai Aset (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @if($registers->isNotEmpty())
                        @foreach($registers as $idx => $reg)
                        <tr>
                            <td class="border border-black px-2 py-1 text-center font-bold">{{ $idx + 1 }}</td>
                            <td class="border border-black px-2 py-1 font-semibold">{{ $astap->nama_barang }}</td>
                            <td class="border border-black px-2 py-1 font-mono text-center">{{ $kode108 }}</td>
                            <td class="border border-black px-2 py-1 font-mono text-[8.5pt]">{{ $reg->nibar ?: '-' }}</td>
                            <td class="border border-black px-2 py-1 text-center font-mono">{{ $mutasi->astap?->tahun_perolehan ?: date('Y', strtotime($tglObj)) }}</td>
                            <td class="border border-black px-2 py-1 text-center">{{ $reg->kondisi ?: ($mutasi->kondisi ?: 'Baik') }}</td>
                            <td class="border border-black px-2 py-1 text-right font-mono">{{ number_format($hargaSatuan, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="border border-black px-2 py-1 text-center font-bold">1</td>
                            <td class="border border-black px-2 py-1 font-semibold">{{ $astap?->nama_barang ?: 'Barang Pelimpahan SKPD' }}</td>
                            <td class="border border-black px-2 py-1 font-mono text-center">{{ $kode108 }}</td>
                            <td class="border border-black px-2 py-1 font-mono text-center">-</td>
                            <td class="border border-black px-2 py-1 text-center font-mono">{{ $mutasi->astap?->tahun_perolehan ?: date('Y', strtotime($tglObj)) }}</td>
                            <td class="border border-black px-2 py-1 text-center">{{ $mutasi->kondisi ?: 'Baik' }}</td>
                            <td class="border border-black px-2 py-1 text-right font-mono">{{ number_format($nilaiTotal, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <!-- Total Row -->
                    <tr class="font-bold bg-gray-100">
                        <td colspan="4" class="border border-black px-2 py-1.5 text-center uppercase tracking-wider">
                            TOTAL NILAI PEROLEHAN BMD ({{ $totalVol }} Unit / {{ $mutasi->satuan ?: 'Unit' }})
                        </td>
                        <td colspan="2" class="border border-black px-2 py-1.5 text-center">
                            {{ $totalVol }} {{ $mutasi->satuan ?: 'Unit' }}
                        </td>
                        <td class="border border-black px-2 py-1.5 text-right font-mono text-[9.5pt]">
                            Rp {{ number_format($nilaiTotal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($mutasi->alasan_mutasi)
        <p class="text-[9.5pt] mb-3">
            <strong>Catatan / Keterangan Pelimpahan:</strong> {{ $mutasi->alasan_mutasi }}
        </p>
        @endif

        <p class="text-justify mb-6 text-[10pt]">
            Demikian Berita Acara Serah Terima (BAST) ini dibuat dan ditandatangani oleh kedua belah pihak dengan itikad baik dan sebenarnya dalam rangkap secukupnya untuk dipergunakan sebagaimana mestinya.
        </p>

        <!-- TANDA TANGAN (3 KOLOM RESMI PEMKAB) -->
        <div class="grid grid-cols-2 gap-8 text-center text-[9.5pt] pt-2">
            <!-- Pihak Kesatu -->
            <div>
                <p class="font-bold text-slate-800">PIHAK KESATU</p>
                <p class="text-[9pt] text-slate-600">Yang Menyerahkan,</p>
                <div class="h-20 flex items-center justify-center">
                    <!-- Space for TTD / Stempel -->
                </div>
                <p class="font-bold underline text-[10pt] uppercase">{{ $mutasi->pj_asal_nama ?: 'Pejabat Penyerah OPD' }}</p>
                <p class="font-mono text-[9pt]">NIP. {{ $mutasi->pj_asal_nip ?: '..........................................' }}</p>
            </div>

            <!-- Pihak Kedua -->
            <div>
                <p class="font-bold text-slate-800">PIHAK KEDUA</p>
                <p class="text-[9pt] text-slate-600">Yang Menerima,</p>
                <div class="h-20 flex items-center justify-center">
                    <div style="padding:4px; border:1.5px solid #0d9488; background:#f0fdfa; border-radius:5px; display:inline-flex; align-items:center; gap:6px; text-align:left;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(url('/validasi-tte/' . ($mutasi->nomor_bamb ?: 'BSRE-PELIMPAHAN-BMD'))) }}" alt="QR TTE" style="width:36px; height:36px; flex-shrink:0;">
                        <div style="font-size:7.5px; line-height:1.35; color:#1e293b;">
                            <div style="font-weight:700; color:#134e4a;">DITANDATANGANI ELEKTRONIK</div>
                            <div style="color:#374151;">Pengurus Barang Aset</div>
                            <div style="font-size:6.5px; color:#6b7280; font-family:monospace;">Sertifikat BSrE - BSSN</div>
                        </div>
                    </div>
                </div>
                <p class="font-bold underline text-[10pt] uppercase">{{ $mutasi->pj_tujuan_nama ?: 'BUDI HARTONO, S.Sos' }}</p>
                <p class="font-mono text-[9pt]">NIP. {{ $mutasi->pj_tujuan_nip ?: '19760229 200801 1 010' }}</p>
            </div>
        </div>

        <!-- Mengetahui Direktur RSUD -->
        <div class="mt-6 text-center text-[9.5pt]">
            <p class="font-bold text-slate-800">Mengetahui,</p>
            <p class="font-bold text-[9pt] text-slate-700">DIREKTUR RSUD dr. H. KOESNADI KABUPATEN BONDOWOSO</p>
            <div class="h-20 flex items-center justify-center">
                <!-- Space for TTD / Stempel -->
            </div>
            <p class="font-bold underline text-[10pt] uppercase">dr. DIAN ARISANDI, M.Kes</p>
            <p class="font-mono text-[9pt]">NIP. 19730514 200212 2 003</p>
        </div>

    </div>

</body>
</html>
