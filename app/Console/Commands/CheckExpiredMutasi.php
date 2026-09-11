<?php

namespace App\Console\Commands;

use App\Http\Controllers\MutasiController;
use Illuminate\Console\Command;

class CheckExpiredMutasi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mutasi:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa dan tolak secara otomatis pengajuan mutasi aset yang belum disetujui dalam 24 jam';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = MutasiController::autoRejectExpiredMutasis();
        $this->info("Pemeriksaan selesai. {$count} pengajuan mutasi yang kedaluwarsa telah otomatis ditolak.");
        return Command::SUCCESS;
    }
}
