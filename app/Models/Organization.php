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
        'organization_type_id',
        'logo',
        'email',
        'phone',
        'pic_name',
        'address',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'pending_bank_name',
        'pending_bank_account_number',
        'pending_bank_account_holder',
        'bank_details_status',
        'bank_details_reject_reason',
        'status',
        'platform_fee_percentage',
        'platform_fee_operator',
        'platform_fee_fixed',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function organizationType()
    {
        return $this->belongsTo(OrganizationType::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function membersList()
    {
        return $this->belongsToMany(Member::class, 'member_organization')
            ->withPivot('joined_at', 'status', 'role', 'membership_number', 'notes')
            ->withTimestamps();
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
