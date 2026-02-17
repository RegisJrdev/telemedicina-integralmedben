<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CentralPatientAnswer extends Model
{
    protected $connection = 'mysql';

    protected $table = 'central_patient_answers';

    protected $fillable = [
        'central_patient_id',
        'question_id',
        'answer',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(CentralPatient::class, 'central_patient_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
