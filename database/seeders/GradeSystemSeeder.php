<?php

namespace Database\Seeders;

use App\Models\ClassGroup;
use App\Models\GradeSystem;
use Illuminate\Database\Seeder;

class GradeSystemSeeder extends Seeder
{
    public function run()
    {
        $classGroups = ClassGroup::all();
        
        foreach ($classGroups as $classGroup) {
            GradeSystem::create([
                'name'           => 'Very Good',
                'remark'         => 'Excellent performance',
                'grade_from'     => '8',
                'grade_till'     => '10',
                'class_group_id' => $classGroup->id,
            ]);
            
            GradeSystem::create([
                'name'           => 'Good',
                'remark'         => 'Satisfactory performance',
                'grade_from'     => '5',
                'grade_till'     => '7.9',
                'class_group_id' => $classGroup->id,
            ]);
            
            GradeSystem::create([
                'name'           => 'Not Good',
                'remark'         => 'Needs improvement',
                'grade_from'     => '0',
                'grade_till'     => '4.9',
                'class_group_id' => $classGroup->id,
            ]);
        }
    }
}
