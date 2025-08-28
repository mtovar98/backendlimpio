<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Pagos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1,h2,h3 { margin:0 0 6px; }
        .header { border-bottom:1px solid #000; margin-bottom:10px; padding-bottom:6px; }
        .meta { margin-top:4px; font-size:11px; color:#555; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th,td { border:1px solid #999; padding:6px; text-align:left; }
        .right { text-align:right; }
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

    <h2>Reporte de Pagos</h2>
    <p>
        <strong>Cliente:</strong> {{ $user->first_name }} {{ $user->last_name }} (ID: {{ $user->id_number }})<br>
        @if($from || $to)
            <strong>Periodo:</strong> {{ $from ?? '—' }} al {{ $to ?? '—' }}<br>
        @endif
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha de pago</th>
                <th>Plan</th>
                <th class="right">Monto</th>
                <th>Vence</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
        @php $todayDate = \Carbon\Carbon::parse($today); @endphp
        @forelse($rows as $i => $row)
            @php
                $payDate = \Carbon\Carbon::parse($row->created_at);
                $expDate = $row->payments_expires_at ? \Carbon\Carbon::parse($row->payments_expires_at) : null;
                $status  = $expDate && $todayDate->lte($expDate) ? 'vigente' : 'vencido';
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $payDate->format('Y-m-d H:i') }}</td>
                <td>{{ $row->plan_name }}</td>
                <td class="right">{{ number_format((float)$row->amount, 2, '.', '') }}</td>
                <td>{{ $expDate ? $expDate->format('Y-m-d') : '—' }}</td>
                <td>{{ $status }}</td>
            </tr>
        @empty
            <tr><td colspan="6">Sin pagos en el periodo.</td></tr>
        @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="right">Total</th>
                <th class="right">{{ $totalAmount }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
