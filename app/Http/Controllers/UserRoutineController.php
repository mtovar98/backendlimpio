<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Routine;
use App\Models\UserRoutine;
use PhpParser\Node\Stmt\ElseIf_;

class UserRoutineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

     /**
    * Asignar una rutina a un usuario usando su número de documento (id_number).
    * Reglas:
    * - Usuario debe existir y estar activo.
    * - Rutina debe existir y NO estar desactivada (soft-deleted).
    * - Solo una asignación por día (evitar "simultáneas" en la práctica).
    * Respuesta incluye la rutina actual y fecha de asignación.
    */
    public function store(Request $request)
    {
        // validacion de entrada
        $validator = Validator::make($request->all(), [
            'id_number' => 'required|integer|exists:users,id_number',
            'id_routines' => 'required|integer|exists:routines,id_routines'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error de validacion',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // buscar usuario por id_number y validar que este activo 
        $user = User::where('id_number', $data['id_number'])->first();
        if (! $user || ! $user->users_active) {
            return response()->json([
                'message' => 'Usuario inactivo, No se puede asignar rutina'
            ], 403);
        }

        // Verificar que la rutina exista y no este en soft-delete
            //    (Routine::find() no devuelve soft-deleted por el SoftDeletes del modelo)
        $routine = Routine::find($data['id_routines']);
        if (! $routine) {
            return response()->json([
                'message' => 'La rutina no existe o esta desactivada',
            ], 422);
        }

        // evitar asignacion duplicada en el mismo dia 
        $today = Carbon::today()->toDateString();
        $existsToday = UserRoutine::where('id_users', $user->id_users)
                    ->whereDate('created_at', $today)
                    ->exists();
        if ($existsToday) {
            // la rutina actual es la mas reciente
            $current = UserRoutine::with('routine')
                    ->where('id_users', $user->id_users)
                    ->latest('created_at')
                    ->first();
            return response()->json([
                'message' => 'Ya existe una asignacion de rutina para este usuario hoy',
                'current_routine' => $current?->routine,
                'assigned_at' =>optional($current)->created_at?->toDateTimeString(),
            ], 409);
        }

        // crear la asignacion
        $assignment = UserRoutine::create([
            'id_users' => $user->id_users,
            'id_routines' => $routine->id_routines,
        ]);

        // determinar la rutina actual la mas reciente
        $current = UserRoutine::with('routine')
                ->where('id_users', $user->id_users)
                ->latest('created_at')
                ->first();

        // responder
        return response()->json([
            'message' => 'Rutina asignada correctamente',
            'data' => $assignment,
            'current_routine' => $current?->routine,
            'assigned_at' => $assignment->created_at?->toDateTimeString(),
        ], 201);
    }

    /**
    * Historial de rutinas por número de documento.
    * Query params:
    * - id_number (required)
    * - from (YYYY-MM-DD) optional
    * - to   (YYYY-MM-DD) optional
    * - current=1 (optional) -> devuelve solo la asignación más reciente
    */
    public function history(Request $request) 
    {
        // validacion basica de entrada
        $validator = Validator::make($request->all(), [
            'id_number' => 'required|integer|exists:users,id_number',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'current' => 'nullable|in:0,1,true,false',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validacion',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $user = User::where('id_number', $data['id_number'])->first();

        // construir query por usuario
        $query = UserRoutine::with('routine')
                ->where('id_users', $user->id_users)
                ->orderByDesc('created_at');

        // Rango de fechas si viene
        if (!empty($data['from']) && !empty($data['to'])) {
            $query->whereBetween('created_at', [$data['from'], $data['to']]);
        } elseif (!empty($data['from'])) {
            $query->whereDate('created_at', '>=', $data['from']);
        } elseif (!empty($data['to'])) {
            $query->whereDate('created_at', '<=', $data['to']);
        }

        // solo la actual, la mas reciente 
        $currentOnly = isset($data['current']) && in_array((string)$data['current'], ['1', 'true'], true);
        if ($currentOnly) {
            $current = $query->first();
            if (! $current) {
                return response()->json([
                    'data' => null,
                    'message' => 'Sin asignaciones registradas para este usuario en el periodo indicado'
                ], 200);
            }

            return response()->json([
                'data'       => $current,
                'is_current' => true,
            ], 200);
        }

        // listado completo + marcar la actual
        $assignments = $query->get();
        $latestId    = optional($assignments->first())->id_user_routines;

        // Transformar para añadir is_current por registro
        $dataOut = $assignments->map(function ($row) use ($latestId) {
            $arr = $row->toArray();
            $arr['is_current'] = ($row->id_user_routines === $latestId);
            return $arr;
        });

        return response()->json([
            'data' => $dataOut
        ], 200);
    }


   /**
     * show the specified resource in storage.
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
