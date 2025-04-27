<?php

namespace Database\Seeders;

use App\Models\MyClass;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        $school = School::first();
        
        $subjectNames = [
            'Khmer',
            'Math',
            'Science',
            'Social',
            'English',
            'PE',
        ];
        
        $shortNames = [
            'Khmer'   => 'KHM',
            'Math'    => 'MAT',
            'Science' => 'SCI',
            'Social'  => 'SOC',
            'English' => 'ENG',
            'PE'      => 'PE',
        ];
        
        $classes = MyClass::whereNotIn('name', ['Office1', 'Office2', 'Library'])->get();
        
        foreach ($classes as $class) {
            foreach ($subjectNames as $subjectName) {
                Subject::create([
                    'name'       => $subjectName,
                    'short_name' => $shortNames[$subjectName],
                    'school_id'  => $school->id,
                    'my_class_id'=> $class->id,
                ]);
            }
        }
    }
}
