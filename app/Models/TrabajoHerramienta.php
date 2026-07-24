<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrabajoHerramienta extends Model
{
    protected $table = 'trabajo_herramientas';
    protected $primaryKey = 'id_trabajo_herramienta';

    protected $fillable = [
        'id_trabajo_mantenimiento',
        'id_herramienta',
        'observacion',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];

    // Relación con TrabajoMantenimiento
    public function trabajoMantenimiento()
    {
        return $this->belongsTo(
            TrabajoMantenimiento::class,
            'id_trabajo_mantenimiento',
            'id_trabajo_mantenimiento'
        );
    }

    // Relación con Herramienta
    public function herramienta()
    {
        return $this->belongsTo(
            Herramienta::class,
            'id_herramienta',
            'id_herramienta'
        );
    }
}