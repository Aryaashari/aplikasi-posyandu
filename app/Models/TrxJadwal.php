<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxJadwal extends Model
{
    protected $table = 'trx_jadwal';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function posyandu()
    {
        return $this->belongsTo(MdPosyandu::class, 'id_posyandu');
    }
}
