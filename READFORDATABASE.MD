agar data aman kalo gasengaja kejadian nomer 2 gimana caranya?
8:17 AM
Untuk mengamankan data di server agar tidak terhapus karena ketidaksengajaan, berikut langkah-langkah proteksi standar industri di Laravel:

1. Kunci Mutlak dengan Fitur Resmi Laravel (Paling Ampuh & Mudah)
Laravel memiliki fitur khusus untuk memblokir total perintah penghapusan database (migrate:fresh, db:wipe, dll) saat di server production.

Buka file 

app/Providers/AppServiceProvider.php
, lalu tambahkan baris DB::prohibitDestructiveCommands() di method boot():

php
use Illuminate\Support\Facades\DB;
public function boot(): void
{
    // Larang keras perintah migrate:fresh / db:wipe jika di server production
    DB::prohibitDestructiveCommands($this->app->isProduction());
}
Efeknya: Jika ada orang yang tidak sengaja mengetik php artisan migrate:fresh di server produksi (bahkan jika memakai --force), Laravel akan langsung menolak dan menggagalkan perintah tersebut.

2. Pastikan File .env di Server Diset ke production
Di server live, selalu pastikan konfigurasi file .env:

env
APP_ENV=production
APP_DEBUG=false
Jika APP_ENV=production, Laravel secara otomatis akan menampilkan peringatan bahaya warna merah dan meminta konfirmasi sebelum menjalankan perintah database.

3. Pasang Backup Otomatis Database (Safety Net)
Jika terjadi kesalahan fatal lain (misal harddisk server rusak atau salah klik di database):

Buat jadwal otomatis (Cron Job) untuk mem-backup database setiap hari atau setiap beberapa jam menggunakan perintah mysqldump atau package spatie/laravel-backup.
Dengan backup rutin, data rumah sakit bisa dipulihkan (restore) kembali kapan saja.
4. Batasi Hak Akses Database (User Privilege)
Di MySQL/PostgreSQL server:

Jangan gunakan user root untuk koneksi aplikasi di .env.
Buat user database baru yang hanya memiliki hak SELECT, INSERT, UPDATE, DELETE, ALTER tanpa hak DROP DATABASE.
💡 Rekomendasi Terbaik: Terapkan Langkah 1 & 2, karena kedua langkah ini sangat cepat dipasang dan langsung memberi perlindungan 100% dari ketidaksengajaan menjalankan perintah reset database.