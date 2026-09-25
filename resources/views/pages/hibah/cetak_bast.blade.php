<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak BAST Hibah Aset - {{ $hibah->nomor_bast }}</title>
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
                margin: 12mm 15mm 12mm 15mm;
            }
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center">

    <!-- Action Toolbar (Hidden when printing) -->
    <div class="no-print w-full max-w-4xl bg-slate-950/90 border border-slate-800 rounded-2xl p-4 mb-6 flex items-center justify-between shadow-xl">
        <div class="flex items-center space-x-3">
            <a href="{{ request('returnTo') ?: route('master.hibah') }}"
               class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer">
                <span>&larr;</span>
                <span>Kembali ke Katalog Hibah</span>
            </a>
            <div>
                <div class="flex items-center space-x-2">
                    <h2 class="text-sm font-bold text-white">Lembar Cetak Resmi BAST Hibah Aset</h2>
                    <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $hibah->tipe_hibah === 'masuk' ? 'bg-amber-400/20 text-amber-300 border border-amber-400/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40' }}">
                        {{ $hibah->tipe_hibah === 'masuk' ? '🎁 HIBAH MASUK' : '📤 HIBAH KELUAR' }}
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 font-mono mt-0.5">No. BAST: {{ $hibah->nomor_bast }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            @if($hibah->dokumen_path)
            <a href="{{ asset('storage/' . $hibah->dokumen_path) }}" target="_blank"
               class="px-4 py-2 rounded-xl bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-300 border border-indigo-500/40 text-xs font-bold transition-all flex items-center space-x-1.5">
                <span>📄</span>
                <span>Scan Dokumen Asli</span>
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
        $tglObj = $hibah->tanggal_bast ?: ($hibah->astap?->created_at ?: now());
        $hariTgl = \Carbon\Carbon::parse($tglObj)->isoFormat('dddd');
        $tglFormatted = \Carbon\Carbon::parse($tglObj)->isoFormat('D MMMM Y');
        $tglAngka = \Carbon\Carbon::parse($tglObj)->format('d');
        $blnNama = \Carbon\Carbon::parse($tglObj)->isoFormat('MMMM');
        $thnAngka = \Carbon\Carbon::parse($tglObj)->format('Y');

        $astap = $hibah->astap;
        $register = $hibah->register;
        $totalVol = (int) ($hibah->jumlah_volume ?: 1);
        $nilaiTotal = (float) ($hibah->nilai_aset ?: ($astap?->total_realisasi ?: 0));
        $hargaSatuan = $totalVol > 0 ? ($nilaiTotal / $totalVol) : $nilaiTotal;
        $kode108 = $astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: ($astap?->jenisAstap?->jenis ?: '1.3.2.00.00.00'));
        $satuan = $hibah->satuan ?: ($astap?->satuan ?: 'Unit');
        $isMasuk = $hibah->tipe_hibah === 'masuk';

        // Tentukan daftar register jika ada
        $itemRows = collect();
        if ($register) {
            $itemRows->push([
                'nama' => $astap?->nama_barang ?: 'Barang Hibah',
                'kode_108' => $kode108,
                'nibar' => $register->nibar ?: ($register->no_register ?: '-'),
                'kondisi' => $register->kondisi ?: 'Baik',
                'tahun' => $astap?->tahun_perolehan ?: $hibah->tahun,
                'satuan' => $satuan,
                'harga_satuan' => $hargaSatuan,
                'nilai_total' => $hargaSatuan,
            ]);
        } elseif ($astap && $astap->registers && $astap->registers->isNotEmpty()) {
            $selectedRegisters = $astap->registers->take($totalVol);
            foreach ($selectedRegisters as $r) {
                $itemRows->push([
                    'nama' => $astap->nama_barang,
                    'kode_108' => $kode108,
                    'nibar' => $r->nibar ?: ($r->no_register ?: '-'),
                    'kondisi' => $r->kondisi ?: 'Baik',
                    'tahun' => $astap->tahun_perolehan ?: $hibah->tahun,
                    'satuan' => $satuan,
                    'harga_satuan' => $hargaSatuan,
                    'nilai_total' => $hargaSatuan,
                ]);
            }
        }

        // Fallback jika tidak ada register detail
        if ($itemRows->isEmpty()) {
            $itemRows->push([
                'nama' => $astap?->nama_barang ?: 'Barang Hibah Aset Daerah',
                'kode_108' => $kode108,
                'nibar' => '-',
                'kondisi' => 'Baik',
                'tahun' => $astap?->tahun_perolehan ?: $hibah->tahun,
                'satuan' => $satuan,
                'harga_satuan' => $hargaSatuan,
                'nilai_total' => $nilaiTotal,
            ]);
        }

        $qrHash = md5($hibah->nomor_bast ?: ('HIBAH-' . $hibah->id));
        $qrUrl = url('/validasi-tte/' . $qrHash);
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
            <h4 class="font-bold text-[10.5pt] uppercase tracking-normal m-0 text-black">
                {{ $isMasuk ? 'PENERIMAAN HIBAH BARANG MILIK DAERAH (BMD)' : 'PENYERAHAN / PENGURANGAN HIBAH BARANG MILIK DAERAH (BMD)' }}
            </h4>
            <p class="text-[10pt] font-semibold mt-1 text-black">Nomor : <span class="font-mono font-bold">{{ $hibah->nomor_bast }}</span></p>
        </div>

        <!-- PARAGRAF PEMBUKA -->
        <p class="text-justify mb-3 text-[10pt]">
            Pada hari ini <strong class="capitalize">{{ $hariTgl }}</strong> tanggal <strong>{{ $tglFormatted }}</strong> ({{ $tglAngka }} bulan {{ $blnNama }} tahun {{ $thnAngka }}), bertempat di Rumah Sakit Umum Daerah dr. H. Koesnadi Kabupaten Bondowoso, yang bertanda tangan di bawah ini :
        </p>

        <!-- PIHAK PERTAMA & KEDUA -->
        <div class="space-y-3 mb-4 text-[9.5pt]">
            @if($isMasuk)
                <!-- PIHAK KESATU: PEMBERI HIBAH (INSTANSI / LEMBAGA LUAR) -->
                <table class="w-full">
                    <tr>
                        <td class="w-6 align-top font-bold">1.</td>
                        <td class="w-32 align-top">Nama Instansi / Pihak</td>
                        <td class="w-3 align-top">:</td>
                        <td class="align-top font-bold uppercase">{{ $hibah->pihak_hibah }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Penanggung Jawab</td>
                        <td class="align-top">:</td>
                        <td class="align-top">Pimpinan / Pejabat Berwenang Pemberi Hibah</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Kedudukan / Jabatan</td>
                        <td class="align-top">:</td>
                        <td class="align-top">Pihak Pemberi Hibah</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="3" class="pt-0.5 italic text-slate-700">
                            Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong> (Yang Menyerahkan).
                        </td>
                    </tr>
                </table>

                <!-- PIHAK KEDUA: PENERIMA HIBAH (RSUD DR. H. KOESNADI) -->
                <table class="w-full mt-2">
                    <tr>
                        <td class="w-6 align-top font-bold">2.</td>
                        <td class="w-32 align-top">Nama</td>
                        <td class="w-3 align-top">:</td>
                        <td class="align-top font-bold">BUDI HARTONO, S.Sos</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">NIP</td>
                        <td class="align-top">:</td>
                        <td class="align-top font-mono">19760229 200801 1 010</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Jabatan</td>
                        <td class="align-top">:</td>
                        <td class="align-top">Pengurus Barang Pengguna Aset</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Instansi Penerima</td>
                        <td class="align-top">:</td>
                        <td class="align-top font-semibold">RSUD dr. H. Koesnadi Kabupaten Bondowoso</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="3" class="pt-0.5 italic text-slate-700">
                            Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong> (Yang Menerima).
                        </td>
                    </tr>
                </table>
            @else
                <!-- PIHAK KESATU: PENYERAH HIBAH KELUAR (RSUD DR. H. KOESNADI) -->
                <table class="w-full">
                    <tr>
                        <td class="w-6 align-top font-bold">1.</td>
                        <td class="w-32 align-top">Nama</td>
                        <td class="w-3 align-top">:</td>
                        <td class="align-top font-bold">BUDI HARTONO, S.Sos</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">NIP</td>
                        <td class="align-top">:</td>
                        <td class="align-top font-mono">19760229 200801 1 010</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Jabatan</td>
                        <td class="align-top">:</td>
                        <td class="align-top">Pengurus Barang Pengguna Aset</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Instansi Penyerah</td>
                        <td class="align-top">:</td>
                        <td class="align-top font-semibold">RSUD dr. H. Koesnadi Kabupaten Bondowoso</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="3" class="pt-0.5 italic text-slate-700">
                            Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong> (Yang Menyerahkan).
                        </td>
                    </tr>
                </table>

                <!-- PIHAK KEDUA: PENERIMA HIBAH KELUAR (INSTANSI / LEMBAGA LUAR) -->
                <table class="w-full mt-2">
                    <tr>
                        <td class="w-6 align-top font-bold">2.</td>
                        <td class="w-32 align-top">Nama Penerima / Pihak</td>
                        <td class="w-3 align-top">:</td>
                        <td class="align-top font-bold uppercase">{{ $hibah->pihak_hibah }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Penanggung Jawab</td>
                        <td class="align-top">:</td>
                        <td class="align-top">Pimpinan / Perwakilan Lembaga Penerima</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Kedudukan / Jabatan</td>
                        <td class="align-top">:</td>
                        <td class="align-top">Pihak Penerima Hibah</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="3" class="pt-0.5 italic text-slate-700">
                            Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong> (Yang Menerima).
                        </td>
                    </tr>
                </table>
            @endif
        </div>

        <!-- DASAR HUKUM / MAKSUD SERAH TERIMA -->
        <p class="text-justify mb-2 text-[10pt]">
            @if($isMasuk)
                Berdasarkan kesepakatan dan Naskah Perjanjian Hibah Daerah (NPHD) / Dokumen Serah Terima Hibah Nomor: <strong>{{ $hibah->nomor_bast }}</strong>, PIHAK PERTAMA menyerahkan Barang Hibah kepada PIHAK KEDUA, dan PIHAK KEDUA menerima penyerahan barang hibah tersebut dalam keadaan baik dan lengkap untuk dicatat serta dipergunakan dalam menunjang pelayanan RSUD dr. H. Koesnadi Kabupaten Bondowoso, dengan rincian sebagai berikut:
            @else
                Berdasarkan Dokumen Pengalihan / Penyerahan Hibah Barang Milik Daerah Nomor: <strong>{{ $hibah->nomor_bast }}</strong>, PIHAK PERTAMA menyerahkan Barang Milik Daerah (BMD) kepada PIHAK KEDUA sebagai hibah, dan PIHAK KEDUA menerima penyerahan barang tersebut dalam keadaan baik dan lengkap sesuai ketentuan perundang-undangan yang berlaku, dengan rincian sebagai berikut:
            @endif
        </p>

        <!-- TABEL RINCIAN BARANG HIBAH -->
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
                        <th class="border border-black px-2 py-1.5 text-right w-28">Harga Satuan (Rp)</th>
                        <th class="border border-black px-2 py-1.5 text-right w-32">Nilai Aset (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itemRows as $idx => $it)
                    <tr>
                        <td class="border border-black px-2 py-1 text-center font-bold">{{ $idx + 1 }}</td>
                        <td class="border border-black px-2 py-1 font-semibold">{{ $it['nama'] }}</td>
                        <td class="border border-black px-2 py-1 font-mono text-center text-[8.5pt]">{{ $it['kode_108'] }}</td>
                        <td class="border border-black px-2 py-1 font-mono text-center text-[8.5pt]">{{ $it['nibar'] }}</td>
                        <td class="border border-black px-2 py-1 text-center font-mono">{{ $it['tahun'] }}</td>
                        <td class="border border-black px-2 py-1 text-center">{{ $it['kondisi'] }}</td>
                        <td class="border border-black px-2 py-1 text-right font-mono">{{ number_format($it['harga_satuan'], 0, ',', '.') }}</td>
                        <td class="border border-black px-2 py-1 text-right font-mono font-semibold">{{ number_format($it['nilai_total'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach

                    <!-- Total Row -->
                    <tr class="font-bold bg-gray-100">
                        <td colspan="4" class="border border-black px-2 py-1.5 text-center uppercase tracking-wider">
                            TOTAL NILAI ASET HIBAH ({{ $totalVol }} {{ $satuan }})
                        </td>
                        <td colspan="2" class="border border-black px-2 py-1.5 text-center">
                            {{ $totalVol }} {{ $satuan }}
                        </td>
                        <td class="border border-black px-2 py-1.5 text-right font-mono text-[9pt]">
                            Total :
                        </td>
                        <td class="border border-black px-2 py-1.5 text-right font-mono text-[9.5pt]">
                            Rp {{ number_format($nilaiTotal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($hibah->keterangan)
        <p class="text-[9.5pt] mb-3">
            <strong>Catatan / Keterangan Hibah:</strong> {{ $hibah->keterangan }}
        </p>
        @endif

        <p class="text-justify mb-6 text-[10pt]">
            Demikian Berita Acara Serah Terima (BAST) Hibah Aset ini dibuat dan ditandatangani oleh kedua belah pihak dengan itikad baik dan sebenarnya dalam rangkap secukupnya untuk dipergunakan sebagaimana mestinya.
        </p>

        <!-- TANDA TANGAN (3 PIHAK: PENYERAH, PENERIMA, MENGETAHUI DIREKTUR) -->
        <div class="grid grid-cols-2 gap-8 text-center text-[9.5pt] pt-2">
            <!-- Pihak Kesatu -->
            <div>
                <p class="font-bold text-slate-800">PIHAK KESATU</p>
                <p class="text-[9pt] text-slate-600">Yang Menyerahkan,</p>
                
                @if(!$isMasuk)
                    <!-- RSUD Koesnadi yang menyerahkan (TTE BSrE) -->
                    <div class="h-20 flex items-center justify-center">
                        <div style="padding:4px; border:1.5px solid #0d9488; background:#f0fdfa; border-radius:5px; display:inline-flex; align-items:center; gap:6px; text-align:left;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($qrUrl) }}" alt="QR TTE" style="width:36px; height:36px; flex-shrink:0;">
                            <div style="font-size:7.5px; line-height:1.35; color:#1e293b;">
                                <div style="font-weight:700; color:#134e4a;">DITANDATANGANI ELEKTRONIK</div>
                                <div style="color:#374151;">Pengurus Barang Aset</div>
                                <div style="font-size:6.5px; color:#6b7280; font-family:monospace;">Sertifikat BSrE - BSSN</div>
                            </div>
                        </div>
                    </div>
                    <p class="font-bold underline text-[10pt] uppercase">BUDI HARTONO, S.Sos</p>
                    <p class="font-mono text-[9pt]">NIP. 19760229 200801 1 010</p>
                @else
                    <!-- Instansi luar yang menyerahkan (TTD Basah) -->
                    <div class="h-20 flex items-center justify-center">
                        <!-- Space for TTD / Stempel -->
                    </div>
                    <p class="font-bold underline text-[10pt] uppercase">{{ $hibah->pihak_hibah }}</p>
                    <p class="font-mono text-[9pt]">Pejabat Pemberi Hibah</p>
                @endif
            </div>

            <!-- Pihak Kedua -->
            <div>
                <p class="font-bold text-slate-800">PIHAK KEDUA</p>
                <p class="text-[9pt] text-slate-600">Yang Menerima,</p>

                @if($isMasuk)
                    <!-- RSUD Koesnadi yang menerima (TTE BSrE) -->
                    <div class="h-20 flex items-center justify-center">
                        <div style="padding:4px; border:1.5px solid #0d9488; background:#f0fdfa; border-radius:5px; display:inline-flex; align-items:center; gap:6px; text-align:left;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($qrUrl) }}" alt="QR TTE" style="width:36px; height:36px; flex-shrink:0;">
                            <div style="font-size:7.5px; line-height:1.35; color:#1e293b;">
                                <div style="font-weight:700; color:#134e4a;">DITANDATANGANI ELEKTRONIK</div>
                                <div style="color:#374151;">Pengurus Barang Aset</div>
                                <div style="font-size:6.5px; color:#6b7280; font-family:monospace;">Sertifikat BSrE - BSSN</div>
                            </div>
                        </div>
                    </div>
                    <p class="font-bold underline text-[10pt] uppercase">BUDI HARTONO, S.Sos</p>
                    <p class="font-mono text-[9pt]">NIP. 19760229 200801 1 010</p>
                @else
                    <!-- Pihak Luar yang menerima hibah (TTD Basah) -->
                    <div class="h-20 flex items-center justify-center">
                        <!-- Space for TTD / Stempel -->
                    </div>
                    <p class="font-bold underline text-[10pt] uppercase">{{ $hibah->pihak_hibah }}</p>
                    <p class="font-mono text-[9pt]">Penerima Hibah</p>
                @endif
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
