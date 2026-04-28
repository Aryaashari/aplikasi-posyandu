<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxKehadiran extends Model
{
    protected $table = 'trx_kehadiran';
    protected $guarded = ['id'];
    public $timestamps = true;

    public function anak()
    {
        return $this->belongsTo(MdAnak::class, 'id_anak');
    }
}
