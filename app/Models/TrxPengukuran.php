<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxPengukuran extends Model
{
    protected $table = 'trx_pengukuran';
    protected $guarded = ['id'];

    public function anak()
    {
        return $this->belongsTo(MdAnak::class, 'id_anak');
    }
}
