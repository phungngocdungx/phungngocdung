<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Export dữ liệu từ bảng 'users'
        $data = DB::table('users')->get();

        // Đổi tên file mỗi lần chạy Seeder để tránh ghi đè file
        $timestamp = now()->timestamp; // Tạo timestamp duy nhất
        Storage::put("data/users_{$timestamp}.json", $data->toJson());

        // Nếu bạn có dữ liệu khác muốn thêm vào database, bạn có thể làm ở đây
        // DB::table('users')->insert([...]);
    }
}
