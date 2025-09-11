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
        Schema::create('application_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique(); // Khóa để xác định cài đặt, ví dụ: 'global_view_pin'
            $table->text('encrypted_setting_value')->nullable(); // Giá trị cài đặt đã được mã hóa
            // Nếu bạn chắc chắn giá trị mã hóa không quá dài, có thể dùng $table->string(...)
            // Nhưng text() sẽ linh hoạt hơn. Hoặc dùng binary() nếu lưu byte thô.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_settings');
    }
};
