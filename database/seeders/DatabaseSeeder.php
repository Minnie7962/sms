<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            
            // Core system settings
            PermissionSeeder::class,          // Permissions and admin user
            AcademicSettingSeeder::class,     // Academic configurations
            SchoolSessionSeeder::class,       // Academic sessions
            
            // Academic structure
            SchoolClassSeeder::class,         // Classes
            SectionSeeder::class,             // Class sections
            SemesterSeeder::class,            // Academic semesters
            CourseSeeder::class,              // Course definitions
            
            // Teaching assignments and content
            AssignedTeacherSeeder::class,     // Teacher assignments
            SyllabusSeeder::class,            // Course syllabi
            RoutineSeeder::class,             // Class routines
            
            // Student data
            StudentParentInfoSeeder::class,   // Parent information
            StudentAcademicInfoSeeder::class, // Student academic records
            
            // Academic activities
            GradingSystemSeeder::class,       // Grading system
            GradeRuleSeeder::class,           // Grading rules
            ExamSeeder::class,                // Exams
            ExamRuleSeeder::class,            // Exam rules
            AssignmentSeeder::class,          // Assignments
            
            // Performance records
            MarkSeeder::class,                // Individual marks
            FinalMarkSeeder::class,           // Final grades
            CourseAttendanceSeeder::class,    // Course attendance
            SectionAttendanceSeeder::class,   // Section attendance
            PromotionSeeder::class,           // Student promotions
            
            // Communications
            NoticeSeeder::class,              // School notices
            EventSeeder::class,               // School events
        ]);
    }
}
