<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrabajoRepuesto extends Model
{
    protected $table = 'trabajo_repuestos';
    protected $primaryKey = 'id_trabajo_repuesto';

    protected $fillable = [
        'id_trabajo_mantenimiento',
        'id_repuesto',
        'cantidad',
        'observacion',
        'estado'
    ];

    protected $casts = [
        'cantidad' => 'integer',
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

    // Relación con Repuesto (asumiendo que existe)
    public function repuesto()
    {
        return $this->belongsTo(
            Repuesto::class,
            'id_repuesto',
            'id_repuesto'
        );
    }
}