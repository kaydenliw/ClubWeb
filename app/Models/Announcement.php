<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'created_by',
        'title',
        'content',
        'scheduled_at',
        'is_published',
        'published_at',
        'publish_date',
        'approval_status',
        'reject_reason',
        'is_highlighted',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'publish_date' => 'datetime',
        'is_highlighted' => 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeScheduled($query)
    {
        return $query->where('is_published', false)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>', now());
    }
}
