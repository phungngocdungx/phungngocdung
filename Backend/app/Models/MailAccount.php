<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;

class MailAccount extends Model
{
    protected $fillable = [
        'email',
        'app_password',
        'imap_host',
        'imap_port',
        'imap_encryption'
    ];

    /**
     * Set the user's password attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function setAppPasswordAttribute($value)
    {
        $this->attributes['app_password'] = Crypt::encrypt($value);
    }

    /**
     * Get the user's password attribute (decrypted).
     *
     * @return string
     */
    public function getAppPasswordAttribute($value)
    {
        try {
            $decryptedPassword = Crypt::decrypt($value);
            Log::info("Mật khẩu đã giải mã cho {$this->email}: " . ($decryptedPassword ? 'Thành công' : 'Thất bại (trống)'));
            return $decryptedPassword;
        } catch (\Exception $e) {
            Log::error("Lỗi giải mã mật khẩu cho {$this->email}: " . $e->getMessage());
            return null;
        }
    }

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    /**
     * Helper function to check if the provided password matches the stored hash.
     * This will now compare against the encrypted value.
     * You might need to adjust this if you intend to keep bcrypt hashing for verification.
     *
     * @param string $plainPassword
     * @return bool
     */
    public function checkPassword(string $plainPassword): bool
    {
        try {
            return Crypt::decrypt($this->attributes['app_password']) === $plainPassword;
        } catch (\Exception $e) {
            return false;
        }
    }
}
