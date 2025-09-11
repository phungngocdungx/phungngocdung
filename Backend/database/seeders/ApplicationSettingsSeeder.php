<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ApplicationSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('application_settings')->insert([
            'setting_key' => 'global_view_pin',
            'encrypted_setting_value' => Crypt::encryptString('12345678'), // Mã PIN mặc định ví dụ
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
