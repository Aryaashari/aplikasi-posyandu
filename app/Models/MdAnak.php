<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdAnak extends Model
{
    protected $table = 'md_anak';
    protected $guarded = ['id'];

    public function posyandu()
    {
        return $this->belongsTo(MdPosyandu::class, 'id_posyandu');
    }

    public function pengukuran()
    {
        return $this->hasMany(TrxPengukuran::class, 'id_anak');
    }

    public function imunisasi()
    {
        return $this->hasMany(TrxImunisasi::class, 'id_anak');
    }

    public function kehadiran()
    {
        return $this->hasMany(TrxKehadiran::class, 'id_anak');
    }

    public function pmt()
    {
        return $this->hasMany(TrxPmt::class, 'id_anak');
    }
}
