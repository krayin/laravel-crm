<?php

namespace SuiteZap\LawFirm\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use SuiteZap\LawFirm\DataGrids\ProcessoDataGrid;
use SuiteZap\LawFirm\Repositories\ProcessoRepository;
use Webkul\Activity\Repositories\ActivityRepository;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\Lead\Repositories\LeadRepository;

use SuiteZap\LawFirm\Rules\ValidarCNJ;
use SuiteZap\LawFirm\Rules\ValidarCpfCnpj;

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
     * ActivityRepository object
     *
     * @var \Webkul\Activity\Repositories\ActivityRepository
     */
    protected $activityRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \SuiteZap\LawFirm\Repositories\ProcessoRepository  $processoRepository
     * @param  \Webkul\Contact\Repositories\PersonRepository  $personRepository
     * @param  \Webkul\Lead\Repositories\LeadRepository  $leadRepository
     * @param  \Webkul\Activity\Repositories\ActivityRepository  $activityRepository
     * @return void
     */
    public function __construct(
        ProcessoRepository $processoRepository,
        PersonRepository $personRepository,
        LeadRepository $leadRepository,
        ActivityRepository $activityRepository
    ) {
        $this->processoRepository = $processoRepository;
        $this->personRepository = $personRepository;
        $this->leadRepository = $leadRepository;
        $this->activityRepository = $activityRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(ProcessoDataGrid::class)->process();
        }

        return view('lawfirm::admin.processos.index');
    }

    /**
     * Display a listing of the resource for a specific lead.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function leadProcessos($id)
    {
        return app(\SuiteZap\LawFirm\DataGrids\LeadProcessosDataGrid::class)->process();
    }

    /**
     * Display a listing of the resource for a specific person.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function personProcessos($id)
    {
        return app(\SuiteZap\LawFirm\DataGrids\PersonProcessosDataGrid::class)->process();
    }

    /**
     * Display a listing of the resource for a specific organization.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function organizationProcessos($id)
    {
        return app(\SuiteZap\LawFirm\DataGrids\OrganizationProcessosDataGrid::class)->process();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $leadId = request('lead_id');
        $preSelectedLead = null;

        if ($leadId) {
            $preSelectedLead = $this->leadRepository->find($leadId);
        }

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
            'titulo' => 'required|string|max:255',
            'numero_cnj' => ['nullable', 'string', 'unique:processos,numero_cnj', new ValidarCNJ],
            'protocolo_distribuicao' => 'nullable|string|max:255',
            'status' => 'required|string|max:255',
            'person_id' => 'required|exists:persons,id',
            'lead_id' => 'nullable|exists:leads,id',
            'tribunal' => 'nullable|string|max:255',
            'comarca' => 'nullable|string|max:255',
            'vara' => 'nullable|string|max:255',
            'juiz_atual' => 'nullable|string|max:255',
            'link_acesso' => 'nullable|string|max:500',
            'fase_processual' => 'nullable|string|max:255',
            'parte_contraria' => 'nullable|string|max:255', // Legacy field, keeping for now or replacing usage
            'opposing_party_name' => 'nullable|string|max:255',
            'opposing_party_type' => 'nullable|in:PF,PJ',
            'opposing_party_document' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $type = request('opposing_party_type');
                    if ($type === 'PF') {
                        $rule = new \SuiteZap\LawFirm\Rules\Cpf;
                        if (!$rule->passes($attribute, $value)) {
                            $fail('O CPF da parte contrária é inválido.');
                        }
                    } elseif ($type === 'PJ') {
                        $rule = new \SuiteZap\LawFirm\Rules\Cnpj;
                        if (!$rule->passes($attribute, $value)) {
                            $fail('O CNPJ da parte contrária é inválido.');
                        }
                    }
                }
            ],
            'advogado_parte_contraria' => 'nullable|string|max:255',
            'area_direito' => 'nullable|string|max:255',
            'probabilidade_exito' => 'nullable|string|max:255',
            'data_distribuicao' => 'nullable|date',
            'data_audiencia' => 'nullable|date',
            'valor_causa' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
            'tipo_parte' => 'nullable|in:autor,reu',
            'tipo_pessoa' => 'nullable|in:Física,Jurídica',
            'cpf_cnpj' => ['nullable', 'string', 'max:20', new ValidarCpfCnpj],
            'advogado_oab' => 'nullable|string|max:20',
            'whatsapp_advogado_contrario' => ['nullable', 'string', 'max:20', 'regex:/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/'],
            'email_advogado_contrario' => 'nullable|email:rfc,dns|max:255',
            'subarea_direito' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'prazos.*.titulo' => 'required|string|max:255',
            'prazos.*.data_vencimento' => 'required|date',
            'prazos.*.status' => 'required|in:Pendente,Concluído',
            'prazos.*.descricao' => 'nullable|string',
            'financeiros.*.tipo' => 'required|in:receita,despesa',
            'financeiros.*.nome' => 'required|string|max:255',
            'financeiros.*.valor' => 'required|numeric',
            'financeiros.*.data_vencimento' => 'required|date',
            'financeiros.*.status' => 'required|in:pendente,pago,cancelado',
            'financeiros.*.category' => 'nullable|string|max:50',
            'financeiros.*.issued_at' => 'nullable|date',
            'financeiros.*.payment_method' => 'nullable|string|max:50',
            'financeiros.*.payment_date' => 'nullable|date',
            'anexos.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480', // 20MB Max, Types: PDF, Docs, Images
        ], [
            'whatsapp_advogado_contrario.regex' => 'O formato do WhatsApp é inválido. Use: (99) 99999-9999.',
            'prazos.*.titulo.required' => 'O título do prazo é obrigatório.',
            'prazos.*.data_vencimento.required' => 'A data de vencimento do prazo é obrigatória.',
            'anexos.*.mimes' => 'Apenas arquivos PDF, Image (JPG/PNG) e Word (DOC/DOCX) são permitidos.',
            'anexos.*.max' => 'O tamanho máximo do arquivo é 20MB.',
        ]);

        $data = request()->all();
        $data['person_id'] = !empty($data['person_id']) ? $data['person_id'] : null;
        $data['lead_id'] = !empty($data['lead_id']) ? $data['lead_id'] : null;
        $data['user_id'] = !empty($data['user_id']) ? $data['user_id'] : null;

        Event::dispatch('lawfirm.processo.create.before');

        $processo = $this->processoRepository->create($data);

        // CREATE PRAZOS
        if (isset($data['prazos']) && is_array($data['prazos'])) {
            foreach ($data['prazos'] as $prazoData) {
                $processo->prazos()->create([
                    'titulo' => $prazoData['titulo'],
                    'data_vencimento' => $prazoData['data_vencimento'],
                    'status' => $prazoData['status'] ?? 'Pendente',
                    'descricao' => $prazoData['descricao'] ?? null,
                    'tipo' => 'comum'
                ]);
            }
        }

        // CREATE FINANCEIROS
        if (isset($data['financeiros']) && is_array($data['financeiros'])) {
            foreach ($data['financeiros'] as $finData) {
                // Check for Installments
                if (isset($finData['parcelar']) && $finData['parcelar'] == '1' && isset($finData['parcelas_qtd']) && $finData['parcelas_qtd'] > 1) {
                    $qtd = (int) $finData['parcelas_qtd'];
                    $freq = (int) ($finData['parcelas_frequencia'] ?? 30);
                    $totalValue = (float) $finData['valor'];
                    $baseValue = floor(($totalValue / $qtd) * 100) / 100; // 2 decimal places floor
                    $remainder = round($totalValue - ($baseValue * $qtd), 2);
                    $startDate = Carbon::parse($finData['data_vencimento']);

                    for ($i = 1; $i <= $qtd; $i++) {
                        $currentValue = $baseValue;
                        if ($i == $qtd) {
                            $currentValue += $remainder; // Add remainder to last installment
                        }

                        $dueDate = $startDate->copy()->addDays($freq * ($i - 1));

                        $processo->financeiros()->create([
                            'tipo' => $finData['tipo'],
                            'nome' => $finData['nome'] . " (Parcela $i/$qtd)",
                            'valor' => $currentValue,
                            'data_vencimento' => $dueDate,
                            'status' => $finData['status'] ?? 'pendente',
                            'category' => $finData['category'] ?? null,
                            'issued_at' => $finData['issued_at'] ?? null,
                            'payment_method' => $finData['payment_method'] ?? null,
                            'payment_date' => ($finData['status'] ?? 'pendente') === 'pago' ? ($finData['payment_date'] ?? now()->toDateString()) : null,
                        ]);
                    }
                } else {
                    // Single Record
                    $processo->financeiros()->create([
                        'tipo' => $finData['tipo'],
                        'nome' => $finData['nome'],
                        'valor' => $finData['valor'],
                        'data_vencimento' => $finData['data_vencimento'],
                        'status' => $finData['status'] ?? 'pendente',
                        'category' => $finData['category'] ?? null,
                        'issued_at' => $finData['issued_at'] ?? null,
                        'payment_method' => $finData['payment_method'] ?? null,
                        'payment_date' => ($finData['status'] ?? 'pendente') === 'pago' ? ($finData['payment_date'] ?? now()->toDateString()) : null,
                    ]);
                }
            }
        }

        // UPLOAD ANEXOS (GED)
        if (request()->hasFile('anexos')) {
            foreach (request()->file('anexos') as $file) {
                // Store in 'public' disk so files are accessible via web
                $path = $file->store('processos/' . $processo->id, 'public');

                $processo->anexos()->create([
                    'path' => $path,
                    'nome_original' => $file->getClientOriginalName(),
                    'tipo_mime' => $file->getMimeType(),
                    'tamanho' => $file->getSize(),
                ]);
            }
        }

        $this->logProcessHistory($processo, 'Criado');
        $this->ensureCalendarEvent($processo);

        Event::dispatch('lawfirm.processo.create.after', $processo);

        session()->flash('success', trans('lawfirm::app.processos.create-success'));

        return redirect()->route('admin.processos.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $processo = $this->processoRepository->findOrFail($id);
        $processo->load([
            'person',
            'lead',
            'financeiros' => function ($query) {
                $query->orderByRaw("CASE WHEN status = 'pendente' THEN 1 ELSE 2 END ASC")
                    ->orderBy('data_vencimento', 'asc');
            }
        ]);

        return view('lawfirm::admin.processos.show', compact('processo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $processo = \SuiteZap\LawFirm\Models\Processo::with([
            'person',
            'lead',
            'responsavel',
            'financeiros' => function ($query) {
                $query->orderByRaw("CASE WHEN status = 'pendente' THEN 1 ELSE 2 END")
                    ->orderBy('data_vencimento', 'asc');
            }
        ])->findOrFail($id);

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
            'titulo' => 'required|string|max:255',
            'numero_cnj' => ['nullable', 'string', 'unique:processos,numero_cnj,' . $id, new ValidarCNJ],
            'status' => 'required|string|max:255',
            'person_id' => 'required|exists:persons,id',
            'lead_id' => 'nullable|exists:leads,id',
            'tribunal' => 'nullable|string|max:255',
            'comarca' => 'nullable|string|max:255',
            'vara' => 'nullable|string|max:255',
            'link_acesso' => 'nullable|string|max:500',
            'fase_processual' => 'nullable|string|max:255',
            'parte_contraria' => 'nullable|string|max:255',
            'opposing_party_name' => 'nullable|string|max:255',
            'opposing_party_type' => 'nullable|in:PF,PJ',
            'opposing_party_document' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $type = request('opposing_party_type');
                    if ($type === 'PF') {
                        $rule = new \SuiteZap\LawFirm\Rules\Cpf;
                        if (!$rule->passes($attribute, $value)) {
                            $fail('O CPF da parte contrária é inválido.');
                        }
                    } elseif ($type === 'PJ') {
                        $rule = new \SuiteZap\LawFirm\Rules\Cnpj;
                        if (!$rule->passes($attribute, $value)) {
                            $fail('O CNPJ da parte contrária é inválido.');
                        }
                    }
                }
            ],
            'advogado_parte_contraria' => 'nullable|string|max:255',
            'area_direito' => 'nullable|string|max:255',
            'probabilidade_exito' => 'nullable|string|max:255',
            'data_distribuicao' => 'nullable|date',
            'data_audiencia' => 'nullable|date',
            'valor_causa' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
            'tipo_parte' => 'nullable|in:autor,reu',
            'tipo_pessoa' => 'nullable|in:Física,Jurídica',
            'cpf_cnpj' => ['nullable', 'string', 'max:20', new ValidarCpfCnpj],
            'advogado_oab' => 'nullable|string|max:20',
            'whatsapp_advogado_contrario' => ['nullable', 'string', 'max:20', 'regex:/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/'],
            'email_advogado_contrario' => 'nullable|email:rfc,dns|max:255',
            'subarea_direito' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'prazos.*.titulo' => 'required|string|max:255',
            'prazos.*.data_vencimento' => 'required|date',
            'prazos.*.status' => 'required|in:Pendente,Concluído',
            'prazos.*.descricao' => 'nullable|string',
            'financeiros.*.tipo' => 'required|in:receita,despesa',
            'financeiros.*.nome' => 'required|string|max:255',
            'financeiros.*.valor' => 'required|numeric',
            'financeiros.*.data_vencimento' => 'required|date',
            'financeiros.*.status' => 'required|in:pendente,pago,cancelado',
            'financeiros.*.category' => 'nullable|string|max:50',
            'financeiros.*.issued_at' => 'nullable|date',
            'financeiros.*.payment_method' => 'nullable|string|max:50',
            'financeiros.*.payment_date' => 'nullable|date',
            'anexos.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480', // 20MB Max
        ], [
            'whatsapp_advogado_contrario.regex' => 'O formato do WhatsApp é inválido. Use: (99) 99999-9999.',
            'prazos.*.titulo.required' => 'O título do prazo é obrigatório.',
            'prazos.*.data_vencimento.required' => 'A data de vencimento do prazo é obrigatória.',
            'anexos.*.mimes' => 'Apenas arquivos PDF, Image (JPG/PNG) e Word (DOC/DOCX) são permitidos.',
            'anexos.*.max' => 'O tamanho máximo do arquivo é 20MB.',
        ]);

        $data = request()->all();
        $data['person_id'] = !empty($data['person_id']) ? $data['person_id'] : null;
        $data['lead_id'] = !empty($data['lead_id']) ? $data['lead_id'] : null;
        $data['user_id'] = !empty($data['user_id']) ? $data['user_id'] : null;

        Event::dispatch('lawfirm.processo.update.before', $id);

        $processo = $this->processoRepository->update($data, $id);

        // SYNC PRAZOS
        $prazosEnviados = $data['prazos'] ?? [];

        // 1. Identify IDs to keep
        $idsParaManter = collect($prazosEnviados)->pluck('id')->filter()->toArray();

        // 2. Delete removed items (iterate models to trigger Observer)
        $prazosParaDeletar = $processo->prazos()->whereNotIn('id', $idsParaManter)->get();
        foreach ($prazosParaDeletar as $prazo) {
            $prazo->delete();
        }

        // 3. Create or Update
        foreach ($prazosEnviados as $prazoData) {
            $attributes = [
                'titulo' => $prazoData['titulo'],
                'data_vencimento' => $prazoData['data_vencimento'],
                'status' => $prazoData['status'] ?? 'Pendente',
                'descricao' => $prazoData['descricao'] ?? null,
                'processo_id' => $processo->id,
            ];

            // Default 'tipo' for new items if not present
            if (!isset($prazoData['id'])) {
                $attributes['tipo'] = 'comum';
            }

            if (isset($prazoData['id']) && $prazoData['id']) {
                $prazoModel = $processo->prazos()->where('id', $prazoData['id'])->first();
                if ($prazoModel) {
                    $prazoModel->update($attributes);
                }
            } else {
                $processo->prazos()->create($attributes);
            }
        }

        // SYNC FINANCEIROS
        $finEnviados = $data['financeiros'] ?? [];

        // 1. IDs to keep
        $idsFinManter = collect($finEnviados)->pluck('id')->filter()->toArray();

        // 2. Delete removed
        $processo->financeiros()->whereNotIn('id', $idsFinManter)->delete();

        // 3. Create/Update
        foreach ($finEnviados as $finData) {

            // Check if it is a NEW record AND has installments
            if ((!isset($finData['id']) || !$finData['id']) && isset($finData['parcelar']) && $finData['parcelar'] == '1' && isset($finData['parcelas_qtd']) && $finData['parcelas_qtd'] > 1) {
                $qtd = (int) $finData['parcelas_qtd'];
                $freq = (int) ($finData['parcelas_frequencia'] ?? 30);
                $totalValue = (float) $finData['valor'];
                $baseValue = floor(($totalValue / $qtd) * 100) / 100;
                $remainder = round($totalValue - ($baseValue * $qtd), 2);
                $startDate = Carbon::parse($finData['data_vencimento']);

                for ($i = 1; $i <= $qtd; $i++) {
                    $currentValue = $baseValue;
                    if ($i == $qtd) {
                        $currentValue += $remainder;
                    }

                    $dueDate = $startDate->copy()->addDays($freq * ($i - 1));

                    $processo->financeiros()->create([
                        'tipo' => $finData['tipo'],
                        'nome' => $finData['nome'] . " (Parcela $i/$qtd)",
                        'valor' => $currentValue,
                        'data_vencimento' => $dueDate,
                        'status' => $finData['status'] ?? 'pendente',
                        'category' => $finData['category'] ?? null,
                        'issued_at' => $finData['issued_at'] ?? null,
                        'payment_method' => $finData['payment_method'] ?? null,
                        'payment_date' => ($finData['status'] ?? 'pendente') === 'pago' ? ($finData['payment_date'] ?? now()->toDateString()) : null,
                        'processo_id' => $processo->id
                    ]);
                }
                continue; // Skip standard create/update for this iteration
            }

            // Standard Create/Update (Single)
            $attributes = [
                'tipo' => $finData['tipo'],
                'nome' => $finData['nome'],
                'valor' => $finData['valor'],
                'data_vencimento' => $finData['data_vencimento'],
                'status' => $finData['status'] ?? 'pendente',
                'category' => $finData['category'] ?? null,
                'issued_at' => $finData['issued_at'] ?? null,
                'payment_method' => $finData['payment_method'] ?? null,
                'payment_date' => ($finData['status'] ?? 'pendente') === 'pago' ? ($finData['payment_date'] ?? now()->toDateString()) : null,
                'processo_id' => $processo->id
            ];

            if (isset($finData['id']) && $finData['id']) {
                $processo->financeiros()->where('id', $finData['id'])->update($attributes);
            } else {
                $processo->financeiros()->create($attributes);
            }
        }

        // UPLOAD ANEXOS (GED) - UPDATE
        if (request()->hasFile('anexos')) {
            foreach (request()->file('anexos') as $file) {
                $path = $file->store('processos/' . $processo->id);

                $processo->anexos()->create([
                    'path' => $path,
                    'nome_original' => $file->getClientOriginalName(),
                    'tipo_mime' => $file->getMimeType(),
                    'tamanho' => $file->getSize(),
                ]);
            }
        }

        $this->logProcessHistory($processo, 'Atualizado');
        $this->ensureCalendarEvent($processo);

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

            // Fetch process before deleting to clean up events
            $processo = $this->processoRepository->find($id);
            if ($processo) {
                // Ensure calendar event logic will handle deletion if status/process data implies cleanup,
                // BUT here we are destroying the process object itself.
                // We should manually clean up or force a cleanup state.
                // Let's manually invoke find-and-delete logic similar to ensureCalendarEvent but purely destructive.
                // Logic: find by tag and delete.
                $this->forceCleanupCalendarEvent($processo);
            }

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

    /**
     * Mass destroy the specified resources from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function massDestroy()
    {
        try {
            $indices = request()->input('indices', []);

            if (empty($indices)) {
                return response()->json([
                    'message' => trans('lawfirm::app.processos.mass-delete.no-selection'),
                ], 400);
            }

            $processos = $this->processoRepository->findWhereIn('id', $indices);
            $deletedCount = 0;

            foreach ($processos as $processo) {
                Event::dispatch('lawfirm.processo.delete.before', $processo->id);

                // Clean up calendar events
                $this->forceCleanupCalendarEvent($processo);

                $this->processoRepository->delete($processo->id);

                Event::dispatch('lawfirm.processo.delete.after', $processo->id);

                $deletedCount++;
            }

            return response()->json([
                'message' => trans('lawfirm::app.processos.mass-delete.success', ['count' => $deletedCount]),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('lawfirm::app.processos.mass-delete.failed'),
            ], 500);
        }
    }

    /**
     * Remove the specified attachment.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyAnexo($id)
    {
        $anexo = \SuiteZap\LawFirm\Models\Anexo::findOrFail($id);

        // Delete from Storage (use 'public' disk)
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($anexo->path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($anexo->path);
        }

        // Delete from DB
        $anexo->delete();

        session()->flash('success', 'Anexo excluído com sucesso.');

        return redirect()->back(); // Return to the same page (edit or view)
    }

    /**
     * Ensure calendar event logic: Create, Update OR Delete based on state.
     * Uses [REF:PROC_ID:{id}] tag to strictly identify the event.
     *
     * @param mixed $processo
     * @return void
     */
    private function ensureCalendarEvent($processo)
    {
        $userId = auth()->guard('user')->id();
        $tag = "[REF:PROC_ID:{$processo->id}]";

        // 1. Find existing activity by TAG
        // We filter by lightweight attributes first: meeting + is_done=0 + user
        $activities = $this->activityRepository->findWhere([
            'type' => 'meeting',
            'is_done' => 0,
            'user_id' => $userId
        ]);

        // Filter collection to find the specific tag in comment
        $existingActivity = $activities->first(function ($activity) use ($tag) {
            return str_contains($activity->comment, $tag);
        });

        // 2. Determine Action: Cleanup OR Upsert
        $isActive = strtolower(trim($processo->status)) === 'ativo';
        $hasDate = !empty($processo->data_audiencia);

        if (!$isActive || !$hasDate) {
            // Case A: Cleanup (Not active OR no date) -> Delete if exists
            if ($existingActivity) {
                $this->activityRepository->delete($existingActivity->id);
            }
            return;
        }

        // Case B: Upsert (Active AND has date)
        $scheduledFrom = Carbon::parse($processo->data_audiencia);
        $scheduledTo = $scheduledFrom->copy()->addHour();
        $title = 'Audiência: ' . $processo->titulo;
        $comment = "Audiência gerada automaticamente pelo Processo Nº {$processo->numero_cnj}. {$tag}";

        $data = [
            'type' => 'meeting',
            'title' => $title,
            'comment' => $comment,
            'schedule_from' => $scheduledFrom,
            'schedule_to' => $scheduledTo,
            'is_done' => 0,
            'user_id' => $userId,
            'participants' => [
                'users' => [$userId],
            ],
        ];

        if ($processo->person_id) {
            $data['participants']['persons'] = [$processo->person_id];
        }

        if ($processo->lead_id) {
            $data['lead_id'] = $processo->lead_id;
        }

        if ($existingActivity) {
            // Update existing
            $this->activityRepository->update($data, $existingActivity->id);
        } else {
            // Create new
            $this->activityRepository->create($data);
        }
    }

    /**
     * Helper to force cleanup on destroy
     *
     * @param mixed $processo
     * @return void
     */
    private function forceCleanupCalendarEvent($processo)
    {
        $userId = auth()->guard('user')->id();
        $tag = "[REF:PROC_ID:{$processo->id}]";

        $activities = $this->activityRepository->findWhere([
            'type' => 'meeting',
            'is_done' => 0,
            'user_id' => $userId
        ]);

        $existingActivity = $activities->first(function ($activity) use ($tag) {
            return str_contains($activity->comment, $tag);
        });

        if ($existingActivity) {
            $this->activityRepository->delete($existingActivity->id);
        }
    }

    /**
     * Search person results.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPerson()
    {
        $term = request('query');

        $results = $this->personRepository->scopeQuery(function ($query) use ($term) {
            return $query->where('name', 'like', '%' . $term . '%');
        })->paginate(10);

        return response()->json($results);
    }

    /**
     * Search lead results.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchLead()
    {
        $term = request('query');

        $results = $this->leadRepository->scopeQuery(function ($query) use ($term) {
            return $query->where('title', 'like', '%' . $term . '%');
        })->paginate(10);

        return response()->json($results);
    }

    /**
     * Log process history as a note.
     *
     * @param mixed $processo
     * @param string $acao
     * @return void
     */
    private function logProcessHistory($processo, $acao)
    {
        $now = Carbon::now();

        $data = [
            'type' => 'note',
            'title' => "Histórico ($acao)",
            'comment' => "Histórico ($acao): Processo atualizado. Status: " . $processo->status,
            'schedule_from' => $now,
            'schedule_to' => $now,
            'is_done' => 1,
            'user_id' => auth()->guard('user')->id(),
        ];

        if ($processo->lead_id) {
            $data['lead_id'] = $processo->lead_id;
        }

        $this->activityRepository->create($data);
    }
}
