<?php

namespace App\Http\Controllers;

use App\Models\Apoderado;
use Illuminate\Http\Request;

class ApoderadoController extends Controller
{
    public function index()
    {
        $apoderados = Apoderado::withCount('alumnos')
            ->orderBy('apellido_p')
            ->orderBy('apellido_m')
            ->orderBy('nombres')
            ->get();

        return view('apoderados.index', compact('apoderados'));
    }

    public function buscarPorDni(Request $request)
    {
        $apoderado = Apoderado::where('dni', $request->query('dni'))->first();

        if (!$apoderado) {
            return response()->json(null);
        }

        return response()->json([
            'nombres'    => $apoderado->nombres,
            'apellido_p' => $apoderado->apellido_p,
            'apellido_m' => $apoderado->apellido_m,
            'correo'     => $apoderado->correo ?? '',
            'telefono'   => $apoderado->telefono ?? '',
        ]);
    }

    public function edit(Apoderado $apoderado)
    {
        return view('apoderados.edit', compact('apoderado'));
    }

    public function update(Request $request, Apoderado $apoderado)
    {
        $request->validate([
            'dni'        => ['required', 'digits:8', 'unique:apoderado,dni,' . $apoderado->id],
            'nombres'    => ['required', 'string', 'max:100'],
            'apellido_p' => ['required', 'string', 'max:50'],
            'apellido_m' => ['required', 'string', 'max:50'],
            'correo'     => ['nullable', 'email', 'max:100'],
            'telefono'   => ['nullable', 'string', 'max:15'],
        ], [
            'dni.required'        => 'El DNI es obligatorio.',
            'dni.digits'          => 'El DNI debe tener 8 dígitos.',
            'dni.unique'          => 'Ya existe un apoderado con ese DNI.',
            'nombres.required'    => 'Los nombres son obligatorios.',
            'apellido_p.required' => 'El apellido paterno es obligatorio.',
            'apellido_m.required' => 'El apellido materno es obligatorio.',
        ]);

        $apoderado->update($request->only(['dni', 'nombres', 'apellido_p', 'apellido_m', 'correo', 'telefono']));

        return redirect()->route('apoderados.index')
            ->with('success', 'Apoderado actualizado correctamente.');
    }
}
