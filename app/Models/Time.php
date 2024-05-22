<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    protected $table = 'tm_times';
    protected $fillable = ['id', 'tahun', 'start', 'end', 'created_at', 'updated_at'];
}
