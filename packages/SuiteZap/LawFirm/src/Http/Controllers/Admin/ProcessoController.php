<?php

namespace SuiteZap\LawFirm\Http\Controllers\Admin;

use Illuminate\Support\Facades\Event;
use SuiteZap\LawFirm\Models\Processo;
// Importante: Importar o DataGrid corretamente
use SuiteZap\LawFirm\DataGrids\ProcessoDataGrid;
use Webkul\Admin\Http\Controllers\Controller as BaseController;

class ProcessoController extends BaseController
{
    public function index()
    {
        // Se a requisição for AJAX (vinda do componente da tabela), retorna JSON
        if (request()->ajax()) {
            return app(ProcessoDataGrid::class)->toJson();
        }

        // Se for acesso normal navegador, retorna a View
        return view('lawfirm::admin.processos.listagem');
    }

    public function create()
    {
        $leadId = request('lead_id');
        $preSelectedLead = null;
        if ($leadId) {
            $preSelectedLead = \Webkul\Lead\Models\Lead::find($leadId);
        }

        $leads = \Webkul\Lead\Models\Lead::all();
        $people = \Webkul\Contact\Models\Person::all();

        return view('lawfirm::admin.processos.create', compact('leads', 'people', 'preSelectedLead'));
    }

    public function store()
    {
        $this->validate(request(), [
            'titulo' => 'required',
            'numero_cnj' => 'required|unique:processos,numero_cnj',
            'status' => 'required',
        ]);

        $processo = Processo::create(request()->all());
        session()->flash('success', trans('lawfirm::app.processos.create-success'));
        return redirect()->route('admin.processos.index');
    }

    public function edit($id)
    {
        $processo = Processo::findOrFail($id);
        $leads = \Webkul\Lead\Models\Lead::all();
        $people = \Webkul\Contact\Models\Person::all();
        return view('lawfirm::admin.processos.edit', compact('processo', 'leads', 'people'));
    }

    public function update($id)
    {
        $this->validate(request(), [
            'titulo' => 'required',
            'numero_cnj' => 'required|unique:processos,numero_cnj,' . $id,
        ]);

        $processo = Processo::findOrFail($id);
        $processo->update(request()->all());
        session()->flash('success', trans('lawfirm::app.processos.update-success'));
        return redirect()->route('admin.processos.index');
    }

    public function destroy($id)
    {
        $processo = Processo::findOrFail($id);
        try {
            $processo->delete();
            return response()->json(['message' => trans('lawfirm::app.processos.delete-success')]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao deletar'], 500);
        }
    }
}
