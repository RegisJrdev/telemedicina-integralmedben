<?php

namespace App\Http\Services\SmsTemplate;

use App\Models\SmsTemplate;

class GetSmsTemplatesService
{
    public function execute()
    {
        return SmsTemplate::with('tenants')
            ->latest()
            ->paginate(15);
    }
}
