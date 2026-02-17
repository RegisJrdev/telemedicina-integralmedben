<?php

namespace App\Http\Services\Tenant;

use App\Models\Question;
use App\Models\Tenant;
use Inertia\Inertia;

class TenantAllService
{
    public function execute()
    {
        $tenants = Tenant::query()
            ->with([
                'domain' => fn ($query) =>
                    $query->select('id', 'tenant_id', 'domain'),
                'questions'
            ])
            ->latest()
            ->paginate();

        $questions = Question::where('is_active', true)->get();

        return Inertia::render('Dashboard', [
            'tenants' => $tenants,
            'questions' => $questions
        ]);
    }
}
