<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql';

    protected $fillable = [
        'title',
        'type',
        'options',
        'is_required',
        'is_unique',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_required' => 'boolean',
        'is_unique' => 'boolean',
        'options' => 'array',
    ];

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'tenant_questions');
    }
}
