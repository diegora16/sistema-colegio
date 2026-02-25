<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\AnioAcademico;

class AnioAcademicoController extends Controller
{
    public function index()
    {
        $anios      = AnioAcademico::orderBy('nombre', 'desc')->get();
        $anioActual = (int) now()->setTimezone('America/Lima')->format('Y');
        return view('configuracion.anios.index', compact('anios', 'anioActual'));
    }

    public function activar(AnioAcademico $anio)
    {
        AnioAcademico::where('id', '!=', $anio->id)
            ->where('estado', 'activo')
            ->update(['estado' => 'cerrado']);

        $anio->update(['estado' => 'activo']);

        return redirect()->route('configuracion.anios.index')
            ->with('success', "Año académico {$anio->nombre} activado correctamente.");
    }

    public function destroy(AnioAcademico $anio)
    {
        if ($anio->inscripcionesPago()->exists()) {
            return back()->with('error', "No se puede eliminar el año {$anio->nombre}: tiene inscripciones de pago registradas.");
        }

        if ($anio->nivelesEducativos()->whereHas('alumnos')->exists()) {
            return back()->with('error', "No se puede eliminar el año {$anio->nombre}: tiene alumnos asociados.");
        }

        $anio->delete();

        return redirect()->route('configuracion.anios.index')
            ->with('success', "Año académico {$anio->nombre} eliminado correctamente.");
    }
}
