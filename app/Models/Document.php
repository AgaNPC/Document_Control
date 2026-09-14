<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $primaryKey = 'DocumentID';

    protected $fillable = [
        'DocNumber',
        'Title',
        'CompanyID',
        'Department',
        'Category',
        'CurrentRevision',
        'Status',
        'ConfidentialityLevel',
        'FilePath',
    ];

    public function requests()
    {
        return $this->hasMany(DocumentRequest::class, 'TargetDocumentID', 'DocumentID');
    }
}
