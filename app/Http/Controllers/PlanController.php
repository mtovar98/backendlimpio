<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plan =  Plan::orderBy('plans_name')->paginate(20);

        return response()->json([
            'data' => $plan
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plans_name' => 'required|string|max:100|unique:plans,plans_name',
            'plans_price' => 'required|numeric|min:0.01',
            'plans_duration_days' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validacion',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validate();

        $plan = Plan::create($data);

        return response()->json([
            'message' => 'plan creado exitosamente',
            'data' => $plan,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id_plans)
    {
        $plan = Plan::find($id_plans);
        if (!$plan) {
            return response()->json([
                'message' => 'plan no encontrado'
            ], 404);
        }

        return response()->json([
            'data' => $plan,
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id_plans)
    {
        // buscar plan
        $plan = Plan::findOrFail($id_plans);

        $data = Validator::make($request->all(), [
            'plans_name' => ['required', 'string', 'max:100', Rule::unique('plans', 'plans_name')->ignore($plan->id_plans, 'id_plans')],
            'plans_price' => 'required|numeric|min:0,01',
            'plans_duration_days' => 'required|integer|min:1',
        ])->validate();

        $plan->update($data);
        
        return response()->json([
            'data' => $plan
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_plans)
    {
        $plan = Plan::findOrFail($id_plans);

        if ($plan->payments()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar: el plan tiene pagos asociados.'
            ], 409);
        }

        $plan->delete(); // eliminación permanente (no hay soft deletes en plans)
        return response()->json(null, 204);
    }
}
