<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisAstap extends Model
{
    protected $fillable = [
        'jenis',
        'nama_jenis',
        'sub_rincian_objek',
        'uraian_sub_rincian',
        'sub_sub_rincian_objek',
        'uraian_sub_sub_rincian',
    ];

    /**
     * Mengambil seluruh data Jenis ASTAP dalam struktur bertingkat (Jenis -> Sub Rincian -> Sub-Sub Rincian)
     */
    public static function getNested108(): array
    {
        $all = self::orderBy('jenis')
            ->orderBy('sub_rincian_objek')
            ->orderBy('sub_sub_rincian_objek')
            ->get();

        if ($all->isEmpty()) {
            return [];
        }

        return $all->groupBy('jenis')->map(function ($items, $jenisKode) {
            $firstItem = $items->first();
            $namaJenis = $firstItem->nama_jenis;

            $subRincians = $items->groupBy('sub_rincian_objek')->map(function ($subItems, $subKode) {
                $firstSub = $subItems->first();
                $namaSub = $firstSub->uraian_sub_rincian;

                $subSubRincians = $subItems->map(function ($item) {
                    return [
                        'kode' => $item->sub_sub_rincian_objek,
                        'nama' => $item->uraian_sub_sub_rincian,
                    ];
                })->values()->toArray();

                return [
                    'kode' => $subKode,
                    'nama' => $namaSub,
                    'subSubRincian' => $subSubRincians,
                ];
            })->values()->toArray();

            return [
                'kode' => $jenisKode,
                'nama' => $namaJenis,
                'subRincian' => $subRincians,
            ];
        })->values()->toArray();
    }
}