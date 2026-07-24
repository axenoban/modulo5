<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repuesto extends Model
{
    protected $table = 'repuestos';
    protected $primaryKey = 'id_repuesto';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'stock',
        'estado'
    ];

    protected $casts = [
        'stock' => 'integer',
        'estado' => 'boolean'
    ];
}
