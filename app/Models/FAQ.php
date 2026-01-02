<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FAQ extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'faqs';

    protected $fillable = [
        'organization_id',
        'question',
        'answer',
        'category',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
