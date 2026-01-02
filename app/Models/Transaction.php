<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'member_id',
        'charge_id',
        'transaction_number',
        'amount',
        'type',
        'payment_method',
        'status',
        'notes',
        'synced_to_accounting',
        'synced_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'synced_to_accounting' => 'boolean',
        'synced_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function charge()
    {
        return $this->belongsTo(Charge::class);
    }
}
