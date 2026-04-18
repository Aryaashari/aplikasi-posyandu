<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdUser extends Model
{
    protected $table = 'md_user';
    protected $guarded = ['id'];
    protected $hidden = ['password'];

    public function posyandu()
    {
        return $this->belongsTo(MdPosyandu::class, 'id_posyandu');
    }
}
