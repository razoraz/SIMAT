<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstapRegister extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function astap()
    {
        return $this->belongsTo(Astap::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function mutasis()
    {
        return $this->hasMany(AstapMutasi::class);
    }

    public function distribusiItemRegisters()
    {
        return $this->hasMany(DistribusiItemRegister::class);
    }

    public function getKode108Attribute(): string
    {
        return $this->astap ? ($this->astap->kode_108 ?? '') : '';
    }

    public function getQrCodePathAttribute($value): string
    {
        return $value ?: '/scan/' . ($this->nibar ?? $this->no_register ?? '');
    }
}
