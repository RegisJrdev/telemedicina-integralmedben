<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            [
                'title' => 'Nome Completo',
                'type' => 'text',
                'options' => null,
            ],
            [
                'title' => 'N° Cartão',
                'type' => 'number',
                'options' => null,
            ],
            [
                'title' => 'Plano',
                'type' => 'option',
                'options' => [
                    ['label' => 'Básico', 'value' => 'basico', 'order' => 0],
                    ['label' => 'Intermediário', 'value' => 'intermediario', 'order' => 1],
                    ['label' => 'Premium', 'value' => 'premium', 'order' => 2],
                ],
            ],
            [
                'title' => 'CPF',
                'type' => 'text',
                'options' => null,
            ],
            [
                'title' => 'WhatsApp',
                'type' => 'tel',
                'options' => null,
            ],
            [
                'title' => 'E-mail',
                'type' => 'email',
                'options' => null,
            ],
            [
                'title' => 'Data de Nascimento',
                'type' => 'date',
                'options' => null,
            ],
        ];

        foreach ($questions as $data) {
            Question::firstOrCreate(
                ['title' => $data['title']],
                [
                    'type' => $data['type'],
                    'options' => $data['options'],
                ]
            );
        }
    }
}
