<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Vô hiệu hóa kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Định nghĩa thứ tự các bảng để truncate (bảng con trước, bảng cha sau)
        // Đây là thứ tự đảo ngược lại so với mối quan hệ khóa ngoại
        $truncateOrder = [
            'account_socialnetwork_details', // Phụ thuộc vào accounts, mail_accounts
            'account_family_members',        // Phụ thuộc vào accounts, family_members
            'users_profiles',                // Phụ thuộc vào users
            'user_details',                  // Phụ thuộc vào users
            'model_has_roles',               // Phụ thuộc vào users, roles
            'role_has_permissions',          // Phụ thuộc vào roles, permissions
            'products',                      // Phụ thuộc vào categories
            'emails',                        // Phụ thuộc vào mail_accounts
            'accounts',                      // Phụ thuộc vào platforms
            'users',
            'permissions',
            'roles',
            'categories',
            'platforms',
            'family_members',
            'mail_accounts',
            'application_settings',
            // Các bảng không có khóa ngoại tham chiếu đến bảng khác có thể truncate an toàn
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            'migrations', // Cẩn thận với bảng migrations, không nên truncate nếu bạn muốn giữ lịch sử di chuyển
            'password_reset_tokens',
            'sessions',
        ];

        // 2. Truncate tất cả các bảng theo đúng thứ tự
        foreach ($truncateOrder as $table) {
            // Kiểm tra nếu bảng tồn tại trước khi truncate để tránh lỗi nếu một bảng không có
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
                if (isset($this->command)) {
                    $this->command->info("🗑️ Truncated table: `$table`");
                } else {
                    echo "🗑️ Truncated table: `$table`\n";
                }
            }
        }

        // 3. Định nghĩa lại mảng $tables cho việc import dữ liệu
        // Bây giờ bao gồm tất cả các bảng bạn muốn import, theo thứ tự hợp lý
        $tablesToImport = [
            'users' => 'Users',
            'permissions' => 'Permissions',
            'roles' => 'Roles',
            'categories' => 'Categories',
            'platforms' => 'Platforms',
            'family_members' => 'FamilyMembers',
            'mail_accounts' => 'MailAccounts',
            'application_settings' => 'ApplicationSettings',
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


        // 4. Import dữ liệu vào các bảng
        foreach ($tablesToImport as $table => $folder) {
            if (!Storage::exists("data/$folder")) {
                if (isset($this->command)) {
                    $this->command->warn("⚠️ Folder `data/$folder` not found.");
                } else {
                    echo "⚠️ Folder `data/$folder` not found.\n";
                }
                continue;
            }

            $files = Storage::files("data/$folder");

            $jsonFiles = collect($files)->filter(
                fn($file) =>
                str_contains($file, "{$table}_") && str_ends_with($file, '.json')
            )->sortDesc()->values();

            if ($jsonFiles->isNotEmpty()) {
                $latestFile = $jsonFiles->first();
                $json = Storage::get($latestFile);
                $data = json_decode($json, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    if (isset($this->command)) {
                        $this->command->error("Error decoding JSON from file: $latestFile. Error: " . json_last_error_msg());
                    } else {
                        echo "Error decoding JSON from file: $latestFile. Error: " . json_last_error_msg() . "\n";
                    }
                    continue;
                }

                if (!is_array($data)) {
                    if (isset($this->command)) {
                        $this->command->error("Invalid data structure in JSON file: $latestFile. Expected an array.");
                    } else {
                        echo "Invalid data structure in JSON file: $latestFile. Expected an array.\n";
                    }
                    continue;
                }

                // Không cần truncate ở đây nữa vì đã truncate ở trên
                DB::table($table)->insert($data);

                if (isset($this->command)) {
                    $this->command->info("✅ Imported `$table` from `$latestFile`");
                } else {
                    echo "✅ Imported `$table` from `$latestFile`\n";
                }
            } else {
                if (isset($this->command)) {
                    $this->command->warn("⚠️ No JSON file found for `$table` in `data/$folder`");
                } else {
                    echo "⚠️ No JSON file found for `$table` in `data/$folder`\n";
                }
            }
        }

        // 5. Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
// class ImportSeeder extends Seeder
// {
//     /**
//      * Run the database seeds.
//      */
//     public function run(): void
//     {
//         $tables = [
//             // Danh sách bảng ưu tiên
//             'users' => 'Users',
//             'permissions' => 'Permissions',
//             'roles' => 'Roles',
//             'categories' => 'Categories',
//             'platforms' => 'Platforms',
//             'family_members' => 'FamilyMembers',
//             'mail_accounts' => 'MailAccounts',
//             'application_settings' => 'ApplicationSettings',
//             // Import các bảng khác
//             // 'users_profiles' => 'UsersProfiles',
//             // 'user_details' => 'UserDetails',
//             // 'products' => 'Products',
//             // 'accounts' => 'Accounts',
//             // 'emails' => 'Emails',
//             // 'model_has_roles' => 'ModelHasRoles',
//             // 'role_has_permissions' => 'RoleHasPermissions',
//             // 'account_family_members' => 'AccountFamilyMembers',
//             // 'account_socialnetwork_details' => 'AccountSocialNetworkDetails',
//         ];

//         // Biến $onlyTables và khối comment liên quan đã được xóa bỏ

//         foreach ($tables as $table => $folder) {
//             // Điều kiện kiểm tra $onlyTables đã được xóa bỏ

//             // Tạo thư mục nếu chưa có
//             if (!Storage::exists("data/$folder")) {
//                 // $this->command là đối tượng command của Laravel, nếu đây là code thuần PHP, bạn cần cách khác để log
//                 if (isset($this->command)) {
//                     $this->command->warn("⚠️ Folder `data/$folder` not found.");
//                 } else {
//                     echo "⚠️ Folder `data/$folder` not found.\n";
//                 }
//                 continue;
//             }

//             $files = Storage::files("data/$folder");

//             // Lấy file mới nhất của bảng
//             $jsonFiles = collect($files)->filter(
//                 fn($file) =>
//                 str_contains($file, "{$table}_") && str_ends_with($file, '.json')
//             )->sortDesc()->values();

//             if ($jsonFiles->isNotEmpty()) {
//                 $latestFile = $jsonFiles->first();
//                 $json = Storage::get($latestFile);
//                 $data = json_decode($json, true);

//                 // Kiểm tra nếu json_decode thất bại
//                 if (json_last_error() !== JSON_ERROR_NONE) {
//                     if (isset($this->command)) {
//                         $this->command->error("Error decoding JSON from file: $latestFile. Error: " . json_last_error_msg());
//                     } else {
//                         echo "Error decoding JSON from file: $latestFile. Error: " . json_last_error_msg() . "\n";
//                     }
//                     continue;
//                 }

//                 // Kiểm tra nếu $data không phải là mảng (trường hợp file JSON không hợp lệ)
//                 if (!is_array($data)) {
//                     if (isset($this->command)) {
//                         $this->command->error("Invalid data structure in JSON file: $latestFile. Expected an array.");
//                     } else {
//                         echo "Invalid data structure in JSON file: $latestFile. Expected an array.\n";
//                     }
//                     continue;
//                 }

//                 DB::table($table)->truncate(); // hoặc dùng delete() nếu không muốn reset id
//                 DB::table($table)->insert($data);

//                 if (isset($this->command)) {
//                     $this->command->info("✅ Imported `$table` from `$latestFile`");
//                 } else {
//                     echo "✅ Imported `$table` from `$latestFile`\n";
//                 }
//             } else {
//                 if (isset($this->command)) {
//                     $this->command->warn("⚠️ No JSON file found for `$table` in `data/$folder`");
//                 } else {
//                     echo "⚠️ No JSON file found for `$table` in `data/$folder`\n";
//                 }
//             }
//         }
//     }
// }

// php artisan db:seed --class=ImportSeeder
