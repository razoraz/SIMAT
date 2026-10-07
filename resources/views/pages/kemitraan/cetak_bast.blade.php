<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak BAST Pemanfaatan Kemitraan - {{ $nomorBast }}</title>
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

    <!-- Action Toolbar (Disembunyikan saat mencetak) -->
    <div class="no-print w-full max-w-4xl bg-slate-950/90 border border-slate-800 rounded-2xl p-4 mb-6 flex items-center justify-between shadow-xl">
        <div class="flex items-center space-x-3">
            <a href="{{ request('returnTo') ?: route('master.kemitraan') }}"
               class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer">
                <span>&larr;</span>
                <span>Kembali ke Master Kemitraan</span>
            </a>
            <div>
                <h2 class="text-sm font-bold text-white">Lembar Cetak Resmi BAST Pemanfaatan BMD (Kemitraan)</h2>
                <p class="text-[11px] text-cyan-400 font-mono">No: {{ $nomorBast }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            @if(!empty($kemitraan?->dokumen_path))
            <a href="{{ asset('storage/' . $kemitraan->dokumen_path) }}" target="_blank"
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

    @php
        $kode108 = $astap->kode_108 ?: ($astap->jenisAstap?->sub_sub_rincian_objek ?: ($astap->jenisAstap?->jenis ?: '1.3.1.01.01.01'));
        $nibarUtama = $astap->registers->first()?->nibar ?: ($spec['objek_nibar'] ?? ($spec['nibar'] ?? '-'));
        $kondisiUtama = $spec['kondisi'] ?? 'Baik';
        $nilaiAsetTotal = (float) ($astap->total_realisasi ?: ($astap->jumlah_anggaran ?: 0));
        $luasStr = $luasTotal > 0 ? ($luasTotal . ' m²') : ($astap->volume_satuan ?: ($astap->jumlah_volume . ' ' . ($astap->satuan ?: 'Unit')));
    @endphp

    <!-- Official Paper Document (Kertas A4 Putih Sesuai Format BAST ASTAP) -->
    <div class="print-container bg-white text-black max-w-4xl w-full p-8 sm:p-12 shadow-2xl rounded-sm text-[10pt] leading-relaxed"
         style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
        
        <!-- KOP SURAT PEMKAB & RSUD (DUAL LOGO RESMI) -->
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
            <h4 class="font-bold text-[10.5pt] uppercase tracking-normal m-0 text-black">PEMANFAATAN BARANG MILIK DAERAH (BMD) KEMITRAAN</h4>
            <p class="text-[10pt] font-semibold mt-1 text-black">Nomor : <span class="font-mono font-bold">{{ $nomorBast }}</span></p>
        </div>

        <!-- PARAGRAF PEMBUKA -->
        <p class="text-justify mb-3 text-[10pt]">
            Pada hari ini <strong class="capitalize">{{ $hariTgl }}</strong> tanggal <strong>{{ $tglFormatted }}</strong> ({{ $carbonTgl->format('d/m/Y') }}), bertempat di RSUD Dr. H. Koesnandi Kabupaten Bondowoso, yang bertanda tangan di bawah ini :
        </p>

        <!-- IDENTITAS PARA PIHAK -->
        <div class="space-y-3 mb-4 text-[9.5pt]">
            <!-- Pihak Pertama -->
            <table class="w-full">
                <tr>
                    <td class="w-6 align-top font-bold">1.</td>
                    <td class="w-36 align-top">Nama</td>
                    <td class="w-3 align-top">:</td>
                    <td class="align-top font-bold uppercase">{{ $pihakSatu['nama'] }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">NIP</td>
                    <td class="align-top">:</td>
                    <td class="align-top font-mono">{{ $pihakSatu['nip'] }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">Pangkat / Gol. Ruang</td>
                    <td class="align-top">:</td>
                    <td class="align-top">{{ $pihakSatu['pangkat'] }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">Jabatan</td>
                    <td class="align-top">:</td>
                    <td class="align-top font-semibold">{{ $pihakSatu['jabatan'] }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">Instansi</td>
                    <td class="align-top">:</td>
                    <td class="align-top">{{ $pihakSatu['instansi'] }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">Alamat Instansi</td>
                    <td class="align-top">:</td>
                    <td class="align-top">{{ $pihakSatu['alamat'] }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3" class="pt-1 italic text-slate-700">
                        Bertindak untuk dan atas nama Rumah Sakit Umum Daerah Dr. H. Koesnandi Kabupaten Bondowoso selaku Kuasa Pengguna Barang Milik Daerah, yang selanjutnya disebut sebagai <strong>PIHAK KESATU</strong>.
                    </td>
                </tr>
            </table>

            <!-- Pihak Kedua -->
            <table class="w-full mt-2">
                <tr>
                    <td class="w-6 align-top font-bold">2.</td>
                    <td class="w-36 align-top">Nama Mitra / Instansi</td>
                    <td class="w-3 align-top">:</td>
                    <td class="align-top font-bold uppercase">{{ $pihakDua['perusahaan'] }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">Penanggung Jawab / Pimpinan</td>
                    <td class="align-top">:</td>
                    <td class="align-top font-bold">{{ $pihakDua['pimpinan'] }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">Jabatan</td>
                    <td class="align-top">:</td>
                    <td class="align-top">{{ $pihakDua['jabatan'] }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">Alamat Domisili</td>
                    <td class="align-top">:</td>
                    <td class="align-top">{{ $pihakDua['alamat'] }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3" class="pt-1 italic text-slate-700">
                        Bertindak untuk dan atas nama <strong>{{ $pihakDua['perusahaan'] }}</strong> selaku Mitra Kerja Sama / Pengelola Pemanfaatan Barang Milik Daerah, yang selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.
                    </td>
                </tr>
            </table>
        </div>

        <!-- DASAR HUKUM / PERJANJIAN KERJA SAMA (PKS) -->
        <p class="text-justify mb-3 text-[10pt]">
            Berdasarkan Surat Perjanjian Kerja Sama (PKS) Nomor: <strong class="font-mono font-bold">{{ $nomorPks }}</strong> Tanggal <strong>{{ \Carbon\Carbon::parse($kemitraan?->tanggal_pks ?: ($spec['tanggal_pks'] ?? $carbonTgl))->isoFormat('D MMMM Y') }}</strong> mengenai Skema <strong>{{ $kemitraan?->skema_kemitraan ?: ($spec['skema_kemitraan'] ?? 'Bangun Guna Serah (BGS) / Kerja Sama Pemanfaatan') }}</strong>, PARA PIHAK secara bersama-sama dan sadar menyatakan bersepakat menyelenggarakan serah terima pemanfaatan Barang Milik Daerah (BMD) dengan ketentuan sebagai berikut:
        </p>

        <!-- PASAL 1: OBJEK PENYERAHAN PEMANFAATAN -->
        <div class="mb-3">
            <div class="font-bold text-center uppercase tracking-wide text-[9.5pt] mb-1">
                Pasal 1<br><span class="underline">Objek Serah Terima Pemanfaatan Barang Milik Daerah</span>
            </div>
            <p class="text-justify mb-2 leading-relaxed">
                PIHAK KESATU menyerahkan kepada PIHAK KEDUA, dan PIHAK KEDUA menerima dari PIHAK KESATU objek pemanfaatan Barang Milik Daerah milik Pemerintah Kabupaten Bondowoso yang tercatat pada RSUD Dr. H. Koesnandi dengan spesifikasi rincian sebagai berikut:
            </p>

            <!-- TABEL SPESIFIKASI OBJEK ASET (STANDAR BAST KEDINASAN) -->
            <div class="my-2">
                <table class="w-full border-collapse border border-black text-[9pt]">
                    <thead>
                        <tr class="bg-gray-100 font-bold text-center">
                            <th class="border border-black px-2 py-1.5 w-8">No</th>
                            <th class="border border-black px-2 py-1.5 text-left">Nama Barang / Spesifikasi Objek</th>
                            <th class="border border-black px-2 py-1.5 font-mono w-28">Kode 108</th>
                            <th class="border border-black px-2 py-1.5 font-mono w-24">NIBAR</th>
                            <th class="border border-black px-2 py-1.5 w-24">Luas / Vol</th>
                            <th class="border border-black px-2 py-1.5 w-16">Kondisi</th>
                            <th class="border border-black px-2 py-1.5 text-left w-32">Bukti Hak / Tanda Bukti</th>
                            <th class="border border-black px-2 py-1.5 text-right w-28">Taksiran Nilai (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-2 py-1.5 text-center font-bold align-top">1</td>
                            <td class="border border-black px-2 py-1.5 align-top">
                                <strong class="font-bold block">{{ $astap->nama_barang }}</strong>
                                <span class="text-[8pt] text-slate-700 block mt-0.5">
                                    Lokasi: {{ $astap->alamat_barang ?: 'Kompleks RSUD Dr. H. Koesnandi Bondowoso' }}
                                </span>
                                @if(!empty($spec['penggunaan']))
                                <span class="text-[8pt] text-slate-700 block italic">Penggunaan: {{ $spec['penggunaan'] }}</span>
                                @endif
                            </td>
                            <td class="border border-black px-2 py-1.5 text-center font-mono text-[8.5pt] align-top">
                                {{ $kode108 }}
                            </td>
                            <td class="border border-black px-2 py-1.5 text-center font-mono text-[8.5pt] align-top">
                                {{ $nibarUtama }}
                            </td>
                            <td class="border border-black px-2 py-1.5 text-center font-mono align-top">
                                {{ $luasStr }}
                            </td>
                            <td class="border border-black px-2 py-1.5 text-center font-medium align-top">
                                {{ $kondisiUtama }}
                            </td>
                            <td class="border border-black px-2 py-1.5 align-top text-[8.5pt]">
                                <span class="block font-semibold">{{ $hakTanah }}</span>
                                <span class="font-mono text-slate-700 text-[8pt]">{{ $sertifikatNo }}</span>
                            </td>
                            <td class="border border-black px-2 py-1.5 text-right font-mono font-bold align-top">
                                {{ number_format($nilaiAsetTotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        <!-- Baris Total -->
                        <tr class="font-bold bg-gray-100">
                            <td colspan="4" class="border border-black px-2 py-1.5 text-center uppercase tracking-wider">
                                TOTAL TAKSIRAN NILAI OBJEK PEMANFAATAN BMD
                            </td>
                            <td colspan="2" class="border border-black px-2 py-1.5 text-center">
                                {{ $luasStr }}
                            </td>
                            <td class="border border-black px-2 py-1.5 text-center text-[8pt] italic text-slate-600">
                                Sesuai PKS
                            </td>
                            <td class="border border-black px-2 py-1.5 text-right font-mono text-[9.5pt]">
                                Rp {{ number_format($nilaiAsetTotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PASAL 2: MASA KONSESI -->
        <div class="mb-3">
            <div class="font-bold text-center uppercase tracking-wide text-[9.5pt] mb-1">
                Pasal 2<br><span class="underline">Jangka Waktu Pemanfaatan / Masa Konsesi</span>
            </div>
            <p class="text-justify mb-2 leading-relaxed">
                Pemanfaatan objek Barang Milik Daerah sebagaimana dimaksud pada Pasal 1 berlaku untuk jangka waktu <strong class="font-bold">{{ $kemitraan?->jangka_waktu ?: ($spec['jangka_waktu'] ?? '5 (Lima) Tahun') }}</strong>, terhitung mulai tanggal <strong class="font-bold">{{ \Carbon\Carbon::parse($kemitraan?->tanggal_mulai ?: ($spec['tanggal_mulai'] ?? $carbonTgl))->isoFormat('D MMMM Y') }}</strong> sampai dengan tanggal <strong class="font-bold">{{ \Carbon\Carbon::parse($kemitraan?->tanggal_selesai ?: ($spec['tanggal_selesai'] ?? $carbonTgl->copy()->addYears(5)))->isoFormat('D MMMM Y') }}</strong>.
            </p>
        </div>

        <!-- PASAL 3: KETENTUAN PEMANFAATAN & STATUS BANGUNAN MITRA -->
        <div class="mb-3">
            <div class="font-bold text-center uppercase tracking-wide text-[9.5pt] mb-1">
                Pasal 3<br><span class="underline">Ketentuan Pemanfaatan &amp; Bangunan Fisik Mitra</span>
            </div>
            <ol class="list-decimal pl-6 space-y-1 text-justify leading-relaxed">
                <li>
                    PIHAK KEDUA berhak menggunakan dan memanfaatkan objek tanah/lahan tersebut semata-mata untuk penyelenggaraan kegiatan kemitraan sesuai ketentuan dalam Surat Perjanjian Kerja Sama (PKS).
                </li>
                <li>
                    Dalam hal PIHAK KEDUA mendirikan bangunan fisik, gedung, ruangan, atau sarana dan prasarana pendukung di atas objek tanah tersebut, maka seluruh bangunan fisik yang didirikan dicatat dan diakui berstatus sebagai <strong class="font-bold">Aset Kemitraan Pihak Ketiga (Pos Neraca Akun 1.5.2 Permendagri No. 108 Tahun 2016)</strong> selama masa konsesi masih berlangsung.
                </li>
                <li>
                    Setelah masa konsesi / kerja sama berakhir sebagaimana ditetapkan dalam Pasal 2, seluruh bangunan fisik, instalasi, dan penambahan fasilitas yang berdiri di atas tanah tersebut menjadi hak milik penuh Pemerintah Kabupaten Bondowoso / RSUD Dr. H. Koesnandi tanpa kewajiban kompensasi atau ganti rugi dalam bentuk apapun kepada PIHAK KEDUA, dan akan diproses melalui mekanisme <strong class="font-bold">Reklasifikasi Menjadi Aset Tetap Definitif (KIB C Gedung dan Bangunan)</strong>.
                </li>
                <li>
                    PIHAK KEDUA wajib memelihara, merawat, dan menjaga keamanan serta kebersihan objek pemanfaatan dengan sebaik-baiknya selama masa konsesi berlangsung.
                </li>
            </ol>
        </div>

        <!-- PASAL 4: PENUTUP -->
        <div class="mb-4">
            <div class="font-bold text-center uppercase tracking-wide text-[9.5pt] mb-1">
                Pasal 4<br><span class="underline">Penutup</span>
            </div>
            <p class="text-justify leading-relaxed">
                Demikian Berita Acara Serah Terima (BAST) Pemanfaatan Barang Milik Daerah ini dibuat dan ditandatangani oleh PARA PIHAK pada hari dan tanggal tersebut di atas dalam rangkap 2 (dua) bermeterai cukup, serta memiliki kekuatan hukum yang sama bagi masing-masing pihak untuk dipergunakan sebagaimana mestinya.
            </p>
        </div>

        <!-- LEMBAR TANDA TANGAN (FORMAT RESMI 3 PIHAK STANDAR RSUD) -->
        <div class="grid grid-cols-2 gap-8 text-center text-[9.5pt] pt-2">
            <!-- Pihak Kesatu -->
            <div>
                <p class="font-bold text-slate-800 uppercase">PIHAK KESATU</p>
                <p class="text-[9pt] text-slate-600">RSUD Dr. H. Koesnandi Bondowoso</p>
                <p class="text-[8.5pt] text-slate-500 font-semibold mb-1">Selaku Kuasa Pengguna Barang</p>
                <div class="h-20 flex items-center justify-center">
                    <!-- Ruang Bersih TTD Direktur & Cap Dinas -->
                </div>
                <p class="font-bold underline text-[10pt] uppercase">{{ $pihakSatu['nama'] }}</p>
                <p class="font-mono text-[9pt]">NIP. {{ $pihakSatu['nip'] }}</p>
            </div>

            <!-- Pihak Kedua -->
            <div>
                <p class="font-bold text-slate-800 uppercase">PIHAK KEDUA</p>
                <p class="text-[9pt] text-slate-600">{{ $pihakDua['perusahaan'] }}</p>
                <p class="text-[8.5pt] text-slate-500 font-semibold mb-1">Mitra Kerja Sama</p>
                <div class="h-20 flex items-center justify-center">
                    <span class="text-[8pt] border border-dashed border-slate-400 px-3 py-1 rounded text-slate-500">
                        Meterai Rp 10.000,- &amp; Cap Basah
                    </span>
                </div>
                <p class="font-bold underline text-[10pt] uppercase">{{ $pihakDua['pimpinan'] }}</p>
                <p class="text-[8.5pt] text-slate-700">{{ $pihakDua['jabatan'] }}</p>
            </div>
        </div>

        <!-- Mengetahui / Saksi: Pengurus Barang Pengguna RSUD -->
        <div class="mt-6 text-center text-[9.5pt]">
            <p class="font-bold text-slate-800 uppercase">Mengetahui / Mengesahkan:</p>
            <p class="font-bold text-[9pt] text-slate-700 uppercase">PENGURUS BARANG PENGGUNA RSUD DR. H. KOESNANDI</p>
            <div class="h-20 flex items-center justify-center">
                <!-- Ruang Bersih TTD Pengurus Barang Pengguna -->
            </div>
            <p class="font-bold underline text-[10pt] uppercase">{{ $pengurusBarang['nama'] }}</p>
            <p class="font-mono text-[9pt]">NIP. {{ $pengurusBarang['nip'] }}</p>
        </div>

    </div>

</body>
</html>
