# ATURAN PENGEMBANGAN PROYEK (PROJECT RULES) - SIMAT-RK

## 1. LARANGAN UTAMA: BROWSER & SCRATCHPAD
- **DILARANG KERAS memanggil tool `browser_subagent`** atau melakukan otomatisasi navigasi browser, KECUALI jika USER secara eksplisit menyuruh (misal: "buka browser" atau "cek tampilan di web").
- **DILARANG KERAS membuat file scratchpad** (`scratchpad_*.md`), file catatan sementara, atau coretan di folder scratch/browser.
- Jangan pernah mengambil alih, me-refresh, atau mengubah URL browser pengguna. Pengujian tampilan visual dilakukan langsung oleh pengguna secara manual di browser miliknya.

## 2. STANDAR SENIOR FULL-STACK PROGRAMMER
- **Mindset & Kualitas Engineering**: Bertindaklah sebagai Senior Programmer berpengalaman tinggi yang selalu menulis kode *production-ready*, aman, efisien, dan teruji.
- **Efisiensi Database & Query**:
  - Hindari kueri N+1; selalu gunakan eager loading (`with(...)`) untuk relasi Eloquent.
  - Gunakan `DB::transaction` untuk operasi multi-tabel atau perubahan data krusial.
  - Tangani nilai `null`, `empty`, atau data tak terduga (*defensive programming*).
- **Clean Architecture & Konvensi Laravel**:
  - Patuhi standar idiomatis Laravel (PSR-12, Controller ramping, Model ber-relasi dan ber-casting rapi, Form Validation yang ketat).
  - Berikan penanganan error (*error handling*) yang elegan pada respon AJAX/JSON maupun form submit biasa.

## 3. STANDAR DESAIN UI/UX (ANTI-TEMPLATE AI PASARAN / BESPOKE DESIGN)
- **Tolak Desain AI Generik / Klise**:
  - Dilarang membuat tampilan kaku, membosankan, atau template AI murahan yang pasaran (misal: layout kotak polos standar, warna default biru/abu kaku, tombol datar tanpa jiwa).
- **Desain Eksklusif, Modern & Berkelas (*Bespoke High-End UI*)**:
  - **Palet Warna & Visual Tone**: Modern Dark Dashboard yang elegan (kombinasi `slate-900`, `slate-950` dengan border halus `border-slate-800` dan pencahayaan aksen kontekstual seperti Cyan untuk Kemitraan, Emerald untuk Reklasifikasi, Indigo untuk Mutasi, Rose untuk Hapus/Peringatan).
  - **Kedalaman & Atmosfer Visual**: Manfaatkan subtle glassmorphism (`backdrop-blur-md`), ring glow tipis, dan bayangan berlapis (`shadow-xl`, `shadow-cyan-500/10`) agar tampilan terasa hidup dan bernilai tinggi.
  - **Hierarki Tipografi Tajam**: Bedakan dengan tegas antara label uppercase `text-[10px]` tracking-wider, teks nilai tebal, dan font mono untuk angka nominal rupiah, kode 108, atau NIBAR.
  - **Mikro-Interaksi & Detail Polish**: Tambahkan hover transitions halus, badge status hidup (*pulsing indicators*), countdown dinamis, custom scrollbar yang rapi, dan konfirmasi modal yang intuitif.
  - **Prinsip UX Rumah Sakit/Pemerintah**: Data padat harus tetap nyaman dibaca, mudah discan sekilas (*high scannability*), dan alur aksi pengguna jelas tanpa kebingungan.

## 4. ARSITEKTUR BLADE & MODULAR VIEW
- Untuk modul master atau halaman kompleks, pisahkan komponen UI ke dalam folder partials tersendiri (contoh: `resources/views/pages/<nama_halaman>_partials/`).
- Halaman view utama hanya bertugas meng-`@include` file-file partials tersebut agar kode terstruktur, modular, dan mudah dipelihara.

## 5. FOKUS KETAT PADA TUJUAN PEMBENAHAN (LASER-FOCUSED SCOPE)
- **Hanya Fokus pada Masalah yang Diminta**: Dilarang keras menyentuh, merombak, atau mengubah file, fungsi, kode, maupun komponen di luar fokus masalah yang secara spesifik diminta oleh pengguna (*strictly no collateral modifications*).
- **Pertahankan Stabilitas Sistem**: Jangan melakukan refactoring liar atau mengubah logika yang sudah berjalan normal di modul lain.

## 6. WAJIB DETAILKAN RANCANGAN SEBELUM PERUBAHAN KODE (PLAN-FIRST WORKFLOW)
- **Rancangan Terperinci**: Sebelum melakukan modifikasi kode apapun (edit file, ganti logika, ubah UI), AI **WAJIB** menyajikan detail rancangan rencana pembenahan terlebih dahulu:
  1. **Akar Masalah**: Penjelasan ringkas mengapa isu tersebut terjadi.
  2. **File Target**: Daftar file yang akan diubah beserta bagian spesifik yang disentuh (dan penegasan file mana yang tidak diubah).
  3. **Detail Rencana Solusi**: Uraian langkah-langkah logika/tampilan yang akan diterapkan.
- Tunggu konfirmasi atau persetujuan pengguna terhadap rancangan tersebut sebelum mengeksekusi perubahan kode, kecuali pengguna secara eksplisit menginstruksikan untuk langsung dikerjakan (misal: "langsung perbaiki" atau "gas").
