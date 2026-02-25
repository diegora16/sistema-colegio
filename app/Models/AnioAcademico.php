<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnioAcademico extends Model
{
    protected $table = 'anio_academico';

    protected $fillable = [
        'nombre',
        'estado',
    ];

    public function nivelesEducativos()
    {
        return $this->hasMany(NivelEducativo::class, 'id_año');
    }

    public function inscripcionesPago()
    {
        return $this->hasMany(InscripcionPago::class, 'id_año');
    }
}
