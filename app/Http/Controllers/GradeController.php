<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
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

    public function update(Request $request, $id)
    {
        $grade = Grade::find($id);

        if(!$grade){
            return response()->json(["errors" => "Nota no encontrada"], 400);
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

    public function destroy($id)
    {
        $grade = Grade::find($id);

        if(!$grade){
            return response()->json(["errors" => "Nota no encontrada"], 400);
        }

        $grade->delete();

        return response()->json(["message" => "Nota eliminada correctamente"], 200);


    }

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

        return response()->json(["data" => $gradeFormated], 200);
    }
}
