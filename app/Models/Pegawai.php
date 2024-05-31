<?php

namespace App\Models;

use App\TmResult;
use Illuminate\Database\Eloquent\Model;

// Models
use App\User;

class Pegawai extends Model
{
    protected $table = 'tm_pegawais';
    protected $fillable = ['id', 'user_id', 'tempat_id', 'nama_instansi', 'nama_kepala', 'jabatan_kepala', 'nama_operator', 'jabatan_operator', 'email', 'telp', 'alamat', 'foto', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tempat()
    {
        return $this->belongsTo(Tempat::class, 'tempat_id');
    }

    public static function getDataUser($id)
    {
        $data = Pegawai::join('tm_users', 'tm_pegawais.user_id', '=', 'tm_users.id')
            ->where('tm_pegawais.id', $id)
            ->first();

        return $data;
    }

    public function quesionerResult()
    {
        return $this->hasMany(TmResult::class, 'user_id', 'user_id');
    }

    public static function queryData($role_id)
    {
        return Pegawai::select('tm_pegawais.id as id', 'user_id', 'nama_instansi', 'tempat_id', 'nama_kepala', 'jabatan_kepala', 'nama_operator', 'jabatan_operator', 'email', 'telp')
            ->with(['user', 'tempat', 'quesionerResult'])
            ->leftjoin('tm_places', 'tm_places.id', '=', 'tm_pegawais.tempat_id')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'tm_pegawais.user_id')
            ->when($role_id, function ($q) use ($role_id) {
                return $q->where('model_has_roles.role_id', $role_id);
            })
            ->orderBy('tm_pegawais.id', 'DESC')
            ->get();
    }
}
