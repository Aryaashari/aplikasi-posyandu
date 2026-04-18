<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdPosyandu extends Model
{
    protected $table = 'md_posyandu';
    protected $guarded = ['id'];

    public function anak()
    {
        return $this->hasMany(MdAnak::class, 'id_posyandu');
    }

    public function users()
    {
        return $this->hasMany(MdUser::class, 'id_posyandu');
    }

    public function jadwal()
    {
        return $this->hasMany(TrxJadwal::class, 'id_posyandu');
    }
}
