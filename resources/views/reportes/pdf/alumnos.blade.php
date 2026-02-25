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
        .header-inner {
            display: table;
            width: 100%;
        }
        .header-left  { display: table-cell; vertical-align: bottom; }
        .header-right { display: table-cell; vertical-align: bottom; text-align: right; }
        .school-name {
            font-size: 13pt;
            font-weight: bold;
            color: #07863f;
            line-height: 1.2;
        }
        .school-sub {
            font-size: 7.5pt;
            color: #6b7280;
            margin-top: 1px;
        }
        .report-title {
            font-size: 10.5pt;
            font-weight: bold;
            color: #111827;
        }
        .report-meta {
            font-size: 7pt;
            color: #6b7280;
            margin-top: 2px;
            line-height: 1.5;
        }

        /* Filtros aplicados */
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

        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
        }
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
        thead th.text-center { text-align: center; }
        tbody tr:nth-child(even) { background-color: #f9fafb; }
        tbody tr:nth-child(odd)  { background-color: #ffffff; }
        tbody td {
            padding: 4px 7px;
            font-size: 8pt;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        tbody td.num   { color: #9ca3af; font-family: monospace; text-align: center; }
        tbody td.mono  { font-family: monospace; color: #4b5563; }
        tbody td.bold  { font-weight: bold; color: #111827; }

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
                <div class="report-title">Reporte de Alumnos Matriculados</div>
                <div class="report-meta">
                    Año académico: {{ $anioActivo?->nombre ?? '—' }}<br>
                    Generado: {{ now()->format('d/m/Y H:i') }}&nbsp;&nbsp;|&nbsp;&nbsp;Total: {{ $alumnos->count() }} alumno(s)
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros aplicados --}}
    <div class="filters-bar">
        <span>Filtros aplicados:</span>
        Nivel: <strong>{{ $nivelNombre ?? 'Todos' }}</strong> &nbsp;|&nbsp;
        Grado: <strong>{{ $gradoNombre ?? 'Todos' }}</strong> &nbsp;|&nbsp;
        Sección: <strong>{{ $seccionNombre ?? 'Todas' }}</strong>
    </div>

    {{-- Tabla --}}
    <table>
        <thead>
            <tr>
                <th style="width:4%;" class="text-center">N°</th>
                <th style="width:9%;">DNI</th>
                <th style="width:24%;">Apellidos y Nombres</th>
                <th style="width:13%;">Nivel</th>
                <th style="width:11%;">Grado</th>
                <th style="width:8%;">Secc.</th>
                <th style="width:22%;">Apoderado</th>
                <th style="width:9%;">Teléfono</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($alumnos as $i => $alumno)
                <tr>
                    <td class="num">{{ $i + 1 }}</td>
                    <td class="mono">{{ $alumno->dni }}</td>
                    <td class="bold">{{ $alumno->nombre_completo }}</td>
                    <td>{{ $alumno->nivelEducativo?->nombre ?? '—' }}</td>
                    <td>{{ $alumno->grado?->nombre ?? '—' }}</td>
                    <td>{{ $alumno->seccion?->nombre ?? '—' }}</td>
                    <td>{{ $alumno->apoderado?->nombre_completo ?? '—' }}</td>
                    <td class="mono">{{ $alumno->apoderado?->telefono ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding: 12px; color:#9ca3af;">
                        No hay alumnos con los filtros seleccionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pie --}}
    <div class="footer">
        <div class="footer-inner">
            <div class="footer-left">I.E.P. Jesús y María — Sistema de Gestión Escolar</div>
            <div class="footer-right">Total: {{ $alumnos->count() }} alumno(s)</div>
        </div>
    </div>

</body>
</html>
