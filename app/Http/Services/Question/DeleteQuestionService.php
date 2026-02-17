<?php

namespace App\Http\Services\Question;

use App\Models\Question;

class DeleteQuestionService
{
    public function execute(Question $question)
    {
        $question->delete();
    }
}
