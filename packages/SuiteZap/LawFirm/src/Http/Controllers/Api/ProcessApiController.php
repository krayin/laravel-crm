<?php

namespace SuiteZap\LawFirm\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use SuiteZap\LawFirm\Models\Processo;
use SuiteZap\LawFirm\Http\Resources\ProcessResource;
use Illuminate\Support\Facades\Validator;

class ProcessApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $query = Processo::with('person', 'prazos');

        // Simple filtering
        if (request()->has('status')) {
            $query->where('status', request('status'));
        }

        if (request()->has('search')) {
            $term = request('search');
            $query->where(function ($q) use ($term) {
                $q->where('titulo', 'like', "%$term%")
                    ->orWhere('numero_cnj', 'like', "%$term%");
            });
        }

        $processos = $query->orderBy('created_at', 'desc')->paginate(20);

        return ProcessResource::collection($processos);
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
            'titulo' => 'required|string|max:255',
            'person_id' => 'required|exists:persons,id',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $processo = Processo::create($request->all());

        return response()->json([
            'message' => 'Processo created successfully',
            'data' => new ProcessResource($processo),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return ProcessResource|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $processo = Processo::with('person', 'prazos', 'financeiros')->find($id);

        if (!$processo) {
            return response()->json(['message' => 'Processo not found'], 404);
        }

        return new ProcessResource($processo);
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
        $processo = Processo::find($id);

        if (!$processo) {
            return response()->json(['message' => 'Processo not found'], 404);
        }

        $processo->update($request->all());

        return response()->json([
            'message' => 'Processo updated successfully',
            'data' => new ProcessResource($processo),
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
        $processo = Processo::find($id);

        if (!$processo) {
            return response()->json(['message' => 'Processo not found'], 404);
        }

        $processo->delete();

        return response()->json(['message' => 'Processo deleted successfully']);
    }
}
