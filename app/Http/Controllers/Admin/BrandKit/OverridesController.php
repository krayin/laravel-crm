<?php

namespace App\Http\Controllers\Admin\BrandKit;

use App\Http\Controllers\Admin\BrandKit\Concerns\BrandKitControllerHelpers;
use App\Http\Controllers\Controller;
use App\Repositories\BrandKitRepository;
use Illuminate\Http\Request;

final class OverridesController extends Controller
{
    use BrandKitControllerHelpers;

    public function __construct(private BrandKitRepository $repo) {}

    public function set(Request $request)
    {
        $data = $this->validateBase($request, [
            'override_key' => ['required', 'string', 'max:128'],
            'value'        => ['nullable'],
        ]);

        $this->autoSnapshot($data['scope_key'], $data['theme_slug'], $request, "override:set:{$data['override_key']}");

        $row = $this->repo->setOverride(
            $data['scope_key'],
            $data['theme_slug'],
            $data['override_key'],
            $request->input('value'),
            $this->userId($request)
        );

        return $this->respond($request, ['ok' => true, 'override' => $row], 'Override salvo com sucesso.');
    }

    public function reset(Request $request)
    {
        $data = $this->validateBase($request, [
            'override_key' => ['required', 'string', 'max:128'],
        ]);

        $this->autoSnapshot($data['scope_key'], $data['theme_slug'], $request, "override:reset:{$data['override_key']}");

        $this->repo->resetOverride($data['scope_key'], $data['theme_slug'], $data['override_key']);

        return $this->respond($request, ['ok' => true], 'Override resetado com sucesso.');
    }

    public function resetAll(Request $request)
    {
        $data = $this->validateBase($request);

        $this->autoSnapshot($data['scope_key'], $data['theme_slug'], $request, 'override:reset-all');

        $this->repo->resetAllOverrides($data['scope_key'], $data['theme_slug']);

        return $this->respond($request, ['ok' => true], 'Todos os overrides foram resetados.');
    }
}
