<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'platform_id',
        'encrypted_username',
        'encrypted_password',
        'encrypted_note',
    ];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function familyMembers()
    {
        // Đảm bảo tên bảng trung gian 'account_family_members'
        // khớp với tên bảng bạn đã tạo trong migration.
        return $this->belongsToMany(FamilyMember::class, 'account_family_members')
                    ->withTimestamps();
    }

    // --- ACCESSORS ĐỂ GIẢI MÃ ---
    public function getUsernameAttribute()
    {
        try {
            // CẢNH BÁO: Crypt::decryptString() mặc định dùng APP_KEY.
            // Cần cơ chế dùng khóa giải mã riêng của từng FamilyMember cho ứng dụng thực tế.
            return Crypt::decryptString($this->attributes['encrypted_username']);
        } catch (DecryptException $e) {
            // Log lỗi nếu cần: Log::error("Lỗi giải mã username cho Account ID {$this->id}: " . $e->getMessage());
            return '[Không thể giải mã]'; 
        }
    }

    public function getPasswordAttribute()
    {
        try {
            return Crypt::decryptString($this->attributes['encrypted_password']);
        } catch (DecryptException $e) {
            return '[Không thể giải mã]';
        }
    }

    public function getNoteAttribute()
    {
        if (empty($this->attributes['encrypted_note'])) {
            return null;
        }
        try {
            return Crypt::decryptString($this->attributes['encrypted_note']);
        } catch (DecryptException $e) {
            return '[Không thể giải mã]';
        }
    }

    /**
     * Định nghĩa mối quan hệ "hasOne" với model AccountSocialnetworkDetail.
     * Mỗi tài khoản có thể có một dòng chi tiết mạng xã hội.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function socialnetworkDetail()
    {
        return $this->hasOne(AccountSocialnetworkDetail::class, 'account_id');
    }
}
