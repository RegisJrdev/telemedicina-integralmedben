<?php

namespace App\Http\Services\Patient;

use App\Models\Patient;

class GetPatientDetailsService
{
    public function execute(Patient $patient)
    {
        $patient->load('answers.question');

        return $patient;
    }
}
