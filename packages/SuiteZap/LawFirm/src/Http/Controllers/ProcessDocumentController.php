<?php

namespace SuiteZap\LawFirm\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use SuiteZap\LawFirm\Models\ChecklistTemplate;
use SuiteZap\LawFirm\Models\ProcessDocument;

class ProcessDocumentController extends Controller
{
    // Importa os itens de um Template para o Processo Atual
    public function importTemplate(Request $request, $processId)
    {
        $request->validate(['template_id' => 'required|exists:law_checklist_templates,id']);

        $template = ChecklistTemplate::find($request->template_id);

        // Itera sobre os itens do JSON e cria os registros
        foreach ($template->items as $itemName) {
            ProcessDocument::firstOrCreate([
                'processo_id' => $processId,
                'name' => $itemName,
            ], [
                'status' => 'pending' // pending, received
            ]);
        }

        session()->flash('success', 'Checklist importado com sucesso!');
        return redirect()->back();
    }

    // Atualiza o status de um documento (Ex: Pendente -> Recebido)
    public function updateStatus(Request $request, $id)
    {
        $document = ProcessDocument::findOrFail($id);
        $document->update([
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        session()->flash('success', 'Status do documento atualizado.');
        return redirect()->back();
    }

    // Deletar um item da lista
    public function destroy($id)
    {
        ProcessDocument::findOrFail($id)->delete();
        session()->flash('success', 'Documento removido da lista.');
        return redirect()->back();
    }
}
