<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;
use App\Models\AstapKemitraan;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Jadwal Sinkronisasi Status Konsesi Kemitraan:
 * Otomatis memperbarui status menjadi 'Konsesi Berakhir' saat melewati tanggal_selesai
 */
Schedule::call(function () {
    AstapKemitraan::syncExpiredStatus();
})->dailyAt('00:05')->name('kemitraan:auto-expire-konsesi');





