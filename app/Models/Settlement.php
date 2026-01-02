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
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'settlement_date' => 'date',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
