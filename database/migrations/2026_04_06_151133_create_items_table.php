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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->text('description')->nullable(); 
            $table->text('mini_description')->nullable(); 
            $table->float('price'); 
            $table->unsignedInteger('stock')->default(6); 
            $table->unsignedInteger('limit_threshold')->default(5); 
            $table->unsignedInteger('out_of_stock_threshold')->default(0); 
            $table->enum('status', ['disponible', 'limite', 'rupture']); // updated enum values
            $table->string('slug')->default('');

            $table->string('search_slug')->default(''); 
            $table->string('search_slug_metaphone')->nullable(); 

            $table->string('category_id');

            $table->decimal('promotion_pourcentage',5,2)->nullable(); 
            $table->float('promotion_discount')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
