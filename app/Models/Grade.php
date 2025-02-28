<?php

namespace App\Models;


use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        "user_id",
        "subject_id",
        "grade"
    ];

    //relacion que una nota pertenece a una materia

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
