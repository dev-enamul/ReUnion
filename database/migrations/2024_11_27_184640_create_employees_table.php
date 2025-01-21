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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('employee_id')->uniqid();
            $table->foreignId('designation_id')->nullable()->constrained();
            $table->foreignId('referred_by')->nullable()->constrained('users');
            $table->string('signature')->nullable();
            $table->decimal('salary')->default(0);

            $table->tinyInteger('status')->default(1)->comment('1= Active, 0= Resigned');
            $table->date('resigned_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
