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

    // table
    public static function getDataForTable($tahun_id, $indikator_id)
    {
        return Quesioner::when($tahun_id, function ($q) use ($tahun_id) {
            return $q->where('tahun_id', $tahun_id);
        })
            ->when($indikator_id, function ($q) use ($indikator_id) {
                return $q->where('indikator_id', $indikator_id);
            })
            ->orderBy('id', 'DESC')->get();
    }

    //  get total quesioner by user_id, tahun_id
    public static function getTotal($tahunId)
    {
        $data = Quesioner::select('tm_indikators.id', 'tm_quesioners.id as quesionerId', 'indikator_id', 'tm_indikators.n_indikator', 'tm_indikators.deskripsi')
            ->join('tm_indikators', 'tm_indikators.id', '=', 'tm_quesioners.indikator_id')
            ->where('tm_quesioners.tahun_id', $tahunId)
            ->count();

        return $data;
    }
}
