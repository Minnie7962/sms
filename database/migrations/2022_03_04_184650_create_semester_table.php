<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('semesters', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('academic_year_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('school_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('check_result')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
