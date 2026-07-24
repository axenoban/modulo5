<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Herramienta extends Model
{
    protected $table = 'herramientas';
    protected $primaryKey = 'id_herramienta';

    protected $fillable = [
        'id_categoria_herramienta',
        'nombre',
        'nro_serie_interno',
        'estado_fisico',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación con CategoriaHerramienta
    public function categoria()
    {
        return $this->belongsTo(
            CategoriaHerramienta::class,
            'id_categoria_herramienta',
            'id_categoria_herramienta'
        );
    }

    // Scope para obtener solo herramientas activas
    public function scopeActivas($query)
    {
        return $query->where('estado', 1);
    }

    // Scope para obtener solo herramientas inactivas
    public function scopeInactivas($query)
    {
        return $query->where('estado', 0);
    }
}