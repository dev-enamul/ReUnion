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
        Schema::create('followup_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->unsignedBigInteger('pipeline_id')->constrained('sales_pipeline')->onDelete('cascade');  
            $table->foreignId('followup_categorie_id')->constrained('followup_categories')->onDelete('set null');   
            $table->integer('purchase_probability')->nullable()->default(null)->comment("0-100%");
            $table->decimal('price')->nullable()->default(null);
            $table->date('next_followup_date')->nullable();
            $table->text('notes')->nullable(); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followup_logs');
    }
};
