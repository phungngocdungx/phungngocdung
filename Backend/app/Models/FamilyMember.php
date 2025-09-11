<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    /** @use HasFactory<\Database\Factories\FamilyMemberFactory> */
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'master_password_hash', // Sẽ được hash, không phải mã hóa đối xứng
    ];

    /**
     * The attributes that should be hidden for serialization.
     * (Quan trọng nếu master_password_hash không nên bị lộ ra API chẳng hạn)
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'master_password_hash', // Thường thì hash mật khẩu không cần thiết phải ẩn khi serialize
                                // nhưng mật khẩu chủ thì có thể cân nhắc.
        // 'remember_token', // Nếu có cột này
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array // Cú pháp cho Laravel 10+
    {
        return [
            'email_verified_at' => 'datetime', // Nếu bạn có cột này
            // 'master_password_hash' => 'hashed', // Nếu bạn muốn Laravel tự động hash khi gán (cần logic khác khi cập nhật)
        ];
    }

    /**
     * The accounts that belong to the FamilyMember.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function accounts()
    {
        // Đảm bảo tên bảng trung gian 'account_family_members'
        // khớp với tên bảng bạn đã tạo trong migration.
        return $this->belongsToMany(Account::class, 'account_family_members')
                    ->withTimestamps(); // Tùy chọn: nếu bảng trung gian có timestamps
    }

    // Nếu bạn muốn sử dụng FamilyMember cho việc xác thực của Laravel
    // và master_password_hash là cột mật khẩu đã hash:
    public function getAuthPassword()
    {
        return $this->master_password_hash;
    }
}
