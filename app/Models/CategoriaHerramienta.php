<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaHerramienta extends Model
{
    protected $table = 'categorias_herramientas';
    protected $primaryKey = 'id_categoria_herramienta';

    protected $fillable = [
        'nombre',
        'requiere_certificacion',
        'estado'
    ];

    // Relaciones (ejemplo si tienes herramientas)
    // public function herramientas()
    // {
    //     return $this->hasMany(
    //         Herramienta::class,
    //         'id_categoria_herramienta',
    //         'id_categoria_herramienta'
    //     );
    // }
}