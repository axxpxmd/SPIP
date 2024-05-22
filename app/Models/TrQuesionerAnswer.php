<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrQuesionerAnswer extends Model
{
    protected $table = 'tr_quesioner_answers';
    protected $fillable = ['id', 'quesioner_id', 'answer_id', 'created_at', 'updated_at'];

    public function answer()
    {
        return $this->belongsTo(Answer::class, 'answer_id');
    }
}
