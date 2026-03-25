<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpandedFormResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_id',
        'responses',
    ];

    protected $casts = [
        'responses' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
