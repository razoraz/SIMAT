<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update sumber_dana di tabel astaps
        DB::table('astaps')
            ->whereIn('sumber_dana', ['pelimpahan_skpd', 'pelimpahan', 'mutasi_masuk'])
            ->update(['sumber_dana' => 'mutasi_eksternal']);

        // 2. Sinkronkan spesifikasi_json
        $records = DB::table('astaps')
            ->whereNotNull('spesifikasi_json')
            ->get(['id', 'spesifikasi_json']);

        foreach ($records as $r) {
            $spec = json_decode($r->spesifikasi_json, true);
            if (is_array($spec) && isset($spec['sumber_dana']) && in_array($spec['sumber_dana'], ['pelimpahan_skpd', 'pelimpahan', 'mutasi_masuk'], true)) {
                $spec['sumber_dana'] = 'mutasi_eksternal';
                DB::table('astaps')->where('id', $r->id)->update([
                    'spesifikasi_json' => json_encode($spec, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('astaps')
            ->where('sumber_dana', 'mutasi_eksternal')
            ->update(['sumber_dana' => 'pelimpahan_skpd']);
    }
};
