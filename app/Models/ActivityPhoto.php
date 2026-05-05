<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityPhoto extends Model
{
    protected $fillable = [
        'activity_id',
        'user_id',
        'path',
        'caption',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function url(): string
    {
        return url($this->path);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    protected static function booted(): void
    {
        static::deleting(function (ActivityPhoto $photo) {
            if ($photo->path) {
                // path stored as 'public/images/activity-photos/filename.ext'
                $fullPath = public_path(ltrim($photo->path, '/'));
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
        });
    }
}
