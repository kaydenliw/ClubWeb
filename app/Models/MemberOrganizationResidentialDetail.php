<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberOrganizationResidentialDetail extends Model
{
    protected $fillable = [
        'member_organization_id',
        'unit_number',
        'block',
        'floor',
        'address_line_1',
        'address_line_2',
        'postcode',
        'city',
        'state',
    ];
}
