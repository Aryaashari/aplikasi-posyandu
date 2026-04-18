<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxImunisasi extends Model
{
    protected $table = 'trx_imunisasi';
    protected $guarded = ['id'];

    public function anak()
    {
        return $this->belongsTo(MdAnak::class, 'id_anak');
    }
}
