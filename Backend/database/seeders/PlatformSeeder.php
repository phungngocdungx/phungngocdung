<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tùy chọn: Xóa dữ liệu cũ trong bảng platforms để tránh trùng lặp khi chạy lại seeder
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Tạm thời tắt kiểm tra khóa ngoại nếu có lỗi
        // Platform::truncate(); // Hoặc DB::table('platforms')->truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Bật lại
        DB::table('platforms')->delete();


        Platform::create([
            'name' => 'Google',
            'description' => 'Các dịch vụ của Google (Gmail, Drive, etc.)',
            'logo_path' => '/images/logos/google.png' // Đường dẫn ví dụ
        ]);

        Platform::create([
            'name' => 'Facebook',
            'description' => 'Mạng xã hội Facebook',
            'logo_path' => '/images/logos/facebook.png'
        ]);

        Platform::create([
            'name' => 'Netflix',
            'description' => 'Dịch vụ xem phim trực tuyến',
            'logo_path' => '/images/logos/netflix.png'
        ]);

        Platform::create([
            'name' => 'Shopee',
            'description' => 'Sàn thương mại điện tử',
            'logo_path' => '/images/logos/shopee.png'
        ]);
    }
}
