<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tempat extends Model
{
    protected $table = 'tm_places';
    protected $fillable = ['id', 'zona_id', 'alamat', 'n_tempat', 'status', 'created_at', 'updated_at'];

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'zona_id');
    }

    public function indikator()
    {
        return $this->belongsTo(Indikator::class, 'id', 'zona_id');
    }

    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'tempat_id', 'id');
    }
}
