<?php

namespace App\Http\Services\PublicForm;

class PublicFormService
{
    public function __construct(
        private GetTenantQuestionsService $getTenantQuestionsService,
        private StorePatientService $storePatientService
    ) {}

    public function getTenantQuestions(string $tenantId)
    {
        return $this->getTenantQuestionsService->execute($tenantId);
    }

    public function storePatient(array $data)
    {
        return $this->storePatientService->execute($data);
    }
}
