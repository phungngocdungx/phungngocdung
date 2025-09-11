<?php

namespace Database\Seeders;

use App\Models\FamilyMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FamilyMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tùy chọn: Xóa dữ liệu cũ
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // FamilyMember::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table('family_members')->delete();

        FamilyMember::create([
            'name' => 'Bố Tuấn',
            'email' => 'botuan@example.com',
            'master_password_hash' => Hash::make('BoTuanSecurePass123!') 
        ]);

        FamilyMember::create([
            'name' => 'Mẹ Lan',
            'email' => 'melan@example.com',
            'master_password_hash' => Hash::make('MeLanSecurePass456!')
        ]);
    }
}
