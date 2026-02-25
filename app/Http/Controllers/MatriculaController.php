<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\AnioAcademico;
use App\Models\InscripcionPago;
use App\Models\TipoPago;
use Illuminate\Http\Request;

class MatriculaController extends Controller
{
    public function index()
    {
        $anioActivo = AnioAcademico::where('estado', 'activo')->first();

        $tipoMatricula = TipoPago::where('nombre', 'Matrícula')->first();

        $alumnos = Alumno::with(['nivelEducativo', 'grado', 'seccion'])
            ->when($anioActivo, function ($q) use ($anioActivo) {
                $q->whereHas('nivelEducativo', fn($q2) => $q2->where('id_año', $anioActivo->id));
            })
            ->orderBy('apellido_p')
            ->orderBy('apellido_m')
            ->orderBy('nombres')
            ->get();

        // IDs de alumnos que ya tienen matrícula en este año
        $yaMatriculados = $anioActivo && $tipoMatricula
            ? InscripcionPago::where('id_año', $anioActivo->id)
                ->where('id_tipo_pago', $tipoMatricula->id)
                ->pluck('id_alumno')
                ->toArray()
            : [];

        return view('pagos.matricular', compact('alumnos', 'tipoMatricula', 'anioActivo', 'yaMatriculados'));
    }

    public function store(Request $request)
    {
        $anioActivo = AnioAcademico::where('estado', 'activo')->first();

        if (!$anioActivo) {
            return back()->with('error', 'No hay un año académico activo. Activa uno en Configuración → Años.');
        }

        $tipoMatricula = TipoPago::where('nombre', 'Matrícula')->firstOrFail();

        $request->validate([
            'id_alumno'      => ['required', 'exists:alumno,id'],
            'costo_total'    => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
            'fecha_registro' => ['required', 'date'],
        ], [
            'id_alumno.required'      => 'Selecciona un alumno.',
            'costo_total.required'    => 'El costo de matrícula es obligatorio.',
            'costo_total.numeric'     => 'El costo debe ser un número.',
            'costo_total.min'         => 'El costo debe ser mayor a 0.',
            'fecha_registro.required' => 'La fecha de registro es obligatoria.',
        ]);

        // Verificar que el alumno no esté ya matriculado este año
        $yaMatriculado = InscripcionPago::where('id_alumno', $request->id_alumno)
            ->where('id_año', $anioActivo->id)
            ->where('id_tipo_pago', $tipoMatricula->id)
            ->exists();

        if ($yaMatriculado) {
            return back()->withInput()->withErrors([
                'id_alumno' => 'Este alumno ya tiene una matrícula registrada para el año activo.',
            ]);
        }

        $alumno = Alumno::findOrFail($request->id_alumno);

        $inscripcion = InscripcionPago::create([
            'id_alumno'      => $alumno->id,
            'id_seccion'     => $alumno->id_seccion,
            'id_grado'       => $alumno->id_grado,
            'id_educativo'   => $alumno->id_educativo,
            'id_año'         => $anioActivo->id,
            'id_tipo_pago'   => $tipoMatricula->id,
            'id_mes'         => null,
            'costo_total'    => $request->costo_total,
            'fecha_registro' => $request->fecha_registro,
            'estado'         => 'pendiente',
        ]);

        return redirect()->route('pagos.show', $inscripcion)
            ->with('success', 'Matrícula creada. Registre los pagos para generarlas mensualidades automáticamente.');
    }
}
