<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivitySubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'user_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'status',
        'points_awarded',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
        'comment',
        'reviewer_comment',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'points_awarded' => 'integer',
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
