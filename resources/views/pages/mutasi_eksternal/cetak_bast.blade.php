<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak BAST Mutasi Eksternal BMD - {{ $mutasi->nomor_bamb }}</title>
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
    <div class="no-print w-full max-w-[760px] bg-slate-950/90 border border-slate-800 rounded-2xl p-4 mb-6 flex items-center justify-between shadow-xl">
        <div class="flex items-center space-x-3">
            <a href="{{ request('returnTo') ?: route('mutasi.eksternal') }}"
               class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer">
                <span>&larr;</span>
                <span>{{ request('returnTo') ? 'Kembali ke BAST' : 'Kembali ke Katalog' }}</span>
            </a>
            <div>
                <h2 class="text-sm font-bold text-white">Lembar Cetak Resmi BAST Mutasi Eksternal BMD</h2>
                <p class="text-[11px] text-slate-400 font-mono">No: {{ $mutasi->nomor_bamb }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            @if($mutasi->dokumen_lampiran)
            <a href="{{ asset('storage/' . $mutasi->dokumen_lampiran) }}" target="_blank"
               class="px-3.5 py-2 rounded-xl bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-300 border border-indigo-500/40 text-xs font-bold transition-all flex items-center space-x-1.5">
                <span>📄</span>
                <span>Scan BAST Asli</span>
            </a>
            @endif
            <button type="button" onclick="window.print()"
                    class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Surat</span>
            </button>
        </div>
    </div>

    <!-- Official Paper Document -->
    @php
        \Carbon\Carbon::setLocale('id');
        $isKeluar = ($mutasi->tipe === 'keluar');
        $tglObj = $mutasi->tanggal_mutasi ?: ($mutasi->astap?->created_at ?: now());
        $hariTgl = \Carbon\Carbon::parse($tglObj)->isoFormat('dddd');
        $tglFormatted = \Carbon\Carbon::parse($tglObj)->isoFormat('D MMMM Y');
        $astap = $mutasi->astap;
        $registers = $astap?->registers ?? collect();
        $totalVol = (int) ($mutasi->jumlah_volume ?: ($registers->count() ?: 1));
        $nilaiTotal = (float) ($mutasi->nilai_perolehan ?: ($astap?->total_realisasi ?: 0));
        $hargaSatuan = $totalVol > 0 ? ($nilaiTotal / $totalVol) : $nilaiTotal;
        $kode108 = $astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: ($astap?->jenisAstap?->jenis ?: '1.3.2.00.00.00'));
        $specJson = is_array($astap?->spesifikasi_json) ? $astap->spesifikasi_json : (is_string($astap?->spesifikasi_json) ? (json_decode($astap->spesifikasi_json, true) ?: []) : []);
        $extractedUnits = \App\Http\Controllers\MutasiEksternalController::extractUnitsFromSpec(
            $specJson ?: [],
            $astap?->nama_barang ?: 'Barang Mutasi Eksternal',
            $mutasi->kondisi ?: 'Baik',
            $mutasi->ruangan_tujuan ?: 'RSUD'
        );

        // Data Pejabat Pengurus Barang RSUD dr. H. Koesnadi
        $isPjYus = str_contains(strtolower($mutasi->pj_tujuan_nama ?? ''), 'yus');
        $pbNama = (!$isPjYus && !empty($mutasi->pj_tujuan_nama) && !str_contains(strtolower($mutasi->pj_tujuan_nama), 'pejabat penerima')) 
            ? $mutasi->pj_tujuan_nama 
            : 'BUDI HARTONO, S.Sos';
        $pbNip = (!$isPjYus && !empty($mutasi->pj_tujuan_nip) && !str_contains($mutasi->pj_tujuan_nip, '19771002') && !str_contains($mutasi->pj_tujuan_nip, '19690412'))
            ? $mutasi->pj_tujuan_nip 
            : '19760229 200801 1 010';
        $pbJabatan = (!$isPjYus && !empty($mutasi->pj_tujuan_jabatan) && !str_contains(strtolower($mutasi->pj_tujuan_jabatan), 'direktur'))
            ? $mutasi->pj_tujuan_jabatan 
            : 'Pengurus Barang Pengguna';

        // Mengetahui: Direktur RSUD Dr. H. Koesnadi
        $direkturNama = !empty($mutasi->direktur_nama) ? $mutasi->direktur_nama : 'dr. YUS PRIYATNA ADRYANTO, Sp.P, FISR';
        $direkturNip = !empty($mutasi->direktur_nip) ? $mutasi->direktur_nip : '19771002 200604 1 006';

        $nomorSurat = $mutasi->nomor_bamb ?: ($astap?->bast_dokumen_nomor ?: ('BAST-EXT-' . str_pad($mutasi->id, 4, '0', STR_PAD_LEFT) . '/430.10.7/' . date('Y', strtotime($tglObj))));
        $opdAsal = $mutasi->opd_asal ?: ($astap?->mutasi_asal ?: 'SKPD / Instansi Luar');
        $ruangRSUD = $mutasi->unit?->nama ?: ($mutasi->ruangan_tujuan ?: ($astap?->alamat_barang ?: 'Gudang/Ruangan RSUD'));
        $opdTujuan = $mutasi->opd_tujuan ?: ('RSUD dr. H. Koesnadi (' . $ruangRSUD . ')');
        $alamatInstansi = $mutasi->alamat_instansi ?: ($mutasi->astap?->spesifikasi_json['alamat_instansi'] ?? '');

        // Identitas Pihak Kesatu & Pihak Kedua Disesuaikan dengan Arah Mutasi
        if ($isKeluar) {
            // Mutasi Keluar: Pihak Kesatu = RSUD Dr. H. Koesnadi (Menyerahkan), Pihak Kedua = OPD Luar (Menerima)
            $isP1Yus = str_contains(strtolower($mutasi->pj_asal_nama ?? ''), 'yus');
            $p1Nama = (!$isP1Yus && !empty($mutasi->pj_asal_nama)) ? $mutasi->pj_asal_nama : $pbNama;
            $p1Nip = (!$isP1Yus && !empty($mutasi->pj_asal_nip) && $mutasi->pj_asal_nip !== '-' && !str_contains($mutasi->pj_asal_nip, '19771002') && !str_contains($mutasi->pj_asal_nip, '19690412')) ? $mutasi->pj_asal_nip : $pbNip;
            $p1Jabatan = (!$isP1Yus && !empty($mutasi->pj_asal_jabatan) && !str_contains(strtolower($mutasi->pj_asal_jabatan), 'direktur')) ? $mutasi->pj_asal_jabatan : 'Pengurus Barang Pengguna';
            $p1Instansi = 'RSUD dr. H. Koesnadi Kabupaten Bondowoso';
            $p1Alamat = 'Jl. Kapten Piere Tendean No. 3, Bondowoso';

            $p2Nama = $mutasi->pj_tujuan_nama ?: 'Pejabat Penerima OPD';
            $p2Nip = $mutasi->pj_tujuan_nip ?: '-';
            $p2Jabatan = $mutasi->pj_tujuan_jabatan ?: 'Pejabat Penerima OPD';
            $p2Instansi = $mutasi->opd_tujuan ?: 'SKPD / Instansi Luar';
            $p2Alamat = $alamatInstansi ?: '-';
        } else {
            // Mutasi Masuk: Pihak Kesatu = OPD Luar (Menyerahkan), Pihak Kedua = RSUD Dr. H. Koesnadi (Menerima)
            $p1Nama = $mutasi->pj_asal_nama ?: 'Pejabat Penyerah OPD';
            $p1Nip = $mutasi->pj_asal_nip ?: '-';
            $p1Jabatan = $mutasi->pj_asal_jabatan ?: 'Pengurus Barang / PPK Asal';
            $p1Instansi = $opdAsal;
            $p1Alamat = $alamatInstansi;

            $p2Nama = $pbNama;
            $p2Nip = $pbNip;
            $p2Jabatan = $pbJabatan;
            $p2Instansi = 'RSUD dr. H. Koesnadi Kabupaten Bondowoso (Unit: ' . $ruangRSUD . ')';
            $p2Alamat = 'Jl. Kapten Piere Tendean No. 3, Bondowoso';
        }

        // Rincian Barang / Unit yang Diserahterimakan
        $printItems = [];
        if ($isKeluar && $mutasi->mutasiRegisters && $mutasi->mutasiRegisters->isNotEmpty()) {
            foreach ($mutasi->mutasiRegisters as $mr) {
                $reg = $mr->register;
                $ast = $reg?->astap ?: $astap;
                $uNama = $ast?->nama_barang ?: 'Barang Mutasi Eksternal';
                $uKondisi = $mr->kondisi ?: ($reg?->kondisi ?: ($mutasi->kondisi ?: 'Baik'));
                $uHarga = (float)($reg?->harga_satuan ?: ($ast?->harga_satuan ?: $hargaSatuan));
                $printItems[] = [
                    'nama_barang'  => $uNama,
                    'nibar'        => $reg?->nibar ?: ($reg?->no_register ?: '-'),
                    'kode_108'     => $ast?->kode_108 ?: $kode108,
                    'kondisi'      => $uKondisi,
                    'satuan'       => $ast?->satuan ?: ($mutasi->satuan ?: 'Unit'),
                    'harga_satuan' => $uHarga,
                    'volume'       => 1,
                    'nilai_total'  => $uHarga,
                ];
            }
        } elseif ($registers->isNotEmpty()) {
            foreach ($registers as $idx => $reg) {
                $uNama = $extractedUnits[$idx]['nama'] ?? ($astap?->nama_barang ?: 'Barang Mutasi Eksternal');
                $uKondisi = $reg->kondisi ?: ($extractedUnits[$idx]['kondisi'] ?? ($mutasi->kondisi ?: 'Baik'));
                $uHarga = !empty($extractedUnits[$idx]['nilai_satuan']) ? (float)$extractedUnits[$idx]['nilai_satuan'] : $hargaSatuan;
                $printItems[] = [
                    'nama_barang'  => $uNama,
                    'nibar'        => $reg->nibar ?: '-',
                    'kode_108'     => $kode108,
                    'kondisi'      => $uKondisi,
                    'satuan'       => $mutasi->satuan ?: ($astap?->satuan ?: 'Unit'),
                    'harga_satuan' => $uHarga,
                    'volume'       => 1,
                    'nilai_total'  => $uHarga,
                ];
            }
        } else {
            $printItems[] = [
                'nama_barang'  => $astap?->nama_barang ?: 'Barang Mutasi Eksternal',
                'nibar'        => '-',
                'kode_108'     => $kode108,
                'kondisi'      => $mutasi->kondisi ?: 'Baik',
                'satuan'       => $mutasi->satuan ?: ($astap?->satuan ?: 'Unit'),
                'harga_satuan' => $hargaSatuan,
                'volume'       => $totalVol,
                'nilai_total'  => $nilaiTotal,
            ];
        }

        $printTotalVol = array_sum(array_column($printItems, 'volume')) ?: $totalVol;
        $printNilaiTotal = array_sum(array_column($printItems, 'nilai_total')) ?: $nilaiTotal;
    @endphp

    <div class="print-container w-full max-w-[760px] shrink-0 bg-white text-black p-8 sm:p-12 md:p-14 shadow-2xl rounded-sm space-y-3.5 select-text print:p-0 print:m-0 print:shadow-none print:max-w-none"
         style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 10pt; color: #000000 !important; background-color: #ffffff !important; min-height: 100%; box-sizing: border-box;">
        
        <!-- KOP SURAT PEMKAB & RSUD DENGAN DUA LOGO RESMI (PERSIS DENGAN BAST LAINNYA) -->
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

        <!-- JUDUL SURAT RESMI & NOMOR -->
        <div class="text-center text-black mb-3">
            <h3 class="font-bold text-[11pt] sm:text-[11.5pt] uppercase underline tracking-normal text-black m-0">
                {{ $isKeluar ? 'BERITA ACARA SERAH TERIMA PEMINDAHTANGANAN BMD' : 'BERITA ACARA SERAH TERIMA BARANG MILIK DAERAH (BMD)' }}
            </h3>
            <p class="text-[9.5pt] sm:text-[10pt] font-semibold text-black mt-1 m-0">
                Nomor : <span>{{ $nomorSurat }}</span>
            </p>
        </div>

        <!-- KLAUSUL PEMBUKA -->
        <p class="text-justify mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
            Pada hari ini <strong>{{ $hariTgl }}</strong> tanggal <strong>{{ $tglFormatted }}</strong>, bertempat di Rumah Sakit Umum Daerah dr. H. Koesnadi Kabupaten Bondowoso, yang bertanda tangan di bawah ini :
        </p>

        <!-- IDENTITAS PIHAK KESATU & PIHAK KEDUA -->
        <div class="space-y-1 mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
            <!-- PIHAK KESATU -->
            <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                <tr style="border: none !important;">
                    <td style="border: none !important; width: 22px; vertical-align: top; padding: 1.5px 0;" class="font-bold">1.</td>
                    <td style="border: none !important; width: 130px; vertical-align: top; padding: 1.5px 0;">Nama</td>
                    <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold uppercase">{{ $p1Nama }}</td>
                </tr>
                <tr style="border: none !important;">
                    <td style="border: none !important; padding: 1.5px 0;"></td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">NIP</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-mono">{{ $p1Nip }}</td>
                </tr>
                <tr style="border: none !important;">
                    <td style="border: none !important; padding: 1.5px 0;"></td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Jabatan</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">{{ $p1Jabatan }}</td>
                </tr>
                <tr style="border: none !important;">
                    <td style="border: none !important; padding: 1.5px 0;"></td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Instansi / OPD</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold uppercase">{{ $p1Instansi }}</td>
                </tr>
                @if(!empty($p1Alamat) && $p1Alamat !== '-')
                <tr style="border: none !important;">
                    <td style="border: none !important; padding: 1.5px 0;"></td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Alamat Instansi</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">{{ $p1Alamat }}</td>
                </tr>
                @endif
                <tr style="border: none !important;">
                    <td style="border: none !important; padding: 1.5px 0;"></td>
                    <td colspan="3" style="border: none !important; padding: 2px 0 6px 0;" class="italic">
                        Selanjutnya disebut sebagai <strong>PIHAK KESATU (Yang Menyerahkan)</strong>.
                    </td>
                </tr>

                <!-- PIHAK KEDUA -->
                <tr style="border: none !important;">
                    <td style="border: none !important; width: 22px; vertical-align: top; padding: 1.5px 0;" class="font-bold">2.</td>
                    <td style="border: none !important; width: 130px; vertical-align: top; padding: 1.5px 0;">Nama</td>
                    <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold uppercase">{{ $p2Nama }}</td>
                </tr>
                <tr style="border: none !important;">
                    <td style="border: none !important; padding: 1.5px 0;"></td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">NIP</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-mono">{{ $p2Nip }}</td>
                </tr>
                <tr style="border: none !important;">
                    <td style="border: none !important; padding: 1.5px 0;"></td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Jabatan</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">{{ $p2Jabatan }}</td>
                </tr>
                <tr style="border: none !important;">
                    <td style="border: none !important; padding: 1.5px 0;"></td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Instansi / Unit</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold uppercase">{{ $p2Instansi }}</td>
                </tr>
                @if(!empty($p2Alamat) && $p2Alamat !== '-')
                <tr style="border: none !important;">
                    <td style="border: none !important; padding: 1.5px 0;"></td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Alamat Instansi</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">{{ $p2Alamat }}</td>
                </tr>
                @endif
                <tr style="border: none !important;">
                    <td style="border: none !important; padding: 1.5px 0;"></td>
                    <td colspan="3" style="border: none !important; padding: 2px 0 4px 0;" class="italic">
                        Selanjutnya disebut sebagai <strong>PIHAK KEDUA (Yang Menerima)</strong>.
                    </td>
                </tr>
            </table>
        </div>

        <!-- KLAUSUL PERNYATAAN PENYERAHAN -->
        <p class="text-justify mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
            PIHAK KESATU menyerahkan kepada PIHAK KEDUA, dan PIHAK KEDUA menerima dari PIHAK KESATU Barang Milik Daerah (BMD) hasil mutasi eksternal antar-OPD dengan rincian sebagai berikut :
        </p>

        <!-- TABEL RESMI BARANG MILIK DAERAH (BMD) DENGAN FORMAT PERSIS SEPERTI BAST LAINNYA -->
        <div class="my-2.5 overflow-x-auto">
            <table class="w-full text-center border-collapse border border-black text-[9pt] sm:text-[9.5pt]" style="border-collapse: collapse; width: 100%; border: 1px solid black; background-color: #ffffff !important; color: #000000 !important;">
                <thead>
                    <tr style="font-weight:700; border:1px solid black; background-color:#ffffff; color:#000000 !important;">
                        <th rowspan="2" style="border:1px solid black; padding:5px 4px; text-align:center; width:4%; font-weight:700; color:#000000;">No</th>
                        <th rowspan="2" style="border:1px solid black; padding:5px 6px; text-align:left; width:22%; font-weight:700; color:#000000;">Nama Barang / Aset</th>
                        <th rowspan="2" style="border:1px solid black; padding:5px 6px; text-align:left; width:18%; font-weight:700; color:#000000;">Kode 108 / NIBAR</th>
                        <th rowspan="2" style="border:1px solid black; padding:5px 4px; text-align:center; width:6%; font-weight:700; color:#000000;">Vol</th>
                        <th rowspan="2" style="border:1px solid black; padding:5px 4px; text-align:center; width:6%; font-weight:700; color:#000000;">Sat</th>
                        <th colspan="3" style="border:1px solid black; padding:3px; text-align:center; width:11%; font-weight:700; color:#000000;">Kondisi</th>
                        <th rowspan="2" style="border:1px solid black; padding:5px 6px; text-align:right; width:15%; font-weight:700; color:#000000;">Harga Satuan (Rp)</th>
                        <th rowspan="2" style="border:1px solid black; padding:5px 6px; text-align:right; width:18%; font-weight:700; color:#000000;">Nilai Total (Rp)</th>
                    </tr>
                    <tr style="font-weight:700; border:1px solid black; background-color:#ffffff; color:#000000 !important;">
                        <th style="border:1px solid black; padding:2px 3px; text-align:center; font-size:7.5pt; font-weight:700; color:#000000;">Baik</th>
                        <th style="border:1px solid black; padding:2px 3px; text-align:center; font-size:7.5pt; font-weight:700; color:#000000;">KB</th>
                        <th style="border:1px solid black; padding:2px 3px; text-align:center; font-size:7.5pt; font-weight:700; color:#000000;">RB</th>
                    </tr>
                </thead>
                <tbody style="background-color: #ffffff !important; color: #000000 !important;">
                    @foreach($printItems as $idx => $pItem)
                    @php
                        $knd = $pItem['kondisi'] ?? 'Baik';
                        $isBaik = ($knd === 'Baik' || $knd === 'B' || empty($knd));
                        $isKB = ($knd === 'Kurang Baik' || $knd === 'KB' || $knd === 'Rusak Ringan' || $knd === 'RR');
                        $isRB = ($knd === 'Rusak Berat' || $knd === 'RB' || $knd === 'Rusak');
                    @endphp
                    <tr style="border: 1px solid black; background-color: #ffffff !important; color: #000000 !important;">
                        <td class="px-1.5 py-1 text-center" style="border: 1px solid black;">{{ $idx + 1 }}</td>
                        <td class="px-2 py-1 text-left font-bold" style="border: 1px solid black;">{{ $pItem['nama_barang'] }}</td>
                        <td class="px-2 py-1 font-mono text-[8.5pt] text-left break-all" style="border: 1px solid black;">
                            <div>{{ $pItem['kode_108'] }}</div>
                            @if(!empty($pItem['nibar']) && $pItem['nibar'] !== '-')
                            <div class="text-[8pt] text-black">NIBAR: {{ $pItem['nibar'] }}</div>
                            @endif
                        </td>
                        <td class="px-1 py-1 text-center font-bold" style="border: 1px solid black;">{{ $pItem['volume'] }}</td>
                        <td class="px-1 py-1 text-center" style="border: 1px solid black;">{{ $pItem['satuan'] }}</td>
                        <td class="px-1 py-1 text-center text-[8.5pt]" style="border: 1px solid black;">{{ $isBaik ? '✓' : '' }}</td>
                        <td class="px-1 py-1 text-center text-[8.5pt]" style="border: 1px solid black;">{{ $isKB ? '✓' : '' }}</td>
                        <td class="px-1 py-1 text-center text-[8.5pt]" style="border: 1px solid black;">{{ $isRB ? '✓' : '' }}</td>
                        <td class="px-2 py-1 text-right font-mono text-[8.5pt]" style="border: 1px solid black;">{{ number_format($pItem['harga_satuan'], 0, ',', '.') }}</td>
                        <td class="px-2 py-1 text-right font-mono font-bold text-[8.5pt]" style="border: 1px solid black;">{{ number_format($pItem['nilai_total'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach

                    <!-- BARIS TOTAL JUMLAH -->
                    <tr style="font-weight: 700; border: 1px solid black; background-color: #ffffff !important; color: #000000 !important;">
                        <td colspan="3" style="border: 1px solid black; padding: 5px 8px; text-align: right; text-transform: uppercase;">Total Aset Yang Diserahkan</td>
                        <td style="border: 1px solid black; padding: 5px 4px; text-align: center;">{{ $printTotalVol }}</td>
                        <td style="border: 1px solid black; padding: 5px 4px; text-align: center;">{{ $printItems[0]['satuan'] ?? ($mutasi->satuan ?: 'Unit') }}</td>
                        <td colspan="3" style="border: 1px solid black;"></td>
                        <td colspan="2" style="border: 1px solid black; padding: 5px 8px; text-align: right; font-family: monospace; font-size: 9.5pt;">Rp {{ number_format($printNilaiTotal, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($mutasi->alasan_mutasi)
        <div class="my-2.5 p-2 rounded border border-black text-[9pt] leading-relaxed" style="background-color: #ffffff !important; color: #000000 !important;">
            <strong>{{ $isKeluar ? 'Maksud / Catatan Mutasi Keluar:' : 'Maksud / Catatan Mutasi Masuk:' }}</strong>
            <span>{{ $mutasi->alasan_mutasi }}</span>
        </div>
        @endif

        <!-- KLAUSUL PENUTUP -->
        <p class="text-justify my-3 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
            Demikian Berita Acara Serah Terima Barang Milik Daerah ini dibuat dengan sebenarnya dalam rangkap secukupnya untuk dipergunakan sebagaimana mestinya.
        </p>

        <!-- BLOK TANDA TANGAN RESMI (3 PIHAK: PENYERAH, PENERIMA BSrE, MENGETAHUI DIREKTUR) -->
        <div class="mt-5 pt-2 space-y-4" style="color: #000000 !important;">
            <!-- Baris Atas: Pihak Kesatu & Pihak Kedua -->
            <div class="grid grid-cols-2 gap-8 text-center text-black text-[9.5pt] sm:text-[10pt]" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; color: #000000 !important;">
                
                @if($isKeluar)
                    <!-- KELUAR: Kolom Kiri = Yang Menyerahkan (RSUD Koesnadi) -->
                    <div>
                        <p class="m-0">Yang Menyerahkan,</p>
                        <p class="font-bold m-0">{{ $p1Jabatan }}</p>
                        
                        <!-- TTD Elektronik BSrE RSUD jika signed -->
                        <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;">
                            @if($mutasi->signed)
                            <div style="padding:4px; border:1.5px solid #16a34a; background:#f0fdf4; border-radius:5px; display:inline-flex; align-items:center; gap:6px; text-align:left;">
                                @php
                                    $qrHash = $mutasi->qr_hash ?: md5($mutasi->nomor_bamb ?: ('ME-' . $mutasi->id));
                                    $qrUrl = url('/validasi-tte/' . $qrHash);
                                @endphp
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($qrUrl) }}" alt="QR TTE" style="width:36px; height:36px; flex-shrink:0;">
                                <div style="font-size:7.5px; line-height:1.35; color:#1e293b;">
                                    <div style="font-weight:700; color:#14532d;">DITANDATANGANI ELEKTRONIK</div>
                                    <div style="color:#374151;">Pengurus Barang Aset</div>
                                    <div style="font-size:6.5px; color:#6b7280; font-family:monospace;">Sertifikat BSrE - BSSN</div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <p class="font-bold underline uppercase m-0">{{ $p1Nama }}</p>
                        <p class="m-0">NIP. {{ $p1Nip }}</p>
                    </div>

                    <!-- KELUAR: Kolom Kanan = Yang Menerima (Pejabat Penerima OPD Luar) -->
                    <div>
                        <p class="m-0">Yang Menerima,</p>
                        <p class="font-bold m-0">{{ $p2Instansi }}</p>
                        
                        <!-- Ruang TTD Basah -->
                        <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;"></div>

                        <p class="font-bold underline uppercase m-0">{{ $p2Nama }}</p>
                        <p class="m-0">{{ $p2Nip && $p2Nip !== '-' ? 'NIP. ' . $p2Nip : 'NIP. ..........................................' }}</p>
                    </div>
                @else
                    <!-- MASUK: Kolom Kiri = Yang Menyerahkan (OPD Luar) -->
                    <div>
                        <p class="m-0">Yang Menyerahkan,</p>
                        <p class="font-bold m-0">{{ $p1Instansi }}</p>
                        
                        <!-- Ruang TTD Basah -->
                        <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;"></div>

                        <p class="font-bold underline uppercase m-0">{{ $p1Nama }}</p>
                        <p class="m-0">{{ $p1Nip && $p1Nip !== '-' ? 'NIP. ' . $p1Nip : 'NIP. ..........................................' }}</p>
                    </div>

                    <!-- MASUK: Kolom Kanan = Yang Menerima (Pengurus Barang RSUD) -->
                    <div>
                        <p class="m-0">Yang Menerima,</p>
                        <p class="font-bold m-0">{{ $p2Jabatan }}</p>
                        
                        <!-- TTD Elektronik BSrE RSUD jika signed -->
                        <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;">
                            @if($mutasi->signed)
                            <div style="padding:4px; border:1.5px solid #16a34a; background:#f0fdf4; border-radius:5px; display:inline-flex; align-items:center; gap:6px; text-align:left;">
                                @php
                                    $qrHash = $mutasi->qr_hash ?: md5($mutasi->nomor_bamb ?: ('ME-' . $mutasi->id));
                                    $qrUrl = url('/validasi-tte/' . $qrHash);
                                @endphp
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($qrUrl) }}" alt="QR TTE" style="width:36px; height:36px; flex-shrink:0;">
                                <div style="font-size:7.5px; line-height:1.35; color:#1e293b;">
                                    <div style="font-weight:700; color:#14532d;">DITANDATANGANI ELEKTRONIK</div>
                                    <div style="color:#374151;">Pengurus Barang Aset</div>
                                    <div style="font-size:6.5px; color:#6b7280; font-family:monospace;">Sertifikat BSrE - BSSN</div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <p class="font-bold underline uppercase m-0">{{ $p2Nama }}</p>
                        <p class="m-0">NIP. {{ $p2Nip }}</p>
                    </div>
                @endif
            </div>

            <!-- Baris Bawah: Mengetahui DIREKTUR RSUD Dr. H. Koesnandi -->
            <div class="text-center text-black text-[9.5pt] sm:text-[10pt] pt-2" style="color: #000000 !important;">
                <p class="m-0">Mengetahui,</p>
                <p class="font-bold m-0">DIREKTUR RSUD dr. H. KOESNADI</p>
                <p class="m-0 text-[8.5pt]">KABUPATEN BONDOWOSO</p>
                
                <!-- Ruang TTD Direktur (Tinggi 55px) -->
                <div class="my-1 flex items-center justify-center" style="height: 55px; min-height: 55px;"></div>

                <p class="font-bold underline uppercase m-0">{{ $direkturNama }}</p>
                <p class="m-0">NIP. {{ $direkturNip }}</p>
            </div>
        </div>

    </div>

</body>
</html>
