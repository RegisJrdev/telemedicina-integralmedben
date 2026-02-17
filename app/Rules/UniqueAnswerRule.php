<?php

namespace App\Rules;

use App\Models\CentralPatient;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueAnswerRule implements ValidationRule
{
    public function __construct(private int $questionId) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value || trim((string) $value) === '') {
            return;
        }

        $cpf = preg_replace('/\D/', '', $value);

        $exists = CentralPatient::where('cpf', $cpf)->exists();

        if ($exists) {
            $fail('Este paciente já foi cadastrado anteriormente.');
        }
    }
}
