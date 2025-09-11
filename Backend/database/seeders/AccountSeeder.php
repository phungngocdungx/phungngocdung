<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Platform;
use App\Models\FamilyMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tùy chọn: Xóa dữ liệu cũ. Quan trọng: Xóa bảng trung gian trước, rồi mới đến bảng accounts
        // Lưu ý: Tên bảng trung gian 'account_family_members' cần khớp với tên bạn đã đặt
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('account_family_members')->truncate(); // Tên bảng trung gian
        // Account::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table('account_family_members')->delete(); // Sửa tên bảng nếu bạn đặt khác
        DB::table('accounts')->delete();


        // Lấy các đối tượng Platform và FamilyMember đã được tạo
        $googlePlatform = Platform::where('name', 'Google')->first();
        $facebookPlatform = Platform::where('name', 'Facebook')->first();
        $netflixPlatform = Platform::where('name', 'Netflix')->first();

        $boTuan = FamilyMember::where('email', 'botuan@example.com')->first();
        $meLan = FamilyMember::where('email', 'melan@example.com')->first();

        // --- Tài khoản Google của Bố Tuấn ---
        if ($googlePlatform && $boTuan) {
            $accountBoTuanGoogle = Account::create([
                'platform_id' => $googlePlatform->id,
                'encrypted_username' => Crypt::encryptString('botuan.google@gmail.com'),
                'encrypted_password' => Crypt::encryptString('GooglePassword!@#'),
                'encrypted_note' => Crypt::encryptString('Tài khoản Google chính của Bố Tuấn')
            ]);
            // Liên kết tài khoản này với Bố Tuấn qua bảng trung gian
            // Điều này hoạt động nếu mối quan hệ familyMembers() trong model Account được định nghĩa đúng
            $accountBoTuanGoogle->familyMembers()->attach($boTuan->id);
        }

        // --- Tài khoản Facebook của Mẹ Lan ---
        if ($facebookPlatform && $meLan) {
            $accountMeLanFacebook = Account::create([
                'platform_id' => $facebookPlatform->id,
                'encrypted_username' => Crypt::encryptString('melan.facebook.user'),
                'encrypted_password' => Crypt::encryptString('FacebookPassSecure456'),
                'encrypted_note' => Crypt::encryptString('Tài khoản Facebook của Mẹ Lan')
            ]);
            $accountMeLanFacebook->familyMembers()->attach($meLan->id);
        }
        
        // --- Tài khoản Netflix dùng chung ---
        if ($netflixPlatform && $boTuan && $meLan) {
            $accountNetflixChung = Account::create([
                'platform_id' => $netflixPlatform->id,
                'encrypted_username' => Crypt::encryptString('giadinh@netflix.com'),
                'encrypted_password' => Crypt::encryptString('NetflixSharedPass789'),
                'encrypted_note' => Crypt::encryptString('Tài khoản Netflix cho cả gia đình')
            ]);
            // Liên kết tài khoản Netflix với cả Bố Tuấn và Mẹ Lan
            $accountNetflixChung->familyMembers()->attach([$boTuan->id, $meLan->id]);
        }
    }
}
