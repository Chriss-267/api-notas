<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InscriptionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "subject_id" => "required|exists:subjects,id",
            "user_id" => "required|exists:users,id"
        ]);


        //validator

        if($validator->fails()){
            return response()->json(["errors" => $validator->errors()], 400);
        }

        //validar que sea alumno
        $user = User::find($request->user_id);

        if($user->rol != 0){
            return response()->json(["errors" => "Solo puede inscribirse un alumno"], 400);
        }

        //validar que no este inscrito

        if (Inscription::where("subject_id", $request->subject_id)
        ->where("user_id", $request->user_id)
        ->exists()) {
        return response()->json(["errors" => "Ya estás inscrito a esta materia"], 400);
        }


        //inscribiendo

        Inscription::create([
            "subject_id" => $request->subject_id,
            "user_id" => $request->user_id
        ]);

        return response()->json(["data" => "Te has inscrito correctamente"], 200);
    }
}
