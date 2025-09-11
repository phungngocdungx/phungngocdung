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
        Schema::create('mail_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('app_password'); // bạn nên mã hóa nếu cần bảo mật
            $table->string('imap_host')->default('imap.gmail.com');
            $table->integer('imap_port')->default(993);
            $table->string('imap_encryption')->default('ssl');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_accounts');
    }
};
