<?php

namespace Webkul\UI\DataGrid\Traits;

use Carbon\Carbon;

trait DatagridCollection
{
    use DatagridHelper, DatagridReferences;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getCollection()
    {
        $parsedUrl = $this->parseUrl();

        if (count($parsedUrl)) {
            $this->collection = $this->queryBuilder;

            $filteredOrSortedCollection = $this->sortOrFilterCollection(
                $this->collection,
                $parsedUrl
            );

            if ($this->paginate) {
                if ($this->itemsPerPage > 0) {
                    return $filteredOrSortedCollection
                            ->orderBy(
                                $this->index,
                                $this->sortOrder
                            )
                            ->paginate($this->itemsPerPage)
                            ->appends(request()->except('page'));
                }
            } else {
                return $filteredOrSortedCollection->orderBy($this->index, $this->sortOrder)->get();
            }
        }

        if ($this->paginate) {
            if ($this->itemsPerPage > 0) {
                $this->collection = $this->queryBuilder
                                    ->orderBy($this->index, $this->sortOrder)
                                    ->paginate($this->itemsPerPage)
                                    ->appends(request()->except('page'));
            }
        } else {
            $this->collection = $this->queryBuilder
                                    ->orderBy($this->index, $this->sortOrder)
                                    ->get();
        }

        return $this->collection;
    }

    /**
     * Filter collection.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @param  array                           $info
     * @return void
     */
    private function filterCollection($collection, $info, $columnName, $type = "filter")
    {
        if ($this->enableFilterMap && isset($this->filterMap[$columnName])) {
            $columnName = $this->filterMap[$columnName];
        }

        if ($type == "filter") {
            foreach ($info as $condition => $filter_value) {
                switch ($condition) {
                    case 'in':
                        foreach (explode(',', $filter_value) as $value) {
                            $collection->orWhere(
                                $columnName,
                                'like',
                                "%$value%"
                            );
                        }
                        break;
    
                    case 'bw':
                        $dates = explode(',', $filter_value);
        
                        if (sizeof($dates) == 2) {
                            if ($dates[1] == "") {
                                $dates[1] = Carbon::today()->format('Y-m-d');
                            }
                            
                            $collection->whereBetween(
                                $columnName,
                                $dates
                            );
                        }
                        break;
    
                    default:
                        $collection->where(
                            $columnName,
                            $this->operators[$condition],
                            $filter_value
                        );
                        break;
                }
            }
        } else {
            $count_keys = count(array_keys($info));
    
            if ($count_keys > 1) {
                throw new \Exception(trans('ui::datagrid.multiple_sort_keys'));
            }

            $columnName = $this->findColumnType(array_keys($info)[0]);

            $collection->orderBy(
                $columnName[1],
                array_values($info)[0]
            );
        }
        

        return $collection;
    }

    /**
     * Prepare search data.
     *
     * @return collection
     */
    public function prepareSearch($collection, $info)
    {
        $count_keys = count(array_keys($info));

        if ($count_keys > 1) {
            throw new \Exception(trans('ui::datagrid.multiple_search_keys'));
        }

        if ($count_keys == 1) {
            $collection->where(function ($collection) use ($info) {
                foreach ($this->completeColumnDetails as $column) {
                    if ($column['searchable'] ?? false) {
                        if ($this->enableFilterMap && isset($this->filterMap[$column['index']])) {
                            $collection->orWhere(
                                $this->filterMap[$column['index']],
                                'like',
                                '%' . $info['all'] . '%'
                            );
                        } else {
                            $collection->orWhere($column['index'], 'like', '%' . $info['all'] . '%');
                        }
                    }
                }
            });
        }

        return $collection;
    }

