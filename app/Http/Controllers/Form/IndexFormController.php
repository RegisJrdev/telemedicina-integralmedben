<?php
namespace App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IndexFormController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $forms = Form::query()
            ->with('user:id,name')
            ->when($user, function ($query) use ($user) {
                if (! $user->hasAnyRole(['Admin', 'Manager'])) {
                    $query->where(function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                            ->orWhere('is_public', true);
                    });
                }
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Form/Index', [
            'forms'         => $forms,
            'filters'       => $request->only(['status', 'search']),
            'statusOptions' => [
                ['value' => '', 'label' => 'Todos'],
                ['value' => 'rascunho', 'label' => 'Rascunho'],
                ['value' => 'ativo', 'label' => 'Ativo'],
                ['value' => 'pausado', 'label' => 'Pausado'],
                ['value' => 'encerrado', 'label' => 'Encerrado'],
            ],
            // Agora vai funcionar!
            'can'           => [
                'create' => $user->can('forms.create'),
                'edit'   => $user->can('forms.edit'),
                'delete' => $user->can('forms.delete'),
                'manage' => $user->hasAnyRole(['Admin', 'Manager']),
            ],
        ]);
    }
}