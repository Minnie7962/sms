<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\School;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $school = School::first();

        $startYear = 2005;
        $currentYear = 2025;
        
        $latestAcademicYear = null;
        
        for ($year = $startYear; $year <= $currentYear; $year++) {
            $academicYear = AcademicYear::create([
                'start_year'  => $year,
                'stop_year'   => $year + 1,
                'school_id'   => $school->id,
                'is_current'  => ($year == $currentYear),
            ]);
            
            $latestAcademicYear = $academicYear;
        }
        
        // Set the current academic year in the school
        $school->academic_year_id = $latestAcademicYear->id;
        $school->save();
    }
}