<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    protected $table = 'seccion';

    protected $fillable = [
        'id_educativo',
        'id_grado',
        'nombre',
    ];

    public function nivelEducativo()
    {
        return $this->belongsTo(NivelEducativo::class, 'id_educativo');
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'id_grado');
    }

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_seccion');
    }

    public function inscripcionesPago()
    {
        return $this->hasMany(InscripcionPago::class, 'id_seccion');
    }
}
