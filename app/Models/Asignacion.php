<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    protected $table = 'asignaciones';
    protected $primaryKey = 'id_asignacion';

    protected $fillable = [
        'id_trabajo_mantenimiento',
        'id_personal',
        'rol_asignacion',
        'horas_invertidas',
        'fecha_asignacion',
        'estado'
    ];

    protected $casts = [
        'horas_invertidas' => 'decimal:2',
        'fecha_asignacion' => 'datetime',
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

    // Relación con Personal (asumiendo que existe)
    public function personal()
    {
        return $this->belongsTo(
            Personal::class,
            'id_personal',
            'id_personal'
        );
    }
}