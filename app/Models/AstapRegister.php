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
}
