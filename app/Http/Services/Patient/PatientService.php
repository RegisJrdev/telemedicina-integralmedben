<?php

namespace App\Http\Services\Patient;

use App\Models\Patient;

class PatientService
{
    public function __construct(
        private GetPatientsService $getPatientsService,
        private GetPatientDetailsService $getPatientDetailsService
    ) {}

    public function getPatients()
    {
        return $this->getPatientsService->execute();
    }

    public function getPatientDetails(Patient $patient)
    {
        return $this->getPatientDetailsService->execute($patient);
    }
}
