<?php

namespace SuiteZap\LawFirm\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Carbon\Carbon;

class PrazoDataGrid extends DataGrid
{
    /**
     * Primary column.
     *
     * @var string
     */
    protected $primaryColumn = 'id';

    /**
     * Default sort column.
     *
     * @var string
     */
    protected $sortColumn = 'data_vencimento';

    /**
     * Default sort order.
     *
     * @var string
     */
    protected $sortOrder = 'asc';

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('law_processo_prazos')
            ->join('processos', 'law_processo_prazos.processo_id', '=', 'processos.id')
            ->leftJoin('persons', 'processos.person_id', '=', 'persons.id')
            ->addSelect(
                'law_processo_prazos.id',
                'law_processo_prazos.titulo',
                'law_processo_prazos.descricao',
                'law_processo_prazos.data_vencimento',
                'law_processo_prazos.status',
                'law_processo_prazos.status as raw_status',
                'processos.titulo as processo_titulo',
                'processos.id as processo_id',
                'persons.name as client_name'
            );

        $this->addFilter('id', 'law_processo_prazos.id');
        $this->addFilter('titulo', 'law_processo_prazos.titulo');
        $this->addFilter('data_vencimento', 'law_processo_prazos.data_vencimento');
        $this->addFilter('status', 'law_processo_prazos.status');
        $this->addFilter('processo_titulo', 'processos.titulo');
        $this->addFilter('client_name', 'persons.name');

        // Security / ACL Logic
        $user = auth()->guard('user')->user();

        if ($user && $user->view_permission !== 'global') {
            if ($user->view_permission == 'group') {
                $userIds = $user->groups->mapMany(function ($group) {
                    return $group->users->pluck('id');
                })->flatten()->unique();
                $queryBuilder->whereIn('processos.user_id', $userIds);
            } else {
                $queryBuilder->where('processos.user_id', $user->id);
            }
        }

        // Ordenação: Não-concluídos primeiro (por data ASC), Concluídos no final
        $queryBuilder->orderByRaw("CASE WHEN law_processo_prazos.status = 'concluido' THEN 1 ELSE 0 END ASC");
        $queryBuilder->orderBy('law_processo_prazos.data_vencimento', 'asc');

        $this->setQueryBuilder($queryBuilder);

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        // Coluna: Título do Prazo
        $this->addColumn([
            'index' => 'titulo',
            'label' => 'Prazo / Tarefa',
            'type' => 'string',
            'sortable' => true,
            'filterable' => true,
        ]);

        // Coluna: Processo Ref. (com Cliente)
        $this->addColumn([
            'index' => 'processo_titulo',
            'label' => 'Processo Ref.',
            'type' => 'string',
            'sortable' => true,
            'filterable' => true,
            'closure' => function ($row) {
                $url = route('admin.processos.show', $row->processo_id);
                $clientInfo = $row->client_name
                    ? '<br><small class="text-gray-500">' . e($row->client_name) . '</small>'
                    : '';
                return '<a href="' . $url . '" class="text-blue-600 hover:underline font-medium">'
                    . e($row->processo_titulo) . '</a>' . $clientInfo;
            },
        ]);

        // Coluna: Vencimento
        $this->addColumn([
            'index' => 'data_vencimento',
            'label' => 'Vencimento',
            'type' => 'date',
            'sortable' => true,
            'filterable' => true,
            'width' => '120px',
            'closure' => function ($row) {
                if (!$row->data_vencimento) {
                    return '-';
                }

                // Parse com tratamento de formato
                $date = null;
                try {
                    $date = Carbon::parse($row->data_vencimento);
                } catch (\Exception $e) {
                    try {
                        $date = Carbon::createFromFormat('d/m/Y', $row->data_vencimento);
                    } catch (\Exception $e2) {
                        return '-';
                    }
                }

                return $date->format('d/m/Y');
            },
        ]);

