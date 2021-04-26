<?php

namespace Webkul\UI\DataGrid;

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

abstract class DataGrid
{
    /**
     * set index columns, ex: id.
     *
     * @var int
     */
    protected $index = "id";

    /**
     * Default sort order of datagrid
     *
     * @var string
     */
    protected $sortOrder = 'desc';

    /**
     * enable search field
     *
     * @var boolean
     */
    protected $enableSearch = true;

    /**
     * enable items per page
     *
     * @var boolean
     */
    protected $enablePerPage = true;

    /**
     * enable sidebar filters
     *
     * @var boolean
     */
    protected $enableFilters = true;

    /**
     * array to hold all the columns which will be displayed on frontend.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $completeColumnDetails = [];

    /**
     * Hold query builder instance of the query prepared by executing datagrid
     * class method setQueryBuilder
     *
     * @var array
     */
    protected $queryBuilder = [];

    /**
     * Final result of the datagrid program that is collection object.
     *
     * @var array
     */
    protected $collection = [];

    /**
     * Set of handly click tools which you could be using for various operations.
     * ex: dyanmic and static redirects, deleting, etc.
     *
     * @var array
     */
    protected $actions = [];

    /**
     * Works on selection of values index column as comma separated list as response
     * to your endpoint set as route.
     *
     * @var array
     */
    protected $massActions = [];

    /**
     * Parsed value of the url parameters
     *
     * @var array
     */
    protected $parse;

    /**
     * To show mass action or not.
     *
     * @var bool
     */
    protected $enableMassAction = false;

    /**
     * To enable actions or not.
     */
    protected $enableAction = false;

    /**
     * paginate the collection or not
     *
     * @var bool
     */
    protected $paginate = true;

    /**
     * If paginated then value of pagination.
     *
     * @var int
     */
    protected $itemsPerPage = 10;

    /**
     * @var array
     */
    protected $operators = [
        'eq'       => "=",
        'lt'       => "<",
        'gt'       => ">",
        'lte'      => "<=",
        'gte'      => ">=",
        'neqs'     => "<>",
        'neqn'     => "!=",
        'eqo'      => "<=>",
        'like'     => "like",
        'blike'    => "like binary",
        'nlike'    => "not like",
        'ilike'    => "ilike",
        'and'      => "&",
        'bor'      => "|",
        'regex'    => "regexp",
        'notregex' => "not regexp",
    ];

    /**
     * @var array
     */
    protected $bindings = [
        0 => "select",
        1 => "from",
        2 => "join",
        3 => "where",
        4 => "having",
        5 => "order",
        6 => "union",
    ];

    /**
     * @var array
     */
    protected $selectcomponents = [
        0  => "aggregate",
        1  => "columns",
        2  => "from",
        3  => "joins",
        4  => "wheres",
        5  => "groups",
        6  => "havings",
        7  => "orders",
        8  => "limit",
        9  => "offset",
        10 => "lock",
    ];

    /**
     * @var array
     */
    protected $tabFilters = [];

    abstract public function prepareQueryBuilder();

    abstract public function addColumns();

    /**
     * @return void
     */
    public function __construct()
    {
        $this->invoker = $this;
    }

    /**
     * Parse the URL and get it ready to be used.
     *
     * @return void
     */
    private function parseUrl()
    {
        $parsedUrl = [];
        $unparsed = url()->full();

        $route = request()->route() ? request()->route()->getName() : "";

        if (count(explode('?', $unparsed)) > 1) {
            $to_be_parsed = explode('?', $unparsed)[1];

            parse_str($to_be_parsed, $parsedUrl);
            unset($parsedUrl['page']);
        }

        if (isset($parsedUrl['grand_total'])) {
            foreach ($parsedUrl['grand_total'] as $key => $value) {
                $parsedUrl['grand_total'][$key] = str_replace(',', '.', $parsedUrl['grand_total'][$key]);
            }           
        }

        $this->itemsPerPage = isset($parsedUrl['perPage']) ? $parsedUrl['perPage']['eq'] : $this->itemsPerPage;

        unset($parsedUrl['perPage']);

        return $parsedUrl;
    }

    /**
     * @param string $column
     *
     * @return void
     */
    public function addColumn($column)
    {
        $this->fireEvent('add.column.before.' . $column['index']);

        $this->columns[] = $column;

        $this->setCompleteColumnDetails($column);

        $this->fireEvent('add.column.after.' . $column['index']);
    }

