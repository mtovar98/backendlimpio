<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Asistencias</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2, h3 { margin: 0 0 6px; }
        .header { border-bottom: 1px solid #000; margin-bottom: 10px; padding-bottom: 6px; }
        table { width:100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border:1px solid #999; padding:6px; text-align:left; }
        .meta { margin-top:4px; font-size:11px; color:#555; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $gymName }}</h1>
        <div class="meta">
            @if($gymEmail) <div>Email: {{ $gymEmail }}</div> @endif
            <div>Versión: {{ $version }} — Desarrollador: {{ $devName }}</div>
            <div>Emitido: {{ $issuedAt }}</div>
        </div>
    </div>

    <h2>Reporte de Asistencias</h2>
    <p><strong>Cliente:</strong> {{ $user->first_name }} {{ $user->last_name }} (ID: {{ $user->id_number }})<br>
       <strong>Periodo:</strong> {{ $from }} al {{ $to }}<br>
       <strong>Total asistencias:</strong> {{ $total }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha y hora</th>
            </tr>
        </thead>
        <tbody>
        @forelse($rows as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i') }}</td>
            </tr>
        @empty
            <tr><td colspan="2">Sin asistencias en el periodo.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
