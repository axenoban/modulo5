<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    protected $table = 'personal';
    protected $primaryKey = 'id_personal';

    protected $fillable = [
        'nombre',
        'apellido',
        'cargo',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];
}
