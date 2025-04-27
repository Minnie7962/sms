<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\MyClass;
use App\Models\ExamSlot;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class ExamSlotSeeder extends Seeder
{
    public function run()
    {
        $exams = Exam::all();

        foreach ($exams as $exam) {
            $classes = MyClass::all();
            
            foreach ($classes as $class) {
                if (in_array($class->name, ['Office1', 'Office2', 'Library'])) {
                    continue;
                }
                
                $subjects = Subject::where('my_class_id', $class->id)->get();

                foreach ($subjects as $subject) {
                    ExamSlot::create([
                        'name'        => $subject->name . ' - ' . $class->name,
                        'description' => 'Exam slot for ' . $subject->name . ' in ' . $class->name,
                        'total_marks' => 10,
                        'exam_id'     => $exam->id,
                    ]);
                }
            }
        }
    }
}
