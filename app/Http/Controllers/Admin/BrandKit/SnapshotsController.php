<?php

namespace App\Http\Controllers\Admin\BrandKit;

use App\Http\Controllers\Admin\BrandKit\Concerns\BrandKitControllerHelpers;
use App\Http\Controllers\Controller;
use App\Repositories\BrandKitRepository;
use Illuminate\Http\Request;

final class SnapshotsController extends Controller
{
    use BrandKitControllerHelpers;

    public function __construct(private BrandKitRepository $repo) {}

    public function store(Request $request)
    {
        $data = $this->validateBase($request, [
            'name' => ['required', 'string', 'max:120'],
        ]);

        $snapshot = $this->repo->createSnapshot(
            $data['scope_key'],
            $data['theme_slug'],
            $data['name'],
            $this->userId($request),
            false
        );

        return $this->respond($request, ['ok' => true, 'snapshot' => $snapshot], 'Snapshot criado com sucesso.');
    }

    public function restore(Request $request, int $id)
    {
        $this->autoSnapshotFromSnapshotId($id, $request, "snapshot:restore:{$id}");

        $this->repo->restoreSnapshot($id);

        return $this->respond($request, ['ok' => true], 'Snapshot restaurado com sucesso.');
    }
}
