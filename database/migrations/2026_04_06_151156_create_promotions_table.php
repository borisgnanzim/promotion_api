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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->uuid('ref')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('pourcentage',5,2);
            $table->float('discount');
            $table->float('max_discount');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            // à checker plus tard
            $table->string('store_ref');
            $table->foreign('store_ref')->references('ref')->on('stores')->onDelete('set null');
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
