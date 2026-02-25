<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apoderado extends Model
{
    protected $table = 'apoderado';

    protected $fillable = [
        'dni',
        'nombres',
        'apellido_p',
        'apellido_m',
        'correo',
        'telefono',
    ];

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'id_apoderado');
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellido_p} {$this->apellido_m}";
    }
}
