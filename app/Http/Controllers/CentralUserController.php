<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Services\User\UserService;
use App\Models\User;
use Inertia\Inertia;

class CentralUserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index()
    {
        $users = $this->userService->getUsers();

        return Inertia::render('CentralUser/Index', [
            'users' => $users,
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->create($request->validated());

        return redirect()->route('central-users.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->update($user, $request->validated());

        return redirect()->route('central-users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        $this->userService->delete($user);

        return redirect()->route('central-users.index')->with('success', 'Usuário excluído com sucesso.');
    }
}
