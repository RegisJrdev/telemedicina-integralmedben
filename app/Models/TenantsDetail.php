<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenantsDetail extends Model
{
    use SoftDeletes;

    protected $table = 'tenants_details';

    protected $fillable = [
        'code',
        'descricao',
        'slug',
        'form_id',
        'path_arquivos',
        'tenant_id',
        'user_id',
        'logo',
        'favicon',
        'cor_primaria',
        'cor_secundaria',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}