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
        Schema::create('account_family_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('account_id')
                ->constrained('accounts') // Foreign key to 'id' on 'accounts' table
                ->onDelete('cascade');

            $table->foreignId('family_member_id')
                ->constrained('family_members') // Foreign key to 'id' on 'family_members' table
                ->onDelete('cascade');

            // Đặt tên cho ràng buộc unique nếu cần, ví dụ 'account_family_member_unique'
            // Hoặc bỏ qua tên tự đặt nếu bạn muốn Laravel tự tạo: $table->unique(['account_id', 'family_member_id']);
            $table->unique(['account_id', 'family_member_id'], 'account_family_member_account_id_family_member_id_unique');
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_family_members');
    }
};
