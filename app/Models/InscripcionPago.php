<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Grado;
use App\Models\NivelEducativo;

class InscripcionPago extends Model
{
    protected $table = 'inscripcion_pago';

    protected $fillable = [
        'id_alumno',
        'id_seccion',
        'id_grado',
        'id_educativo',
        'id_año',
        'id_tipo_pago',
        'id_mes',
        'costo_total',
        'fecha_registro',
        'estado',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'costo_total'    => 'decimal:2',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'id_seccion');
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'id_grado');
    }

    public function nivelEducativo()
    {
        return $this->belongsTo(NivelEducativo::class, 'id_educativo');
    }

    public function anioAcademico()
    {
        return $this->belongsTo(AnioAcademico::class, 'id_año');
    }

    public function tipoPago()
    {
        return $this->belongsTo(TipoPago::class, 'id_tipo_pago');
    }

    public function mes()
    {
        return $this->belongsTo(Mes::class, 'id_mes');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_inscripcion_pago');
    }

    public function getTotalPagadoAttribute(): float
    {
        return $this->pagos()->sum('aporte');
    }

    public function getSaldoPendienteAttribute(): float
    {
        return $this->costo_total - $this->total_pagado;
    }
}
