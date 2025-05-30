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
        Schema::create('emails', function (Blueprint $table) {
            $table->id(); // Khóa chính tự tăng

            // Khóa ngoại liên kết với bảng mail_accounts
            $table->foreignId('mail_account_id')
                  ->constrained('mail_accounts') // Tên bảng mail_accounts
                  ->onDelete('cascade'); // Nếu tài khoản bị xóa, các email liên quan cũng bị xóa

            // ID duy nhất của email từ máy chủ mail (rất quan trọng)
            $table->string('message_id')->nullable()->index();

            $table->string('subject')->nullable(); // Tiêu đề email
            $table->string('from')->nullable();    // Địa chỉ email người gửi
            $table->string('from_name')->nullable(); // Tên người gửi

            $table->dateTime('date')->nullable(); // Ngày giờ của email

            $table->longText('body_html')->nullable(); // Nội dung HTML của email
            $table->longText('body_text')->nullable(); // Nội dung dạng text thuần của email

            // Bạn có thể thêm các cột khác nếu cần, ví dụ:
            // $table->json('to')->nullable(); // Danh sách người nhận
            // $table->json('cc')->nullable(); // Danh sách CC
            // $table->boolean('has_attachments')->default(false);

            $table->timestamps(); // created_at và updated_at

            // Tạo unique constraint để tránh trùng lặp email cho mỗi tài khoản dựa trên message_id
            // Message-ID thường là duy nhất toàn cầu, nhưng để an toàn hơn có thể kết hợp với mail_account_id
            $table->unique(['mail_account_id', 'message_id'], 'mail_account_message_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
