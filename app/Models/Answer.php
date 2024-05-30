<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $table = 'tm_answers';
    protected $fillable = ['id', 'jawaban', 'created_at', 'updated_at'];

    public function answerOnQuesioner()
    {
        return $this->hasMany(TrQuesionerAnswer::class, 'answer_id', 'id');
    }
}
