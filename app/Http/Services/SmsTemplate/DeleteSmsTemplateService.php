<?php

namespace App\Http\Services\SmsTemplate;

use App\Models\SmsTemplate;

class DeleteSmsTemplateService
{
    public function execute(SmsTemplate $template): void
    {
        $template->delete();
    }
}
