<?php

namespace App\Http\Services\Question;

use App\Models\Question;

class GetAllQuestionsService
{
    public function execute()
    {
        return Question::with('options')->latest()->paginate(15);
    }
}
