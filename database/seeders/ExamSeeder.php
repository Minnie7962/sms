<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\Exam;
use App\Models\ExamSlot;
use App\Models\MyClass;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run()
    {
        $school = School::first();
        
        foreach (AcademicYear::all() as $academicYear) {
            $semesters = Semester::where('academic_year_id', $academicYear->id)->get();
            
            foreach ($semesters as $semester) {
                $examTypes = [
                    [
                        'name'         => 'Mid-term Examination',
                        'month_offset' => 2,
                    ],
                    [
                        'name'         => 'Final Examination',
                        'month_offset' => 5,
                    ],
                ];
                
                foreach ($examTypes as $examType) {
                    $yearStart  = $academicYear->start_year;
                    $startMonth = ($semester->name === 'First Semester') ? 9 : 2;
                    $startMonth += $examType['month_offset'];
                    
                    $year = $yearStart;
                    if ($startMonth > 12) {
                        $startMonth -= 12;
                        $year++;
                    }
                    
                    $startDate = Carbon::createFromDate($year, $startMonth, 15);
                    $endDate   = $startDate->copy()->addDays(10);
                    
                    $exam = Exam::create([
                        'name'           => $examType['name'] . ' ' . $semester->name . ' ' . $academicYear->start_year . '-' . $academicYear->stop_year,
                        'description'    => $examType['name'] . ' for ' . $semester->name . ' of Academic Year ' . $academicYear->start_year . '-' . $academicYear->stop_year,
                        'semester_id'    => $semester->id,
                        'start_date'     => $startDate->format('Y-m-d'),
                        'stop_date'      => $endDate->format('Y-m-d'),
                        'active'         => false,
                        'publish_result' => false,
                    ]);
                    
                    $classGroups = ClassGroup::all();
                    
                    foreach ($classGroups as $classGroup) {
                        $classes = MyClass::where('class_group_id', $classGroup->id)
                            ->whereNotIn('name', ['Office1', 'Office2', 'Library'])
                            ->get();
                            
                        foreach ($classes as $class) {
                            $subjects = Subject::where('my_class_id', $class->id)->get();
                            
                            foreach ($subjects as $subject) {
                                ExamSlot::create([
                                    'name'        => $subject->name . ' - ' . $class->name,
                                    'description' => $examType['name'] . ' for ' . $subject->name . ' in ' . $class->name,
                                    'total_marks' => 10,
                                    'exam_id'     => $exam->id,
                                ]);
                            }
                        }
                    }
                }
                
                // Activate past exams
                if ($academicYear->id < $school->academic_year_id) {
                    Exam::where('semester_id', $semester->id)->update([
                        'active'         => true,
                        'publish_result' => true,
                    ]);
                }
            }
        }
        
        // Activate the current exam for the current semester
        $currentSemester = Semester::find($school->semester_id);
        if ($currentSemester) {
            $latestExam = Exam::where('semester_id', $currentSemester->id)
                ->orderBy('id', 'desc')
                ->first();
            if ($latestExam) {
                $latestExam->active = true;
                $latestExam->save();
            }
        }
    }
}
