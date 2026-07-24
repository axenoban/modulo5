<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    protected $table = 'mantenimientos';
    protected $primaryKey = 'id_mantenimiento';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tarifa_base',
        'tiempo_estimado',
        'estado'
    ];

    protected $casts = [
        'tarifa_base' => 'decimal:2',
        'tiempo_estimado' => 'integer',
        'estado' => 'boolean'
    ];

    // Relación con trabajos_mantenimiento
    public function trabajosMantenimiento()
    {
        return $this->hasMany(
            TrabajoMantenimiento::class,
            'id_mantenimiento',
            'id_mantenimiento'
        );
    }
}