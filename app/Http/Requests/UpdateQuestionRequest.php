<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:text,email,number,tel,date,option',
            'options' => 'required_if:type,option|array|min:1',
            'options.*' => 'required_if:type,option|string|max:255',
            'is_required' => 'nullable|boolean',
            'is_unique' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título da pergunta é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'type.required' => 'O tipo do campo é obrigatório.',
            'type.in' => 'O tipo selecionado é inválido.',
        ];
    }
}
