<?php

namespace SuiteZap\LawFirm\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    /**
     * POST /api/lawfirm/documents/{id}/upload
     * Upload de arquivo para documento do checklist.
     * S3 Compatible: Usa disco configurável.
     */
    public function uploadFile(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:20480' // 20MB
        ]);

        $document = ProcessDocument::findOrFail($id);

        // Upload usando disco configurável (respeita FILESYSTEM_DISK)
        $path = $request->file('file')->store(
            'checklist/' . $document->processo_id,
            config('filesystems.default')
        );

        $document->update([
            'file_path' => $path,
            'status' => 'received'
        ]);

        return response()->json([
            'message' => 'Arquivo enviado com sucesso',
            'data' => $document,
            'file_url' => Storage::url($path)
        ]);
    }
}
