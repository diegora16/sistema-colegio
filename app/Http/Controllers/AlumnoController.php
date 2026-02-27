<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Apoderado;
use App\Models\AnioAcademico;
use App\Models\NivelEducativo;
use App\Models\Grado;
use App\Models\Seccion;
use App\Models\TipoPago;
use App\Services\AnioService;
use Illuminate\Http\Request;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $anioActivo    = AnioAcademico::where('estado', 'activo')->first();
        $tipoMatricula = TipoPago::where('nombre', 'Matrícula')->first();
        $busqueda      = $request->input('q', '');

        $alumnos = Alumno::with([
            'apoderado',
            'nivelEducativo',
            'grado',
            'seccion',
            'inscripcionesPago' => fn($q) => $q
                ->when($anioActivo && $tipoMatricula, fn($q2) =>
                    $q2->where('id_año', $anioActivo->id)
                       ->where('id_tipo_pago', $tipoMatricula->id)
                )
                ->with(['grado', 'nivelEducativo', 'seccion']),
        ])
            ->when($anioActivo, fn($q) =>
                $q->whereHas('nivelEducativo', fn($q2) => $q2->where('id_año', $anioActivo->id))
            )
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

        return view('alumnos.index', compact('alumnos', 'anioActivo', 'busqueda'));
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

        $alumno = Alumno::create([
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

        return redirect()->route('pagos.matricular', ['alumno_id' => $alumno->id])
            ->with('success', "Alumno '{$request->nombres} {$request->apellido_p} {$request->apellido_m}' registrado. Ahora completa su matrícula.");
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
            'id_seccion'           => ['nullable', 'exists:seccion,id'],
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

    public function promoverForm()
    {
        $anioActivo = AnioAcademico::where('estado', 'activo')->first();

        if (!$anioActivo) {
            return redirect()->route('alumnos.index')->with('error', 'No hay año académico activo.');
        }

        // Alumnos cuyo id_educativo apunta a un año distinto al activo
        $alumnos = Alumno::with(['nivelEducativo.anioAcademico', 'grado', 'seccion'])
            ->whereHas('nivelEducativo', fn($q) => $q->where('id_año', '!=', $anioActivo->id))
            ->orderBy('apellido_p')->orderBy('apellido_m')->orderBy('nombres')
            ->get();

        // Calcular siguiente grado para cada alumno
        foreach ($alumnos as $alumno) {
            $nivelActual = $alumno->nivelEducativo?->nombre;
            $gradoActual = $alumno->grado?->nombre;
            $siguiente   = ($nivelActual && $gradoActual)
                ? AnioService::siguienteGrado($nivelActual, $gradoActual)
                : null;

            if ($siguiente) {
                $nuevoNivel          = NivelEducativo::where('nombre', $siguiente['nivel'])->where('id_año', $anioActivo->id)->first();
                $alumno->nuevoNivel  = $nuevoNivel;
                $alumno->nuevoGrado  = $nuevoNivel
                    ? Grado::where('nombre', $siguiente['grado'])->where('id_educativo', $nuevoNivel->id)->first()
                    : null;
                $alumno->egresa      = false;
            } else {
                $alumno->nuevoNivel = null;
                $alumno->nuevoGrado = null;
                $alumno->egresa     = ($nivelActual === 'Secundaria' && $gradoActual === '5°');
            }
        }

        $porPromover = $alumnos->filter(fn($a) => !$a->egresa && $a->nuevoGrado);
        $egresados   = $alumnos->filter(fn($a) => $a->egresa);

        return view('alumnos.promover', compact('anioActivo', 'porPromover', 'egresados'));
    }

    public function promoverEjecutar()
    {
        $anioActivo = AnioAcademico::where('estado', 'activo')->firstOrFail();

        $alumnos = Alumno::with(['nivelEducativo', 'grado'])
            ->whereHas('nivelEducativo', fn($q) => $q->where('id_año', '!=', $anioActivo->id))
            ->get();

        $promovidos = 0;

        foreach ($alumnos as $alumno) {
            $nivelActual = $alumno->nivelEducativo?->nombre;
            $gradoActual = $alumno->grado?->nombre;
            if (!$nivelActual || !$gradoActual) continue;

            $siguiente = AnioService::siguienteGrado($nivelActual, $gradoActual);
            if (!$siguiente) continue; // egresa — no se promueve automáticamente

            $nuevoNivel = NivelEducativo::where('nombre', $siguiente['nivel'])->where('id_año', $anioActivo->id)->first();
            if (!$nuevoNivel) continue;

            $nuevoGrado = Grado::where('nombre', $siguiente['grado'])->where('id_educativo', $nuevoNivel->id)->first();
            if (!$nuevoGrado) continue;

            $alumno->update([
                'id_educativo' => $nuevoNivel->id,
                'id_grado'     => $nuevoGrado->id,
                'id_seccion'   => null,
            ]);

            $promovidos++;
        }

        return redirect()->route('alumnos.index')
            ->with('success', "{$promovidos} " . ($promovidos === 1 ? 'alumno promovido' : 'alumnos promovidos') . " correctamente al año {$anioActivo->nombre}. Recuerda asignarles su sección.");
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
