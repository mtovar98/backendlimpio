<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        //definimos reglas
        $rules = [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'id_number' => 'required|integer|unique:users,id_number',
            'phone' => 'required|string|max:15',
            'email' => 'nullable|email|max:50',
            'birth_date' => 'required|date',
            'id_roles' => 'required|exists:roles,id_roles',
            'password' => ['string', Password::defaults(), 'required_if:id_roles,1,2,3'],
        ];

        //ejecutar validacion
        $validator = Validator::make($request->all(), $rules);

        //si falla validacion devolver errores 422
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validacion',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $user = User::create([
            'first_name'   => $data['first_name'],
            'last_name'    => $data['last_name'],
            'id_number'    => $data['id_number'],
            'phone'        => $data['phone'],
            'email'        => $data['email'] ?? null,
            'birth_date'   => $data['birth_date'],
            'id_roles'     => $data['id_roles'],
            'password'     => Hash::make($data['password']),
        ]);

        //generar token
        $token = $user->createToken('auth_token')->plainTextToken;

        // respuesta exitos
        return response()->json([
            'message' => 'usuario registrado correctamente',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        // validar datos de entrada
        $validator = Validator::make($request->all(), [
            'id_number' => 'required|integer',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validacion',
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $validator->validated();

        // buscar usuario por id_number
        $user = User::where('id_number', $credentials['id_number'])->first();

        if (! in_array($user->id_roles, [1,2,3])) {
            return response()->json([
                'message' => 'Este rol no tiene permitido iniciar sesión'
            ], 403);
        }

        // 3. Verificar existencia y contraseña
        if (! $user || ! $user->password || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        // 4. Generar token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Devolver respuesta
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ]);
    }

    public function logout(Request $request)
    {
        // Elimina el token actual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}
