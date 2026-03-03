<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingresos {{ $anio->nombre ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1f2937;
            background: #fff;
        }

        /* ── Cabecera ── */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 2px solid #07863f;
        }
        .header-left, .header-right { display: table-cell; vertical-align: top; }
        .header-right { text-align: right; }
        .school-name { font-size: 13px; font-weight: bold; color: #07863f; }
        .school-sub  { font-size: 9px; color: #6b7280; margin-top: 2px; }
        .report-title { font-size: 13px; font-weight: bold; color: #1f2937; }
        .report-meta  { font-size: 9px; color: #6b7280; margin-top: 3px; }

        /* ── Resumen ── */
        .resumen {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 10px 16px;
            margin-bottom: 14px;
            display: table;
            width: 100%;
        }
        .resumen-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
        .resumen-valor { font-size: 18px; font-weight: bold; color: #07863f; margin-top: 3px; }

        /* ── Tabla ── */
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        thead tr th {
            background: #07863f;
            color: #fff;
            padding: 7px 10px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        thead tr th.right { text-align: right; }
        tbody tr td { padding: 6px 10px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        tbody tr:nth-child(even) td { background: #f9fafb; }
        tbody tr.sin-ingreso td { color: #d1d5db; }
        td.right { text-align: right; font-family: monospace; }
        td.mes-name { font-weight: 600; color: #374151; }
        tbody tr.sin-ingreso td.mes-name { font-weight: normal; }

        /* Barra visual */
        .barra-wrap { width: 100%; background: #e5e7eb; border-radius: 3px; height: 7px; overflow: hidden; }
        .barra-fill  { height: 100%; background: #07863f; border-radius: 3px; }

        /* Fila totales */
        tfoot tr td {
            background: #f0fdf4;
            border-top: 2px solid #07863f;
            padding: 8px 10px;
            font-weight: bold;
            font-size: 11px;
        }
        tfoot tr td.right { color: #07863f; font-family: monospace; font-size: 12px; }

        /* Pie */
        .footer { margin-top: 14px; text-align: right; font-size: 8px; color: #9ca3af; }
    </style>
</head>
<body>

    {{-- Cabecera --}}
    <div class="header">
        <div class="header-left">
            <div class="school-name">I.E.P. Jesús y María</div>
            <div class="school-sub">Sistema de Gestión Escolar</div>
        </div>
        <div class="header-right">
            <div class="report-title">Reporte de Ingresos por Año</div>
            <div class="report-meta">
                Año: {{ $anio->nombre ?? '—' }}&nbsp;&nbsp;|&nbsp;&nbsp;
                Generado: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    {{-- Resumen total --}}
    <div class="resumen">
        <div class="resumen-label">Total cobrado en {{ $anio->nombre ?? '—' }}</div>
        <div class="resumen-valor">S/ {{ number_format($totalGeneral, 2) }}</div>
    </div>

    {{-- Tabla de ingresos --}}
    @php $maxMes = collect($ingresosPorMes)->max('total') ?: 1; @endphp

    <table>
        <thead>
            <tr>
                <th style="width:30%">Mes</th>
                <th class="right" style="width:25%">Total cobrado</th>
                <th style="width:30%">Distribución</th>
                <th class="right" style="width:15%">% del año</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ingresosPorMes as $fila)
                <tr class="{{ $fila['total'] == 0 ? 'sin-ingreso' : '' }}">
                    <td class="mes-name">{{ $fila['mes'] }}</td>
                    <td class="right">S/ {{ number_format($fila['total'], 2) }}</td>
                    <td style="padding-top:10px; padding-bottom:10px;">
                        @if ($fila['total'] > 0)
                            <div class="barra-wrap">
                                <div class="barra-fill" style="width: {{ number_format(($fila['total'] / $maxMes) * 100, 1) }}%;"></div>
                            </div>
                        @endif
                    </td>
                    <td class="right">
                        @if ($fila['total'] > 0 && $totalGeneral > 0)
                            {{ number_format(($fila['total'] / $totalGeneral) * 100, 1) }}%
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>TOTAL ANUAL</td>
                <td class="right">S/ {{ number_format($totalGeneral, 2) }}</td>
                <td></td>
                <td class="right">100%</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        I.E.P. Jesús y María — Sistema de Gestión Escolar — {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
