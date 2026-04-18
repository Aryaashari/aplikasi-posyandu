<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrxPmt extends Model
{
    protected $table = 'trx_pmt';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function anak()
    {
        return $this->belongsTo(MdAnak::class, 'id_anak');
    }
}
