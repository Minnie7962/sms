<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions grouped by modules
        $permissions = [
            // User Management
            'create users', 'view users', 'edit users', 'delete users',

            // Student Management
            'promote students',

            // Notices
            'create notices', 'view notices', 'edit notices', 'delete notices',

            // Events
            'create events', 'view events', 'edit events', 'delete events',

            // Syllabi
            'create syllabi', 'view syllabi', 'edit syllabi', 'delete syllabi',

            // Routines
            'create routines', 'view routines', 'edit routines', 'delete routines',

            // Exams
            'create exams', 'view exams', 'delete exams',
            'create exams rule', 'view exams rule', 'edit exams rule', 'delete exams rule',
            'view exams history',

            // Grading Systems
            'create grading systems', 'view grading systems', 'edit grading systems', 'delete grading systems',
            'create grading systems rule', 'view grading systems rule', 'edit grading systems rule', 'delete grading systems rule',

            // Attendance
            'take attendances', 'view attendances', 'update attendances type',

            // Assignments
            'submit assignments', 'create assignments', 'view assignments',

            // Marks
            'save marks', 'view marks',

            // Academic Settings
            'create school sessions', 'create semesters', 'view semesters', 'edit semesters', 'assign teachers',
            'create courses', 'view courses', 'edit courses', 'view academic settings',
            'update marks submission window', 'update browse by session',

            // Classes and Sections
            'create classes', 'view classes', 'edit classes',
            'create sections', 'view sections', 'edit sections',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $user = \App\Models\User::factory()->create([
            'email' => 'admin@ut.com',
            'first_name' => 'Ryna',
            'last_name' => 'Hout',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Poipet',
            'zip' => '1207',
            'role' => 'admin',
            'password' => bcrypt('password'), // Securely hash the password
        ]);
        
        $user->givePermissionTo(
            'create school sessions',
            'update browse by session',
            'create semesters',
            'edit semesters',
            'assign teachers',
            'create courses',
            'view courses',
            'edit courses',
            'create classes',
            'view classes',
            'edit classes',
            'create sections',
            'view sections',
            'edit sections',
            'create exams',
            'view exams',
            'create exams rule',
            'edit exams rule',
            'delete exams rule',
            'view exams rule',
            'create routines',
            'view routines',
            'edit routines',
            'delete routines',
            'view marks',
            'view academic settings',
            'update marks submission window',
            'create users',
            'edit users',
            'view users',
            'promote students',
            'update attendances type',
            'view attendances',
            'take attendances',
            'create grading systems',
            'view grading systems',
            'edit grading systems',
            'delete grading systems',
            'create grading systems rule',
            'view grading systems rule',
            'edit grading systems rule',
            'delete grading systems rule',
            'create notices',
            'view notices',
            'edit notices',
            'delete notices',
            'create events',
            'view events',
            'edit events',
            'delete events',
            'create syllabi',
            'view syllabi',
            'edit syllabi',
            'delete syllabi',
            'view assignments'
        );
    }
}
