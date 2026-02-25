<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelEducativo extends Model
{
    protected $table = 'nivel_educativo';

    protected $fillable = [
        'id_año',
        'nombre',
        'precio',
    ];

    public function anioAcademico()
    {
        return $this->belongsTo(AnioAcademico::class, 'id_año');
    }

    public function grados()
    {
        return $this->hasMany(Grado::class, 'id_educativo');
    }

    public function secciones()
    {
        return $this->hasMany(Seccion::class, 'id_educativo');
    }

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_educativo');
    }
}
