<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberOrganizationCarDetail extends Model
{
    protected $fillable = [
        'member_organization_id',
        'car_brand',
        'car_model',
        'car_plate',
        'car_color',
        'car_year',
    ];
}
