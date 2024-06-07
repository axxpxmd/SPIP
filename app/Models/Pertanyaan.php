<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertanyaan extends Model
{
    protected $table = 'tm_questions';
    protected $fillable = ['id', 'indikator_id', 'n_question', 'status_wajib', 'created_at', 'updated_at'];

    public function indikator()
    {
        return $this->belongsTo(Indikator::class, 'indikator_id');
    }

    public function questionOnQuesioner()
    {
        return $this->hasMany(Quesioner::class, 'question_id', 'id');
    }

    public static function queryTable($indikator_id)
    {
        return Pertanyaan::select('tm_questions.id as id', 'tm_questions.indikator_id', 'n_question')
            ->with(['indikator'])
            ->join('tm_indikators', 'tm_indikators.id', '=', 'tm_questions.indikator_id')
            ->when($indikator_id, function ($q) use ($indikator_id) {
                return  $q->where('indikator_id', $indikator_id);
            })
            ->orderBy('tm_questions.id', 'DESC')
            ->get();
    }
}
