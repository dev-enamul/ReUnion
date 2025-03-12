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
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique(); // Ensuring unique slugs
            $table->boolean('status')->default(1)->comment("0=Inactive, 1=Active"); // Changed to boolean
            
            // $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            // $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            // $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->softDeletes();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};
