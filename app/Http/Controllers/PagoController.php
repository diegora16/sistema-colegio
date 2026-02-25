<?php

namespace App\Http\Controllers;

use App\Models\AnioAcademico;
use App\Models\InscripcionPago;
use App\Models\Mes;
use App\Models\Pago;
use App\Models\TipoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PagoController extends Controller
{
    public function index(Request $request)
    {
        $anioActivo = AnioAcademico::where('estado', 'activo')->first();

        $inscripciones = InscripcionPago::with(['alumno', 'tipoPago', 'mes'])
            ->withSum('pagos', 'aporte')
            ->when($anioActivo, fn($q) => $q->where('id_año', $anioActivo->id))
            ->when($request->estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->tipo,   fn($q, $t) => $q->where('id_tipo_pago', $t))
            ->orderBy('created_at', 'desc')
            ->get();

        $tipos = TipoPago::orderBy('id')->get();

        return view('pagos.index', compact('inscripciones', 'tipos', 'anioActivo'));
    }

    public function show(InscripcionPago $inscripcion)
    {
        $inscripcion->load([
            'alumno.apoderado',
            'alumno.nivelEducativo',
            'alumno.grado',
            'alumno.seccion',
            'tipoPago',
            'mes',
            'anioAcademico',
            'pagos' => fn($q) => $q->orderBy('fecha')->orderBy('created_at'),
        ]);

        return view('pagos.show', compact('inscripcion'));
    }

    public function pagar(Request $request, InscripcionPago $inscripcion)
    {
        $totalPagado = (float) $inscripcion->pagos()->sum('aporte');
        $saldo       = (float) $inscripcion->costo_total - $totalPagado;

        if ($saldo <= 0) {
            return back()->with('error', 'Esta inscripción ya está completamente pagada.');
        }

        $request->validate([
            'aporte' => ['required', 'numeric', 'min:0.01', 'max:' . $saldo],
            'fecha'  => ['required', 'date'],
            'foto'   => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'aporte.required' => 'El monto es obligatorio.',
            'aporte.numeric'  => 'El monto debe ser un número.',
            'aporte.min'      => 'El monto debe ser mayor a 0.',
            'aporte.max'      => 'El monto no puede exceder el saldo pendiente (S/ ' . number_format($saldo, 2) . ').',
            'fecha.required'  => 'La fecha del pago es obligatoria.',
            'foto.required'   => 'El comprobante (foto) es obligatorio.',
            'foto.image'      => 'El comprobante debe ser una imagen (jpg, png, webp).',
            'foto.max'        => 'La imagen no puede superar 2 MB.',
        ]);

        $path = $request->file('foto')->store('pagos', 'public');

        Pago::create([
            'id_inscripcion_pago' => $inscripcion->id,
            'aporte'              => $request->aporte,
            'fecha'               => $request->fecha,
            'foto'                => $path,
        ]);

        $mensualidadGenerada = $this->actualizarEstado($inscripcion);

        if ($mensualidadGenerada) {
            return redirect()->route('pagos.index')
                ->with('success', 'Matrícula pagada. Se generó la mensualidad del mes actual automáticamente.');
        }

        return redirect()->route('pagos.show', $inscripcion)
            ->with('success', 'Pago registrado correctamente.');
    }

    public function eliminarPago(InscripcionPago $inscripcion, Pago $pago)
    {
        Storage::disk('public')->delete($pago->foto);
        $pago->delete();

        $this->actualizarEstado($inscripcion);

        return redirect()->route('pagos.show', $inscripcion)
            ->with('success', 'Pago eliminado correctamente.');
    }

    public function eliminarInscripcion(InscripcionPago $inscripcion)
    {
        // Eliminar fotos y pagos registrados
        foreach ($inscripcion->pagos as $pago) {
            Storage::disk('public')->delete($pago->foto);
        }
        $inscripcion->pagos()->delete();

        // Si es una matrícula, eliminar las mensualidades sin pagos del mismo alumno/año
        $tipoMatricula = TipoPago::where('nombre', 'Matrícula')->first();
        if ($tipoMatricula && (int) $inscripcion->id_tipo_pago === (int) $tipoMatricula->id) {
            $tipoMensualidad = TipoPago::where('nombre', 'Mensualidad')->first();
            if ($tipoMensualidad) {
                $mensualidades = InscripcionPago::where('id_alumno', $inscripcion->id_alumno)
                    ->where('id_año', $inscripcion->id_año)
                    ->where('id_tipo_pago', $tipoMensualidad->id)
                    ->whereDoesntHave('pagos')
                    ->get();
                $mensualidades->each->delete();
            }
        }

        $inscripcion->delete();

        return redirect()->route('pagos.index')
            ->with('success', 'Inscripción eliminada correctamente.');
    }

    /**
     * Recalcula el estado de la inscripción.
     * Retorna true si se generó la mensualidad del mes actual.
     */
    private function actualizarEstado(InscripcionPago $inscripcion): bool
    {
        $totalPagado    = (float) $inscripcion->pagos()->sum('aporte');
        $estadoAnterior = $inscripcion->estado;

        if ($totalPagado <= 0) {
            $estado = 'pendiente';
        } elseif ($totalPagado >= (float) $inscripcion->costo_total) {
            $estado = 'pagado';
        } else {
            $estado = 'parcial';
        }

        $inscripcion->update(['estado' => $estado]);

        // Si acaba de pagarse una matrícula → generar mensualidad del mes actual
        if ($estado === 'pagado' && $estadoAnterior !== 'pagado') {
            $tipoMatricula = TipoPago::where('nombre', 'Matrícula')->first();
            if ($tipoMatricula && (int) $inscripcion->id_tipo_pago === (int) $tipoMatricula->id) {
                return $this->generarMensualidadMesActual($inscripcion);
            }
        }

        return false;
    }

    /**
     * Genera la mensualidad del mes actual para la matrícula recién pagada.
     */
    private function generarMensualidadMesActual(InscripcionPago $matricula): bool
    {
        $tipoMensualidad = TipoPago::where('nombre', 'Mensualidad')->first();
        if (!$tipoMensualidad) return false;

        $mesRecord = Mes::where('numero', now()->month)->first();
        if (!$mesRecord) return false; // Mes fuera del año escolar (ej: enero, febrero)

        $yaExiste = InscripcionPago::where('id_alumno', $matricula->id_alumno)
            ->where('id_año', $matricula->id_año)
            ->where('id_tipo_pago', $tipoMensualidad->id)
            ->where('id_mes', $mesRecord->id)
            ->exists();

        if ($yaExiste) return false;

        $matricula->loadMissing('alumno.nivelEducativo');
        $precio = (float) ($matricula->alumno->nivelEducativo?->precio ?? 0);

        InscripcionPago::create([
            'id_alumno'      => $matricula->id_alumno,
            'id_seccion'     => $matricula->id_seccion,
            'id_año'         => $matricula->id_año,
            'id_tipo_pago'   => $tipoMensualidad->id,
            'id_mes'         => $mesRecord->id,
            'costo_total'    => $precio,
            'fecha_registro' => now()->toDateString(),
            'estado'         => 'pendiente',
        ]);

        return true;
    }
}
