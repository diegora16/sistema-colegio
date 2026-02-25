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

        /* Filtros */
        .filters-bar {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 5px 8px;
            font-size: 7.5pt;
            color: #374151;
            margin-bottom: 10px;
        }
        .filters-bar span { color: #6b7280; }

        /* Resumen --*/
        .summary { display: table; width: 100%; margin-bottom: 12px; border-collapse: separate; border-spacing: 4px; }
        .summary-cell {
            display: table-cell;
            width: 33%;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 4px;
            padding: 6px 8px;
            text-align: center;
        }
        .summary-cell.red { background: #fef2f2; border-color: #fecaca; }
        .summary-label { font-size: 6.5pt; color: #6b7280; text-transform: uppercase; letter-spacing: 0.04em; }
        .summary-value { font-size: 11pt; font-weight: bold; color: #111827; margin-top: 2px; }
        .summary-cell.red .summary-value { color: #dc2626; }

        /* Tabla */
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background-color: #07863f;
            color: #ffffff;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 5px 7px;
            text-align: left;
        }
        thead th.text-right  { text-align: right; }
        thead th.text-center { text-align: center; }
        tbody tr:nth-child(even) { background-color: #f9fafb; }
        tbody tr:nth-child(odd)  { background-color: #ffffff; }
        tbody td {
            padding: 4px 7px;
            font-size: 8pt;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        td.num   { color: #9ca3af; font-family: monospace; text-align: center; }
        td.mono  { font-family: monospace; }
        td.bold  { font-weight: bold; color: #111827; }
        td.right { text-align: right; font-family: monospace; }
        td.center { text-align: center; }
        td.red   { color: #dc2626; font-weight: bold; }

        /* Fila totales */
        .totals-row td {
            font-weight: bold;
            background-color: #f0fdf4 !important;
            border-top: 2px solid #07863f;
            font-size: 8pt;
        }

        /* Badges estado */
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 9999px;
            font-size: 6.5pt;
            font-weight: bold;
        }
        .badge-pendiente { background: #fef3c7; color: #92400e; }
        .badge-parcial   { background: #dbeafe; color: #1e40af; }

        /* Pie */
        .footer {
            margin-top: 12px;
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
                <div class="report-title">Reporte de Morosidad</div>
                <div class="report-meta">
                    Año académico: {{ $anioActivo?->nombre ?? '—' }}<br>
                    Generado: {{ now()->format('d/m/Y H:i') }}&nbsp;&nbsp;|&nbsp;&nbsp;Total: {{ $inscripciones->count() }} registro(s)
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="filters-bar">
        <span>Filtros aplicados:</span>
        Nivel: <strong>{{ $nivelNombre ?? 'Todos' }}</strong> &nbsp;|&nbsp;
        Mes: <strong>{{ $mesNombre ?? 'Todos' }}</strong>
    </div>

    {{-- Resumen --}}
    <div class="summary">
        <div class="summary-cell">
            <div class="summary-label">Alumnos morosos</div>
            <div class="summary-value">{{ $inscripciones->count() }}</div>
        </div>
        <div class="summary-cell">
            <div class="summary-label">Deuda total</div>
            <div class="summary-value">S/ {{ number_format($totalDeuda, 2) }}</div>
        </div>
        <div class="summary-cell red">
            <div class="summary-label">Saldo pendiente</div>
            <div class="summary-value">S/ {{ number_format($totalSaldo, 2) }}</div>
        </div>
    </div>

    {{-- Tabla --}}
    <table>
        <thead>
            <tr>
                <th style="width:4%;" class="text-center">N°</th>
                <th style="width:20%;">Alumno</th>
                <th style="width:10%;">Tipo</th>
                <th style="width:12%;">Nivel</th>
                <th style="width:13%;">Grado / Secc.</th>
                <th style="width:9%;">Mes</th>
                <th style="width:8%;" class="text-right">Costo</th>
                <th style="width:8%;" class="text-right">Pagado</th>
                <th style="width:8%;" class="text-right">Saldo</th>
                <th style="width:8%;" class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($inscripciones as $i => $ins)
                @php
                    $pagado = (float) ($ins->pagos_sum_aporte ?? 0);
                    $saldo  = (float) $ins->costo_total - $pagado;
                @endphp
                <tr>
                    <td class="num">{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $ins->alumno->nombre_completo }}</strong><br>
                        <span style="font-size:6.5pt; color:#9ca3af; font-family:monospace;">{{ $ins->alumno->dni }}</span>
                    </td>
                    <td>{{ $ins->tipoPago?->nombre ?? '—' }}</td>
                    <td>{{ $ins->nivelEducativo?->nombre ?? '—' }}</td>
                    <td>
                        {{ $ins->grado?->nombre ?? '—' }}
                        @if ($ins->seccion)
                            / {{ $ins->seccion->nombre }}
                        @endif
                    </td>
                    <td>{{ $ins->mes?->nombre ?? '—' }}</td>
                    <td class="right">S/ {{ number_format($ins->costo_total, 2) }}</td>
                    <td class="right">S/ {{ number_format($pagado, 2) }}</td>
                    <td class="right red">S/ {{ number_format($saldo, 2) }}</td>
                    <td class="center">
                        @if ($ins->estado === 'pendiente')
                            <span class="badge badge-pendiente">Pendiente</span>
                        @else
                            <span class="badge badge-parcial">Parcial</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center; padding:12px; color:#9ca3af;">
                        No hay alumnos con cuotas pendientes.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($inscripciones->isNotEmpty())
            <tfoot>
                <tr class="totals-row">
                    <td colspan="6" style="padding:5px 7px; color:#065f2e;">TOTALES</td>
                    <td class="right" style="padding:5px 7px;">S/ {{ number_format($totalDeuda, 2) }}</td>
                    <td class="right" style="padding:5px 7px;">S/ {{ number_format($totalPagado, 2) }}</td>
                    <td class="right red" style="padding:5px 7px;">S/ {{ number_format($totalSaldo, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- Pie --}}
    <div class="footer">
        <div class="footer-inner">
            <div class="footer-left">I.E.P. Jesús y María — Sistema de Gestión Escolar</div>
            <div class="footer-right">{{ $inscripciones->count() }} registro(s) de morosidad</div>
        </div>
    </div>

</body>
</html>
