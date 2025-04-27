<?php

namespace Database\Seeders;

use App\Models\MyClass;
use Illuminate\Database\Seeder;

class MyClassSeeder extends Seeder
{
    public function run()
    {
        MyClass::query()->delete();

        $classes = [
            ['id' => 1, 'name' => 'Kindergarten', 'class_group_id' => 2],
            ['id' => 2, 'name' => 'Grade 1',      'class_group_id' => 3],
            ['id' => 3, 'name' => 'Grade 2',      'class_group_id' => 4],
            ['id' => 4, 'name' => 'Grade 3',      'class_group_id' => 5],
            ['id' => 5, 'name' => 'Grade 4',      'class_group_id' => 6],
            ['id' => 6, 'name' => 'Grade 5',      'class_group_id' => 7],
            ['id' => 7, 'name' => 'Grade 6',      'class_group_id' => 8],
        ];
        
        foreach ($classes as $class) {
            MyClass::create($class);
        }
    }
}
