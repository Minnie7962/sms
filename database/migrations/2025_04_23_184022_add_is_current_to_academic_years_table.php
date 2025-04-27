<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->boolean('is_current')->default(false)->after('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn('is_current');
        });
    }
};
