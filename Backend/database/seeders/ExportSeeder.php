<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = now()->timestamp;

        // Danh sách bảng và thư mục lưu trữ
        $tables = [
            // Danh sách bảng ưu tiên
            'users' => 'Users',
            'permissions' => 'Permissions',
            'roles' => 'Roles',
            'categories' => 'Categories',
            'platforms' => 'Platforms',
            'family_members' => 'FamilyMembers',
            'mail_accounts' => 'MailAccounts',
            'application_settings' => 'ApplicationSettings',
            // Import các bảng khác
            'users_profiles' => 'UsersProfiles',
            'user_details' => 'UserDetails',
            'products' => 'Products',
            'accounts' => 'Accounts',
            'emails' => 'Emails',
            'model_has_roles' => 'ModelHasRoles',
            'role_has_permissions' => 'RoleHasPermissions',
            'account_family_members' => 'AccountFamilyMembers',
            'account_socialnetwork_details' => 'AccountSocialNetworkDetails',
        ];
        
        foreach ($tables as $table => $folder) {
            // Tạo thư mục nếu chưa tồn tại
            if (!Storage::exists("data/$folder")) {
                Storage::makeDirectory("data/$folder");
            }

            // Export dữ liệu
            $data = DB::table($table)->get();
            $fileName = "{$table}_{$timestamp}.json";
            Storage::put("data/$folder/$fileName", $data->toJson(JSON_PRETTY_PRINT));

            $this->command->info("✅ Exported `$table` to `data/$folder/$fileName`");

            // === XÓA FILE CŨ THỨ 3 TRỞ ĐI ===
            $files = Storage::files("data/$folder");

            // Lọc file có tên dạng đúng cho bảng này
            $jsonFiles = collect($files)->filter(
                fn($file) =>
                str_contains($file, "{$table}_") && str_ends_with($file, '.json')
            )->sortDesc(); // Mới nhất lên đầu

            // Nếu có hơn 2 file thì xóa file cũ thứ 3 trở đi
            if ($jsonFiles->count() > 2) {
                $oldFiles = $jsonFiles->slice(2); // từ file thứ 3 trở đi
                foreach ($oldFiles as $oldFile) {
                    Storage::delete($oldFile);
                    $this->command->warn("🗑️ Deleted old file: $oldFile");
                }
            }
        }
    }
}
// php artisan db:seed --class=ExportSeeder 