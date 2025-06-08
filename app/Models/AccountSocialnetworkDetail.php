<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * Class AccountSocialnetworkDetail
 *
 * Model này đại diện cho bảng `account_socialnetwork_details`,
 * chứa các thông tin chi tiết dành riêng cho các tài khoản mạng xã hội.
 *
 * @package App\Models
 */
class AccountSocialnetworkDetail extends Model
{
    use HasFactory;

    /**
     * Tên bảng mà model này quản lý.
     * Laravel có thể tự động nhận diện nếu tuân thủ quy tắc,
     * nhưng khai báo tường minh sẽ giúp code rõ ràng hơn.
     *
     * @var string
     */
    protected $table = 'account_socialnetwork_details';

    /**
     * Các thuộc tính có thể được gán hàng loạt (mass-assignable).
     * Điều này cần thiết cho các phương thức như `create()` hoặc `update()`.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'mail_account_id',
        'tiktok_user_id',
        'follower_count',
        'last_login_ip',
        'status',
    ];

    /**
     * Chuyển đổi kiểu dữ liệu cho các thuộc tính.
     * Giúp đảm bảo dữ liệu luôn có định dạng đúng khi truy xuất.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'follower_count' => 'integer',
    ];

    /**
     * Định nghĩa mối quan hệ "belongsTo" với model Account.
     * Mỗi dòng chi tiết này sẽ thuộc về một tài khoản chính.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * Định nghĩa mối quan hệ "belongsTo" với model MailAccount.
     * Mỗi dòng chi tiết này có thể được liên kết với một tài khoản email.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mailAccount()
    {
        return $this->belongsTo(MailAccount::class, 'mail_account_id');
    }
}