<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\AnioAcademico;
use App\Models\NivelEducativo;
use Illuminate\Http\Request;

class NivelEducativoController extends Controller
{
    public function index()
    {
        $anioActivo = AnioAcademico::where('estado', 'activo')->first();
        $anios      = AnioAcademico::orderBy('nombre', 'desc')->get();

        $niveles = NivelEducativo::with('anioAcademico')
            ->when($anioActivo, fn($q) => $q->where('id_año', $anioActivo->id))
            ->orderBy('nombre')
            ->get();

        return view('configuracion.niveles.index', compact('niveles', 'anios', 'anioActivo'));
    }

    public function create()
    {
        $anios      = AnioAcademico::orderBy('nombre', 'desc')->get();
        $anioActivo = AnioAcademico::where('estado', 'activo')->first();
        return view('configuracion.niveles.create', compact('anios', 'anioActivo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_año'  => ['required', 'exists:anio_academico,id'],
            'nombre'  => ['required', 'string', 'max:50'],
            'precio'  => ['required', 'numeric', 'min:0', 'max:9999.99'],
        ], [
            'id_año.required' => 'Selecciona un año académico.',
            'id_año.exists'   => 'El año seleccionado no existe.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max'      => 'El nombre no puede superar 50 caracteres.',
            'precio.required' => 'El precio mensual es obligatorio.',
            'precio.numeric'  => 'El precio debe ser un número.',
            'precio.min'      => 'El precio no puede ser negativo.',
        ]);

        NivelEducativo::create($request->only(['id_año', 'nombre', 'precio']));

        return redirect()->route('configuracion.niveles.index')
            ->with('success', "Nivel educativo '{$request->nombre}' creado correctamente.");
    }

    public function show(string $id) {}

    public function edit(NivelEducativo $nivel)
    {
        $anios = AnioAcademico::orderBy('nombre', 'desc')->get();
        return view('configuracion.niveles.edit', compact('nivel', 'anios'));
    }

    public function update(Request $request, NivelEducativo $nivel)
    {
        $request->validate([
            'id_año'  => ['required', 'exists:anio_academico,id'],
            'nombre'  => ['required', 'string', 'max:50'],
            'precio'  => ['required', 'numeric', 'min:0', 'max:9999.99'],
        ], [
            'id_año.required' => 'Selecciona un año académico.',
            'nombre.required' => 'El nombre es obligatorio.',
            'precio.required' => 'El precio mensual es obligatorio.',
            'precio.numeric'  => 'El precio debe ser un número.',
        ]);

        $nivel->update($request->only(['id_año', 'nombre', 'precio']));

        return redirect()->route('configuracion.niveles.index')
            ->with('success', 'Nivel educativo actualizado correctamente.');
    }

    public function destroy(NivelEducativo $nivel)
    {
        if ($nivel->alumnos()->exists()) {
            return back()->with('error', "No se puede eliminar '{$nivel->nombre}': tiene alumnos matriculados.");
        }

        $nivel->delete();

        return redirect()->route('configuracion.niveles.index')
            ->with('success', "Nivel educativo '{$nivel->nombre}' eliminado correctamente.");
    }
}
