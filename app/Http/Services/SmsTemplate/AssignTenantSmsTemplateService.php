<?php

namespace App\Http\Services\SmsTemplate;

use App\Models\SmsTemplate;

class AssignTenantSmsTemplateService
{
    public function execute(SmsTemplate $template, array $tenantIds): void
    {
        $template->tenants()->sync($tenantIds);
    }
}
