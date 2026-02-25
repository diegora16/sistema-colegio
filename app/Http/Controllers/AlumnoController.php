<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Apoderado;
use App\Models\AnioAcademico;
use App\Models\NivelEducativo;
use App\Models\Grado;
use App\Models\Seccion;
use App\Models\TipoPago;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index()
    {
        $anioActivo    = AnioAcademico::where('estado', 'activo')->first();
        $tipoMatricula = TipoPago::where('nombre', 'Matrícula')->first();

        $alumnos = Alumno::with([
            'apoderado',
            'inscripcionesPago' => fn($q) => $q
                ->when($anioActivo && $tipoMatricula, fn($q2) =>
                    $q2->where('id_año', $anioActivo->id)
                       ->where('id_tipo_pago', $tipoMatricula->id)
                )
                ->with(['grado', 'nivelEducativo', 'seccion']),
        ])
            ->when($anioActivo, fn($q) =>
                $q->whereHas('inscripcionesPago', fn($q2) => $q2->where('id_año', $anioActivo->id))
            )
            ->orderBy('apellido_p')
            ->orderBy('apellido_m')
            ->orderBy('nombres')
            ->get();

        return view('alumnos.index', compact('alumnos', 'anioActivo'));
    }

    public function create()
    {
        $anioActivo = AnioAcademico::where('estado', 'activo')->first();

        $niveles   = $anioActivo
            ? NivelEducativo::where('id_año', $anioActivo->id)->orderBy('nombre')->get()
            : collect();
        $grados    = Grado::orderBy('nombre')->get(['id', 'id_educativo', 'nombre']);
        $secciones = Seccion::orderBy('nombre')->get(['id', 'id_educativo', 'id_grado', 'nombre']);

        return view('alumnos.create', compact('niveles', 'grados', 'secciones', 'anioActivo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'apoderado_dni'        => ['required', 'digits:8'],
            'apoderado_nombres'    => ['required', 'string', 'max:100'],
            'apoderado_apellido_p' => ['required', 'string', 'max:50'],
            'apoderado_apellido_m' => ['required', 'string', 'max:50'],
            'apoderado_correo'     => ['nullable', 'email', 'max:100'],
            'apoderado_telefono'   => ['nullable', 'string', 'max:15'],
            'id_educativo'         => ['required', 'exists:nivel_educativo,id'],
            'id_grado'             => ['required', 'exists:grado,id'],
            'id_seccion'           => ['required', 'exists:seccion,id'],
            'dni'                  => ['required', 'digits:8', 'unique:alumno,dni'],
            'nombres'              => ['required', 'string', 'max:100'],
            'apellido_p'           => ['required', 'string', 'max:50'],
            'apellido_m'           => ['required', 'string', 'max:50'],
            'fecha_nacimiento'     => ['required', 'date', 'before:today'],
            'correo'               => ['nullable', 'email', 'max:100'],
            'telefono'             => ['nullable', 'string', 'max:15'],
        ], [
            'apoderado_dni.required'        => 'El DNI del apoderado es obligatorio.',
            'apoderado_dni.digits'          => 'El DNI del apoderado debe tener 8 dígitos.',
            'apoderado_nombres.required'    => 'Los nombres del apoderado son obligatorios.',
            'apoderado_apellido_p.required' => 'El apellido paterno del apoderado es obligatorio.',
            'apoderado_apellido_m.required' => 'El apellido materno del apoderado es obligatorio.',
            'id_educativo.required'         => 'Selecciona un nivel educativo.',
            'id_grado.required'             => 'Selecciona un grado.',
            'id_seccion.required'           => 'Selecciona una sección.',
            'dni.required'                  => 'El DNI del alumno es obligatorio.',
            'dni.digits'                    => 'El DNI del alumno debe tener 8 dígitos.',
            'dni.unique'                    => 'Ya existe un alumno registrado con ese DNI.',
            'nombres.required'              => 'Los nombres del alumno son obligatorios.',
            'apellido_p.required'           => 'El apellido paterno del alumno es obligatorio.',
            'apellido_m.required'           => 'El apellido materno del alumno es obligatorio.',
            'fecha_nacimiento.required'     => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before'       => 'La fecha de nacimiento debe ser anterior a hoy.',
        ]);

        $apoderado = Apoderado::updateOrCreate(
            ['dni' => $request->apoderado_dni],
            [
                'nombres'    => $request->apoderado_nombres,
                'apellido_p' => $request->apoderado_apellido_p,
                'apellido_m' => $request->apoderado_apellido_m,
                'correo'     => $request->apoderado_correo,
                'telefono'   => $request->apoderado_telefono,
            ]
        );

        Alumno::create([
            'id_apoderado'     => $apoderado->id,
            'id_educativo'     => $request->id_educativo,
            'id_grado'         => $request->id_grado,
            'id_seccion'       => $request->id_seccion,
            'dni'              => $request->dni,
            'nombres'          => $request->nombres,
            'apellido_p'       => $request->apellido_p,
            'apellido_m'       => $request->apellido_m,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'correo'           => $request->correo,
            'telefono'         => $request->telefono,
        ]);

        return redirect()->route('alumnos.index')
            ->with('success', "Alumno '{$request->nombres} {$request->apellido_p} {$request->apellido_m}' registrado correctamente.");
    }

    public function show(string $id) {}

    public function edit(Alumno $alumno)
    {
        $alumno->load('apoderado', 'nivelEducativo', 'grado', 'seccion');

        $anioActivo = AnioAcademico::where('estado', 'activo')->first();

        $niveles   = $anioActivo
            ? NivelEducativo::where('id_año', $anioActivo->id)->orderBy('nombre')->get()
            : collect();
        $grados    = Grado::orderBy('nombre')->get(['id', 'id_educativo', 'nombre']);
        $secciones = Seccion::orderBy('nombre')->get(['id', 'id_educativo', 'id_grado', 'nombre']);

        return view('alumnos.edit', compact('alumno', 'niveles', 'grados', 'secciones', 'anioActivo'));
    }

    public function update(Request $request, Alumno $alumno)
    {
        $request->validate([
            'apoderado_dni'        => ['required', 'digits:8'],
            'apoderado_nombres'    => ['required', 'string', 'max:100'],
            'apoderado_apellido_p' => ['required', 'string', 'max:50'],
            'apoderado_apellido_m' => ['required', 'string', 'max:50'],
            'apoderado_correo'     => ['nullable', 'email', 'max:100'],
            'apoderado_telefono'   => ['nullable', 'string', 'max:15'],
            'id_educativo'         => ['required', 'exists:nivel_educativo,id'],
            'id_grado'             => ['required', 'exists:grado,id'],
            'id_seccion'           => ['required', 'exists:seccion,id'],
            'dni'                  => ['required', 'digits:8', 'unique:alumno,dni,' . $alumno->id],
            'nombres'              => ['required', 'string', 'max:100'],
            'apellido_p'           => ['required', 'string', 'max:50'],
            'apellido_m'           => ['required', 'string', 'max:50'],
            'fecha_nacimiento'     => ['required', 'date', 'before:today'],
            'correo'               => ['nullable', 'email', 'max:100'],
            'telefono'             => ['nullable', 'string', 'max:15'],
        ], [
            'apoderado_dni.required'        => 'El DNI del apoderado es obligatorio.',
            'apoderado_dni.digits'          => 'El DNI del apoderado debe tener 8 dígitos.',
            'apoderado_nombres.required'    => 'Los nombres del apoderado son obligatorios.',
            'apoderado_apellido_p.required' => 'El apellido paterno del apoderado es obligatorio.',
            'apoderado_apellido_m.required' => 'El apellido materno del apoderado es obligatorio.',
            'id_educativo.required'         => 'Selecciona un nivel educativo.',
            'id_grado.required'             => 'Selecciona un grado.',
            'id_seccion.required'           => 'Selecciona una sección.',
            'dni.required'                  => 'El DNI del alumno es obligatorio.',
            'dni.digits'                    => 'El DNI del alumno debe tener 8 dígitos.',
            'dni.unique'                    => 'Ya existe un alumno registrado con ese DNI.',
            'nombres.required'              => 'Los nombres del alumno son obligatorios.',
            'apellido_p.required'           => 'El apellido paterno del alumno es obligatorio.',
            'apellido_m.required'           => 'El apellido materno del alumno es obligatorio.',
            'fecha_nacimiento.required'     => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before'       => 'La fecha de nacimiento debe ser anterior a hoy.',
        ]);

        $apoderado = Apoderado::updateOrCreate(
            ['dni' => $request->apoderado_dni],
            [
                'nombres'    => $request->apoderado_nombres,
                'apellido_p' => $request->apoderado_apellido_p,
                'apellido_m' => $request->apoderado_apellido_m,
                'correo'     => $request->apoderado_correo,
                'telefono'   => $request->apoderado_telefono,
            ]
        );

        $alumno->update([
            'id_apoderado'     => $apoderado->id,
            'id_educativo'     => $request->id_educativo,
            'id_grado'         => $request->id_grado,
            'id_seccion'       => $request->id_seccion,
            'dni'              => $request->dni,
            'nombres'          => $request->nombres,
            'apellido_p'       => $request->apellido_p,
            'apellido_m'       => $request->apellido_m,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'correo'           => $request->correo,
            'telefono'         => $request->telefono,
        ]);

        return redirect()->route('alumnos.index')
            ->with('success', 'Alumno actualizado correctamente.');
    }

    public function destroy(Alumno $alumno)
    {
        if ($alumno->inscripcionesPago()->exists()) {
            return back()->with('error', "No se puede eliminar a '{$alumno->nombre_completo}': tiene inscripciones o pagos registrados.");
        }

        $nombre = $alumno->nombre_completo;
        $alumno->delete();

        return redirect()->route('alumnos.index')
            ->with('success', "Alumno '{$nombre}' eliminado correctamente.");
    }
}
