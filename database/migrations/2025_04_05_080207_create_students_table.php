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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('profile_picture')->nullable();
            $table->string('phone');
            $table->string('guardiant')->nullable();
            $table->integer('passing_year')->nullable();
            $table->string('exam_name')->comment('Dakhil, Alim, Fazil')->nullable();
            $table->foreignId('profession_id')->constrained('professions');
            $table->text('profession_details')->nullable();

            $table->string('present_village')->nullable();
            $table->string('present_post')->nullable();
            $table->string('present_upazila')->nullable();
            $table->string('present_zila')->nullable(); 

            $table->string('permanent_village')->nullable();
            $table->string('permanent_post')->nullable();
            $table->string('permanent_upazila')->nullable();
            $table->string('permanent_zila')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
