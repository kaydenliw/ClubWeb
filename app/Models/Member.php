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

    public function charges()
    {
        return $this->belongsToMany(Charge::class, 'charge_member')
            ->withPivot('amount', 'status', 'paid_at')
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
}
