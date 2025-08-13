<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

use function PHPSTORM_META\map;

class UserController extends Controller
{
    /**
     * Mostrar todos los usuarios con su rol 
     */
    public function index()
    {
        // Recupera todos los usuarios junto con su rol 
        $users = User::with('role')->get();
        return response()->json([
            'data' => $users
        ], 200);
    }

    /**
     * Almacenar un nuevo usuario
     */
    public function store(Request $request)
    {
        $validator = validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'id_number' => 'required|integer|unique:users,id_number',
            'phone' => 'required|string|max:15',
            'email' => 'nullable|email|max:50',
            'birth_date' => 'required|date',
            'id_roles' => 'required|exists:roles,id_roles',
            // contraseña solo si el rol es 1,2,3
            'password' => ['string','required_if:id_roles,1,2,3'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validacion',
                'errors' => $validator->errors(),
            ],422);
        }

        // crear un usuario

        $data = $validator->validated();
        $user = User::create([
            'first_name'   => $data['first_name'],
            'last_name'    => $data['last_name'],
            'id_number'    => $data['id_number'],
            'phone'        => $data['phone'],
            'email'        => $data['email'] ?? null,
            'birth_date'   => $data['birth_date'],
            'id_roles'     => $data['id_roles'],
            'users_active' => $request->input('users_active', true),
            'password'     => isset($data['password']) 
                              ? Hash::make($data['password']) 
                              : null,
        ]);

        //respuesta
        return response()->json([
            'success' => true,
            'message' => 'usuario creado exitosamente',
            'data' => $user,
        ], 201);
    }

    /**
     * Mostrar un usuario en particular 
     */
    public function show(string $id_number)
    {
        $user = User::with('role')
            ->where('id_number', $id_number)
            ->first();
        
        if (! $user) {
            return response()->json([
                'message' => 'Usuario no encontrado',
            ], 404);
        } 

        return response()->json([
            'data' => $user
        ], 200);
    }

    /**
     * Actualizar un usuario por su numero de identificacion 
     */
    public function update(Request $request, string $id_number)
    {
        // buscar un usuario existente 
        $user = User::where('id_number', $id_number)->first();

        if (! $user) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // validar datos (solo los que envie el cliente)
        $validator = Validator::make($request->all(), [
            'first_name'   => 'sometimes|required|string|max:50',
            'last_name'    => 'sometimes|required|string|max:50',
            'id_number'    => [
                'sometimes',
                'integer',
                Rule::unique('users','id_number')->ignore($user->id_users, 'id_users'),
            ],
            'phone'        => 'sometimes|required|string|max:15',
            'email'        => 'nullable|email|max:50',
            'birth_date'   => 'sometimes|required|date',
            'id_roles'     => 'sometimes|required|exists:roles,id_roles',
            'users_active' => 'sometimes|boolean',
            'password'     => [
                'nullable',
                'string',
                Password::defaults(),
                'required_if:id_roles,1,2,3',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validacion',
                'errors' => $validator->errors()
            ],422);
        }

        // preparar datos y encriptar contraseña si viene
        $data = $validator->validated();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // actualizar al usuario
        $user->update($data);

        // responder con el usuario actualizado
        return response()->json([
            'message' => 'usuario actualizado correctamente',
            'data' => $user,
        ], 200);
    }

    /**
        * Desactivar (en lugar de eliminar) un usuario por su número de identificación.
     */
    public function destroy(string $id_number)
    {
        // Buscar usuario
        $user = User::where('id_number', $id_number)->first();
        if (! $user) {
            return response()->json([
                'message' => 'Usuario no encontrado',

            ], 404);
        }

        // desactivar
        $user->update(['users_active' => false]);

        // responder
        return response()->json([
            'message' => 'Usuario desactivado correctamente'
        ], 200);
    }
}
