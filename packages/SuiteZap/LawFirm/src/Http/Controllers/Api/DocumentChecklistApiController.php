<?php

namespace SuiteZap\LawFirm\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use SuiteZap\LawFirm\Models\ProcessDocument;
use SuiteZap\LawFirm\Models\Processo;

class DocumentChecklistApiController extends Controller
{
    // GET /api/lawfirm/documents/{processId} -> Lista documentos do processo
    public function index($processId)
    {
        $documents = ProcessDocument::where('processo_id', $processId)->get();
        return response()->json(['data' => $documents]);
    }

    // PUT /api/lawfirm/documents/{id} -> Atualiza status/notas (Webhook do WhatsApp)
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,received,approved,rejected',
            'notes' => 'nullable|string'
        ]);

        $document = ProcessDocument::findOrFail($id);
        $document->update($request->only(['status', 'notes']));

        return response()->json(['message' => 'Atualizado com sucesso', 'data' => $document]);
    }
}
