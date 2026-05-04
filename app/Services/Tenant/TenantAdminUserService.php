<?php
namespace App\Services\Tenant;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TenantAdminUserService
{
    public function execute(array $data, Tenant $tenant): User
    {
        tenancy()->initialize($tenant);

        try {
            $user = User::create([
                'name'     => $data['nome'],
                'email'    => $data['email'],
                'password' => Hash::make($data['senha']),
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('admin');
            }

            return $user;
        } finally {
            tenancy()->end();
        }
    }
}