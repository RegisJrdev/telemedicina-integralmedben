<?php

namespace App\Http\Services\Patient;

use App\Models\Patient;

class GetPatientsService
{
    public function execute()
    {
        return Patient::with('answers.question')
            ->latest()
            ->paginate(10);
    }
}
