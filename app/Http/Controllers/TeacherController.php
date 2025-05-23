<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    /**
     * Listar todos los profesores (usuarios con rol = 1)
     */
    public function index()
    {
        $teachers = User::where('rol', '1')->get();

        return response()->json([
            'teachers' => $teachers
        ], 200);
    }

    /**
     * Crear un nuevo profesor (usuario con rol = 1)
     */
    public function store(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $teacher = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'rol' => '1',
        ]);

        return response()->json([
            'message' => 'Profesor creado correctamente',
            'data' => $teacher
        ], 201);
    }

    /**
     * Mostrar detalles de un profesor (con rol = 1)
     */
    public function show($id)
    {
        $teacher = User::where('rol', '1')->find($id);

        if (!$teacher) {
            return response()->json(['error' => 'Profesor no encontrado'], 404);
        }

        return response()->json(['data' => $teacher], 200);
    }

    /**
     * Actualizar datos de un profesor
     */
    public function update(Request $request, $id)
    {
        $teacher = User::where('rol', '1')->find($id);

        if (!$teacher) {
            return response()->json(['error' => 'Profesor no encontrado'], 404);
        }

        // Validación parcial
        $validated = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $teacher->id,
            'password' => 'sometimes|required|min:6'
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 400);
        }

        // Actualiza solo los campos que vienen en la solicitud
        $teacher->fill($request->only(['name', 'email']));

        if ($request->filled('password')) {
            $teacher->password = bcrypt($request->password);
        }

        $teacher->save();

        return response()->json([
            'message' => 'Profesor actualizado correctamente',
            'data' => $teacher
        ], 200);
    }

    /**
     * Eliminar un profesor
     */
    public function destroy($id)
    {
        $teacher = User::where('rol', '1')->find($id);

        if (!$teacher) {
            return response()->json(['error' => 'Profesor no encontrado'], 404);
        }

        $teacher->delete();

        return response()->json(['message' => 'Profesor eliminado correctamente'], 200);
    }
}