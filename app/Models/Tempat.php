<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tempat extends Model
{
    protected $table = 'tm_places';
    protected $fillable = ['id', 'alamat', 'n_tempat', 'status', 'created_at', 'updated_at'];

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'tempat_id', 'id');
    }
}
