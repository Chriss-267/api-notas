<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(name="Grades", description="API for grades")
 */

class GradeController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/grades",
     *     summary="Registrar una nueva nota",
     *     tags={"Grades"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "subject_id", "grade"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="subject_id", type="integer", example=1),
     *             @OA\Property(property="grade", type="number", format="float", example=15.5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Nota registrada con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Nota registrada con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object", example={"user_id": {"El campo user_id es obligatorio."}})
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "user_id" => "required|exists:users,id",
            "subject_id" => "required|exists:subjects,id",
            "grade" => "required|numeric"
        ]);

        if($validator->fails()){
            return response()->json(["errors" => $validator->errors()], 400);
        }

        Grade::create([
            "user_id" => $request->user_id,
            "subject_id" => $request->subject_id,
            "grade" => $request->grade
        ]);

        return response()->json(["message" => "Nota registrada con éxito"], 200);

        
    }

    /**
     * @OA\Patch(
     *     path="/api/grades/{id}",
     *     summary="Actualizar una nota existente",
     *     tags={"Grades"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la nota",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"grade"},
     *             @OA\Property(property="grade", type="number", format="float", example=9.0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Nota actualizada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="string", example="Nota actualizada correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Error de validación o nota no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="string", example="Nota no encontrada")
     *         )
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $grade = Grade::find($id);

        if(!$grade){
            return response()->json(["errors" => "Nota no encontrada"], 404);
        }

        $validator = Validator::make($request->all(),[
            "grade" => "numeric"
        ]);

        if($validator->fails()){
            return response()->json(["errors" => $validator->errors()], 400);
        }

        $grade->grade = $request->grade;

        $grade->save();

        return response()->json(["data" => "Nota actualizada correctamente"], 200);



    }

    //eliminar una nota
    /**
     * @OA\Delete(
     *     path="/api/grades/{id}",
     *     summary="Eliminar una nota",
     *     tags={"Grades"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la nota",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Nota eliminada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Nota eliminada correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Nota no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="string", example="Nota no encontrada")
     *         )
     *     )
     * )
     */

    public function destroy($id)
    {
        $grade = Grade::find($id);

        if(!$grade){
            return response()->json(["errors" => "Nota no encontrada"], 404);
        }

        $grade->delete();

        return response()->json(["message" => "Nota eliminada correctamente"], 200);


    }

    //obtener notas del alumno por su id

    /**
     * @OA\Get(
     *     path="/api/grades/{id}",
     *     summary="Obtener las notas de un alumno",
     *     tags={"Grades"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del alumno",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de notas del alumno",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="subject_name", type="string", example="Matemáticas"),
     *                 @OA\Property(property="grade", type="number", format="float", example=8.5)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="El alumno no tiene notas asignadas",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="string", example="El Alumno no tiene notas asignadas aún")
     *         )
     *     )
     * )
     */

    public function show($id)
    {
        $grades = Grade::with("subject")->where("user_id", $id)->get();

        if($grades->isEmpty()){
            return response()->json(["data" => "El Alumno no tiene notas asignadas aún"], 200);
        }

        $gradeFormated = $grades->map(function ($grade){
            return [
                "subject_name" => $grade->subject->name,
                "grade" => $grade->grade
            ];
        });

        return response()->json(["data" => $grades], 200);
    }

    //obtener las calificaciones de los alumnos en una materia

    public function gradesStudentsSubject ($id)
    {
        $notas = DB::Select('Select user_id as alumno, subject_id as materia, grade as nota from grades 
        where subject_id = ? ', [$id]);

         if(collect($notas)->isEmpty()){
            return response()->json(["data" => "No hay calificaciones en esta materia"], 200);
        }


        return response()->json(['data' => $notas], 200);
    }
}
