<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Domain;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
    ];

    protected $appends = [
        'tenant_domain',
        'photo_url',
    ];

    public function domain()
    {
        return $this->hasMany(Domain::class , 'tenant_id');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'tenant_questions');
    }

    public static function generateDatabaseName(string $tenantName): string
    {
        return 'tenant_' . str_replace('-', '_', $tenantName);
    }

    public function getTenantDomainAttribute()
    {
        return $this->domain->first()?->domain;
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            return '/storage/' . $this->photo_path;
        }

        return null;
    }
}
