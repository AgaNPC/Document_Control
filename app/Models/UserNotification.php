<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $primaryKey = 'NotificationID';

    protected $fillable = [
        'UserID',
        'RequestID',
        'Title',
        'Message',
        'IsRead',
        'IsHandled',
    ];

    protected $casts = [
        'IsRead' => 'boolean',
        'IsHandled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'id');
    }

    public function request()
    {
        return $this->belongsTo(DocumentRequest::class, 'RequestID', 'RequestID');
    }
}
