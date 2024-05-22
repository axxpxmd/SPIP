<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrResultFile extends Model
{
    protected $table = 'tr_result_files';
    protected $fillable = ['id', 'result_id', 'file', 'created_at', 'updated_at'];
}
