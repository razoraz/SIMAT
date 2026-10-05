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
               class="px-3.5 py-2 rounded-xl bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 text-xs font-bold transition-all flex items-center space-x-1.5">
                <span>📄</span>
                <span>Berkas Upload Scan</span>
            </a>
            @endif
            <button type="button" onclick="window.print()"
                    class="px-5 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-teal-500 hover:from-cyan-400 hover:to-teal-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Official Paper Document (Kertas A4 Putih) -->
    <div class="print-container bg-white text-black max-w-4xl w-full p-8 sm:p-12 shadow-2xl rounded-sm text-[10pt] leading-relaxed"
         style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
        
        <!-- KOP SURAT PEMKAB BONDOWOSO & RSUD DR. H. KOESNANDI -->
        <div class="border-b-[2.5px] border-black pb-3 mb-4" style="border-bottom: 2.5px solid #000000;">
            <table class="w-full border-collapse">
                <tr>
                    <td class="w-[85px] text-center align-middle" style="width: 85px;">
                        <img src="{{ asset('images/logo_bondowoso.png') }}"
                             alt="Logo Pemkab Bondowoso"
                             class="h-[75px] w-auto mx-auto object-contain"
                             onerror="this.style.display='none'">
                    </td>
                    <td class="text-center align-middle px-2">
                        <div class="text-[12pt] font-bold uppercase tracking-wider text-black leading-tight">
                            Pemerintah Kabupaten Bondowoso
                        </div>
                        <div class="text-[11pt] font-bold uppercase tracking-wider text-black leading-tight">
                            Dinas Kesehatan
                        </div>
                        <div class="text-[14pt] font-extrabold uppercase tracking-wide text-black leading-tight mt-0.5">
                            RSUD dr. H. Koesnandi
                        </div>
                        <div class="text-[8.5pt] text-black leading-tight mt-1">
                            Jl. Kapten Piere Tendean No. 1 Bondowoso | Telp: (0332) 421974, 421971 | Fax: (0332) 421974
                        </div>
                        <div class="text-[8.5pt] text-black leading-tight">
                            Email: rsu_koesnandi@yahoo.co.id | Website: www.rsudkoesnandi.id
                        </div>
                    </td>
                    <td class="w-[85px] text-center align-middle" style="width: 85px;">
                        <img src="{{ asset('images/logo_rsud.png') }}"
                             alt="Logo RSUD Koesnandi"
                             class="h-[75px] w-auto mx-auto object-contain"
                             onerror="this.style.display='none'">
                    </td>
                </tr>
            </table>
        </div>

        <!-- JUDUL DAN NOMOR BERITA ACARA -->
        <div class="text-center mb-5">
            <h1 class="text-[12pt] font-bold uppercase tracking-wider underline decoration-1 text-black">
                BERITA ACARA SERAH TERIMA PEMANFAATAN BARANG MILIK DAERAH
            </h1>
            <p class="text-[10pt] font-mono text-black font-semibold mt-0.5">
                Nomor: {{ $nomorBast }}
            </p>
        </div>

        <!-- PEMBUKAAN TANGGAL & HARI -->
        <p class="text-justify mb-3 indent-6 leading-relaxed">
            Pada hari ini, <strong class="font-bold">{{ $hariTgl }}</strong>, tanggal <strong class="font-bold">{{ $tglFormatted }}</strong> ({{ $carbonTgl->format('d-m-Y') }}), bertempat di Kantor Manajemen Rumah Sakit Umum Daerah dr. H. Koesnandi Kabupaten Bondowoso, kami yang bertanda tangan di bawah ini:
        </p>

        <!-- IDENTITAS PARA PIHAK -->
        <div class="space-y-3 mb-4 text-[9.5pt]">
            <!-- PIHAK KESATU -->
            <table class="w-full border-collapse">
                <tr class="align-top">
                    <td class="w-6 font-bold">1.</td>
                    <td class="w-36 font-semibold">Nama</td>
                    <td class="w-3">:</td>
                    <td class="font-bold uppercase">{{ $pihakSatu['nama'] }}</td>
                </tr>
                <tr class="align-top">
                    <td></td>
                    <td class="font-semibold">NIP</td>
                    <td>:</td>
                    <td class="font-mono">{{ $pihakSatu['nip'] }}</td>
                </tr>
                <tr class="align-top">
                    <td></td>
                    <td class="font-semibold">Pangkat / Gol. Ruang</td>
                    <td>:</td>
                    <td>{{ $pihakSatu['pangkat'] }}</td>
                </tr>
                <tr class="align-top">
                    <td></td>
                    <td class="font-semibold">Jabatan</td>
                    <td>:</td>
                    <td class="font-semibold">{{ $pihakSatu['jabatan'] }}</td>
                </tr>
                <tr class="align-top">
                    <td></td>
                    <td class="font-semibold">Alamat Instansi</td>
                    <td>:</td>
                    <td>{{ $pihakSatu['alamat'] }}</td>
                </tr>
                <tr class="align-top">
                    <td></td>
                    <td colspan="3" class="pt-1 italic text-slate-800">
                        Bertindak untuk dan atas nama Rumah Sakit Umum Daerah dr. H. Koesnandi Kabupaten Bondowoso selaku Kuasa Pengguna Barang Milik Daerah, yang selanjutnya disebut sebagai <strong class="font-bold">PIHAK KESATU</strong>.
                    </td>
                </tr>
            </table>

            <!-- PIHAK KEDUA -->
            <table class="w-full border-collapse pt-1">
                <tr class="align-top">
                    <td class="w-6 font-bold">2.</td>
                    <td class="w-36 font-semibold">Nama Instansi / Mitra</td>
                    <td class="w-3">:</td>
                    <td class="font-bold uppercase text-black">{{ $pihakDua['perusahaan'] }}</td>
                </tr>
                <tr class="align-top">
                    <td></td>
                    <td class="font-semibold">Penanggung Jawab / Pimpinan</td>
                    <td>:</td>
                    <td class="font-semibold">{{ $pihakDua['pimpinan'] }}</td>
                </tr>
                <tr class="align-top">
                    <td></td>
                    <td class="font-semibold">Jabatan</td>
                    <td>:</td>
                    <td>{{ $pihakDua['jabatan'] }}</td>
                </tr>
                <tr class="align-top">
                    <td></td>
                    <td class="font-semibold">Alamat Domisili</td>
                    <td>:</td>
                    <td>{{ $pihakDua['alamat'] }}</td>
                </tr>
                <tr class="align-top">
                    <td></td>
                    <td colspan="3" class="pt-1 italic text-slate-800">
                        Bertindak untuk dan atas nama <strong class="font-bold">{{ $pihakDua['perusahaan'] }}</strong> selaku Mitra Kerja Sama / Penyewa Barang Milik Daerah, yang selanjutnya disebut sebagai <strong class="font-bold">PIHAK KEDUA</strong>.
                    </td>
                </tr>
            </table>
        </div>

        <!-- DASAR PERJANJIAN -->
        <p class="text-justify mb-3 leading-relaxed">
            Berdasarkan Surat Perjanjian Kerja Sama (PKS) Nomor: <strong class="font-mono font-bold">{{ $nomorPks }}</strong> Tanggal <strong class="font-bold">{{ \Carbon\Carbon::parse($kemitraan?->tanggal_pks ?: ($spec['tanggal_pks'] ?? $carbonTgl))->isoFormat('D MMMM Y') }}</strong> mengenai Skema <strong class="font-bold">{{ $kemitraan?->skema_kemitraan ?: ($spec['skema_kemitraan'] ?? 'Kerja Sama Operasional / Sewa') }}</strong>, PARA PIHAK secara bersama-sama dan sadar menyatakan bersepakat menyelenggarakan serah terima pemanfaatan Barang Milik Daerah (BMD) dengan ketentuan sebagai berikut:
        </p>

        <!-- PASAL 1: OBJEK PENYERAHAN -->
        <div class="mb-3">
            <div class="font-bold text-center uppercase tracking-wide text-[9.5pt] mb-1">
                Pasal 1<br><span class="underline">Objek Serah Terima Pemanfaatan Barang Milik Daerah</span>
            </div>
            <p class="text-justify indent-6 mb-2 leading-relaxed">
                PIHAK KESATU menyerahkan kepada PIHAK KEDUA, dan PIHAK KEDUA menerima dari PIHAK KESATU objek pemanfaatan Barang Milik Daerah berupa tanah dan/atau fasilitas pendukung milik Pemerintah Kabupaten Bondowoso yang tercatat pada RSUD dr. H. Koesnandi dengan spesifikasi rincian sebagai berikut:
            </p>

            <!-- Tabel Spesifikasi Objek Aset -->
            <table class="w-full border-collapse border border-black text-[9pt] my-2">
                <thead>
                    <tr class="bg-slate-100 text-black">
                        <th class="border border-black px-2 py-1 text-center w-8">No</th>
                        <th class="border border-black px-2 py-1 text-left">Nama Barang / Objek Aset</th>
                        <th class="border border-black px-2 py-1 text-center w-28">NIBAR / Register</th>
                        <th class="border border-black px-2 py-1 text-center w-24">Luas / Volume</th>
                        <th class="border border-black px-2 py-1 text-center w-24">Kondisi</th>
                        <th class="border border-black px-2 py-1 text-left w-36">Bukti Hak / Sertifikat</th>
                        <th class="border border-black px-2 py-1 text-right w-28">Taksiran Nilai (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-black px-2 py-1 text-center align-top">1</td>
                        <td class="border border-black px-2 py-1 align-top">
                            <strong class="font-bold block">{{ $astap->nama_barang }}</strong>
                            <span class="text-[8pt] text-slate-700 block mt-0.5">
                                Lokasi: {{ $astap->alamat_barang ?: 'Kompleks RSUD dr. H. Koesnandi Bondowoso' }}
                            </span>
                            @if(!empty($spec['penggunaan']))
                            <span class="text-[8pt] text-slate-700 block">Penggunaan: {{ $spec['penggunaan'] }}</span>
                            @endif
                        </td>
                        <td class="border border-black px-2 py-1 text-center align-top font-mono text-[8.5pt]">
                            {{ $astap->registers->first()?->nibar ?: ($spec['objek_nibar'] ?? '-') }}
                        </td>
                        <td class="border border-black px-2 py-1 text-center align-top font-mono">
                            {{ $luasTotal > 0 ? ($luasTotal . ' m²') : ($astap->volume_satuan ?: ($astap->jumlah_volume . ' ' . $astap->satuan)) }}
                        </td>
                        <td class="border border-black px-2 py-1 text-center align-top">
                            {{ $spec['kondisi'] ?? 'Baik' }}
                        </td>
                        <td class="border border-black px-2 py-1 align-top text-[8.5pt]">
                            <span class="block font-semibold">{{ $hakTanah }}</span>
                            <span class="font-mono text-slate-700">{{ $sertifikatNo }}</span>
                        </td>
                        <td class="border border-black px-2 py-1 text-right align-top font-mono font-bold">
                            Rp {{ number_format($astap->total_realisasi ?: ($astap->jumlah_anggaran ?: 0), 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- PASAL 2: MASA KONSESI -->
        <div class="mb-3">
            <div class="font-bold text-center uppercase tracking-wide text-[9.5pt] mb-1">
                Pasal 2<br><span class="underline">Jangka Waktu Pemanfaatan / Masa Konsesi</span>
            </div>
            <p class="text-justify indent-6 leading-relaxed">
                Pemanfaatan objek Barang Milik Daerah sebagaimana dimaksud pada Pasal 1 berlaku untuk jangka waktu <strong class="font-bold">{{ $kemitraan?->jangka_waktu ?: ($spec['jangka_waktu'] ?? '5 (Lima) Tahun') }}</strong>, terhitung mulai tanggal <strong class="font-bold">{{ \Carbon\Carbon::parse($kemitraan?->tanggal_mulai ?: ($spec['tanggal_mulai'] ?? $carbonTgl))->isoFormat('D MMMM Y') }}</strong> sampai dengan tanggal <strong class="font-bold">{{ \Carbon\Carbon::parse($kemitraan?->tanggal_selesai ?: ($spec['tanggal_selesai'] ?? $carbonTgl->copy()->addYears(5)))->isoFormat('D MMMM Y') }}</strong>.
            </p>
        </div>

        <!-- PASAL 3: HAK PEMANFAATAN & PENDIRIAN BANGUNAN -->
        <div class="mb-3">
            <div class="font-bold text-center uppercase tracking-wide text-[9.5pt] mb-1">
                Pasal 3<br><span class="underline">Pemanfaatan &amp; Ketentuan Bangunan Mitra</span>
            </div>
            <ol class="list-decimal pl-6 space-y-1.5 text-justify leading-relaxed">
                <li>
                    PIHAK KEDUA berhak menggunakan dan memanfaatkan objek tanah/lahan tersebut semata-mata untuk penyelenggaraan kegiatan kemitraan sesuai ketentuan dalam Surat Perjanjian Kerja Sama (PKS).
                </li>
                <li>
                    Dalam hal PIHAK KEDUA mendirikan bangunan fisik, ruangan, atau sarana dan prasarana pendukung di atas objek tanah tersebut, maka seluruh bangunan fisik yang didirikan dicatat dan diakui berstatus sebagai <strong class="font-bold">Aset Kemitraan Pihak Ketiga (Pos Neraca Akun 1.5.2 Permendagri No. 108 Tahun 2016)</strong> selama masa konsesi masih berlangsung.
                </li>
                <li>
                    Setelah masa konsesi / kerja sama berakhir sebagaimana ditetapkan dalam Pasal 2, seluruh bangunan fisik, instalasi, dan penambahan fasilitas yang berdiri di atas tanah tersebut menjadi hak milik penuh Pemerintah Kabupaten Bondowoso / RSUD dr. H. Koesnandi tanpa kewajiban kompensasi atau ganti rugi dalam bentuk apapun kepada PIHAK KEDUA, dan akan diproses melalui mekanisme <strong class="font-bold">Reklasifikasi Menjadi Aset Tetap Definitif (KIB C Gedung dan Bangunan)</strong>.
                </li>
                <li>
                    PIHAK KEDUA wajib memelihara dan menjaga keamanan serta kebersihan objek pemanfaatan dengan sebaik-baiknya selama masa konsesi berlangsung.
                </li>
            </ol>
        </div>

        <!-- PASAL 4: PENUTUP -->
        <div class="mb-5">
            <div class="font-bold text-center uppercase tracking-wide text-[9.5pt] mb-1">
                Pasal 4<br><span class="underline">Penutup</span>
            </div>
            <p class="text-justify indent-6 leading-relaxed">
                Demikian Berita Acara Serah Terima Pemanfaatan Barang Milik Daerah ini dibuat dan ditandatangani oleh PARA PIHAK pada hari dan tanggal tersebut di atas dalam rangkap 2 (dua) bermeterai cukup, serta memiliki kekuatan hukum yang sama bagi masing-masing pihak untuk dipergunakan sebagaimana mestinya.
            </p>
        </div>

        <!-- LEMBAR TANDA TANGAN PARA PIHAK -->
        <div class="pt-2">
            <table class="w-full border-collapse text-center text-[9.5pt]">
                <tr>
                    <td class="w-1/2 align-top px-4">
                        <div class="font-bold uppercase">PIHAK KEDUA</div>
                        <div class="text-[9pt] font-semibold text-slate-800">{{ $pihakDua['perusahaan'] }}</div>
                        <div class="text-[8pt] text-slate-500 mb-1">Pimpinan / Kuasa Direksi</div>
                        
                        <!-- Space Materai & TTD -->
                        <div class="h-20 flex items-center justify-center">
                            <span class="text-[8pt] border border-dashed border-slate-400 px-3 py-1 rounded text-slate-500">
                                Meterai Rp 10.000,- &amp; Cap Basah
                            </span>
                        </div>

                        <div class="font-bold underline uppercase text-black">
                            {{ $pihakDua['pimpinan'] }}
                        </div>
                        <div class="text-[8.5pt] text-slate-700">
                            {{ $pihakDua['jabatan'] }}
                        </div>
                    </td>

                    <td class="w-1/2 align-top px-4">
                        <div class="font-bold uppercase">PIHAK KESATU</div>
                        <div class="text-[9pt] font-semibold text-slate-800">RSUD dr. H. Koesnandi Bondowoso</div>
                        <div class="text-[8pt] text-slate-500 mb-1">Selaku Kuasa Pengguna Barang</div>

                        <!-- Space TTD Direktur -->
                        <div class="h-20 flex items-center justify-center">
                            <span class="text-[8pt] text-slate-400 italic">
                                [ Tanda Tangan &amp; Cap Dinas ]
                            </span>
                        </div>

                        <div class="font-bold underline uppercase text-black">
                            {{ $pihakSatu['nama'] }}
                        </div>
                        <div class="text-[8.5pt] text-slate-700 font-mono">
                            NIP. {{ $pihakSatu['nip'] }}
                        </div>
                    </td>
                </tr>

                <!-- SAKSI / MENGETAHUI -->
                <tr>
                    <td colspan="2" class="pt-8">
                        <div class="text-center">
                            <div class="text-[9pt] uppercase font-semibold text-slate-700">Mengetahui / Mengesahkan:</div>
                            <div class="font-bold text-[9.5pt] uppercase text-black">Pengurus Barang Pengguna RSUD dr. H. Koesnandi</div>
                            
                            <div class="h-16 flex items-center justify-center">
                                <span class="text-[8pt] text-slate-400 italic">
                                    [ Tanda Tangan ]
                                </span>
                            </div>

                            <div class="font-bold underline uppercase text-black">
                                {{ $pengurusBarang['nama'] }}
                            </div>
                            <div class="text-[8.5pt] text-slate-700 font-mono">
                                NIP. {{ $pengurusBarang['nip'] }}
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

    </div>

</body>
</html>