    /**
     * @param string $column
     *
     * @return void
     */
    public function setCompleteColumnDetails($column)
    {
        $this->completeColumnDetails[] = $column;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $queryBuilder
     *
     * @return void
     */
    public function setQueryBuilder($queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @param array $action
     *
     * @return void
     */
    public function addAction($action)
    {
        if (isset($action['title'])) {
            $eventName = strtolower($action['title']);
            $eventName = explode(' ', $eventName);
            $eventName = implode('.', $eventName);
        } else {
            $eventName = null;
        }

        $this->fireEvent('action.before.' . $eventName);

        array_push($this->actions, $action);

        $this->enableAction = true;

        $this->fireEvent('action.after.' . $eventName);
    }

    /**
     * @param array $massAction
     *
     * @return void
     */
    public function addMassAction($massAction)
    {
        if (isset($massAction['label'])) {
            $eventName = strtolower($massAction['label']);
            $eventName = explode(' ', $eventName);
            $eventName = implode('.', $eventName);
        } else {
            $eventName = null;
        }

        $this->fireEvent('mass.action.before.' . $eventName);

        $this->massActions[] = $massAction;

        $this->enableMassAction = true;

        $this->fireEvent('mass.action.after.' . $eventName);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getCollection()
    {
        $parsedUrl = $this->parseUrl();

        foreach ($parsedUrl as $key => $value) {
            if ($key === 'locale') {
                if (! is_array($value)) {
                    unset($parsedUrl[$key]);
                }
            } elseif (! is_array($value)) {
                unset($parsedUrl[$key]);
            }
        }

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
            $this->collection = $this->queryBuilder->orderBy($this->index, $this->sortOrder)->get();
        }

        return $this->collection;
    }

    /**
     * To find the alias of the column and by taking the column name.
     *
     * @param array $columnAlias
     *
     * @return array
     */
    public function findColumnType($columnAlias)
    {
        foreach ($this->completeColumnDetails as $column) {
            if ($column['index'] == $columnAlias) {
                return [$column['type'], $column['index']];
            }
        }
    }

    /**
     * @param \Illuminate\Support\Collection $collection
     * @param array                          $parseInfo
     *
     * @return \Illuminate\Support\Collection
     */
    public function sortOrFilterCollection($collection, $parseInfo)
    {
        foreach ($parseInfo as $key => $info) {
            $columnType = $this->findColumnType($key)[0] ?? null;
            $columnName = $this->findColumnType($key)[1] ?? null;

            if ($key === "sort") {
                $count_keys = count(array_keys($info));

                if ($count_keys > 1) {
                    throw new \Exception('Fatal Error! Multiple Sort keys Found, Please Resolve the URL Manually');
                }

                $columnName = $this->findColumnType(array_keys($info)[0]);

                $collection->orderBy(
                    $columnName[1],
                    array_values($info)[0]
                );
            } else if ($key === "duration" || $key === "type") {
                $collection = $this->prepareTabFilter($collection);
            } elseif ($key === "search") {
                $collection = $this->prepareSearch($collection, $info);
            } else {
                foreach ($this->completeColumnDetails as $index => $column) {
                    if ($column['index'] === $columnName) {
                        $this->completeColumnDetails[$index]['values'] = explode(',', array_values($info)[0]);
                    }

                    if ($column['index'] === $columnName && ! $column['filterable']) {
                        return $collection;
                    }
                }

                foreach ($info as $condition => $filter_value) {
                    if ($condition == "in") {
                        $collection->orWhereIn(
                            $columnName,
                            explode(',', $filter_value)
                        );
                    } else if ($condition == "bw") {
                        $dates = explode(',', $filter_value);

                        if (sizeof($dates) == 2) {
                            if ($dates[1] == "") {
                                $dates[1] = Carbon::today()->format('Y-m-d');
                            }

                            $collection->orWhereBetween(
                                $columnName,
                                $dates
                            );
                        }
                    } else {
                        $collection->where(
                            $columnName,
                            $this->operators[$condition],
                            $filter_value
                        );
                    }
                }
            }
        }

        return $collection;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    protected function fireEvent($name)
    {
        if (isset($name)) {
            $className = get_class($this->invoker);

            $className = last(explode("\\", $className));

            $className = strtolower($className);

            $eventName = $className . '.' . $name;

            Event::dispatch($eventName, $this->invoker);
        }
    }

    /**
     * @return void
     */
    public function prepareMassActions()
    {
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function data()
    {
        $this->addColumns();

        $this->prepareMassActions();

        $this->prepareQueryBuilder();

        $data = $this->prepareResponseData();

        return $data;
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
                $actionCollection['route'] = route($action['route'], ['id' => $row->id]);

                array_push($arrayCollection['data'][$index]->action, $actionCollection);
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

    /**
     * Prepare tab filter.
     *
     * @return collection
     */
    public function prepareTabFilter($collection)
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
                $key = ($key === "duration") ? "created_at" : $key;

                switch ($value) {
                    case 'yesterday':
                        $collection->where(
                            $key,
                            Carbon::yesterday()
                        );
                        break;

                    case 'today':
                        $collection->where(
                            $key,
                            Carbon::today()
                        );
                        break;

                    case 'tomorrow':
                        $collection->where(
                            $key,
                            Carbon::tomorrow()
                        );
                        break;

                    case 'this_week':
                        break;

                    case 'this_month':
                        break;
                }
            }
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
            throw new \Exception('Multiple Search keys Found, Please Resolve the URL Manually');
        }

        if ($count_keys == 1) {
            $collection->where(function ($collection) use ($info) {
                foreach ($this->completeColumnDetails as $column) {
                    if (isset($column['searchable']) && $column['searchable'] == true) {
                        $collection->orWhere($column['index'], 'like', '%' . $info['all'] . '%');
                    }
                }
            });
        }

        return $collection;
    }
}
