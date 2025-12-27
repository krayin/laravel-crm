<?php

namespace App\Http\Controllers\Admin\BrandKit;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\BrandKit\Concerns\BrandKitControllerHelpers;
use App\Repositories\BrandKitRepository;
use Illuminate\Http\Request;

final class CustomCssController extends Controller
{
    use BrandKitControllerHelpers;

    public function __construct(private BrandKitRepository $repo) {}

    public function store(Request $request)
    {
        $data = $this->validateBase($request, [
            'name'        => ['required','string','max:120'],
            'css_content' => ['required','string','max:50000'],
            'target'      => ['nullable','string','in:admin,login,both'],
        ]);

        $this->autoSnapshot($data['scope_key'], $data['theme_slug'], $request, "css:add:{$data['name']}");

        $css = $this->repo->addCustomCss(
            $data['scope_key'],
            $data['theme_slug'],
            $data['name'],
            $data['css_content'],
            $data['target'] ?? 'admin',
            $this->userId($request)
        );

        return $this->respond($request, ['ok' => true, 'css' => $css], 'CSS salvo (desativado por segurança).');
    }

    public function toggle(Request $request, int $id)
    {
        $this->autoSnapshotFromCssId($id, $request, "css:toggle:{$id}");

        $css = $this->repo->toggleCustomCss($id);

        return $this->respond($request, ['ok' => true, 'css' => $css], 'CSS alternado com sucesso.');
    }

    public function destroy(Request $request, int $id)
    {
        $this->autoSnapshotFromCssId($id, $request, "css:delete:{$id}");

        $this->repo->deleteCustomCss($id);

        return $this->respond($request, ['ok' => true], 'CSS removido com sucesso.');
    }
}
