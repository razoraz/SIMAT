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
        $all = self::whereNotNull('jenis')
            ->where('jenis', '!=', '')
            ->whereNotNull('nama_jenis')
            ->where('nama_jenis', '!=', '')
            ->orderBy('jenis')
            ->orderBy('sub_rincian_objek')
            ->orderBy('sub_sub_rincian_objek')
            ->get();

        if ($all->isEmpty()) {
            return [];
        }

        return $all->groupBy('jenis')->map(function ($items, $jenisKode) {
            $firstItem = $items->first();
            $namaJenis = trim($firstItem->nama_jenis ?? '');

            if (empty($jenisKode) || empty($namaJenis)) {
                return null;
            }

            $subRincians = $items->groupBy('sub_rincian_objek')->map(function ($subItems, $subKode) {
                $firstSub = $subItems->first();
                $namaSub = trim($firstSub->uraian_sub_rincian ?? '');

                if (empty($subKode) || empty($namaSub)) {
                    return null;
                }

                $subSubRincians = $subItems->filter(function ($item) {
                    return !empty($item->sub_sub_rincian_objek) && !empty(trim($item->uraian_sub_sub_rincian ?? ''));
                })->map(function ($item) {
                    return [
                        'id'   => $item->id,
                        'kode' => $item->sub_sub_rincian_objek,
                        'nama' => trim($item->uraian_sub_sub_rincian),
                    ];
                })->values()->toArray();

                return [
                    'kode' => $subKode,
                    'nama' => $namaSub,
                    'subSubRincian' => $subSubRincians,
                ];
            })->filter()->values()->toArray();

            return [
                'kode' => $jenisKode,
                'nama' => $namaJenis,
                'subRincian' => $subRincians,
            ];
        })->filter()->values()->toArray();
    }
}