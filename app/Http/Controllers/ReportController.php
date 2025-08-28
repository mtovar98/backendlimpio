<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;


use App\Models\Attendance;

class ReportController extends Controller
{
    public function revenue(Request $request)
    {
        $year = (int)($request->query('year', now()->year));

        $rows = Payment::query()
            ->join('plans', 'payments.id_plans', '=', 'plans.id_plans')
            ->whereYear('payments.created_at', $year)
            ->selectRaw("
                DATE_FORMAT(payments.created_at, '%Y-%m') as ym,
                plans.plans_name as plan,
                SUM(COALESCE(payments.payments_amount, plans.plans_price)) as total,
                COUNT(*) as count
            ")
            ->groupBy('ym', 'plan')
            ->orderBy('ym')
            ->get();

        $monthlyTotals = Payment::query()
            ->join('plans', 'payments.id_plans', '=', 'plans.id_plans')
            ->whereYear('payments.created_at', $year)
            ->selectRaw("
                DATE_FORMAT(payments.created_at, '%Y-%m') as ym,
                SUM(COALESCE(payments.payments_amount, plans.plans_price)) as total
            ")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $yearTotal = number_format((float) $monthlyTotals->sum('total'), 2, '.', '');

        return response()->json([
            'year' => $year,
            'data' => $rows,
            'monthly_totals' => $monthlyTotals,
            'year_total' => $yearTotal
        ]);
    }

    public function attendancesPdf(Request $request)
    {
        $validator = validator::make($request->all(), [
            'id_number' => 'required|integer|exists:users,id_number',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error de validacion',
                'sucees' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $data = $validator->validated();

        $user = User::where('id_number', $data['id_number'])->firstOrFail();
        
        $rows = Attendance::where('id_users', $user->id_users)
            ->whereBetween(DB::raw('DATE(created_at)'), [$data['from'], $data['to']])
            ->orderBy('created_at')
            ->get(['id_attendances','created_at']);

        // Encabezado institucional (Settings si existe; sino valores por defecto)
        $settings = DB::table('settings')->orderBy('id')->first();
        $gymName  = $settings->gym_name  ?? 'Gimnasio Atenas';
        $gymEmail = $settings->gym_email ?? 'atenasgymbog@gmail.com';
        $version  = $settings->version   ?? '1.0.0';
        $devName  = $settings->developer_name ?? 'Miguel Ángel Tovar Tabares';

        $pdf = Pdf::loadView('pdf.attendances', [
            'gymName'  => $gymName,
            'gymEmail' => $gymEmail,
            'version'  => $version,
            'devName'  => $devName,
            'user'     => $user,
            'from'     => $data['from'],
            'to'       => $data['to'],
            'rows'     => $rows,
            'total'    => $rows->count(),
            'issuedAt' => now()->format('Y-m-d H:i'),
        ])->setPaper('letter', 'portrait');

        $filename = "asistencias-{$user->id_number}-{$data['from']}-{$data['to']}.pdf";
        return $pdf->stream($filename);
    }

    public function paymentsPdf(Request $request)
    {
        $validator = validator::make($request->all(), [
            'id_number' => 'required|integer|exists:users,id_number',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error de validacion',
                'sucees' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $data = $validator->validated();

        $user = User::where('id_number', $data['id_number'])->firstOrFail();

        $q = Payment::query()
            ->join('plans', 'payments.id_plans', '=', 'plans.id_plans')
            ->where('payments.id_users', $user->id_users)
            ->selectRaw("
                payments.id_payments,
                payments.created_at,
                payments.payments_expires_at,
                COALESCE(payments.payments_amount, plans.plans_price) AS amount,
                plans.plans_name AS plan_name
            ")
            ->orderBy('payments.created_at');

        if (!empty($data['from'])) {
            $q->whereDate('payments.created_at', '>=', $data['from']);
        }
        if (!empty($data['to'])) {
            $q->whereDate('payments.created_at', '<=', $data['to']);
        }

        $rows = $q->get();
        $totalAmount = (float) $rows->sum(fn($r) => (float) $r->amount);

        // Encabezado (Settings)
        $s = DB::table('settings')->orderBy('id')->first();
        $pdf = Pdf::loadView('pdf.payments', [
            'gymName'   => $s->gym_name  ?? 'Gimnasio Atenas',
            'gymEmail'  => $s->gym_email ?? null,
            'version'   => $s->version   ?? '1.0.0',
            'devName'   => $s->developer_name ?? 'Miguel Ángel Tovar Tabares',
            'user'      => $user,
            'from'      => $data['from'] ?? null,
            'to'        => $data['to'] ?? null,
            'rows'      => $rows,
            'totalAmount' => number_format($totalAmount, 2, '.', ''),
            'issuedAt'  => now()->format('Y-m-d H:i'),
            'today'     => now()->toDateString(),
        ])->setPaper('letter', 'portrait');

        $fname = 'pagos-' . $user->id_number
            . '-' . ($data['from'] ?? 'ini')
            . '-' . ($data['to'] ?? 'fin') . '.pdf';

        return $pdf->stream($fname);
    }

}
