<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    protected $table = 'tm_indikators';
    protected $fillable = ['id', 'n_indikator', 'deskripsi', 'created_at', 'updated_at'];

    public function quesioner()
    {
        return $this->belongsTo(Quesioner::class, 'id', 'indikator_id');
    }

    public function pertanyaan()
    {
        return $this->hasMany(Pertanyaan::class, 'indikator_id', 'id');
    }
}
