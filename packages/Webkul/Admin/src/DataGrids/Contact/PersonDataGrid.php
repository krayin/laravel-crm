<?php

namespace Webkul\Admin\DataGrids\Contact;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Webkul\Contact\Repositories\OrganizationRepository;
use Webkul\DataGrid\DataGrid;

class PersonDataGrid extends DataGrid
{
    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct(protected OrganizationRepository $organizationRepository) {}

    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('persons')
            ->distinct()
            ->addSelect(
                'persons.crm as id',
                'persons.id as person_id',
                DB::raw("CASE WHEN related_contacts.type = 'Manager' THEN related_contacts.name ELSE NULL END as person_name"),
                'related_contacts.name as person_name',
               // 'related_contacts.name as manager_name',
                'persons.emails',
                'persons.contact_numbers',
                'organizations.name as organization',
                'organizations.id as organization_id',
                'license_attr.text_value as license_no'
            )

            ->leftJoin('organizations', 'persons.organization_id', '=', 'organizations.id')
            ->leftJoin('related_contacts', 'related_contacts.person_id', '=', 'persons.id')
            ->leftJoin('attribute_values', function ($join) {
                $join->on('attribute_values.entity_id', '=', 'persons.id')
                    ->where('attribute_values.entity_type', '=', 'persons');
            })
            ->leftJoin('attribute_values as license_attr', function ($join) {
                $join->on('license_attr.entity_id', '=', 'persons.id')
                    ->where('license_attr.entity_type', '=', 'persons')
                    ->where('license_attr.attribute_id', '=', 63); // new join specifically for license_no
            });


        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $queryBuilder->whereIn('persons.user_id', $userIds);
        }
        if (isset(request()->input('filters')['crm_code'][0])) {
           // dd(request()->input('filters'));
            $crm_code = request()->input('filters')['crm_code'][0];
            $filters = request()->input('filters', []);
            if (isset($filters['crm_code'])) {
                unset($filters['crm_code']);
                $filters['all']=[''];
            }
            request()->merge([
                'filters' => $filters
            ]);

            if (intval($crm_code)>0){

//                $queryBuilder
//                    ->where('attribute_values.text_value','=', $crm_code)
//                    ->where('attribute_values.attribute_id','=', 74);
                $queryBuilder
                    ->where('id','=', $crm_code);

            }

        }

        if (isset(request()->input('filters')['all'][0])) {
            $term = request()->input('filters')['all'][0];

            if (!empty($term)) {
                [$persian, $arabic] = $this->normalizeSearchVariants($term);

                $queryBuilder->whereExists(function ($query) use ($persian, $arabic) {
                    $query->select(DB::raw(1))
                        ->from('related_contacts')
                        ->whereColumn('related_contacts.person_id', 'persons.id')
                        ->where(function ($q) use ($persian, $arabic) {
                            $q->where('related_contacts.name', 'like', "%{$persian}%")
                                ->orWhere('related_contacts.name', 'like', "%{$arabic}%")
                                ->orWhere('related_contacts.mobile_numbers', 'like', "%{$persian}%")
                                ->orWhere('related_contacts.mobile_numbers', 'like', "%{$arabic}%")
                                ->orWhere('related_contacts.emails', 'like', "%{$persian}%")
                                ->orWhere('related_contacts.emails', 'like', "%{$arabic}%")
                                ->orWhere('attribute_values.text_value', 'like', "%{$persian}%")
                                ->orWhere('attribute_values.text_value', 'like', "%{$arabic}%");
                        });
                });
            }
        }


        $this->addFilter('id', 'persons.id');
        $this->addFilter('license_no', 'license_attr.text_value');
        $this->addFilter('emails', 'persons.emails');
        $this->addFilter('person_name', 'persons.name');
        $this->addFilter('organization', 'organizations.name');

      //  dd($queryBuilder);

        Log::info(
            vsprintf(str_replace('?', "'%s'", $queryBuilder->toSql()), $queryBuilder->getBindings())
        );
      //  dd($queryBuilder->get());

        return $queryBuilder;
    }


    protected function normalizeSearchVariants($string): array
    {
        $normalized = trim($string);

        // Arabic → Persian
        $persian = str_replace(['ي', 'ك'], ['ی', 'ک'], $normalized);

        // Persian → Arabic
        $arabic = str_replace(['ی', 'ک'], ['ي', 'ك'], $normalized);

        return [$persian, $arabic];
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => "CRM CODE",
            'type'       => 'integer',
            'filterable' => true,
            'sortable'   => true,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'      => 'license_no',
            'label'      => 'License No',
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => fn ($row) => $row->license_no ?? '-',
        ]);


        $this->addColumn([
            'index'      => 'person_name',
            'label'      => trans('admin::app.contacts.persons.index.datagrid.name'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'searchable' => true,
        ]);


        $this->addColumn([
            'index'      => 'emails',
            'label'      => trans('admin::app.contacts.persons.index.datagrid.emails'),
            'type'       => 'string',
            'sortable'   => false,
            'filterable' => true,
            'searchable' => true,
            'closure'    => fn ($row) => collect(json_decode($row->emails, true) ?? [])->pluck('value')->join(', '),
        ]);

        $this->addColumn([
            'index'      => 'contact_numbers',
            'label'      => trans('admin::app.contacts.persons.index.datagrid.contact-numbers'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'searchable' => true,
            'closure'    => fn ($row) => collect(json_decode($row->contact_numbers, true) ?? [])->pluck('value')->join(', '),
        ]);

//        $this->addColumn([
//            'index'              => 'organization',
//            'label'              => trans('admin::app.contacts.persons.index.datagrid.organization-name'),
//            'type'               => 'string',
//            'searchable'         => true,
//            'filterable'         => true,
//            'sortable'           => true,
//            'filterable_type'    => 'searchable_dropdown',
//            'filterable_options' => [
//                'repository' => OrganizationRepository::class,
//                'column'     => [
//                    'label' => 'name',
//                    'value' => 'name',
//                ],
//            ],
//        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('contacts.persons.view')) {
            $this->addAction([
                'icon'   => 'icon-eye',
                'title'  => trans('admin::app.contacts.persons.index.datagrid.view'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.contacts.persons.view', $row->id);
                },
            ]);
        }

        if (bouncer()->hasPermission('contacts.persons.edit')) {
            $this->addAction([
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.contacts.persons.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.contacts.persons.edit', $row->id);
                },
            ]);
        }

        if (bouncer()->hasPermission('contacts.persons.delete')) {
            $this->addAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.contacts.persons.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => function ($row) {
                    return route('admin.contacts.persons.delete', $row->id);
                },
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        if (bouncer()->hasPermission('contacts.persons.delete')) {
            $this->addMassAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.contacts.persons.index.datagrid.delete'),
                'method' => 'POST',
                'url'    => route('admin.contacts.persons.mass_delete'),
            ]);
        }
    }
}
