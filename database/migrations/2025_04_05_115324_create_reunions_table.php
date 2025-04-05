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
            $table->string('payment_method')->comment('e.g., bKash, Nagad, Rocket');
            $table->string('payment_number')->comment('Sender number used for payment');
            $table->string('payment_photo')->comment('Screenshot or proof of payment');
         
            $table->string('t_shirt_size')->comment('T-shirt size: S, M, L, XL, etc.');
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
