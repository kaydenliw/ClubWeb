<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'required_fields',
    ];

    protected $casts = [
        'required_fields' => 'array',
    ];

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }
}
