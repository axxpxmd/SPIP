<?php

namespace App;

use App\Models\ModelHasRole;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

// models
use App\Models\Pegawai;

class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;

    protected $table = 'tm_users';
    protected $fillable = ['username', 'password'];
    protected $hidden = ['password',];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id', 'user_id');
    }

    public function modelHasRole()
    {
        return $this->belongsTo(ModelHasRole::class, 'id', 'model_id');
    }

    public function verifikatorTempat()
    {
        return $this->hasMany(VerifikatorTempat::class, 'user_id', 'id');
    }
}
