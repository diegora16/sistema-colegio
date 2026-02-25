<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    protected $table = 'tipo_pago';

    protected $fillable = [
        'nombre',
    ];

    public function inscripcionesPago()
    {
        return $this->hasMany(InscripcionPago::class, 'id_tipo_pago');
    }
}
