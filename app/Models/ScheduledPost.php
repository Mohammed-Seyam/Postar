<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduledPost extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'video_id',
        'platform',
        'publish_at',
        'caption',
        'hashtags',
        'status',
    ];

    protected $casts = [
        'publish_at' => 'datetime',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }
}
