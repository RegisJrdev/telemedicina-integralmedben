<?php 

namespace App\Http\Services\Question;

use App\Models\Tenant;

class AssignTenantQuestionService
{
    public function execute($tenantId, $questions)
    {
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            throw new \Exception('Tenant não encontrado');
        }

        $tenant->questions()->sync($questions);

        return $tenant;
    }
}