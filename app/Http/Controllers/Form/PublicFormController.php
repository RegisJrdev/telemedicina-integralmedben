<?php
namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormArquivo;
use App\Models\FormResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class PublicFormController extends Controller
{
    private function canAcceptResponses(Form $form): bool
    {
        return $form->status === 'ativo';
    }

    private function getLogoData(Form $form): ?array
    {
        $logoArquivo = $form->arquivos->first();
        if (! $logoArquivo) {
            return null;
        }
        return [
            'url'     => $logoArquivo->url,
            'posicao' => $logoArquivo->pivot->posicao ?? 'centro',
        ];
    }

    // ⭐ Converte data brasileira (DD/MM/YYYY) para formato ISO (YYYY-MM-DD) para salvar
    private function convertBrazilianDateToISO(string $date): string
    {
        $parts = explode('/', $date);
        if (count($parts) === 3) {
            return "{$parts[2]}-{$parts[1]}-{$parts[0]}";
        }
        return $date;
    }

    // ⭐ Verifica se é campo de data
    private function isDateField($field): bool
    {
        $label = strtolower($field->label);
        return $field->type === 'date' ||
        str_contains($label, 'nascimento') ||
        str_contains($label, 'data') ||
        str_contains($label, 'birth');
    }

    // ⭐ Verifica se é campo de CPF
    private function isCPFField($field): bool
    {
        $label = strtolower($field->label);
        return $field->type === 'cpf' ||
        str_contains($label, 'cpf') ||
        str_contains($label, 'c.p.f');
    }

    public function show(string $slug): Response
    {
        try {
            $form = Form::where('slug', $slug)
                ->where('is_public', true)
                ->with(['fields' => fn($q) => $q->orderBy('order')])
                ->with(['arquivos' => fn($q) => $q
                        ->wherePivot('tipo', FormArquivo::TIPO_LOGO)
                        ->withPivot('posicao', 'tipo'),
                ])
                ->first();
            if (! $form) {
                Log::warning('Formulário não encontrado', [
                    'slug' => $slug,
                    'ip'   => request()->ip(),
                ]);
                abort(404, 'Formulário não encontrado.');
            }
            $logoData = $this->getLogoData($form);
            if (! $this->canAcceptResponses($form)) {
                $statusLabels = [
                    'rascunho'  => 'em edição (rascunho)',
                    'pausado'   => 'pausado',
                    'encerrado' => 'encerrado',
                ];
                $label = $statusLabels[$form->status] ?? $form->status;
                Log::info('Tentativa de acesso a formulário não ativo', [
                    'form_id' => $form->id,
                    'slug'    => $slug,
                    'status'  => $form->status,
                    'ip'      => request()->ip(),
                ]);
                return Inertia::render('Form/Public/Show', [
                    'form'        => [
                        'title'                   => $form->title,
                        'status'                  => $form->status,
                        'statusLabel'             => $label,
                        'primary_color'           => $form->primary_color,
                        'secondary_color'         => $form->secondary_color,
                        'lei'                     => $form->lei,
                        'logo'                    => $logoData,
                        'btn_confirmar_descricao' => $form->btn_confirmar_descricao ?? null,
                        'sub_descricao'           => $form->sub_descricao ?? null,
                        'observacao'              => $form->observacao ?? null,
                        'message'                 => "Este formulário está {$label} e não pode receber respostas no momento.",
                        'instruction' => 'Para permitir o preenchimento, altere o status para "Ativo" nas configurações.',
                        'canActivate' => true,
                    ],
                ]);
            }
            if ($form->expires_at && $form->expires_at <= now()) {
                Log::info('Tentativa de acesso a formulário expirado', [
                    'form_id'    => $form->id,
                    'slug'       => $slug,
                    'expires_at' => $form->expires_at,
                ]);
                return Inertia::render('Form/Public/Show', [
                    'form' => [
                        'title'                   => $form->title,
                        'slug'                    => $slug,
                        'status'                  => 'expirado',
                        'statusLabel'             => 'expirado',
                        'primary_color'           => $form->primary_color,
                        'secondary_color'         => $form->secondary_color,
                        'lei'                     => $form->lei,
                        'logo'                    => $logoData,
                        'btn_confirmar_descricao' => $form->btn_confirmar_descricao ?? null,
                        'sub_descricao'           => $form->sub_descricao ?? null,
                        'observacao'              => $form->observacao ?? null,

                        'message'                 => 'Este formulário expirou e não está mais disponível para respostas.',
                        'instruction'             => 'Renove a data de expiração nas configurações para reativá-lo.',
                        'canActivate'             => false,
                    ],
                ]);
            }
            if ($form->response_limit && $form->responses_count >= $form->response_limit) {
                Log::info('Formulário atingiu limite de respostas', [
                    'form_id'         => $form->id,
                    'slug'            => $slug,
                    'response_limit'  => $form->response_limit,
                    'responses_count' => $form->responses_count,
                ]);
                return Inertia::render('Form/Public/Show', [
                    'form' => [
                        'title'                   => $form->title,
                        'primary_color'           => $form->primary_color,
                        'secondary_color'         => $form->secondary_color,
                        'lei'                     => $form->lei,
                        'btn_confirmar_descricao' => $form->btn_confirmar_descricao ?? null,
                        'sub_descricao'           => $form->sub_descricao ?? null,
                        'observacao'              => $form->observacao ?? null,
                        'status'                  => 'limite_atingido',
                        'statusLabel'             => 'com limite atingido',
                        'message'                 => 'Este formulário atingiu o limite máximo de respostas.',
                        'instruction'             => 'Aumente o limite de respostas nas configurações ou crie um novo formulário.',
                        'canActivate'             => false,
                        'logo'                    => $logoData,
                    ],
                ]);
            }
            return Inertia::render('Form/Public/Show', [
                'form' => [
                    'id'                      => $form->id,
                    'title'                   => $form->title,
                    'slug'                    => $slug,
                    'description'             => $form->description,
                    'expires_at'              => $form->expires_at,
                    'response_limit'          => $form->response_limit,
                    'responses_count'         => $form->responses_count,
                    'is_public'               => $form->is_public,
                    'primary_color'           => $form->primary_color,
                    'secondary_color'         => $form->secondary_color,
                    'lei'                     => $form->lei,
                    'status'                  => $form->status,
                    'logo'                    => $logoData,
                    'btn_confirmar_descricao' => $form->btn_confirmar_descricao ?? null,
                    'sub_descricao'           => $form->sub_descricao ?? null,
                    'observacao'              => $form->observacao ?? null,
                    'fields'                  => $form->fields->map(fn($f) => [
                        'id'          => $f->id,
                        'type'        => $f->type,
                        'label'       => $f->label,
                        'placeholder' => $f->placeholder,
                        'required'    => $f->required,
                        'options'     => $f->options,
                        'help_text'   => $f->help_text,
                        'is_date'     => $this->isDateField($f),
                        'is_cpf'      => $this->isCPFField($f),
                    ]),
                ],
            ]);
        } catch (HttpException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Erro ao exibir formulário', [
                'slug'  => $slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            abort(500, 'Erro ao carregar o formulário. Tente novamente mais tarde.');
        }
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $form = Form::where('slug', $slug)
                ->where('is_public', true)
                ->with('fields')
                ->lockForUpdate()
                ->first();
            if (! $form) {
                abort(404, 'Formulário não encontrado.');
            }
            if (! $this->canAcceptResponses($form)) {
                DB::rollBack();
                Log::warning('Tentativa de envio para formulário não ativo', [
                    'form_id' => $form->id,
                    'slug'    => $slug,
                    'status'  => $form->status,
                    'ip'      => $request->ip(),
                ]);
                return redirect()
                    ->back()
                    ->with('error', "Este formulário está {$form->status} e não pode receber respostas. Entre em contato com o administrador.");
            }
            if ($form->expires_at && $form->expires_at <= now()) {
                DB::rollBack();
                abort(403, 'Este formulário expirou.');
            }
            if ($form->response_limit && $form->responses_count >= $form->response_limit) {
                DB::rollBack();
                abort(403, 'Limite de respostas atingido.');
            }

            $rules    = [];
            $messages = [];

            foreach ($form->fields as $field) {
                $fieldRules = [];
                if ($field->required) {
                    $fieldRules[]                              = 'required';
                    $messages["answers.{$field->id}.required"] = "O campo \"{$field->label}\" é obrigatório.";
                } else {
                    $fieldRules[] = 'nullable';
                }

                switch ($field->type) {
                    case 'email':
                        $fieldRules[] = 'email:rfc,dns';
                        break;
                    case 'number':
                        $fieldRules[] = 'numeric';
                        break;
                    case 'date':
                        // ⭐ VALIDAÇÃO: Espera formato brasileiro DD/MM/YYYY
                        $fieldRules[]                                 = 'date_format:d/m/Y';
                        $messages["answers.{$field->id}.date_format"] = "O campo \"{$field->label}\" deve estar no formato DD/MM/AAAA.";
                        break;
                    case 'cpf':
                        // ⭐ VALIDAÇÃO NUMERIC PARA CPF (11 dígitos)
                        $fieldRules[]                             = 'numeric';
                        $fieldRules[]                             = 'digits:11';
                        $messages["answers.{$field->id}.numeric"] = "O campo \"{$field->label}\" deve conter apenas números.";
                        $messages["answers.{$field->id}.digits"]  = "O campo \"{$field->label}\" deve ter exatamente 11 dígitos.";
                        break;
                }

                $rules["answers.{$field->id}"] = $fieldRules;
            }

            $validated = $request->validate($rules, $messages);

            // ⭐ PROCESSA RESPOSTAS: Converte datas brasileiras para ISO antes de salvar
            $processedAnswers = [];
            foreach ($form->fields as $field) {
                $answer = $validated['answers'][$field->id] ?? null;

                if ($answer && $this->isDateField($field)) {
                    // Converte DD/MM/YYYY -> YYYY-MM-DD para salvar no banco
                    $processedAnswers[$field->id] = $this->convertBrazilianDateToISO($answer);
                } else {
                    // CPF e outros campos: mantém como está
                    $processedAnswers[$field->id] = $answer;
                }
            }

            FormResponse::create([
                'form_id'    => $form->id,
                'user_id'    => auth()->id(),
                'answers'    => $processedAnswers,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $form->increment('responses_count');
            DB::commit();

            return redirect()
                ->route('forms.public.thanks', $slug)
                ->with('success', 'Resposta enviada com sucesso!');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao salvar resposta', [
                'slug'  => $slug,
                'error' => $e->getMessage(),
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao enviar resposta. Tente novamente.');
        }
    }

    public function thanks(string $slug): Response
    {
        $form = Form::where('slug', $slug)
            ->where('is_public', true)
            ->firstOrFail();
        return Inertia::render('Form/Public/Thanks', [
            'form' => [
                'title'           => $form->title,
                'description'     => $form->description,
                'slug'            => $form->slug,
                'primary_color'   => $form->primary_color,
                'secondary_color' => $form->secondary_color,
                'lei'             => $form->lei,
            ],
        ]);
    }
}