<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberOrganizationSportsDetail extends Model
{
    protected $fillable = [
        'member_organization_id',
        'emergency_contact_name',
        'emergency_contact_phone',
        'blood_type',
        'medical_conditions',
        'preferred_sports',
    ];

    protected $casts = [
        'preferred_sports' => 'array',
    ];
}
