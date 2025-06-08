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
        Schema::create('account_socialnetwork_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id')->unique();

            // Có thể không cần liên kết email
            $table->unsignedBigInteger('mail_account_id')->nullable();

            // ID người dùng là thông tin quan trọng, không nên null
            $table->string('tiktok_user_id')->nullable();

            // Số lượng follower mặc định là 0, tốt hơn null
            $table->unsignedInteger('follower_count')->default(0);

            // IP đăng nhập có thể không có
            $table->ipAddress('last_login_ip')->nullable();

            // Trạng thái luôn có giá trị mặc định, không nên null
            $table->string('status', 50)->default('active');

            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('mail_account_id')->references('id')->on('mail_accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_socialnetwork_details');
    }
};
