<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\AnioAcademico;
use App\Models\Grado;
use App\Models\NivelEducativo;
use App\Models\Seccion;
use Illuminate\Http\Request;

class SeccionController extends Controller
{
    public function index()
    {
        $anioActivo = AnioAcademico::where('estado', 'activo')->first();

        $secciones = Seccion::with(['nivelEducativo', 'grado'])
            ->when($anioActivo, function ($q) use ($anioActivo) {
                $q->whereHas('nivelEducativo', fn($q2) => $q2->where('id_año', $anioActivo->id));
            })
            ->orderBy('nombre')
            ->get();

        $niveles = $anioActivo
            ? NivelEducativo::where('id_año', $anioActivo->id)->orderBy('nombre')->get()
            : collect();

        return view('configuracion.secciones.index', compact('secciones', 'niveles', 'anioActivo'));
    }

    public function create()
    {
        $niveles = NivelEducativo::with('anioAcademico')->orderBy('nombre')->get();
        $grados  = Grado::orderBy('nombre')->get(['id', 'id_educativo', 'nombre']);
        return view('configuracion.secciones.create', compact('niveles', 'grados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_educativo' => ['required', 'exists:nivel_educativo,id'],
            'id_grado'     => ['required', 'exists:grado,id'],
            'nombre'       => ['required', 'string', 'max:10'],
        ], [
            'id_educativo.required' => 'Selecciona un nivel educativo.',
            'id_grado.required'     => 'Selecciona un grado.',
            'nombre.required'       => 'El nombre de la sección es obligatorio.',
            'nombre.max'            => 'El nombre no puede superar 10 caracteres.',
        ]);

        Seccion::create($request->only(['id_educativo', 'id_grado', 'nombre']));

        return redirect()->route('configuracion.secciones.index')
            ->with('success', "Sección '{$request->nombre}' creada correctamente.");
    }

    public function show(string $id) {}

    public function edit(Seccion $seccion)
    {
        $niveles = NivelEducativo::with('anioAcademico')->orderBy('nombre')->get();
        $grados  = Grado::orderBy('nombre')->get(['id', 'id_educativo', 'nombre']);
        return view('configuracion.secciones.edit', compact('seccion', 'niveles', 'grados'));
    }

    public function update(Request $request, Seccion $seccion)
    {
        $request->validate([
            'id_educativo' => ['required', 'exists:nivel_educativo,id'],
            'id_grado'     => ['required', 'exists:grado,id'],
            'nombre'       => ['required', 'string', 'max:10'],
        ], [
            'id_educativo.required' => 'Selecciona un nivel educativo.',
            'id_grado.required'     => 'Selecciona un grado.',
            'nombre.required'       => 'El nombre de la sección es obligatorio.',
        ]);

        $seccion->update($request->only(['id_educativo', 'id_grado', 'nombre']));

        return redirect()->route('configuracion.secciones.index')
            ->with('success', 'Sección actualizada correctamente.');
    }

    public function destroy(Seccion $seccion)
    {
        if ($seccion->alumnos()->exists() || $seccion->inscripcionesPago()->exists()) {
            return back()->with('error', "No se puede eliminar '{$seccion->nombre}': tiene alumnos o inscripciones asociadas.");
        }

        $seccion->delete();

        return redirect()->route('configuracion.secciones.index')
            ->with('success', "Sección '{$seccion->nombre}' eliminada correctamente.");
    }
}
