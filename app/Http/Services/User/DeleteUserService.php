<?php

namespace App\Http\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DeleteUserService
{
    public function execute(User $user): void
    {
        if ($user->id === Auth::id()) {
            abort(403, 'Você não pode excluir seu próprio usuário.');
        }

        $user->delete();
    }
}
