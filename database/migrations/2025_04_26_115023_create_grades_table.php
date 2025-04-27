<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Kindergarten, Grade 1, etc.
            $table->timestamps();
        });

        Schema::table('my_classes', function (Blueprint $table) {
            $table->foreignId('grade_id')->nullable()->after('class_group_id')->constrained('grades')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('my_classes', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropColumn('grade_id');
        });

        Schema::dropIfExists('grades');
    }
};
