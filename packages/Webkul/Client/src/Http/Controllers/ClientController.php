<?php

namespace Webkul\Client\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\Client\DataGrids\ClientDataGrid;
use Webkul\Client\Repositories\ClientRepository;
use Webkul\Admin\Http\Requests\AttributeForm;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\JsonResponse;
use Webkul\Admin\Http\Requests\MassDestroyRequest;

class ClientController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(protected ClientRepository $clientRepository)
    {
        request()->request->add(['entity_type' => 'clients']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(ClientDataGrid::class)->toJson();
        }


        return view('client::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('client::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        $this->clientRepository->create(request()->all());

        return redirect()->route('admin.client.index')->with('success',"Client created successfully");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $client = $this->clientRepository->findOrFail($id);

        return view('client::edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        $this->clientRepository->update(request()->all(), $id);

        return redirect()->route('admin.client.index')->with('success',"Client updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): JsonResponse
    {
        $client = $this->clientRepository->findOrFail($id);

        try {
            Event::dispatch('client.delete.before', $id);

            $client->delete($id);

            Event::dispatch('client.delete.after', $id);

            return response()->json([
                'message' => trans('client::app.clients.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('client::app.clients.index.delete-failed'),
            ], 400);
        }
    }

    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $indices = $massDestroyRequest->input('indices');

        foreach ($indices as $index) {
            Event::dispatch('client.delete.before', $index);

            $this->clientRepository->delete($index);

            Event::dispatch('client.delete.after', $index);
        }

        return new JsonResponse([
            'message' => trans('admin::app.clients.index.delete-success'),
        ]);
    }

}
