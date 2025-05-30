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
        $tables = [
            'users' => 'Users',
            'categories' => 'Categories',
            // 'products' => 'Products',
            'roles' => 'Roles',
            'permissions' => 'Permissions',
            'model_has_roles' => 'ModelHasRoles',
            'role_has_permissions' => 'RoleHasPermissions',
            'users_profiles' => 'UsersProfiles',
        ];

        // Biến $onlyTables và khối comment liên quan đã được xóa bỏ

        foreach ($tables as $table => $folder) {
            // Điều kiện kiểm tra $onlyTables đã được xóa bỏ

            // Tạo thư mục nếu chưa có
            if (!Storage::exists("data/$folder")) {
                // $this->command là đối tượng command của Laravel, nếu đây là code thuần PHP, bạn cần cách khác để log
                if (isset($this->command)) {
                    $this->command->warn("⚠️ Folder `data/$folder` not found.");
                } else {
                    echo "⚠️ Folder `data/$folder` not found.\n";
                }
                continue;
            }

            $files = Storage::files("data/$folder");

            // Lấy file mới nhất của bảng
            $jsonFiles = collect($files)->filter(
                fn($file) =>
                str_contains($file, "{$table}_") && str_ends_with($file, '.json')
            )->sortDesc()->values();

            if ($jsonFiles->isNotEmpty()) {
                $latestFile = $jsonFiles->first();
                $json = Storage::get($latestFile);
                $data = json_decode($json, true);

                // Kiểm tra nếu json_decode thất bại
                if (json_last_error() !== JSON_ERROR_NONE) {
                    if (isset($this->command)) {
                        $this->command->error("Error decoding JSON from file: $latestFile. Error: " . json_last_error_msg());
                    } else {
                        echo "Error decoding JSON from file: $latestFile. Error: " . json_last_error_msg() . "\n";
                    }
                    continue;
                }

                // Kiểm tra nếu $data không phải là mảng (trường hợp file JSON không hợp lệ)
                if (!is_array($data)) {
                    if (isset($this->command)) {
                        $this->command->error("Invalid data structure in JSON file: $latestFile. Expected an array.");
                    } else {
                        echo "Invalid data structure in JSON file: $latestFile. Expected an array.\n";
                    }
                    continue;
                }

                DB::table($table)->truncate(); // hoặc dùng delete() nếu không muốn reset id
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
    }
}

// php artisan db:seed --class=ImportSeeder
