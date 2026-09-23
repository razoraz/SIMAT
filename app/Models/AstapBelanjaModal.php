<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstapBelanjaModal extends Model
{
    use HasFactory;

    protected $table = 'astap_belanja_modals';

    protected $guarded = ['id'];

    protected $casts = [
        'spk_tanggal'            => 'date:d/m/Y',
        'surat_pesanan_tanggal'  => 'date:d/m/Y',
        'kwitansi_tanggal'       => 'date:d/m/Y',
        'faktur_tanggal'         => 'date:d/m/Y',
        'sp2d_tanggal'           => 'date:d/m/Y',
        'bast_dokumen_tanggal'   => 'date:d/m/Y',
    ];

    public function setSpkTanggalAttribute($value)
    {
        $this->attributes['spk_tanggal'] = Astap::parseDateInput($value);
    }

    public function setSuratPesananTanggalAttribute($value)
    {
        $this->attributes['surat_pesanan_tanggal'] = Astap::parseDateInput($value);
    }

    public function setKwitansiTanggalAttribute($value)
    {
        $this->attributes['kwitansi_tanggal'] = Astap::parseDateInput($value);
    }

    public function setFakturTanggalAttribute($value)
    {
        $this->attributes['faktur_tanggal'] = Astap::parseDateInput($value);
    }

    public function setSp2dTanggalAttribute($value)
    {
        $this->attributes['sp2d_tanggal'] = Astap::parseDateInput($value);
    }

    public function setBastDokumenTanggalAttribute($value)
    {
        $this->attributes['bast_dokumen_tanggal'] = Astap::parseDateInput($value);
    }

    public function astap()
    {
        return $this->belongsTo(Astap::class);
    }

    public function jenisPengadaan()
    {
        return $this->belongsTo(JenisPengadaan::class);
    }

    public function rekeningBelanja()
    {
        return $this->belongsTo(RekeningBelanja::class);
    }
}
