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
        Schema::create('platforms', function (Blueprint $table) {
            $table->id(); // Primary Key, Auto-increment
            $table->string('name'); // Platform name (e.g., "Facebook", "Gmail")
            $table->text('description')->nullable(); // Optional description
            $table->string('logo_path')->nullable(); // Optional path or URL to the platform's logo
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platforms');
    }
};
