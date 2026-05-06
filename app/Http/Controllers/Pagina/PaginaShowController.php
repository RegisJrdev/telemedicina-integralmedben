<?php
namespace App\Http\Controllers\Pagina;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaginaShowController extends Controller
{
    public function __invoke(Tenant $tenant): Response
    {
        $tenant = Tenant::with(['details', 'details.user', 'forms'])->where('id', $tenant->id)->firstOrFail();

        return Inertia::render('Pagina/Show', [
            'tenant' => $tenant,
        ]);
    }
}
