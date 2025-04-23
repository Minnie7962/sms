<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;

class PromotionSeeder extends Seeder
{
    public function run()
    {
        // Example promotion data without academic year
        $promotions = [
            [
                'old_class_id' => 1,
                'new_class_id' => 2,
                'old_section_id' => 1,
                'new_section_id' => 2,
                'students' => [1, 2, 3], // Array of student IDs
                'school_id' => 1,
            ],
            // Add more promotions as needed
        ];

        foreach ($promotions as $promotion) {
            Promotion::create($promotion);
        }
    }
}