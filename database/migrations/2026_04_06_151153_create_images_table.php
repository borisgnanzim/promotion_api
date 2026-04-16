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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->uuid('ref')->unique();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('path')->nullable();
            $table->string('item_ref');
            $table->string('item_type')->nullable();
            $table->foreign('item_ref')->references('ref')->on('items')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
