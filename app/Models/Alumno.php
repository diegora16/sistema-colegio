<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumno';

    protected $fillable = [
        'id_apoderado',
        'id_educativo',
        'id_grado',
        'id_seccion',
        'dni',
        'nombres',
        'apellido_p',
        'apellido_m',
        'fecha_nacimiento',
        'correo',
        'telefono',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function apoderado()
    {
        return $this->belongsTo(Apoderado::class, 'id_apoderado');
    }

    public function nivelEducativo()
    {
        return $this->belongsTo(NivelEducativo::class, 'id_educativo');
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'id_grado');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'id_seccion');
    }

    public function inscripcionesPago()
    {
        return $this->hasMany(InscripcionPago::class, 'id_alumno');
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellido_p} {$this->apellido_m}";
    }
}
