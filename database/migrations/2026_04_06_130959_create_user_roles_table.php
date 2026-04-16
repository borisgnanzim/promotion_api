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
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->uuid('ref')->unique();
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('assign_by')->nullable();
            $table->string('update_by')->nullable();
            $table->foreign('assign_by')->nullable()->references('ref')->on('users')->onDelete('set null');
            $table->foreign('update_by')->nullable()->references('ref')->on('users')->onDelete('set null');
            $table->date('disabled_at')->nullable();
            $table->string('user_ref');
            $table->string('role_ref');
            $table->foreign('user_ref')->references('ref')->on('users')->onDelete('cascade');
            $table->foreign('role_ref')->references('ref')->on('roles')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
