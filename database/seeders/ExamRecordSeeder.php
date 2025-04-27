<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamRecord;
use App\Models\ExamSlot;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExamRecordSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            // Fetch all published exams with related exam slots
            $publishedExams = Exam::where('publish_result', true)->with('examSlots')->get();

            if ($publishedExams->isEmpty()) {
                Log::warning("No published exams found. Ensure ExamSeeder is run first.");
                return;
            }

            // Fetch students with related records
            $students = User::role('student')->with('studentRecord')->get();

            if ($students->isEmpty()) {
                Log::warning("No students found. Ensure StudentSeeder is run first.");
                return;
            }

            foreach ($publishedExams as $exam) {
                foreach ($exam->examSlots as $examSlot) {
                    // Fetch sections associated with the exam
                    $sections = Section::where('my_class_id', $examSlot->exam->my_class_id)->get();

                    foreach ($sections as $section) {
                        // Filter students belonging to this section
                        $sectionStudents = $students->filter(function ($student) use ($section) {
                            return $student->studentRecord && $student->studentRecord->section_id == $section->id;
                        });

                        // Assign random marks
                        $marks = [];
                        foreach ($sectionStudents as $student) {
                            $marks[$student->id] = rand(0, 100); // Adjust score range if needed
                        }

                        // Insert records using batch method in ExamRecord model
                        ExamRecord::addOrUpdateBatch($exam->id, $section->id, $sectionStudents->pluck('id')->toArray(), $marks);
                    }
                }
            }

            Log::info("Exam records seeded successfully.");
        });
    }
}
