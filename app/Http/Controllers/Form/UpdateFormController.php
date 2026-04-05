<?php
namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use App\Http\Requests\Form\UpdateFormRequest;
use App\Models\Arquivo;
use App\Models\Form;
use App\Models\FormArquivo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpdateFormController extends Controller
{
    public function __invoke(UpdateFormRequest $request, Form $form): RedirectResponse
    {
        if ($form->responses_count > 0 || $form->responses()->exists()) {
            Log::warning('Tentativa de editar formulário com respostas', [
                'form_id'         => $form->id,
                'user_id'         => $request->user()->id,
                'responses_count' => $form->responses_count,
            ]);
            return redirect()
                ->back()
                ->with('error', 'Este formulário não pode ser editado porque já possui respostas. Crie uma nova versão ou duplique o formulário.');
        }
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $form->update([
                'title'           => $validated['title'],
                'description'     => $validated['description'] ?? null,
                'categoria_id'    => $validated['categoria_id'] ?? null,
                'lei_id'          => $validated['lei_id'] ?? null,
                'status'          => $validated['status'],
                'is_public'       => $validated['is_public'] ?? false,
                'published_at'    => $validated['published_at'] ?? null,
                'primary_color'   => $validated['primary_color'] ?? '#22d3ee',
                'secondary_color' => $validated['secondary_color'] ?? '#06b6d4',
                'expires_at'      => $validated['expires_at'] ?? null,
                'response_limit'  => $validated['response_limit'] ?? null,
                'settings'        => $validated['settings'] ?? null,
            ]);
            $this->syncLogo($form, $request, $validated);
            $this->syncFields($form, $validated['fields']);
            DB::commit();
            Log::info('Formulário atualizado', [
                'form_id' => $form->id,
                'user_id' => $request->user()->id,
            ]);
            return redirect()
                ->route('forms.index')
                ->with('success', 'Formulário atualizado com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar formulário', [
                'form_id' => $form->id,
                'user_id' => $request->user()->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar formulário. Tente novamente.');
        }
    }
    private function syncLogo(Form $form, $request, array $validated): void
    {
        $logosExistentes = FormArquivo::where('form_id', $form->id)
            ->where('tipo', FormArquivo::TIPO_LOGO)
            ->with('arquivo')
            ->get();
        if ($request->boolean('remove_logo')) {
            foreach ($logosExistentes as $logoAtual) {
                $this->removerLogo($form, $logoAtual);
            }
            Log::info('Todos os logos removidos do formulário', ['form_id' => $form->id]);
            return;
        }
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            foreach ($logosExistentes as $logoAtual) {
                $this->removerLogo($form, $logoAtual);
            }
            $posicao = $validated['logo_posicao'] ?? 'centro';
            $this->processarLogo($form, $request->file('logo'), $posicao);
            return;
        }
        if ($logosExistentes->isNotEmpty() && isset($validated['logo_posicao'])) {
            $logosExistentes->first()->update([
                'posicao' => $validated['logo_posicao'],
            ]);
            Log::info('Posição do logo atualizada', [
                'form_id' => $form->id,
                'posicao' => $validated['logo_posicao'],
            ]);
        }
    }
    private function removerLogo(Form $form, FormArquivo $formArquivo): void
    {
        $arquivo = $formArquivo->arquivo;
        if ($arquivo) {
            if (Storage::disk($arquivo->disk)->exists($arquivo->caminho)) {
                Storage::disk($arquivo->disk)->delete($arquivo->caminho);
                Log::info('Arquivo deletado do storage', [
                    'caminho' => $arquivo->caminho,
                    'disk'    => $arquivo->disk,
                ]);
            }
            $arquivo->delete();
            Log::info('Registro de arquivo deletado', ['arquivo_id' => $arquivo->id]);
        }
        $formArquivo->delete();
        Log::info('Registro form_arquivo deletado', [
            'form_arquivo_id' => $formArquivo->id,
            'form_id'         => $form->id,
        ]);
    }
    private function processarLogo(Form $form, $file, string $posicao = 'centro'): void
    {
        $nomeOriginal     = $file->getClientOriginalName();
        $extensao         = $file->getClientOriginalExtension();
        $mimeType         = $file->getMimeType();
        $tamanho          = $file->getSize();
        $nomeArmazenado   = Str::uuid() . '.' . $extensao;
        $caminhoDiretorio = 'forms/logos/' . $form->id;
        $caminhoCompleto  = $caminhoDiretorio . '/' . $nomeArmazenado;
        $disk             = 'public';
        $this->criarDiretorioSeNaoExistir($caminhoDiretorio, $disk);
        $file->storeAs($caminhoDiretorio, $nomeArmazenado, $disk);
        $arquivo = Arquivo::create([
            'nome_original'   => $nomeOriginal,
            'nome_armazenado' => $nomeArmazenado,
            'caminho'         => $caminhoCompleto,
            'extensao'        => $extensao,
            'mime_type'       => $mimeType,
            'tamanho'         => $tamanho,
            'disk'            => $disk,
            'user_id'         => auth()->id(),
        ]);
        FormArquivo::create([
            'arquivo_id' => $arquivo->id,
            'form_id'    => $form->id,
            'tipo'       => FormArquivo::TIPO_LOGO,
            'posicao'    => $posicao,
        ]);
        Log::info('Novo logo processado com sucesso', [
            'form_id'    => $form->id,
            'arquivo_id' => $arquivo->id,
            'caminho'    => $caminhoCompleto,
            'posicao'    => $posicao,
        ]);
    }
    private function criarDiretorioSeNaoExistir(string $caminho, string $disk): void
    {
        $storage = Storage::disk($disk);
        if (! $storage->exists($caminho)) {
            $storage->makeDirectory($caminho);
            $caminhoCompleto = $storage->path($caminho);
            if (is_dir($caminhoCompleto)) {
                chmod($caminhoCompleto, 0755);
            }
            Log::info("Diretório criado", [
                'caminho' => $caminho,
                'disk'    => $disk,
            ]);
        }
    }
    private function syncFields(Form $form, array $fields): void
    {
        $form->fields()->delete();
        foreach ($fields as $index => $fieldData) {
            $form->fields()->create([
                'type'        => $fieldData['type'],
                'label'       => $fieldData['label'],
                'placeholder' => $fieldData['placeholder'] ?? null,
                'required'    => $fieldData['required'] ?? false,
                'options'     => in_array($fieldData['type'], ['select', 'checkbox', 'radio'])
                    ? ($fieldData['options'] ?? [])
                    : [],
                'help_text'   => $fieldData['help_text'] ?? null,
                'order'       => $fieldData['order'] ?? $index,
            ]);
        }
        Log::info('Campos sincronizados', [
            'form_id'      => $form->id,
            'total_fields' => count($fields),
        ]);
    }
}