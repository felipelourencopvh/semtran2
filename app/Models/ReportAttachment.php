<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportAttachment extends Model
{
    protected $table = 'report_attachments';

    protected $fillable = [
        'report_id', 'path', 'original_name', 'mime', 'size', 'uploaded_by',
    ];

    public function report() {
        return $this->belongsTo(Report::class);
    }

    public function uploader() {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
