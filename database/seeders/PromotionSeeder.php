<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Promotion;
use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class PromotionSeeder extends Seeder
{
    public function run()
    {
        $school = School::first();
        if (!$school) {
            Log::error("No school found. Please seed the schools table first.");
            return;
        }

        $currentAcademicYear = AcademicYear::find($school->academic_year_id);
        $nextAcademicYear = AcademicYear::where('start_year', '>', $currentAcademicYear->start_year)->first();

        if (!$currentAcademicYear || !$nextAcademicYear) {
            Log::warning("Academic year data is incomplete. Ensure previous and next years exist.");
            return;
        }

        $promotions = [
            [
                'old_class_id'          => 1,
                'new_class_id'          => 2,
                'old_section_id'        => 1,
                'new_section_id'        => 2,
                'academic_year_id'      => $currentAcademicYear->id,
                'next_academic_year_id' => $nextAcademicYear->id,
                'students'              => json_encode([1, 2, 3]), // Ensure stored as JSON
                'school_id'             => $school->id,
                'status'                => 'pending', // Optional, for promotion tracking
            ],
        ];

        foreach ($promotions as $promotionData) {
            Promotion::create($promotionData);
        }

        Log::info("Promotion seeding completed successfully.");
    }
}
