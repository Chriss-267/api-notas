<?php

use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\SubjectController;

Route::group([
    'middleware' => 'api', 
    'prefix' => 'auth'     
], function($router) {
    $router->post('/login', [AuthController::class, 'login']);
    $router->post('/register', [AuthController::class, 'register']);
    $router->post('/logout', [AuthController::class, 'logout']);
    $router->get('/me', [AuthController::class, 'me']);
});

//Teachers (usuarios con rol = 1)
Route::apiResource('teachers', TeacherController::class)->middleware('auth:api')->except(['index', 'show']);

//Subjects
Route::get('/subjects', [SubjectController::class, "index"]);
Route::get('/subjects/{id}', [SubjectController::class, "show"]);
Route::get('/subjects/alumno/{id}', [SubjectController::class, 'getSubjectsbyStudent']);
Route::apiResource('subjects', SubjectController::class);

//Inscriptions
Route::post('/inscriptions', [InscriptionController::class, "store"]);

//grades
Route::get('/grades/{id}', [GradeController::class, "show"]);
Route::post('/grades', [GradeController::class, "store"]);
Route::patch('/grades/{id}', [GradeController::class, "update"]);
Route::delete('/grades/{id}', [GradeController::class, "destroy"]);
Route::get('/grades/materia/{id}', [GradeController::class, 'gradesStudentsSubject']);
