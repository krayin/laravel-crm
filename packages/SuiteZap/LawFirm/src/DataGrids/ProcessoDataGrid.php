<?php

namespace SuiteZap\LawFirm\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class ProcessoDataGrid extends DataGrid
{
    /**
     * Primary column.
     *
     * @var string
     */
    protected $primaryColumn = 'id';

    /**
     * Default sort order.
     *
     * @var string
     */
    protected $sortOrder = 'asc'; // Changed to align with precedence requirement

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('processos')
            ->leftJoin('persons', 'processos.person_id', '=', 'persons.id')
            ->addSelect(
                'processos.id',
                'processos.titulo',
                'processos.numero_cnj',
                'processos.status',
                'processos.area_direito',
                'processos.data_audiencia',
                'processos.juiz_atual',
                'processos.vara',
                'persons.name as person_name'
            );

        // Security / ACL Logic
        $user = auth()->guard('user')->user();

        if ($user->view_permission !== 'global' && $user->permission_type !== 'all') {
            if ($user->view_permission == 'group') {
                $userIds = $user->groups->mapMany(function ($group) {
                    return $group->users->pluck('id');
                })->flatten()->unique();
                $queryBuilder->whereIn('processos.user_id', $userIds);
            } else {
                $queryBuilder->where('processos.user_id', $user->id);
            }
        }

        $this->addFilter('id', 'processos.id');
        $this->addFilter('titulo', 'processos.titulo');
        $this->addFilter('numero_cnj', 'processos.numero_cnj');
        $this->addFilter('person_name', 'persons.name');
        $this->addFilter('area_direito', 'processos.area_direito');
        $this->addFilter('status', 'processos.status');
        $this->addFilter('data_audiencia', 'processos.data_audiencia');
        $this->addFilter('juiz_atual', 'processos.juiz_atual');
        $this->addFilter('vara', 'processos.vara');

        // Sort by Audience Date (Urgency), putting NULLs last
        $queryBuilder->orderByRaw('CASE WHEN processos.data_audiencia IS NULL THEN 1 ELSE 0 END, processos.data_audiencia ASC');

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
        $this->addColumn([
            'index' => 'id',
            'label' => trans('lawfirm::app.processos.datagrid.id'),
            'type' => 'integer',
            'sortable' => true,
            'filterable' => true,
            'width' => '50px',
        ]);

        $this->addColumn([
            'index' => 'titulo',
            'label' => trans('lawfirm::app.processos.datagrid.titulo'),
            'type' => 'string',
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'numero_cnj',
            'label' => trans('lawfirm::app.processos.datagrid.cnj'),
            'type' => 'string',
            'sortable' => true,
            'filterable' => true,
            'width' => '150px',
        ]);

        $this->addColumn([
            'index' => 'vara',
            'label' => 'Vara / Fórum',
            'type' => 'string',
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'juiz_atual',
            'label' => 'Juiz(a)',
            'type' => 'string',
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'person_name',
            'label' => trans('admin::app.contacts.persons.index.datagrid.name'),
            'type' => 'string',
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'data_audiencia',
            'label' => trans('lawfirm::app.processos.form.data_audiencia'),
            'type' => 'datetime',
            'sortable' => true,
            'filterable' => true,
            'width' => '150px',
            'closure' => function ($row) {
                if (!$row->data_audiencia) {
                    return '-';
                }

                $date = \Carbon\Carbon::parse($row->data_audiencia);
                $now = \Carbon\Carbon::now()->startOfDay();
                $diff = $now->diffInDays($date, false);

                $formatted = $date->format('d/m/Y H:i');

                if ($diff <= 0) {
                    return '<div class="px-2 py-1 rounded text-red-800 bg-red-100 font-bold">' . $formatted . '</div>';
                } elseif ($diff <= 5) {
                    return '<div class="px-2 py-1 rounded text-orange-800 bg-orange-100 font-bold">' . $formatted . '</div>';
                }

                return '<div class="text-gray-600 font-medium">' . $formatted . '</div>';
            },
        ]);

        $this->addColumn([
            'index' => 'status',
            'label' => trans('lawfirm::app.processos.datagrid.status'),
            'type' => 'string',
            'sortable' => true,
            'filterable' => true,
            'width' => '100px',
            'closure' => function ($row) {
                $color = 'bg-gray-200 text-gray-800';
                if ($row->status == 'Ativo')
                    $color = 'bg-green-100 text-green-800';
                if ($row->status == 'Suspenso')
                    $color = 'bg-yellow-100 text-yellow-800';

                return '<span class="px-2 py-1 rounded-full text-xs font-semibold ' . $color . '">' . $row->status . '</span>';
            }
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        // Ação de Visualizar
        $this->addAction([
            'icon' => 'icon-eye',
            'title' => trans('lawfirm::app.processos.view'),
            'method' => 'GET',
            'url' => function ($row) {
                return route('admin.processos.show', $row->id);
            },
        ]);

        // Ação de Editar
        $this->addAction([
            'icon' => 'icon-edit',
            'title' => trans('lawfirm::app.processos.edit'),
            'method' => 'GET',
            'url' => function ($row) {
                return route('admin.processos.edit', $row->id);
            },
        ]);

        // Ação de Excluir
        $this->addAction([
            'icon' => 'icon-delete',
            'title' => trans('lawfirm::app.processos.delete'),
            'method' => 'DELETE',
            'url' => function ($row) {
                return route('admin.processos.destroy', $row->id);
            },
        ]);
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        $this->addMassAction([
            'icon' => 'icon-delete',
            'title' => trans('lawfirm::app.processos.delete'),
            'method' => 'POST',
            'url' => route('admin.processos.mass_delete'),
        ]);
    }
}
