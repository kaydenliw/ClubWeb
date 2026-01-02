<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'member_id',
        'ticket_number',
        'subject',
        'message',
        'status',
        'priority',
        'category',
        'reply',
        'replied_at',
        'first_response_at',
        'resolved_at',
        'first_response_time_minutes',
        'resolution_time_minutes',
        'assigned_to',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function calculateFirstResponseTime()
    {
        if ($this->first_response_at) {
            return $this->created_at->diffInMinutes($this->first_response_at);
        }
        return null;
    }

    public function calculateResolutionTime()
    {
        if ($this->resolved_at) {
            return $this->created_at->diffInMinutes($this->resolved_at);
        }
        return null;
    }

    public function getFirstResponseTimeFormatted()
    {
        if (!$this->first_response_time_minutes) {
            return 'N/A';
        }

        $hours = floor($this->first_response_time_minutes / 60);
        $minutes = $this->first_response_time_minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        return "{$minutes}m";
    }

    public function getResolutionTimeFormatted()
    {
        if (!$this->resolution_time_minutes) {
            return 'N/A';
        }

        $hours = floor($this->resolution_time_minutes / 60);
        $minutes = $this->resolution_time_minutes % 60;

        if ($hours > 24) {
            $days = floor($hours / 24);
            $remainingHours = $hours % 24;
            return "{$days}d {$remainingHours}h";
        }

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        return "{$minutes}m";
    }
}
