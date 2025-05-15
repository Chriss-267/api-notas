<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(name="Subjects", description="API for subjects")
 */

class SubjectController extends Controller
{
    
    //obtenemos la lista de materias

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
     *      
     * )
     */

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

     //obbtener materias inscritas

     public function getSubjectsbyStudent ($userId)
     {

        $materias = Subject::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with('users') 
        ->get();

        return response()->json($materias);
    }

}
