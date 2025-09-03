<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subject::create([
            "name" => "Programación III",
            "description" => "Curso de programación en Java",
            
        ]);


        Subject::create([
            "name" => "Arquitectura de aplicaciones en la nube",
            "description" => "Curso de Aws",
            
        ]);


    }
}
