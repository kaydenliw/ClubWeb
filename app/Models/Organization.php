<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'logo',
        'email',
        'phone',
        'address',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'status',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function charges()
    {
        return $this->hasMany(Charge::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function faqs()
    {
        return $this->hasMany(FAQ::class);
    }

    public function contactTickets()
    {
        return $this->hasMany(ContactTicket::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
