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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id(); // Primary Key, Auto-increment

            $table->foreignId('platform_id')
                ->constrained('platforms') // Foreign key to 'id' on 'platforms' table
                ->onDelete('cascade'); // If a platform is deleted, related accounts are also deleted

            $table->binary('encrypted_username'); // Trường này lưu trữ tên người dùng đã được mã hóa
            $table->binary('encrypted_password'); // Trường này lưu trữ mật khẩu đã được mã hóa
            $table->binary('encrypted_note')->nullable(); // Trường này lưu trữ ghi chú đã được mã hóa (nếu có)
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
