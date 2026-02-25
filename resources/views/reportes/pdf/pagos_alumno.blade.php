<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #1f2937;
        }
        @page { margin: 15mm 12mm; }

        /* Cabecera */
        .header {
            border-bottom: 2.5px solid #07863f;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-inner { display: table; width: 100%; }
        .header-left  { display: table-cell; vertical-align: bottom; }
        .header-right { display: table-cell; vertical-align: bottom; text-align: right; }
        .school-name  { font-size: 13pt; font-weight: bold; color: #07863f; line-height: 1.2; }
        .school-sub   { font-size: 7.5pt; color: #6b7280; margin-top: 1px; }
        .report-title { font-size: 10.5pt; font-weight: bold; color: #111827; }
        .report-meta  { font-size: 7pt; color: #6b7280; margin-top: 2px; line-height: 1.5; }

        /* Info alumno */
        .alumno-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 12px;
        }
        .alumno-inner { display: table; width: 100%; }
        .alumno-left  { display: table-cell; width: 50%; vertical-align: top; padding-right: 10px; }
        .alumno-right { display: table-cell; width: 50%; vertical-align: top; }
        .alumno-name  { font-size: 11pt; font-weight: bold; color: #065f2e; }
        .alumno-dni   { font-size: 7.5pt; color: #6b7280; font-family: monospace; margin-top: 2px; }
        .alumno-row   { font-size: 7.5pt; color: #374151; margin-top: 3px; }
        .alumno-label { color: #6b7280; }

        /* Tabla */
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background-color: #07863f;
            color: #ffffff;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 5px 8px;
            text-align: left;
        }
        thead th.text-right  { text-align: right; }
        thead th.text-center { text-align: center; }
        tbody tr:nth-child(even) { background-color: #f9fafb; }
        tbody tr:nth-child(odd)  { background-color: #ffffff; }
        tbody td {
            padding: 5px 8px;
            font-size: 8pt;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        td.right  { text-align: right; font-family: monospace; }
        td.center { text-align: center; }
        td.green  { color: #059669; }
        td.red    { color: #dc2626; font-weight: bold; }
        td.gray   { color: #9ca3af; }

        /* Badges estado */
        .badge {
            display: inline-block;
            padding: 1px 7px;
            border-radius: 9999px;
            font-size: 7pt;
            font-weight: bold;
        }
        .badge-pendiente { background: #fef3c7; color: #92400e; }
        .badge-parcial   { background: #dbeafe; color: #1e40af; }
        .badge-pagado    { background: #d1fae5; color: #065f46; }

        /* Fila totales */
        .totals-row td {
            font-weight: bold;
            background-color: #f0fdf4 !important;
            border-top: 2px solid #07863f;
            padding: 6px 8px;
        }

        /* Pie */
        .footer {
            margin-top: 14px;
            padding-top: 6px;
            border-top: 1px solid #e5e7eb;
            font-size: 6.5pt;
            color: #9ca3af;
        }
        .footer-inner { display: table; width: 100%; }
        .footer-left  { display: table-cell; }
        .footer-right { display: table-cell; text-align: right; }
    </style>
</head>
<body>

    {{-- Cabecera --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-left">
                <div class="school-name">I.E.P. Jesús y María</div>
                <div class="school-sub">Sistema de Gestión Escolar</div>
            </div>
            <div class="header-right">
                <div class="report-title">Estado de Cuenta — Alumno</div>
                <div class="report-meta">
                    Año académico: {{ $anioActivo?->nombre ?? '—' }}<br>
                    Generado: {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Datos del alumno --}}
    <div class="alumno-box">
        <div class="alumno-inner">
            <div class="alumno-left">
                <div class="alumno-name">{{ $alumno->nombre_completo }}</div>
                <div class="alumno-dni">DNI: {{ $alumno->dni }}</div>
                <div class="alumno-row">
                    <span class="alumno-label">Apoderado:</span> {{ $alumno->apoderado?->nombre_completo ?? '—' }}
                    @if ($alumno->apoderado?->telefono)
                        &nbsp;|&nbsp; Tel: {{ $alumno->apoderado->telefono }}
                    @endif
                </div>
            </div>
            <div class="alumno-right">
                <div class="alumno-row"><span class="alumno-label">Nivel:</span> {{ $alumno->nivelEducativo?->nombre ?? '—' }}</div>
                <div class="alumno-row"><span class="alumno-label">Grado:</span> {{ $alumno->grado?->nombre ?? '—' }}</div>
                <div class="alumno-row"><span class="alumno-label">Sección:</span> {{ $alumno->seccion?->nombre ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- Tabla historial --}}
    <table>
        <thead>
            <tr>
                <th style="width:22%;">Tipo de Pago</th>
                <th style="width:15%;">Mes</th>
                <th style="width:16%;" class="text-right">Costo Total</th>
                <th style="width:16%;" class="text-right">Pagado</th>
                <th style="width:16%;" class="text-right">Saldo</th>
                <th style="width:15%;" class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($inscripciones as $ins)
                @php
                    $pagado = (float) $ins->pagos->sum('aporte');
                    $saldo  = (float) $ins->costo_total - $pagado;
                @endphp
                <tr>
                    <td><strong>{{ $ins->tipoPago?->nombre ?? '—' }}</strong></td>
                    <td>{{ $ins->mes?->nombre ?? '—' }}</td>
                    <td class="right">S/ {{ number_format($ins->costo_total, 2) }}</td>
                    <td class="right green">S/ {{ number_format($pagado, 2) }}</td>
                    <td class="right {{ $saldo > 0 ? 'red' : 'gray' }}">S/ {{ number_format($saldo, 2) }}</td>
                    <td class="center">
                        @if ($ins->estado === 'pendiente')
                            <span class="badge badge-pendiente">Pendiente</span>
                        @elseif ($ins->estado === 'parcial')
                            <span class="badge badge-parcial">Parcial</span>
                        @else
                            <span class="badge badge-pagado">Pagado</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:12px; color:#9ca3af;">
                        No hay registros de pago para este alumno.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($inscripciones->isNotEmpty())
            <tfoot>
                <tr class="totals-row">
                    <td colspan="2" style="color:#065f2e;">TOTALES</td>
                    <td class="right">S/ {{ number_format($totalCosto, 2) }}</td>
                    <td class="right green">S/ {{ number_format($totalPagado, 2) }}</td>
                    <td class="right {{ $totalSaldo > 0 ? 'red' : 'gray' }}">S/ {{ number_format($totalSaldo, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- Pie --}}
    <div class="footer">
        <div class="footer-inner">
            <div class="footer-left">I.E.P. Jesús y María — Sistema de Gestión Escolar</div>
            <div class="footer-right">Estado de cuenta generado el {{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

</body>
</html>
