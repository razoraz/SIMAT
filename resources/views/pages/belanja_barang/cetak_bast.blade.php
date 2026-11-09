<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak BAST Belanja Barang - Faktur {{ $belanja->nomor_faktur }}</title>
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
<body class="bg-slate-900 text-slate-100 min-h-screen p-3 sm:p-6 md:p-8 flex flex-col items-center">

    @php
        \Carbon\Carbon::setLocale('id');

        if (!function_exists('terbilang_belanja_barang')) {
            function terbilang_belanja_barang($angka) {
                $angka = floor(abs((float) $angka));
                $baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
                $terbilang = '';
                if ($angka < 12) {
                    $terbilang = ' ' . $baca[(int)$angka];
                } elseif ($angka < 20) {
                    $terbilang = terbilang_belanja_barang($angka - 10) . ' Belas';
                } elseif ($angka < 100) {
                    $terbilang = terbilang_belanja_barang(floor($angka / 10)) . ' Puluh' . terbilang_belanja_barang(fmod($angka, 10));
                } elseif ($angka < 200) {
                    $terbilang = ' Seratus' . terbilang_belanja_barang($angka - 100);
                } elseif ($angka < 1000) {
                    $terbilang = terbilang_belanja_barang(floor($angka / 100)) . ' Ratus' . terbilang_belanja_barang(fmod($angka, 100));
                } elseif ($angka < 2000) {
                    $terbilang = ' Seribu' . terbilang_belanja_barang($angka - 1000);
                } elseif ($angka < 1000000) {
                    $terbilang = terbilang_belanja_barang(floor($angka / 1000)) . ' Ribu' . terbilang_belanja_barang(fmod($angka, 1000));
                } elseif ($angka < 1000000000) {
                    $terbilang = terbilang_belanja_barang(floor($angka / 1000000)) . ' Juta' . terbilang_belanja_barang(fmod($angka, 1000000));
                } elseif ($angka < 1000000000000) {
                    $terbilang = terbilang_belanja_barang(floor($angka / 1000000000)) . ' Miliar' . terbilang_belanja_barang(fmod($angka, 1000000000));
                } elseif ($angka < 1000000000000000) {
                    $terbilang = terbilang_belanja_barang(floor($angka / 1000000000000)) . ' Triliun' . terbilang_belanja_barang(fmod($angka, 1000000000000));
                }
                return trim($terbilang);
            }
        }

        $astaps = $belanja->astaps;
        if ($astaps->isEmpty() && $belanja->astap) {
            $astaps = collect([$belanja->astap]);
        }
        $firstAstap = $astaps->first();
        $tahun = $firstAstap?->tahun_perolehan ?: ($belanja->tanggal_faktur ? \Carbon\Carbon::parse($belanja->tanggal_faktur)->format('Y') : date('Y'));
        $triwulan = $firstAstap?->triwulan ?: 'TW I';

        $tglFakturCarbon = $belanja->tanggal_faktur ? \Carbon\Carbon::parse($belanja->tanggal_faktur) : ($belanja->created_at ?: now());
        $hariTgl = $tglFakturCarbon->isoFormat('dddd');
        $tglFormatted = $tglFakturCarbon->isoFormat('D MMMM Y');
        $tglAngka = $tglFakturCarbon->format('d');
        $blnNama = $tglFakturCarbon->isoFormat('MMMM');
        $thnAngka = $tglFakturCarbon->format('Y');

        $nomorBast = '000.2.3.2/BAST-BLJ/' . str_pad($belanja->id, 4, '0', STR_PAD_LEFT) . '/430.10.7/' . $thnAngka;
        $totalPembelian = (float) $belanja->total_pembelian;
        $terbilangStr = $totalPembelian > 0 ? (terbilang_belanja_barang($totalPembelian) . ' Rupiah') : 'Nol Rupiah';

        $totalVolFaktur = (int) $astaps->sum('jumlah_volume');
        $qrHash = md5('BAST-BELANJA-' . $belanja->id . '-' . $belanja->nomor_faktur);
        $qrUrl = url('/validasi-tte/' . $qrHash);
    @endphp

    <!-- ACTION TOOLBAR (HIDDEN WHEN PRINTED) -->
    <div class="no-print w-full max-w-4xl bg-slate-950/90 border border-slate-800 rounded-2xl p-4 mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xl">
        <div class="flex items-center space-x-3">
            <a href="{{ request('returnTo') ?: route('master.belanja_barang') }}"
               class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer">
                <span>&larr;</span>
                <span>Kembali ke Belanja Barang</span>
            </a>
            <div>
                <div class="flex items-center space-x-2">
                    <h2 class="text-sm font-bold text-white">Lembar Cetak Resmi BAST Belanja Barang</h2>
                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        🏪 AKUN 5.1.02
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 font-mono mt-0.5">Faktur: {{ $belanja->nomor_faktur }} · {{ $belanja->toko_penyedia }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-2 flex-wrap">
            @if($belanja->dokumen_faktur)
            <a href="{{ asset('storage/' . $belanja->dokumen_faktur) }}" target="_blank"
               class="px-3 py-1.5 rounded-xl bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-300 border border-indigo-500/40 text-xs font-bold transition-all flex items-center space-x-1.5">
                <span>📄</span>
                <span>Nota Asli</span>
            </a>
            @endif

            @if($belanja->dokumen_kuitansi)
            <a href="{{ asset('storage/' . $belanja->dokumen_kuitansi) }}" target="_blank"
               class="px-3 py-1.5 rounded-xl bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 text-xs font-bold transition-all flex items-center space-x-1.5">
                <span>📦</span>
                <span>Kuitansi Gudang</span>
            </a>
            @endif

            <button type="button" onclick="window.print()"
                    class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- OFFICIAL PAPER DOCUMENT (A4 PUTIH) -->
    <div class="print-container bg-white text-black max-w-4xl w-full p-8 sm:p-12 shadow-2xl rounded-sm text-[10pt] leading-relaxed"
         style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">

        <!-- KOP SURAT RESMI PEMKAB & RSUD -->
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

        <!-- JUDUL SURAT RESMI -->
        <div class="text-center mb-4">
            <h3 class="font-bold text-[12pt] uppercase underline tracking-normal m-0 text-black">BERITA ACARA SERAH TERIMA (BAST)</h3>
            <h4 class="font-bold text-[10.5pt] uppercase tracking-normal m-0 text-black">
                PENERIMAAN BARANG HASIL BELANJA PERBEKALAN / BARANG (AKUN 5.1.02)
            </h4>
            <p class="text-[10pt] font-semibold mt-1 text-black">Nomor : <span class="font-mono font-bold">{{ $nomorBast }}</span></p>
        </div>

        <!-- KLAUSUL PEMBUKA -->
        <p class="text-justify mb-3 text-[10pt]">
            Pada hari ini <strong class="capitalize">{{ $hariTgl }}</strong> tanggal <strong>{{ $tglFormatted }}</strong> ({{ $tglAngka }} bulan {{ $blnNama }} tahun {{ $thnAngka }}), bertempat di Rumah Sakit Umum Daerah dr. H. Koesnandi Kabupaten Bondowoso, yang bertanda tangan di bawah ini :
        </p>

        <!-- PIHAK PERTAMA & KEDUA -->
        <div class="space-y-3 mb-4 text-[9.5pt]">
            <!-- PIHAK KESATU: PENYEDIA / TOKO REKANAN -->
            <table class="w-full">
                <tr>
                    <td class="w-6 align-top font-bold">1.</td>
                    <td class="w-40 align-top">Nama Toko / Penyedia</td>
                    <td class="w-3 align-top">:</td>
                    <td class="align-top font-bold uppercase">{{ $belanja->toko_penyedia }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">Kedudukan / Jabatan</td>
                    <td class="align-top">:</td>
                    <td class="align-top">Pimpinan / Rekanan Penyedia Belanja Barang Perbekalan</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="align-top">Alamat / Kontak</td>
                    <td class="align-top">:</td>
                    <td class="align-top">{{ $belanja->toko_penyedia }} (Rekanan Resmi RSUD dr. H. Koesnandi)</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3" class="pt-0.5 italic text-slate-700">
                        Selanjutnya disebut sebagai <strong>PIHAK PERTAMA (Yang Menyerahkan)</strong>.
                    </td>
                </tr>
            </table>

            <!-- PIHAK KEDUA: PENGURUS BARANG RSUD -->
            <table class="w-full mt-2">
                <tr>
                    <td class="w-6 align-top font-bold">2.</td>
                    <td class="w-40 align-top">Nama</td>
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
                    <td class="align-top font-semibold">RSUD dr. H. Koesnandi Kabupaten Bondowoso</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3" class="pt-0.5 italic text-slate-700">
                        Selanjutnya disebut sebagai <strong>PIHAK KEDUA (Yang Menerima)</strong>.
                    </td>
                </tr>
            </table>
        </div>

        <!-- KLAUSUL TRANSAKSI -->
        <p class="text-justify mb-2.5 text-[9.5pt]">
            Berdasarkan <strong>Faktur / Nota Pembelian Nomor: {{ $belanja->nomor_faktur }}</strong> tanggal <strong>{{ $belanja->tanggal_faktur ? \Carbon\Carbon::parse($belanja->tanggal_faktur)->translatedFormat('d F Y') : '-' }}</strong>, 
            <strong>PIHAK PERTAMA</strong> telah menyerahkan barang perbekalan operasional rumah sakit kepada <strong>PIHAK KEDUA</strong>, dan <strong>PIHAK KEDUA</strong> telah menerima, memeriksa fisik, dan menguji kelayakan seluruh barang belanja tersebut dalam keadaan lengkap, baru, dan berfungsi baik dengan rincian sebagai berikut:
        </p>

        <!-- TABEL DAFTAR RINCIAN BARANG & REGISTER NIBAR -->
        <div class="mb-4">
            <table class="w-full border-collapse border border-black text-[9pt]" style="border: 1px solid #000000;">
                <thead>
                    <tr class="bg-gray-100 text-center font-bold" style="background-color: #f3f4f6;">
                        <th class="border border-black p-1.5 w-8">No</th>
                        <th class="border border-black p-1.5 text-left">Nama Barang &amp; Kode 108</th>
                        <th class="border border-black p-1.5 w-16">Volume</th>
                        <th class="border border-black p-1.5 w-14">Satuan</th>
                        <th class="border border-black p-1.5 w-24 text-right">Harga Satuan (Rp)</th>
                        <th class="border border-black p-1.5 w-28 text-right">Subtotal (Rp)</th>
                        <th class="border border-black p-1.5 w-28 text-center">Alokasi Penempatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($astaps as $i => $item)
                        @php
                            $kodeBarang = $item->kode_108 ?: ($item->jenisAstap?->sub_sub_rincian_objek ?: ($item->jenisAstap?->kode_108 ?: '-'));
                            $alokasiTxt = $item->unit?->nama ?: ($item->registers?->first()?->ruang_pemegang ?: ($belanja->status_alokasi === 'ruangan' ? 'Ruangan Unit' : 'Belum Ditempatkan'));
                        @endphp
                        <tr>
                            <td class="border border-black p-1.5 text-center align-top">{{ $i + 1 }}</td>
                            <td class="border border-black p-1.5 align-top">
                                <div class="font-bold">{{ $item->nama_barang }}</div>
                                <div class="font-mono text-[8pt] text-gray-700">Kode: {{ $kodeBarang }}</div>
                                @if($item->registers && $item->registers->isNotEmpty())
                                    <div class="mt-1 pt-1 border-t border-gray-300 text-[7.5pt] font-mono leading-tight text-gray-800">
                                        <span class="font-bold">NIBAR:</span>
                                        {{ $item->registers->pluck('nibar')->take(5)->implode(', ') }}
                                        @if($item->registers->count() > 5)
                                            <span class="italic font-sans text-gray-600">(+{{ $item->registers->count() - 5 }} unit lainnya)</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="border border-black p-1.5 text-center font-bold align-top">{{ $item->jumlah_volume }}</td>
                            <td class="border border-black p-1.5 text-center align-top">{{ $item->satuan ?: 'Unit' }}</td>
                            <td class="border border-black p-1.5 text-right font-mono align-top">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="border border-black p-1.5 text-right font-mono font-bold align-top">{{ number_format($item->total_realisasi, 0, ',', '.') }}</td>
                            <td class="border border-black p-1.5 text-center text-[8.5pt] align-top">{{ $alokasiTxt }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 font-bold" style="background-color: #f3f4f6;">
                        <td colspan="2" class="border border-black p-1.5 text-center uppercase tracking-wider">TOTAL BELANJA</td>
                        <td class="border border-black p-1.5 text-center font-mono">{{ $totalVolFaktur }}</td>
                        <td class="border border-black p-1.5 text-center">Unit</td>
                        <td class="border border-black p-1.5 text-center">-</td>
                        <td class="border border-black p-1.5 text-right font-mono font-black text-[9.5pt]">
                            Rp {{ number_format($totalPembelian, 0, ',', '.') }}
                        </td>
                        <td class="border border-black p-1.5 text-center text-[8pt] text-gray-700">
                            {{ $belanja->status_alokasi === 'ruangan' ? 'Ruangan Unit' : ($belanja->status_alokasi === 'sebagian' ? 'Sebagian Ruang/Gudang' : 'Belum Ditempatkan') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- TERBILANG TOTAL NILAI RUPIAH -->
        <div class="mb-3 p-2 bg-gray-50 border border-gray-400 text-[9pt] rounded-sm" style="background-color: #f9fafb; border: 1px solid #9ca3af;">
            <span class="font-bold">Terbilang :</span>
            <span class="italic font-serif font-bold text-gray-900">"{{ $terbilangStr }}"</span>
        </div>

        @if(!empty($belanja->keterangan) && $belanja->keterangan !== '-')
        <div class="mb-3 text-[9pt] text-gray-800">
            <span class="font-bold">Catatan Pembelian :</span>
            <span>{{ $belanja->keterangan }}</span>
        </div>
        @endif

        <!-- KLAUSUL PENUTUP -->
        <p class="text-justify mb-4 text-[9.5pt]">
            Demikian Berita Acara Serah Terima ini dibuat dengan sebenarnya dalam rangkap secukupnya untuk dapat dipergunakan sebagaimana mestinya dan menjadi bukti pencatatan resmi Buku Inventaris Barang Milik Daerah (BMD) RSUD dr. H. Koesnandi Kabupaten Bondowoso.
        </p>

        <!-- BLOK TANDA TANGAN (3 PIHAK) -->
        <div class="mt-6 pt-2">
            <table class="w-full text-center border-none text-[9.5pt] leading-normal" style="border: none !important; width: 100%;">
                <tr style="border: none !important;">
                    <td class="w-1/2 align-top pb-2" style="border: none !important;">
                        <p class="font-bold m-0">PIHAK PERTAMA</p>
                        <p class="text-[9pt] text-gray-700 m-0">Yang Menyerahkan,</p>
                        <p class="font-bold uppercase text-[9pt] m-0">{{ $belanja->toko_penyedia }}</p>
                        <div class="h-20 flex items-center justify-center">
                            <span class="text-gray-400 text-[8pt] italic">(Tanda Tangan &amp; Cap Toko)</span>
                        </div>
                        <p class="font-bold underline uppercase m-0">{{ $belanja->toko_penyedia }}</p>
                        <p class="text-[8.5pt] text-gray-700 m-0">Pimpinan / Rekanan Penyedia</p>
                    </td>
                    <td class="w-1/2 align-top pb-2" style="border: none !important;">
                        <p class="font-bold m-0">PIHAK KEDUA</p>
                        <p class="text-[9pt] text-gray-700 m-0">Yang Menerima,</p>
                        <p class="font-bold text-[9pt] m-0">RSUD dr. H. Koesnandi</p>
                        <div class="h-20 flex items-center justify-center">
                            <!-- Stempel / TTE Digital BSrE -->
                            <div class="border border-emerald-600 bg-emerald-50/60 p-1.5 rounded flex items-center space-x-2 text-left" style="border: 1px solid #059669; background-color: #ecfdf5;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data={{ urlencode($qrUrl) }}" alt="TTE BSrE" class="w-12 h-12">
                                <div class="text-[7pt] text-emerald-800 leading-tight">
                                    <div class="font-bold text-[7.5pt]">DITANDATANGANI SECARA ELEKTRONIK</div>
                                    <div>Balai Sertifikasi Elektronik (BSrE) BSSN</div>
                                    <div class="font-mono text-[6.5pt] text-emerald-600 mt-0.5">ID: {{ substr($qrHash, 0, 12) }}</div>
                                </div>
                            </div>
                        </div>
                        <p class="font-bold underline uppercase m-0">BUDI HARTONO, S.Sos</p>
                        <p class="text-[8.5pt] text-gray-700 font-mono m-0">NIP. 19760229 200801 1 010</p>
                        <p class="text-[8.5pt] text-gray-700 m-0">Pengurus Barang Pengguna Aset</p>
                    </td>
                </tr>
                <tr style="border: none !important;">
                    <td colspan="2" class="pt-4 align-top" style="border: none !important;">
                        <p class="font-bold m-0">Mengetahui / Mengesahkan,</p>
                        <p class="text-[9pt] text-gray-700 m-0">Pejabat Pembuat Komitmen (PPK)</p>
                        <p class="font-bold text-[9pt] m-0">RSUD dr. H. Koesnandi Kabupaten Bondowoso</p>
                        <div class="h-16 flex items-center justify-center">
                            <span class="text-gray-400 text-[8pt] italic">(Tanda Tangan &amp; Stempel Basah)</span>
                        </div>
                        <p class="font-bold underline uppercase m-0">dr. YUS PRIYATNA ADRYANTO, Sp.P, FISR.</p>
                        <p class="text-[8.5pt] text-gray-700 font-mono m-0">NIP. 19771002 200604 1 006</p>
                    </td>
                </tr>
            </table>
        </div>

    </div>

</body>
</html>

