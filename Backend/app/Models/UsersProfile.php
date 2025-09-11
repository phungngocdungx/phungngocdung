<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersProfile extends Model
{
    use HasFactory;

    protected $table = 'users_profiles';

    protected $fillable = [
        'user_id',
        'avatar',
        'phone',
        'address',
        'city',
        'district',
        'ward',
        'country',
        'birthday',
        'gender',
        'facebook_url',
        'zalo',
        'bio',
        'job_title',
    ];

    protected $casts = [
        'birthday' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}