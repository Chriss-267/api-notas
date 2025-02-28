<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    
    //obtenemos la lista de materias

    public function index()
    {
        

        $subjects = Subject::with('teacher')->get();

        return response()->json(["subjects" => $subjects], 200);
    }

    //obtener una materia en especifico con los alumnos inscritos
    public function show($id)
    {
        $subject = Subject::with('teacher','users')->find($id);

        if(!$subject){
            return response()->json(['message' => "Materia no encontrada"], 404);
        }

        return response()->json(["data" => $subject], 200);

        
    }

}
