<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'settlement_number',
        'amount',
        'settlement_date',
        'scheduled_date',
        'status',
        'completed_at',
        'notes',
        'proof_of_receipt',
        'approval_status',
        'reject_reason',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'settlement_date' => 'date',
        'scheduled_date' => 'date',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
