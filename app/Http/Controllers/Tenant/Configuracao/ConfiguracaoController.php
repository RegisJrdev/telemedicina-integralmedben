<?php
namespace App\Http\Controllers\Tenant\Configuracao;

use App\Http\Controllers\Controller;
use App\Models\TenantsDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ConfiguracaoController extends Controller
{
    private function tenantId(): string
    {
        return (string) tenant()->id;
    }

    private function logoPath(string $fileName): string
    {
        return $this->tenantId() . '/' . $fileName;
    }

    /**
     * Gera URL usando o host atual do request (funciona com subdomínios)
     */
    private function tenantUrl(string $path): string
    {
        // Pega apenas o path relativo (ex: /storage/tenants/med_bem/logo.png)
        $relativePath = parse_url(Storage::disk('tenants')->url($path), PHP_URL_PATH);

        // Monta com o host atual do request (inclui subdomínio e porta)
        return request()->getSchemeAndHttpHost() . $relativePath;
    }

    public function index(): Response
    {
        $tenantDetail = TenantsDetail::firstOrCreate([
            'tenant_id' => $this->tenantId(),
        ]);

        $logoUrl = null;
        if ($tenantDetail->logo) {
            $fileName = basename($tenantDetail->logo);
            $logoUrl  = $this->tenantUrl(
                $this->logoPath($fileName)
            );
        }

        return Inertia::render('Tenant/Configuracao/Index', [
            'configurations' => [
                [
                    'key'         => 'logo',
                    'label'       => 'Logo do Sistema',
                    'description' => 'Imagem exibida no painel e telas públicas.',
                    'type'        => 'image',
                    'icon'        => 'image',
                    'category'    => 'Aparência',
                    'value'       => $logoUrl,
                    'updated_at'  => $tenantDetail->updated_at?->format('d/m/Y H:i'),
                ],
            ],
        ]);
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => ['required', 'image', 'max:2048'],
        ]);

        $tenantDetail = TenantsDetail::firstOrCreate([
            'tenant_id' => $this->tenantId(),
        ]);

        if ($request->hasFile('logo')) {
            $file     = $request->file('logo');
            $fileName = 'logo_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $file->storeAs($this->tenantId(), $fileName, 'tenants');

            if ($tenantDetail->logo) {
                $oldFileName = basename($tenantDetail->logo);
                $oldPath     = $this->logoPath($oldFileName);

                if (Storage::disk('tenants')->exists($oldPath)) {
                    Storage::disk('tenants')->delete($oldPath);
                }
            }

            $tenantDetail->update([
                'logo' => $fileName,
            ]);
        }

        return redirect()->back()->with('success', 'Logo atualizado com sucesso!');
    }
}