<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AttendanceController extends Controller
{
    /**
     * Mostrar todas las asistencias, con opción de filtrar por usuario o fecha.
     *
     * Query params opcionales:
     * - id_users   : filtrar por ID interno de usuario
     * - date       : filtrar por fecha exacta (YYYY-MM-DD)
     * - from, to   : filtrar por rango de fechas
     */

    public function index(Request $request)
    {
        $query = Attendance::with('user');

        //filtro por usuario
        if ($request->filled('id_users')) {
            $query->where('id_users', $request->id_users);
        }

        // filtro por fecha exacta (created_at)
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        //filtro por rango de fechas
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from,
                $request->to
            ]);
        }

        $attendances = $query->get();

        return response()->json([
            'data' => $attendances
        ], 200);
    }

    /**
     * Registrar la asistencia de un usuario usando su número de documento.
     */
    public function store(Request $request)
    {
        // 1. Validar entrada: id_number existe
        $validator = Validator::make($request->all(), [
            'id_number' => 'required|integer|exists:users,id_number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $idNumber = $validator->validated()['id_number'];

        // 2. Buscar usuario y verificar activo
        $user = User::where('id_number', $idNumber)->first();
        
        if ((int)$user->id_roles !== 4) {
            return response()->json([
                'message' => 'Solo se registran asistencias a clientes (rol 4).'
            ], 422);
        }
        if (! $user->users_active) {
            return response()->json([
                'message' => 'Usuario inactivo. No se puede registrar asistencia.'
            ], 403);
        }
        if (!$user->plan_is_active) { // ← usa el accessor que agregamos
            return response()->json([
                'message' => 'El plan está vencido.',
                'expires_at' => $user->plan_expires_at,
            ], 422);
        }

        // 3. Obtener último pago y validar vigencia
        $lastPayment = $user->payments()
                            ->orderByDesc('payments_expires_at')
                            ->first();

        $today = Carbon::today()->toDateString();

        if (! $lastPayment || $lastPayment->payments_expires_at < $today) {
            return response()->json([
                'message'     => 'Plan vencido. No se puede registrar asistencia.',
                'plan_status' => 'vencido',
                'expires_at'  => $lastPayment->payments_expires_at ?? null,
            ], 403);
        }

        // 5. Crear la asistencia
        $attendance = Attendance::create([
            'id_users' => $user->id_users,
        ]);

        // 6. Responder con detalles y estado del plan
        return response()->json([
            'message'       => 'Asistencia registrada correctamente',
            'data'          => $attendance,
            'plan_status'   => $user->plan_status,
            'expires_at'    => $user->plan_expires_at,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
