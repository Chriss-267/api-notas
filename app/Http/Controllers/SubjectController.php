<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(name="Subjects", description="API for subjects")
 */
class SubjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/subjects",
     *     summary="Get all subjects",
     *     description="Return a list of all subjects",
     *     tags={"Subjects"},
     *     @OA\Response(
     *         response=200,
     *         description="List of subjects successfully obtained"
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No subjects registered"
     *     )
     * )
     */
    public function index()
    {
        $subjects = Subject::with('teacher')->get();

        if ($subjects->isEmpty()) {
            return response()->json(['message' => 'No hay materias registradas'], 204);
        }

        return response()->json(["subjects" => $subjects], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/subjects",
     *     summary="Crear nueva materia",
     *     tags={"Subjects"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "user_id"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="imagen", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(response="201", description="Materia creada correctamente"),
     *     @OA\Response(response="400", description="Error de validación")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
             'user_id' => 'required|exists:users,id,rol,1', //AQUI ESTA LO DE LA VALIDACION
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $subject = new Subject([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $request->user_id
        ]);

        //guardar imagen si existe
        if ($request->hasFile('imagen')) {
            $imageName = time().'.'.$request->imagen->extension();
            $request->imagen->move(public_path('images/subjects'), $imageName);
            $subject->imagen = $imageName;
        }

        $subject->save();

        return response()->json(['message' => 'Materia creada exitosamente', 'data' => $subject], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/subjects/{id}",
     *     summary="Mostrar detalles de una materia",
     *     tags={"Subjects"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Datos de la materia"),
     *     @OA\Response(response="404", description="Materia no encontrada")
     * )
     */
    public function show($id)
    {
        $subject = Subject::with('teacher', 'users')->find($id);

        if (!$subject) {
            return response()->json(['error' => 'Materia no encontrada'], 404);
        }

        return response()->json(["data" => $subject], 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/subjects/{id}",
     *     summary="Actualizar parcialmente una materia",
     *     tags={"Subjects"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent),
     *     @OA\Response(response="200", description="Materia actualizada correctamente"),
     *     @OA\Response(response="404", description="Materia no encontrada")
     * )
     */
    public function update(Request $request, $id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json(['error' => 'Materia no encontrada'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'user_id' => 'sometimes|required|exists:users,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        //rellena solo los campos que vienen en la solicitud
        $subject->fill($validated);

        //si hay nueva imagen, reemplaza la anterior
        if ($request->hasFile('imagen')) {
            //elimina imagen anterior si existe
            if ($subject->imagen && file_exists(public_path('images/subjects/'.$subject->imagen))) {
                unlink(public_path('images/subjects/'.$subject->imagen));
            }

            $imageName = time().'.'.$request->imagen->extension();
            $request->imagen->move(public_path('images/subjects'), $imageName);
            $subject->imagen = $imageName;
        }

        $subject->save();

        return response()->json(['message' => 'Materia actualizada correctamente', 'data' => $subject], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/subjects/{id}",
     *     summary="Eliminar una materia",
     *     tags={"Subjects"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Materia eliminada correctamente"),
     *     @OA\Response(response="404", description="Materia no encontrada")
     * )
     */
    public function destroy($id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json(['error' => 'Materia no encontrada'], 404);
        }

        //elimina la imagen si existe
        if ($subject->imagen && file_exists(public_path('images/subjects/'.$subject->imagen))) {
            unlink(public_path('images/subjects/'.$subject->imagen));
        }

        $subject->delete();

        return response()->json(['message' => 'Materia eliminada correctamente'], 200);
    }

    //obtener materias inscritas
    public function getSubjectsbyStudent($userId)
    {
        $materias = Subject::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with('teacher','users') 
        ->get();

        return response()->json($materias);
    }
}