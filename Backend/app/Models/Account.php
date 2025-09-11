<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform_id',
        'encrypted_username',
        'encrypted_password',
        'encrypted_password_2', // Vẫn giữ trong fillable
        'encrypted_note',
    ];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function familyMembers()
    {
        return $this->belongsToMany(FamilyMember::class, 'account_family_members')
            ->withTimestamps();
    }

    public function socialnetworkDetail()
    {
        return $this->hasOne(AccountSocialnetworkDetail::class, 'account_id');
    }

    // --- ACCESSORS ĐỂ GIẢI MÃ ---
    public function getUsernameAttribute()
    {
        try {
            return Crypt::decryptString($this->attributes['encrypted_username']);
        } catch (DecryptException $e) {
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

    // XÓA HÀM getPassword2Attribute() Ở ĐÂY

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

    public function getPassword2Attribute()
    {
        $value = $this->attributes['encrypted_password_2'] ?? null;

        if (empty($value)) {
            return null; // Trả về null nếu không có giá trị
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return 'Không thể giải mã!';
        }
    }

    // --- MUTATORS ĐỂ MÃ HÓA TRƯỚC KHI LƯU ---
    public function setUsernameAttribute($value)
    {
        $this->attributes['encrypted_username'] = Crypt::encryptString($value);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['encrypted_password'] = Crypt::encryptString($value);
    }

    // XÓA HÀM setPassword2Attribute() Ở ĐÂY

    public function setNoteAttribute($value)
    {
        if ($value !== null) {
            $this->attributes['encrypted_note'] = Crypt::encryptString($value);
        } else {
            $this->attributes['encrypted_note'] = null;
        }
    }
}
