<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quesioner extends Model
{
    protected $table = 'tm_quesioners';
    protected $fillable = ['id', 'tahun_id', 'indikator_id', 'question_id', 'created_at', 'updated_at'];

    public function indikator()
    {
        return $this->belongsTo(Indikator::class, 'indikator_id');
    }

    public function question()
    {
        return $this->belongsTo(Pertanyaan::class, 'question_id');
    }

    public function tahun()
    {
        return $this->belongsTo(Time::class, 'tahun_id');
    }

    /**
     * QUERY
     */

    //  get total quesioner by user_id, tahun_id and zona_id
    public static function getTotal($tahunId, $zonaId)
    {
        $data = Quesioner::select('tm_indikators.id', 'tm_quesioners.id as quesionerId', 'indikator_id', 'tm_indikators.n_indikator', 'tm_indikators.deskripsi')
            ->join('tm_indikators', 'tm_indikators.id', '=', 'tm_quesioners.indikator_id')
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->where('tm_indikators.zona_id', $zonaId)
            ->count();

        return $data;
    }
}
