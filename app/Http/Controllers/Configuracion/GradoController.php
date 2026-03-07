<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\AnioAcademico;
use App\Models\Grado;
use App\Models\NivelEducativo;
use Illuminate\Http\Request;

class GradoController extends Controller
{
    public function index()
    {
        $anioActivo = AnioAcademico::where('estado', 'activo')->first();

        $grados = Grado::with('nivelEducativo.anioAcademico')
            ->when($anioActivo, function ($q) use ($anioActivo) {
                $q->whereHas('nivelEducativo', fn($q2) => $q2->where('id_año', $anioActivo->id));
            })
            ->orderBy('nombre')
            ->get();

        $niveles = $anioActivo
            ? NivelEducativo::where('id_año', $anioActivo->id)->orderBy('nombre')->get()
            : collect();

        return view('configuracion.grados.index', compact('grados', 'niveles', 'anioActivo'));
    }

    public function create()
    {
        $anioActivo = AnioAcademico::where('estado', 'activo')->first();
        $niveles    = $anioActivo
            ? NivelEducativo::where('id_año', $anioActivo->id)->orderBy('nombre')->get()
            : collect();
        return view('configuracion.grados.create', compact('niveles', 'anioActivo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_educativo' => ['required', 'exists:nivel_educativo,id'],
            'nombre'       => ['required', 'string', 'max:20'],
        ], [
            'id_educativo.required' => 'Selecciona un nivel educativo.',
            'id_educativo.exists'   => 'El nivel seleccionado no existe.',
            'nombre.required'       => 'El nombre del grado es obligatorio.',
            'nombre.max'            => 'El nombre no puede superar 20 caracteres.',
        ]);

        Grado::create($request->only(['id_educativo', 'nombre']));

        return redirect()->route('configuracion.grados.index')
            ->with('success', "Grado '{$request->nombre}' creado correctamente.");
    }

    public function show(string $id) {}

    public function edit(Grado $grado)
    {
        $anioActivo = AnioAcademico::where('estado', 'activo')->first();
        $niveles    = $anioActivo
            ? NivelEducativo::where('id_año', $anioActivo->id)->orderBy('nombre')->get()
            : collect();
        return view('configuracion.grados.edit', compact('grado', 'niveles', 'anioActivo'));
    }

    public function update(Request $request, Grado $grado)
    {
        $request->validate([
            'id_educativo' => ['required', 'exists:nivel_educativo,id'],
            'nombre'       => ['required', 'string', 'max:20'],
        ], [
            'id_educativo.required' => 'Selecciona un nivel educativo.',
            'nombre.required'       => 'El nombre del grado es obligatorio.',
        ]);

        $grado->update($request->only(['id_educativo', 'nombre']));

        return redirect()->route('configuracion.grados.index')
            ->with('success', 'Grado actualizado correctamente.');
    }

    public function destroy(Grado $grado)
    {
        if ($grado->alumnos()->exists()) {
            return back()->with('error', "No se puede eliminar '{$grado->nombre}': tiene alumnos matriculados.");
        }

        $grado->delete();

        return redirect()->route('configuracion.grados.index')
            ->with('success', "Grado '{$grado->nombre}' eliminado correctamente.");
    }
}
