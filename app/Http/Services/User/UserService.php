<?php

namespace App\Http\Services\User;

use App\Models\User;

class UserService
{
    public function __construct(
        private GetUsersService $getUsersService,
        private CreateUserService $createUserService,
        private UpdateUserService $updateUserService,
        private DeleteUserService $deleteUserService,
    ) {}

    public function getUsers()
    {
        return $this->getUsersService->execute();
    }

    public function create(array $data): User
    {
        return $this->createUserService->execute($data);
    }

    public function update(User $user, array $data): User
    {
        return $this->updateUserService->execute($user, $data);
    }

    public function delete(User $user): void
    {
        $this->deleteUserService->execute($user);
    }
}
