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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('nationality')->default('Khmer');
            $table->string('phone')->unique()->nullable();
            $table->string('address');
            $table->string('address2')->nullable();
            $table->string('city');
            $table->string('zip');
            $table->string('photo')->nullable();
            $table->string('birthday')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('religion')->nullable();
            $table->string('role');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
