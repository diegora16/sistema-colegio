<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pago';

    protected $fillable = [
        'id_inscripcion_pago',
        'aporte',
        'fecha',
        'foto',
    ];

    protected $casts = [
        'fecha'  => 'date',
        'aporte' => 'decimal:2',
    ];

    public function inscripcionPago()
    {
        return $this->belongsTo(InscripcionPago::class, 'id_inscripcion_pago');
    }
}
