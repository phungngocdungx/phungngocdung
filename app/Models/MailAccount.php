<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailAccount extends Model
{
    protected $fillable = [
        'email', 'app_password', 'imap_host', 'imap_port', 'imap_encryption'
    ];

    public function emails()
    {
        return $this->hasMany(Email::class);
    }
}
