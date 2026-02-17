<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'central_patient_id',
    ];

    public function centralPatient(): BelongsTo
    {
        return $this->belongsTo(CentralPatient::class, 'central_patient_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(PatientAnswer::class);
    }
}