    /**
     * Prepare tab filter.
     *
     * @return collection
     */
    public function prepareTabFilter($collection, $key, $info)
    {
        foreach ($this->tabFilters as $filterIndex => $filter) {
            if ($filter['key'] == $key) {
                foreach ($filter['values'] as $filterValueIndex => $filterValue) {
                    if (array_keys($info)[0] == "bw" && $filterValue['key'] == 'custom') {
                        $this->tabFilters[$filterIndex]['values'][$filterValueIndex]['isActive'] = true;
                    } else {
                        $this->tabFilters[$filterIndex]['values'][$filterValueIndex]['isActive'] = ($filterValue['key'] == array_values($info)[0]);
                    }
                }

                $value = array_values($info)[0];
                $column = ($key === "duration") ? $this->filterMap["created_at"] ?? "created_at" : $key;

                $endDate = Carbon::now()->format('Y-m-d');

                switch ($value) {
                    case 'yesterday':
                        $collection->where(
                            $column,
                            Carbon::yesterday()
                        );
                        break;

                    case 'today':
                        $collection->where(
                            $column,
                            Carbon::today()
                        );
                        break;

                    case 'tomorrow':
                        $collection->where(
                            $column,
                            Carbon::tomorrow()
                        );
                        break;

                    case 'this_week':
                        $startDate = Carbon::now()->subDays(7)->format('Y-m-d');
        
                        $collection->whereBetween(
                            $column,
                            [$startDate, $endDate]
                        );
                        break;
        
                    case 'this_month':
                        $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        
                        $collection->whereBetween(
                            $column,
                            [$startDate, $endDate]
                        );
                        break;

                    default:
                        if ($value != "all") {
                            if ($key == "duration") {
                                $collection->whereBetween(
                                    $column,
                                    explode(",", $value)
                                );
                            } else {
                                $collection->where($this->filterMap[$column] ?? $column, $value);
                            }
                        }
                }
            }
        }

        return $collection;
    }

    /**
     * Prepare Response data.
     *
     * @return array
     */
    public function prepareResponseData()
    {
        $collection = $this->getCollection();

        // pagination data
        $paginationData = [
            'has_pages' => false,
        ];
        
        if ($this->paginate && $collection->hasPages()) {
            $paginationData = [
                'has_pages' => true,
            ];

            if ($collection->onFirstPage()) {
                $paginationData['on_first_page'] = true;
            } else {
                $paginationData['on_first_page'] = false;
                $paginationData['previous_page_url'] = urldecode($collection->previousPageUrl());
            }

            $paginationData['elements'] = $collection->links()->elements;
            $paginationData['current_page'] = $collection->currentPage();
            $paginationData['total_rows'] = $collection->total();
            $paginationData['current_rows'] = $collection->count();

            if ($collection->hasMorePages()) {
                $paginationData['has_more_pages'] = true;
                $paginationData['next_page_url'] = urldecode($collection->nextPageUrl());
            } else {
                $paginationData['has_more_pages'] = false;
            }
        }

        // actions data
        $arrayCollection = $collection->toArray();

        foreach ($arrayCollection['data'] as $index => $row) {
            foreach ($this->actions as $action) {
                if (! isset($arrayCollection['data'][$index]->action)) {
                    $arrayCollection['data'][$index]->action = [];
                }

                $actionCollection = $action;

                $actionCollection['route'] = route($action['route'], array_merge($action['params'] ?? [], [
                    'id' => $row->id
                ]));

                array_push($arrayCollection['data'][$index]->action, $actionCollection);

                if ($this->redirectRow) {
                    $arrayCollection['data'][$index]->redirect_url = route($this->redirectRow['route'], $arrayCollection['data'][$index]->{$this->redirectRow['id']});
                }
            }
        }

        // closure data
        foreach ($this->completeColumnDetails as $columnIndex => $column) {
            if (isset($column['closure']) && $column['closure']) {
                foreach ($arrayCollection['data'] as $index => $row) {
                    $arrayCollection['data'][$index]->{$column['index']} = $column['closure']($row);
                }
            }

            if (isset($column['filterable_type']) && $column['filterable_type'] == "date_range") {
                if (! isset($this->completeColumnDetails[$columnIndex]['values'])) {
                    $this->completeColumnDetails[$columnIndex]['values'] = ["", ""];
                }
            }
        }

        return [
            'records'           => $arrayCollection,
            'columns'           => $this->completeColumnDetails,
            'actions'           => $this->actions,
            'redirectRow'       => $this->redirectRow,
            'massactions'       => $this->massActions,
            'index'             => $this->index,
            'enableMassActions' => $this->enableMassAction,
            'enableActions'     => $this->enableAction,
            'paginated'         => $this->paginate,
            'paginationData'    => $paginationData,
            'enableSearch'      => $this->enableSearch,
            'tabFilters'        => $this->tabFilters,
            'enablePerPage'     => $this->enablePerPage,
            'enableFilters'     => $this->enableFilters,
        ];
    }
}