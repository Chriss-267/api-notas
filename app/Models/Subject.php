<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'imagen'
    ];


    //relacion muchos a muchos para los alumnos

    public function users()
    {
        return $this->belongsToMany(User::class, 'inscriptions', 'subject_id', 'user_id');
    }

    //relacion una materia pertenece a un maestro
    
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id')->where('rol', '1');
    }



    
}
