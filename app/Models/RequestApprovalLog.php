<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestApprovalLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'LogID';
    public $timestamps = false;

    protected $fillable = [
        'RequestID',
        'StepOrder',
        'ApproverID',
        'Action',
        'Notes',
        'ActionDate',
    ];

    protected $casts = [
        'ActionDate' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(DocumentRequest::class, 'RequestID', 'RequestID');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'ApproverID', 'id');
    }
}
