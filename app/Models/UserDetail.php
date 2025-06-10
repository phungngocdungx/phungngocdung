<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $table = 'user_details';

    protected $fillable = [
        'user_id',
        'id_number',
        'id_issued_date',
        'id_issued_place',
        'marital_status',
        'nationality',
        'instagram_url',
        'linkedin_url',
        'tiktok_url',
        'company_name',
        'company_address',
        'working_status',
        'shipping_note',
        'preferred_payment',
        'points',
        'slug',
        'status',
        'last_login_at',
        'device_info',
    ];

    protected $casts = [
        'id_issued_date' => 'date',
        'last_login_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}