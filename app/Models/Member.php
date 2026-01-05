<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'phone',
        'car_brand',
        'car_model',
        'car_plate',
        'status',
        'synced_to_accounting',
        'accounting_sync_at',
    ];

    protected $casts = [
        'synced_to_accounting' => 'boolean',
        'accounting_sync_at' => 'datetime',
        'date_of_birth' => 'date',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'member_organization')
            ->withPivot('joined_at', 'status', 'role', 'membership_number', 'notes')
            ->withTimestamps();
    }

    public function charges()
    {
        return $this->belongsToMany(Charge::class, 'charge_member')
            ->withPivot('amount', 'status', 'paid_at', 'next_renewal_date')
            ->withTimestamps();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function contactTickets()
    {
        return $this->hasMany(ContactTicket::class);
    }

    public function getPaymentStatusAttribute()
    {
        // Get active recurring charges only
        $activeRecurringCharge = $this->charges()
            ->whereIn('recurring_frequency', ['monthly', 'bi-monthly', 'semi-annually', 'annually'])
            ->wherePivot('status', '!=', 'paid')
            ->orderBy('charge_member.next_renewal_date', 'asc')
            ->first();

        if (!$activeRecurringCharge) {
            return ['status' => 'na', 'label' => 'N/A', 'color' => 'gray'];
        }

        $nextRenewalDate = $activeRecurringCharge->pivot->next_renewal_date;

        if (!$nextRenewalDate) {
            return ['status' => 'na', 'label' => 'N/A', 'color' => 'gray'];
        }

        $daysUntilDue = now()->diffInDays($nextRenewalDate, false);

        if ($daysUntilDue < 0) {
            return ['status' => 'overdue', 'label' => 'Payment Due', 'color' => 'red'];
        } elseif ($daysUntilDue <= 7) {
            return ['status' => 'due_soon', 'label' => 'Due in 7 days', 'color' => 'yellow'];
        }

        return ['status' => 'paid', 'label' => 'Paid', 'color' => 'green'];
    }

    public function getNextRenewalDateAttribute()
    {
        $activeRecurringCharge = $this->charges()
            ->whereIn('recurring_frequency', ['monthly', 'bi-monthly', 'semi-annually', 'annually'])
            ->orderBy('charge_member.next_renewal_date', 'asc')
            ->first();

        return $activeRecurringCharge ? $activeRecurringCharge->pivot->next_renewal_date : null;
    }

    public function getChargesDisplayAttribute()
    {
        return $this->charges()
            ->get()
            ->pluck('recurring_frequency')
            ->map(function($freq) {
                return ucwords(str_replace('-', ' ', $freq));
            })
            ->unique()
            ->implode(', ');
    }
}
