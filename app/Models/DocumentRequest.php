<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'RequestID';

    protected $fillable = [
        'RequestType',
        'TargetDocumentID',
        'DocNumber',
        'Title',
        'Department',
        'Category',
        'Reason',
        'TempFilePath',
        'CopyCount',
        'PlacementLocation',
        'CurrentStepOrder',
        'CurrentStatus',
        'RequestedBy',
    ];

    public function targetDocument()
    {
        return $this->belongsTo(Document::class, 'TargetDocumentID', 'DocumentID');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'RequestedBy', 'id');
    }

    public function approvalLogs()
    {
        return $this->hasMany(RequestApprovalLog::class, 'RequestID', 'RequestID');
    }
}
