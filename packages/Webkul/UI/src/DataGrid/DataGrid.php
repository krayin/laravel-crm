<?php

namespace Webkul\UI\DataGrid;

use Carbon\Carbon;
use Webkul\UI\DataGrid\Traits\DatagridCollection;

abstract class DataGrid
{
    use DatagridCollection;

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

            switch ($key) {
                case 'sort':
                    $collection = $this->filterCollection($collection, $info, $columnName, "sort");
                    break;

                case 'type':
                case 'duration':
                    $collection = $this->prepareTabFilter($collection);
                    break;

                case 'search':
                    $collection = $this->prepareSearch($collection, $info);
                    break;

                default:
                    $this->attachColumnValues($columnName, $info);

                    $collection = $this->filterCollection($collection, $info, $columnName);
                    break;
            }
        }

        return $collection;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function toArray()
    {
        $this->addColumns();

        $this->prepareActions();

        $this->prepareMassActions();

        $this->prepareQueryBuilder();

        $data = $this->prepareResponseData();

        return $data;
    }
}
