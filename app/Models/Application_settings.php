<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application_settings extends Model
{
    /** @use HasFactory<\Database\Factories\Application_settingssFactory> */
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'application_settings'; // Khai báo rõ tên bảng nếu cần

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'setting_key',
        'encrypted_setting_value',
    ];

    /**
     * Helper method to retrieve and decrypt a setting value.
     *
     * @param string $key The setting_key to look for.
     * @param mixed $default The default value to return if the key is not found or decryption fails.
     * @return mixed The decrypted value or default.
     */
    public static function getDecryptedValue(string $key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();

        if ($setting && $setting->encrypted_setting_value) {
            try {
                // Giả định giá trị được mã hóa bằng APP_KEY của Laravel
                return Crypt::decryptString($setting->encrypted_setting_value);
            } catch (DecryptException $e) {
                // Log lỗi hoặc xử lý lỗi nếu cần thiết
                // Log::error("Lỗi giải mã Application_settings '{$key}': " . $e->getMessage());
                return $default; // Trả về giá trị mặc định nếu giải mã lỗi
            }
        }
        return $default;
    }

    /**
     * Helper method to set and encrypt a setting value.
     *
     * @param string $key The setting_key.
     * @param string $plainValue The plain text value to encrypt and store.
     * @return Application_settings|null The saved or updated setting.
     */
    public static function setEncryptedValue(string $key, string $plainValue): ?Application_settings
    {
        if (empty($plainValue)) {
            // Nếu giá trị rỗng, có thể bạn muốn xóa setting hoặc lưu null
            return self::updateOrCreate(
                ['setting_key' => $key],
                ['encrypted_setting_value' => null, 'updated_at' => now()]
            );
        }

        // Giả định mã hóa bằng APP_KEY của Laravel
        $encryptedValue = Crypt::encryptString($plainValue);

        return self::updateOrCreate(
            ['setting_key' => $key],
            ['encrypted_setting_value' => $encryptedValue, 'updated_at' => now()]
        );
    }
}
