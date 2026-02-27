<?php

namespace App\Http\Controllers;

use App\Models\Apoderado;
use Illuminate\Http\Request;

class ApoderadoController extends Controller
{
    public function index(Request $request)
    {
        $busqueda   = $request->input('q', '');

        $apoderados = Apoderado::withCount('alumnos')
            ->when($busqueda, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('nombres',     'like', "%{$busqueda}%")
                       ->orWhere('apellido_p', 'like', "%{$busqueda}%")
                       ->orWhere('apellido_m', 'like', "%{$busqueda}%")
                       ->orWhere('dni',        'like', "%{$busqueda}%")
                )
            )
            ->orderBy('apellido_p')
            ->orderBy('apellido_m')
            ->orderBy('nombres')
            ->paginate(20)
            ->withQueryString();

        return view('apoderados.index', compact('apoderados', 'busqueda'));
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
