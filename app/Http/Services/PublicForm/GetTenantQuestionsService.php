<?php

namespace App\Http\Services\PublicForm;

use App\Models\Tenant;

class GetTenantQuestionsService
{
    public function execute(string $tenantId)
    {
        return Tenant::find($tenantId)->questions()->get();
    }
}
