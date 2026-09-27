<?php

namespace App\Models\Portfolio;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'subject', 'budget', 'message', 'ip', 'user_agent', 'mail_sent', 'read_at'];

    protected $casts = ['mail_sent' => 'boolean', 'read_at' => 'datetime'];
}
