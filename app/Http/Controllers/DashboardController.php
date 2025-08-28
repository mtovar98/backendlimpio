<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function kpis()
    {
        $today = now()->toDateString();
        $start = now()->startOfMonth()->toDateString();
        $end   = now()->endOfMonth()->toDateString();

        $totalUsers = User::where('id_roles', 4)->count();

        $activeUsers = Payment::whereDate('payments_expires_at', '>=', $today)
            ->distinct('id_users')->count('id_users');

        $attendancesToday = Attendance::whereDate('created_at', $today)->count();

        $paymentsThisMonth = Payment::whereBetween(DB::raw('DATE(created_at)'), [$start, $end])->count();

        return response()->json([
            'total_users'        => $totalUsers,
            'active_users'       => $activeUsers,
            'attendances_today'  => $attendancesToday,
            'payments_this_month'=> $paymentsThisMonth,
        ]);
    }

    public function alerts(Request $request)
    {
        $days  = (int) $request->query('days', 20);
        $today = now()->toDateString();
        $soon  = now()->addDays($days)->toDateString();

        // Vencidos: clientes (rol 4) sin pago vigente hoy
        $expired = User::where('id_roles', 4)
            ->whereDoesntHave('payments', function($q) use ($today){
                $q->whereDate('payments_expires_at', '>=', $today);
            })
            ->orderBy('last_name')
            ->limit(50)
            ->get(['id_users','first_name','last_name','id_number']);

        // Por vencer: clientes con pago vigente que expira en los próximos X días
        $expiringSoon = User::where('id_roles', 4)
            ->whereHas('payments', function($q) use ($today, $soon){
                $q->whereDate('payments_expires_at', '>=', $today)
                ->whereDate('payments_expires_at', '<=', $soon);
            })
            ->orderBy('last_name')
            ->limit(50)
            ->get(['id_users','first_name','last_name','id_number']);

        return response()->json([
            'days'          => $days,
            'expired'       => $expired,       // cada user incluye los appends: plan_status/expires
            'expiring_soon' => $expiringSoon,
        ]);
    }
}
