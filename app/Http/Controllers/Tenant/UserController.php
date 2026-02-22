<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Services\User\UserService;
use App\Models\Tenant;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index()
    {
        $users = $this->userService->getUsers();
        $tenant = Tenant::find(tenant('id'));

        return Inertia::render('User/Index', [
            'users' => $users,
            'tenantName' => $tenant->name,
            'tenantPhoto' => $tenant->photo_url,
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->create($request->validated());

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->update($user, $request->validated());

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        $this->userService->delete($user);

        return redirect()->route('users.index')->with('success', 'Usuário excluído com sucesso.');
    }
}
