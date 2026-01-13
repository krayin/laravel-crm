<?php

namespace SuiteZap\LawFirm\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use SuiteZap\LawFirm\Models\Prazo;
use SuiteZap\LawFirm\Http\Resources\DeadlineResource;
use Illuminate\Support\Facades\Validator;

class DeadlineApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $query = Prazo::with('processo');

        if (request()->has('status')) {
            $query->where('status', request('status'));
        }

        // Filter by Process ID if provided
        if (request()->has('processo_id')) {
            $query->where('processo_id', request('processo_id'));
        }

        $prazos = $query->orderBy('data_vencimento', 'asc')->paginate(20);

        return DeadlineResource::collection($prazos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'processo_id' => 'required|exists:processos,id',
            'titulo' => 'required|string|max:255',
            'data_vencimento' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $prazo = Prazo::create($request->all());

        return response()->json([
            'message' => 'Deadline created successfully',
            'data' => new DeadlineResource($prazo),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return DeadlineResource|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $prazo = Prazo::with('processo')->find($id);

        if (!$prazo) {
            return response()->json(['message' => 'Deadline not found'], 404);
        }

        return new DeadlineResource($prazo);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $prazo = Prazo::find($id);

        if (!$prazo) {
            return response()->json(['message' => 'Deadline not found'], 404);
        }

        $prazo->update($request->all());

        return response()->json([
            'message' => 'Deadline updated successfully',
            'data' => new DeadlineResource($prazo),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $prazo = Prazo::find($id);

        if (!$prazo) {
            return response()->json(['message' => 'Deadline not found'], 404);
        }

        $prazo->delete();

        return response()->json(['message' => 'Deadline deleted successfully']);
    }
}
