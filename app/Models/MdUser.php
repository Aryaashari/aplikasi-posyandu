<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MdUser extends Authenticatable
{
    use Notifiable;
    protected $table = 'md_user';
    protected $guarded = ['id'];
    protected $hidden = ['password'];

    public function posyandu()
    {
        return $this->belongsTo(MdPosyandu::class, 'id_posyandu');
    }
}
