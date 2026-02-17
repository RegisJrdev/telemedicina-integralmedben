<?php

namespace App\Http\Services\Tenant;

use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Database\Models\Domain;

class TenantCreateService
{
    public function execute(array $data): Tenant
    {
        if (Tenant::where('id', $data['name'])->exists()) {
            throw new \Exception('Já existe uma clínica com este nome.');
        }

        $subdomain = $data['subdomain'] . '.localhost';

        if (Domain::where('domain', $subdomain)->exists()) {
            throw new \Exception('Subdomínio em uso.');
        }

        $tenantData = [
            'id'        => $data['name'],
            'name'      => $data['name'],
            'subdomain' => $data['subdomain'],
        ];

        if (isset($data['photo']) && $data['photo']) {
            $tenantData['photo_path'] = $data['photo']->store('tenants', 'public');
        }

        if (isset($data['bg_color']) && $data['bg_color']) {
            $tenantData['bg_color'] = $data['bg_color'];
        }

        if (isset($data['button_color']) && $data['button_color']) {
            $tenantData['button_color'] = $data['button_color'];
        }

        $addressFields = ['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado'];
        foreach ($addressFields as $field) {
            if (isset($data[$field])) {
                $tenantData[$field] = $data[$field];
            }
        }

        $tenant = Tenant::create($tenantData);

        $tenant->domains()->create([
            'domain' => $subdomain,
        ]);

        return $tenant;
    }
}