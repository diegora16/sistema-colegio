<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\AnioAcademico;
use Illuminate\Http\Request;

class AnioAcademicoController extends Controller
{
    public function index()
    {
        $anios = AnioAcademico::orderBy('nombre', 'desc')->get();
        return view('configuracion.anios.index', compact('anios'));
    }

    public function create()
    {
        return view('configuracion.anios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'digits:4', 'unique:anio_academico,nombre'],
        ], [
            'nombre.required' => 'El año es obligatorio.',
            'nombre.digits'   => 'El año debe ser exactamente 4 dígitos (ej: 2026).',
            'nombre.unique'   => 'Este año académico ya existe.',
        ]);

        AnioAcademico::create([
            'nombre' => $request->nombre,
            'estado' => 'cerrado',
        ]);

        return redirect()->route('configuracion.anios.index')
            ->with('success', "Año académico {$request->nombre} creado correctamente.");
    }

    public function show(string $id) {}

    public function edit(AnioAcademico $anio)
    {
        return view('configuracion.anios.edit', compact('anio'));
    }

    public function update(Request $request, AnioAcademico $anio)
    {
        $request->validate([
            'nombre' => ['required', 'digits:4', 'unique:anio_academico,nombre,' . $anio->id],
            'estado' => ['required', 'in:activo,cerrado'],
        ], [
            'nombre.required' => 'El año es obligatorio.',
            'nombre.digits'   => 'El año debe ser exactamente 4 dígitos.',
            'nombre.unique'   => 'Este año académico ya existe.',
        ]);

        if ($request->estado === 'activo') {
            AnioAcademico::where('id', '!=', $anio->id)
                ->where('estado', 'activo')
                ->update(['estado' => 'cerrado']);
        }

        $anio->update([
            'nombre' => $request->nombre,
            'estado' => $request->estado,
        ]);

        return redirect()->route('configuracion.anios.index')
            ->with('success', 'Año académico actualizado correctamente.');
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
