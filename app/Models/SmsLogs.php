<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLogs extends Model
{

    protected $connection = 'mysql';

    protected $table = "sms_logs";

    protected $fillable = [
        'tenant_id',
        'patient_id',
        // 'message_id',
    ];

    public function patients() {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function tenant() {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
