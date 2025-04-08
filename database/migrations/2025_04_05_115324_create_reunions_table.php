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
        Schema::create('reunions', function (Blueprint $table) {
            $table->id();
         
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
         
            $table->float('fee')->comment('Registration or participation fee');
            $table->string('payment_method')->nullable()->comment('e.g., bKash, Nagad, Rocket');
            $table->string('payment_number')->nullable();
            $table->string('payment_photo')->nullable();
            $table->string('payment_to')->nullable();
            $table->boolean('is_interest_memorial')->default(false)->nullable();
         
            $table->string('t_shirt_size')->nullable()->comment('T-shirt size: S, M, L, XL, etc.');
            $table->boolean('is_active')->default(false)->comment('Approved by admin');
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reunions');
    }
};
