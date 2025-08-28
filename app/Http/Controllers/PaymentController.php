<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\Rule;


class PaymentController extends Controller
{
    /**
     * Listar todos los pagos, con su usuario y plan asociados.
     */
    public function index(Request $request)
    {
        // recuperar todos los pagos con usuario y plan
        $q = Payment::with(['user.role', 'plan']);

        if ($status = $request->query('status')) {
            $today = now()->toDateString();
            if ($status === 'vigente') {
                $q->whereDate('payments_expires_at', '>=', $today);
            } elseif ($status === 'vencido') {
                $q->whereDate('payments_expires_at', '<=', $today);
            }

            // si viene otro valor lo ignoramos para mantenerlo simple
        }

        // filtro por id_number del cliente
        if ($idNumber = $request->query('id_number')) {
            $q->whereHas('user', fn($u) =>  $u->where('id_number', $idNumber));
        }

        //Rango de fechas por fecha de pago (created_at)
        if ($from = $request->query('from')) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $q->whereDate('created_at', '<=', $to);
        }
        if ($expiresFrom = $request->query('expires_from')) {
            $q->whereDate('payments_expires_at', '>=', $expiresFrom);
        }
        if ($expiresTo = $request->query('expires_to')) {
            $q->whereDate('payments_expires_at', '<=', $expiresTo);
        }


        return $q->orderByDesc('created_at')->paginate(20); // paginacion
    
    }

    /**
     * crear un nuevo pago
     */
    public function store(Request $request)
    {
        // 1. Validar entrada
        $validator = Validator::make($request->all(), [
            'id_users' => 'required|exists:users,id_users',
            'id_plans' => 'required|exists:plans,id_plans',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // 2. Evitar duplicar pago en el mismo día
        $today = Carbon::today();
        $exists = Payment::where('id_users', $data['id_users'])
                        ->whereDate('created_at', $today)
                        ->exists();
        if ($exists) {
            return response()->json([
                'message' => 'Ya existe un pago registrado para este usuario hoy'
            ], 409);
        }

        // 3. Calcular fecha de vencimiento según duración del plan
        $plan = Plan::find($data['id_plans']);
        $expiresAt = Carbon::now()->addDays($plan->plans_duration_days)->toDateString();

        // 4. Crear el pago
        $payment = Payment::create([
            'id_users'            => $data['id_users'],
            'id_plans'            => $data['id_plans'],
            'payments_amount'     => $plan->plans_price, // 👈 snapshot
            'payments_expires_at' => $expiresAt,
        ]);

        // 5. Devolver respuesta
        return response()->json([
            'message' => 'Pago registrado correctamente',
            'data'    => $payment,
        ], 201);
    }

    /**
     * mostrar un pago por su id
     */
    public function show(string $id)
    {
        // buscar el pago con su usuario y plan
        $payment = Payment::with(['user.role', 'plan'])->find($id);

        // si no existe devolver 404
        if (! $payment) {
            return response()->json([
                'message' => 'pago no encontrado'
            ], 404);
        }

        return response()->json([
            'data' => $payment
        ], 200);
    }

    /**
     * actualizar un pago existente
     */
    public function update(Request $request, string $id)
    {
        // buscar pago
        $payment = Payment::find($id);
        if (! $payment) {
            return response()->json([
                'message' => 'Pago no encontrado'
            ], 404);
        }

        // validar solo id_plans si viene
        $validator = Validator::make($request->all(), [
            'id_plans' => [
                'required',
                Rule::exists('plans', 'id_plans'),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validacion',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // recalcular expiracion segun el nuevo plan 
        $plan = Plan::find($data['id_plans']);
        $payment->payments_expires_at = Carbon::now()
            ->addDays($plan->plans_duration_days)
            ->toDateString();
        
        // actualizar el plan
        $payment->id_plans = $data['id_plans'];
        $payment->save();

        // responder con el pago actualizado
        return response()->json([
            'message' => 'pago actualizado correctamente',
            'data' => $payment
        ], 200);
    }

    /**
     * eliminar un pago por su id 
     */
    public function destroy(string $id)
    {
        // buscar el pago 
        $payment = Payment::find($id);

        if (! $payment) {
            return response()->json([
                'message' => 'Pago no encontrado'
            ], 404);
        }

        // eliminar pago 
        $payment->delete();

        // devolver pago eliminado 
        return response()->json([
            'message' => 'Pago eliminado correctamente',
        ], 200);
    }

    ////// buscar pago por user

    public function byUser(Request $request, string $idNumber)
    {
        $user = User::where('id_number', $idNumber)->firstOrFail();

        $q = Payment::with(['plan', 'user'])
            ->where('id_users', $user->id_users);

         // opcional: reutiliza el filtro de estado
        if ($status = $request->query('status')) {
            $today = now()->toDateString();
            if ($status === 'vigente') {
                $q->whereDate('payments_expires_at', '>=', $today);
            } elseif ($status === 'vencido') {
                $q->whereDate('payments_expires_at', '<', $today);
            }
        }

        return $q->orderByDesc('created_at')->paginate(20);

    }
}
