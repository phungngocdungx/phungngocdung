<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Email extends Model
{
    use HasFactory; // Nên thêm nếu bạn dùng factories

    protected $fillable = [
        'mail_account_id',
        'message_id',
        'subject',
        'from',
        'from_name',
        'date',
        'body_html',
        'body_text',      // <--- Thêm vào đây
        // 'body'          // Nếu bạn vẫn muốn giữ cột 'body' chung thì để, nếu không thì bỏ
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime', // Quan trọng để xử lý ngày tháng
    ];

    public function mailAccount()
    {
        return $this->belongsTo(MailAccount::class);
    }
}
