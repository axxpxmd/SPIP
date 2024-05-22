<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $table = 'tm_zonas';
    protected $fillable = ['id', 'n_zona', 'created_at', 'updated_at'];
}