        // Coluna: Urgência (Semáforo)
        $this->addColumn([
            'index' => 'urgencia',
            'label' => 'Status Temporal',
            'type' => 'string',
            'sortable' => false,
            'filterable' => false,
            'width' => '140px',
            'closure' => function ($row) {
                // Normaliza o status do banco para minúsculo
                $dbStatus = strtolower(trim($row->raw_status ?? ''));

                // Lista de palavras que significam "Fim"
                $finishedStates = ['concluido', 'concluído', 'finalizado', 'completed', 'done'];

                // Se concluído, badge cinza
                if (in_array($dbStatus, $finishedStates)) {
                    return '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-600">
                                ✓ Concluído
                            </span>';
                }

                if (!$row->data_vencimento) {
                    return '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                Sem data
                            </span>';
                }

                // Parse da data (banco usa Y-m-d, display usa d/m/Y)
                $date = null;
                $rawDate = $row->data_vencimento;

                // Tenta formato ISO (Y-m-d) do banco de dados
                if (preg_match('/^\d{4}-\d{2}-\d{2}/', $rawDate)) {
                    $date = Carbon::createFromFormat('Y-m-d', substr($rawDate, 0, 10))->startOfDay();
                }
                // Tenta formato BR (d/m/Y)
                elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $rawDate)) {
                    $date = Carbon::createFromFormat('d/m/Y', $rawDate)->startOfDay();
                }
                // Fallback
                else {
                    try {
                        $date = Carbon::parse($rawDate)->startOfDay();
                    } catch (\Exception $e) {
                        return '-';
                    }
                }

                $hoje = Carbon::now()->startOfDay();

                // ATRASADO (vermelho forte) - data é anterior a hoje
                if ($date->lt($hoje)) {
                    $diasAtrasado = $date->diffInDays($hoje);
                    return '<span class="px-2 py-1 rounded-full text-xs font-bold text-white" 
                                style="background-color: #dc2626 !important;">
                                ⚠ Atrasado ' . $diasAtrasado . 'd
                            </span>';
                }

                // VENCE HOJE (vermelho piscante)
                if ($date->eq($hoje)) {
                    return '<span class="px-2 py-1 rounded-full text-xs font-bold text-white animate-pulse" 
                                style="background-color: #dc2626 !important;">
                                🔥 VENCE HOJE
                            </span>';
                }

                // Dias restantes (futuro)
                $diasRestantes = $hoje->diffInDays($date);

                // URGENTE (amarelo) - próximos 3 dias
                if ($diasRestantes <= 3) {
                    return '<span class="px-2 py-1 rounded-full text-xs font-bold" 
                                style="background-color: #fbbf24 !important; color: #78350f !important;">
                                ⏰ Urgente ' . $diasRestantes . 'd
                            </span>';
                }

                // NO PRAZO (verde)
                return '<span class="px-2 py-1 rounded-full text-xs font-semibold" 
                            style="background-color: #22c55e !important; color: white !important;">
                            ✓ No Prazo
                        </span>';
            },
        ]);

        // Coluna: Status do Prazo (Soliciation)
        $this->addColumn([
            'index' => 'status',
            'label' => trans('lawfirm::app.deadlines.status'),
            'type' => 'string',
            'sortable' => true,
            'closure' => function ($row) {
                // ---------------------------------------------------------
                // 1. REGRA SUPREMA: Verifica se já acabou (Status Real)
                // ---------------------------------------------------------
                // Normaliza o status do banco para minúsculo.
                $realStatus = strtolower(trim($row->status));

                // Lista de palavras que significam "Fim"
                $finishedStates = ['concluido', 'concluído', 'finalizado', 'completed', 'done'];

                if (in_array($realStatus, $finishedStates)) {
                    // ESTADO 1: Concluído (Ignora qualquer data)
                    return '<span class="badge badge-success">Concluído</span>';
                }

                // ---------------------------------------------------------
                // 2. Lógica Temporal (Só roda se NÃO estiver concluído)
                // ---------------------------------------------------------
    
                // ESTADO 2: Sem Data
                if (empty($row->data_vencimento)) {
                    return '<span class="badge badge-secondary">Sem Data</span>';
                }

                $dueDate = null;
                $rawDate = $row->data_vencimento;

                // Tenta formato ISO (Y-m-d) do banco de dados
                if (preg_match('/^\d{4}-\d{2}-\d{2}/', $rawDate)) {
                    $dueDate = \Carbon\Carbon::createFromFormat('Y-m-d', substr($rawDate, 0, 10))->startOfDay();
                }
                // Tenta formato BR (d/m/Y)
                elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $rawDate)) {
                    $dueDate = \Carbon\Carbon::createFromFormat('d/m/Y', $rawDate)->startOfDay();
                }
                // Fallback
                else {
                    try {
                        $dueDate = \Carbon\Carbon::parse($rawDate)->startOfDay();
                    } catch (\Exception $e) {
                        return '<span class="badge badge-secondary">Data Inválida</span>';
                    }
                }

                $now = \Carbon\Carbon::now()->startOfDay();
                $diff = $now->diffInDays($dueDate, false); // false = permite negativos
    
                // ESTADO 3: Vencido (Passado e não é hoje)
                if ($dueDate->isPast() && !$dueDate->isToday()) {
                    return '<span class="badge badge-danger">Vencido</span>';
                }

                // ESTADO 4: Vence Hoje
                if ($diff === 0) {
                    return '<span class="badge badge-danger">Vence Hoje!</span>';
                }

                // ESTADO 5: Urgente (3 dias ou menos)
                if ($diff <= 3 && $diff > 0) {
                    return '<span class="badge badge-warning">Urgente ' . $diff . 'd</span>';
                }

                // ESTADO 6: No Prazo (Mais de 3 dias)
                return '<span class="badge badge-primary">No Prazo</span>';
            },
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        // View Process
        $this->addAction([
            'icon' => 'icon-eye',
            'title' => 'Ver Processo',
            'method' => 'GET',
            'url' => function ($row) {
                return route('admin.processos.show', $row->processo_id);
            },
        ]);

        // Edit Prazo
        $this->addAction([
            'icon' => 'icon-edit',
            'title' => 'Editar Prazo',
            'method' => 'GET',
            'url' => function ($row) {
                return route('admin.prazos.edit', $row->id);
            },
        ]);
    }
}
