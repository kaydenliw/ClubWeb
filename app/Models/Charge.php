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
        'image',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
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
            ->withPivot('amount', 'status', 'paid_at')
            ->withTimestamps();
    }
}
