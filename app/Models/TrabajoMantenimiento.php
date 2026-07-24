<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrabajoMantenimiento extends Model
{
    protected $table = 'trabajos_mantenimiento';
    protected $primaryKey = 'id_trabajo_mantenimiento';

    protected $fillable = [
        'id_diagnostico',
        'id_mantenimiento',
        'fecha_programada',
        'fecha_inicio',
        'fecha_fin',
        'observaciones',
        'estado'
    ];

    protected $casts = [
        'fecha_programada' => 'date',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación con Mantenimiento
    public function mantenimiento()
    {
        return $this->belongsTo(
            Mantenimiento::class,
            'id_mantenimiento',
            'id_mantenimiento'
        );
    }

    // Relación con Diagnostico (asumiendo que existe)
    public function diagnostico()
    {
        return $this->belongsTo(
            Diagnostico::class,
            'id_diagnostico',
            'id_diagnostico'
        );
    }

    // Relación con Asignaciones
    public function asignaciones()
    {
        return $this->hasMany(
            Asignacion::class,
            'id_trabajo_mantenimiento',
            'id_trabajo_mantenimiento'
        );
    }

    // Relación con TrabajoHerramientas
    public function trabajoHerramientas()
    {
        return $this->hasMany(
            TrabajoHerramienta::class,
            'id_trabajo_mantenimiento',
            'id_trabajo_mantenimiento'
        );
    }

    // Relación con TrabajoRepuestos
    public function trabajoRepuestos()
    {
        return $this->hasMany(
            TrabajoRepuesto::class,
            'id_trabajo_mantenimiento',
            'id_trabajo_mantenimiento'
        );
    }
}