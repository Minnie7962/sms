<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure age field exists and make email/password nullable
        Schema::table('users', function (Blueprint $table) {
            // Check if age column exists before adding it
            if (!Schema::hasColumn('users', 'age')) {
                $table->integer('age')->nullable()->after('birthday');
            }
            
            // Make email and password nullable if they aren't already
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            
            // Drop columns if they exist
            if (Schema::hasColumn('users', 'religion')) {
                $table->dropColumn('religion');
            }
            
            if (Schema::hasColumn('users', 'blood_group')) {
                $table->dropColumn('blood_group');
            }
        });
        
        // Make sure student_records is properly set up
        Schema::table('student_records', function (Blueprint $table) {
            // Make sure user_id is not nullable (critical for relationships)
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // These are relatively destructive changes, so the down migration is minimal
            if (Schema::hasColumn('users', 'age')) {
                $table->dropColumn('age');
            }
            
            // Add back removed columns as nullable
            $table->string('religion')->nullable();
            $table->string('blood_group')->nullable();
            
            // Email and password should not be nullable in a regular application
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
        
        Schema::table('student_records', function (Blueprint $table) {
            // Revert user_id to nullable (though this isn't recommended)
            $table->foreignId('user_id')->nullable()->change();
        });
    }
};