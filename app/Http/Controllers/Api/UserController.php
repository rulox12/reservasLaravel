<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'users' => User::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'identification' => 'required|string|unique:users|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'city' => 'nullable|string|max:100',
            'password' => 'required|string|min:8',
            'role' => 'required|in:client,admin',
        ]);

        $user = User::create([
            'full_name' => $validatedData['full_name'],
            'identification' => $validatedData['identification'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'city' => $validatedData['city'] ?? null,
            'password' => bcrypt($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        return response()->json([
            'success' => true,
            'message' => '¡Usuario creado con éxito!',
            'user' => $user
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'identification' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->ignore($id),
            ],
            'phone' => 'required|string|max:20',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($id),
            ],
            'city' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:client,admin',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'full_name' => $validatedData['full_name'],
            'identification' => $validatedData['identification'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'city' => $validatedData['city'] ?? $user->city,
            'password' => isset($validatedData['password']) ? bcrypt($validatedData['password']) : $user->password,
            'role' => $validatedData['role'],
        ]);

        return response()->json([
            'success' => true,
            'message' => '¡Usuario actualizado con éxito!',
            'user' => $user
        ]);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => '¡Usuario eliminado con éxito!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el usuario: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'value' => 'required|string|max:255',
        ]);

        $type = $request->input('type');
        $value = $request->input('value');

        $users = User::query();

        if ($type === 'identification') {
            $users->where('identification', 'like', '%' . $value . '%');
        } elseif ($type === 'full_name') {
            $users->where('full_name', 'like', '%' . $value . '%');
        }

        $users = $users->get();

        return response()->json(['users' => $users]);
    }
}
