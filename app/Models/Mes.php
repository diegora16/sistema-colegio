<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mes extends Model
{
    protected $table = 'mes';

    protected $fillable = [
        'nombre',
        'numero',
    ];

    public function inscripcionesPago()
    {
        return $this->hasMany(InscripcionPago::class, 'id_mes');
    }
}
