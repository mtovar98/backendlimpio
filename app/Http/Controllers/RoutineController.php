<?php

namespace App\Http\Controllers;

use App\Models\Routine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class RoutineController extends Controller
{
    /**
     * Listar rutinas con filtros opcionales:
     * - name: busca por nombre (LIKE)
     * - frequency: iguala la frecuencia exacta (ej: "3 días/semana")
     * - duration_min / duration_max: rango en minutos
     */
    public function index(Request $request)
    {
        $query = Routine::query();

        if ($request->filled('name')) {
            $query->where('routines_name', 'like', '%' . $request->name . '%');
        }
        if($request->filled('frequency')) {
            $query->where('routines_frequency', 'like', '%'.  $request->frequency . '%');
        }
        
        if ($request->filled('duration_min')) {
            $query->where('routines_duration', '>=', (int) $request->duration_min);
        }

        if ($request->filled('duration_max')) {
            $query->where('routines_duration', '<=', (int) $request->duration_max);
        }

        $routines = $query->get();

        return response()->json([
            'data' => $routines
        ], 200);
    }

    /**
     * crear una nueva rutina
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'routines_name' => 'required|string|max:50|unique:routines,routines_name',
            'routines_description' => 'required|string|max:255',
            'routines_duration' => 'required|integer|min:1',
            'routines_frequency' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error de validacion',
                'errors' => $validator->errors(),
            ], 422);
        }

        $routine = Routine::create($validator->validated());

        return response()->json([
            'message' => 'Rutina creada exitosamente',
            'data' => $routine,
        ], 201);
        
    }

    /**
     * Mostrar el detalle de una rutina por su ID.
     */
    public function show(string $id)
    {
        // buscar rutina 
        $routine = Routine::find($id);
        if (! $routine) {
            return response()->json([
                'message' => ' rutina no encontrada'
            ], 404);
        }

        return response()->json([
            'data' => $routine
        ], 200);

    }

    /**
     * Actualizar una rutina existente.
     */
    public function update(Request $request, string $id)
    {
        // buscar la rutina
        $routine = Routine::find($id);

        if (! $routine) {
            return response()->json([
                'message' => 'rutina no encontrada',
            ], 404);
        }

        // validar solo lo que llegue 
        $validator = Validator::make($request->all(), [
            'routines_name' => ['sometimes', 'required', 'string', 'max:50',
                                Rule::unique('routines', 'routines_name')->ignore($routine->id_routines, 'id_routines'),],
            'routines_description' => 'sometimes|required|string|max:255',
            'routines_duration' => 'sometimes|required|integer|min:1',
            'routines_frequency' => 'sometimes|required|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error de validacion',
                'errors' => $validator->errors()
            ], 422);
        }

        //actualizar
        $routine->update($validator->validated());

        //responder
        return response()->json([
            'message' => 'Rutina actualizada correctamente',
            'data' => $routine,
        ], 200);

    }

    /**
     * Desactivar (soft delete) una rutina por su ID.
     */
    public function destroy(string $id)
    {
        // buscar rutinas (no borradas)
        $routine = Routine::find($id) ;

        if (! $routine) {
            return response()->json([
                'message' => 'rutina no encontrada',
            ], 404);
        }

        // soft delete (marca deleted_at)
        $routine->delete();

        // respuesta 
        return response()->json([
            'message' => ' Rutina desactivada correctamente'
        ], 200);
    }
}
