<?php

namespace Database\Seeders;

use App\Models\MyClass;
use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run()
    {
        Section::query()->delete();
        
        $classes = MyClass::whereNotIn('name', ['Office1', 'Office2', 'Library'])->get();
        
        $id = 1;
        $sections = [];
        
        foreach ($classes as $class) {
            $sections[] = [
                'id'          => $id++,
                'name'        => 'A',
                'my_class_id' => $class->id,
            ];
            
            $sections[] = [
                'id'          => $id++,
                'name'        => 'B',
                'my_class_id' => $class->id,
            ];
        }
        
        foreach ($sections as $section) {
            Section::create($section);
        }
    }
}
