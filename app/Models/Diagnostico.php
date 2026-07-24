<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostico extends Model
{
    protected $table = 'diagnosticos';
    protected $primaryKey = 'id_diagnostico';

    protected $fillable = [
        'codigo',
        'descripcion',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];
}
