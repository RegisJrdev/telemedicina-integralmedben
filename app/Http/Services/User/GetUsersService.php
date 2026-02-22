<?php

namespace App\Http\Services\User;

use App\Models\User;

class GetUsersService
{
    public function execute()
    {
        return User::latest()->paginate(15);
    }
}
