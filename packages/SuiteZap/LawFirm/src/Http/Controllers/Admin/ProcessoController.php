<?php

namespace SuiteZap\LawFirm\Http\Controllers\Admin;

use Illuminate\Support\Facades\Event;
use SuiteZap\LawFirm\DataGrids\ProcessoDataGrid;
use SuiteZap\LawFirm\Models\Processo;
use SuiteZap\LawFirm\Repositories\ProcessoRepository;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Lead\Repositories\LeadRepository;

class ProcessoController extends Controller
{
    /**
     * ProcessoRepository object
     *
     * @var \SuiteZap\LawFirm\Repositories\ProcessoRepository
     */
    protected $processoRepository;

    /**
     * PersonRepository object
     *
     * @var \Webkul\Contact\Repositories\PersonRepository
     */
    protected $personRepository;

    /**
     * LeadRepository object
     *
     * @var \Webkul\Lead\Repositories\LeadRepository
     */
    protected $leadRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \SuiteZap\LawFirm\Repositories\ProcessoRepository  $processoRepository
     * @param  \Webkul\Contact\Repositories\PersonRepository  $personRepository
     * @param  \Webkul\Lead\Repositories\LeadRepository  $leadRepository
     * @return void
     */
    public function __construct(
        ProcessoRepository $processoRepository,
        PersonRepository $personRepository,
        LeadRepository $leadRepository
    ) {
        $this->processoRepository = $processoRepository;
        $this->personRepository = $personRepository;
        $this->leadRepository = $leadRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Se a requisição for AJAX (vinda do componente DataGrid), retorna JSON
        if (request()->ajax()) {
            return app(ProcessoDataGrid::class)->toJson();
        }

        // Se for acesso normal do navegador, retorna a View
        return view('lawfirm::admin.processos.listagem');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Buscar Lead pré-selecionado (se vier da tela de Leads)
        $leadId = request('lead_id');
        $preSelectedLead = null;

        if ($leadId) {
            $preSelectedLead = $this->leadRepository->find($leadId);
        }

        // Buscar listas para os dropdowns
        $persons = $this->personRepository->all();
        $leads = $this->leadRepository->all();

        return view('lawfirm::admin.processos.create', compact('persons', 'leads', 'preSelectedLead'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->validate(request(), [
            // Campos obrigatórios
            'titulo' => 'required|string|max:255',
            'numero_cnj' => 'required|string|unique:processos,numero_cnj',
            'status' => 'required|in:Ativo,Suspenso,Arquivado',

            // Relacionamentos
            'person_id' => 'nullable|exists:persons,id',
            'lead_id' => 'nullable|exists:leads,id',

            // Informações do Processo
            'tribunal' => 'nullable|string|max:255',
            'comarca' => 'nullable|string|max:255',
            'vara' => 'nullable|string|max:255',
            'link_acesso' => 'nullable|string|max:500',
            'fase_processual' => 'nullable|string|in:Inicial,Contestação,Réplica,Instrução,Sentença,Recurso,Execução',

            // Partes
            'parte_contraria' => 'nullable|string|max:255',
            'advogado_parte_contraria' => 'nullable|string|max:255',

            // Classificação
            'area_direito' => 'nullable|string|in:Civil,Trabalhista,Penal,Tributário,Família,Consumidor,Previdenciário',
            'probabilidade_exito' => 'nullable|string|in:Alta,Média,Baixa',

            // Datas
            'data_distribuicao' => 'nullable|date',
            'data_audiencia' => 'nullable|date',

            // Valores e Descrição
            'valor_causa' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        // Preparar dados para criação
        $data = request()->all();

        // Converter campos vazios de FK para null
        $data['person_id'] = $data['person_id'] ?: null;
        $data['lead_id'] = $data['lead_id'] ?: null;

        // Criar o processo
        Event::dispatch('lawfirm.processo.create.before');

        $processo = $this->processoRepository->create($data);

        Event::dispatch('lawfirm.processo.create.after', $processo);

        session()->flash('success', trans('lawfirm::app.processos.create-success'));

        return redirect()->route('admin.processos.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Buscar o processo
        $processo = $this->processoRepository->findOrFail($id);

        // Buscar listas para os dropdowns
        $persons = $this->personRepository->all();
        $leads = $this->leadRepository->all();

        return view('lawfirm::admin.processos.edit', compact('processo', 'persons', 'leads'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        $this->validate(request(), [
            // Campos obrigatórios
            'titulo' => 'required|string|max:255',
            'numero_cnj' => 'required|string|unique:processos,numero_cnj,' . $id,
            'status' => 'required|in:Ativo,Suspenso,Arquivado',

            // Relacionamentos
            'person_id' => 'nullable|exists:persons,id',
            'lead_id' => 'nullable|exists:leads,id',

            // Informações do Processo
            'tribunal' => 'nullable|string|max:255',
            'comarca' => 'nullable|string|max:255',
            'vara' => 'nullable|string|max:255',
            'link_acesso' => 'nullable|string|max:500',
            'fase_processual' => 'nullable|string|in:Inicial,Contestação,Réplica,Instrução,Sentença,Recurso,Execução',

            // Partes
            'parte_contraria' => 'nullable|string|max:255',
            'advogado_parte_contraria' => 'nullable|string|max:255',

            // Classificação
            'area_direito' => 'nullable|string|in:Civil,Trabalhista,Penal,Tributário,Família,Consumidor,Previdenciário',
            'probabilidade_exito' => 'nullable|string|in:Alta,Média,Baixa',

            // Datas
            'data_distribuicao' => 'nullable|date',
            'data_audiencia' => 'nullable|date',

            // Valores e Descrição
            'valor_causa' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        // Preparar dados para atualização
        $data = request()->all();

        // Converter campos vazios de FK para null
        $data['person_id'] = $data['person_id'] ?: null;
        $data['lead_id'] = $data['lead_id'] ?: null;

        Event::dispatch('lawfirm.processo.update.before', $id);

        // Atualizar o processo
        $processo = $this->processoRepository->update($data, $id);

        Event::dispatch('lawfirm.processo.update.after', $processo);

        session()->flash('success', trans('lawfirm::app.processos.update-success'));

        return redirect()->route('admin.processos.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            Event::dispatch('lawfirm.processo.delete.before', $id);

            $this->processoRepository->delete($id);

            Event::dispatch('lawfirm.processo.delete.after', $id);

            return response()->json([
                'message' => trans('lawfirm::app.processos.delete-success'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('lawfirm::app.processos.delete-failed'),
            ], 500);
        }
    }
}
