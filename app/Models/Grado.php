<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grado extends Model
{
    protected $table = 'grado';

    protected $fillable = [
        'id_educativo',
        'nombre',
    ];

    public function nivelEducativo()
    {
        return $this->belongsTo(NivelEducativo::class, 'id_educativo');
    }

    public function secciones()
    {
        return $this->hasMany(Seccion::class, 'id_grado');
    }

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_grado');
    }
}
