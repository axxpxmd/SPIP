<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    protected $table = 'tm_indikators';
    protected $fillable = ['id', 'zona_id', 'n_indikator', 'deskripsi', 'created_at', 'updated_at'];

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'zona_id');
    }

    public function quesioner()
    {
        return $this->belongsTo(Quesioner::class, 'id', 'indikator_id');
    }

    public function pertanyaan()
    {
        return $this->hasMany(Pertanyaan::class, 'indikator_id', 'id');
    }
}
