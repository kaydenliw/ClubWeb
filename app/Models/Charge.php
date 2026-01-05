<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Charge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'amount',
        'type',
        'recurring_frequency',
        'is_recurring',
        'recurring_months',
        'image',
        'status',
        'platform_fee_percentage',
        'platform_fee_operator',
        'platform_fee_fixed',
        'approval_status',
        'reject_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_recurring' => 'boolean',
        'scheduled_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'charge_member')
            ->withPivot('amount', 'status', 'paid_at', 'next_renewal_date')
            ->withTimestamps();
    }
}
