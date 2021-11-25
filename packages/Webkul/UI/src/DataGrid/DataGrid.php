<?php

namespace Webkul\UI\DataGrid;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Webkul\UI\DataGrid\Traits\ProvideBouncer;
use Webkul\UI\DataGrid\Traits\ProvideCollection;
use Webkul\UI\DataGrid\Traits\ProvideExceptionHandler;
use Webkul\UI\DataGrid\Traits\ProvideExport;

abstract class DataGrid
{
    use ProvideBouncer, ProvideCollection, ProvideExceptionHandler, ProvideExport;

    /**
     * Set index columns, ex: id. Default index is `id`.
     *
     * @var int
     */
    protected $index = 'id';

    /**
     * Default sort order of datagrid.
     *
     * @var string
     */
    protected $sortOrder = 'asc';

    /**
     * Hold query builder instance of the query prepared by executing datagrid
     * class method `setQueryBuilder`.
     *
     * @var object
     */
    protected $queryBuilder;

    /**
     * Situation handling property when working with custom columns in datagrid, helps abstaining
     * aliases on custom column.
     *
     * @var bool
     */
    protected $enableFilterMap = false;

    /**
     * This is array where aliases and custom column's name are passed.
     *
     * @var array
     */
    protected $filterMap = [];

    /**
     * Row properties.
     *
     * @var array
     */
    protected $rowProperties = [];

    /**
     * Array to hold all the columns which will be displayed on frontend.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Complete column details.
     *
     * @var array
     */
    protected $completeColumnDetails = [];

    /**
     * To enable actions or not.
     *
     * @var bool
     */
    protected $enableAction = false;

    /**
     * Set of handly click tools which you could be using for various operations.
     * ex: dyanmic and static redirects, deleting, etc.
     *
     * @var array
     */
    protected $actions = [];

    /**
     * To show mass action or not.
     *
     * @var bool
     */
    protected $enableMassAction = false;

    /**
     * Works on selection of values index column as comma separated list as response
     * to your endpoint set as route.
     *
     * @var array
     */
    protected $massActions = [];

    /**
     * Final result of the datagrid program that is collection object.
     *
     * @var object
     */
    protected $collection;

    /**
     * Parsed value of the url parameters.
     *
     * @var array
     */
    protected $parse;

    /**
     * Paginate the collection or not.
     *
     * @var bool
     */
    protected $paginate = true;

    /**
     * Enable items per page.
     *
     * @var boolean
     */
    protected $enablePerPage = true;

    /**
     * If paginated then value of pagination.
     *
     * @var int
     */
    protected $itemsPerPage = 10;

    /**
     * Enable search field.
     *
     * @var boolean
     */
    protected $enableSearch = true;

    /**
     * Enable sidebar filters.
     *
     * @var boolean
     */
    protected $enableFilters = true;

    /**
     * Operators mapping.
     *
     * @var array
     */
    protected $operators = [
        'eq'       => '=',
        'lt'       => '<',
        'gt'       => '>',
        'lte'      => '<=',
        'gte'      => '>=',
        'neqs'     => '<>',
        'neqn'     => '!=',
        'eqo'      => '<=>',
        'like'     => 'like',
        'blike'    => 'like binary',
        'nlike'    => 'not like',
        'ilike'    => 'ilike',
        'and'      => '&',
        'bor'      => '|',
        'regex'    => 'regexp',
        'notregex' => 'not regexp',
    ];

    /**
     * Bindings.
     *
     * @var array
     */
    protected $bindings = [
        0 => 'select',
        1 => 'from',
        2 => 'join',
        3 => 'where',
        4 => 'having',
        5 => 'order',
        6 => 'union',
    ];

    /**
     * Select components.
     *
     * @var array
     */
    protected $selectcomponents = [
        0  => 'aggregate',
        1  => 'columns',
        2  => 'from',
        3  => 'joins',
        4  => 'wheres',
        5  => 'groups',
        6  => 'havings',
        7  => 'orders',
        8  => 'limit',
        9  => 'offset',
        10 => 'lock',
    ];

    /**
     * Export option.
     *
     * @var boolean
     */
    protected $export = false;

    /**
     * Create datagrid instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->invoker = $this;
    }

    /**
     * Initial stage. Add your extra settings here.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Abstract method. Required method.
     *
     * @return void
     */
    abstract public function prepareQueryBuilder();

    /**
     * Abstract method. Required method.
     *
     * @return void
     */
    abstract public function addColumns();

    /**
     * Prepare actions. Optional method.
     *
     * @return void
     */
    public function prepareActions()
    {
    }

    /**
     * Preprare mass actions. Optional method.
     *
     * @return void
     */
    public function prepareMassActions()
    {
    }

    /**
     * Trigger event.
     *
     * @param  string  $name
     * @return void
     */
    public function fireEvent($name)
    {
        if (isset($name)) {
            $className = get_class($this->invoker);

            $className = last(explode('\\', $className));

            $className = strtolower($className);

            $eventName = $className . '.' . $name;

            Event::dispatch($eventName, $this->invoker);
        }
    }

    /**
     * Prepare row.
     *
     * @return void
     */
    public function setRowProperties(array $rowProperties)
    {
        $this->checkRequiredRowPropertiesKeys($rowProperties);

        $this->rowProperties = $rowProperties;
    }

    /**
     * Set query builder.
     *
     * @param  \Illuminate\Database\Query\Builder  $queryBuilder
     * @return void
     */
    public function setQueryBuilder($queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Set complete column details.
     *
     * @param  string  $column
     * @return void
     */
    public function setCompleteColumnDetails($column)
    {
        $this->completeColumnDetails[] = $column;
    }

    /**
     * Add the index as alias of the column and use the column to make things happen.
     *
     * @param  string  $alias
     * @param  string  $column
     * @return void
     */
    public function addFilter($alias, $column)
    {
        $this->filterMap[$alias] = $column;

        $this->enableFilterMap = true;
    }

    /**
     * Add column.
     *
     * @param  string  $column
     * @return void
     */
    public function addColumn($column)
    {
        $this->checkRequiredColumnKeys($column);

        $this->fireEvent('add.column.before.' . $column['index']);

        $this->columns[] = $column;

        $this->setCompleteColumnDetails($column);

        $this->fireEvent('add.column.after.' . $column['index']);
    }

    /**
     * Add action. Some datagrids are used in shops also. So extra
     * parameters is their. If needs to give an access just pass true
     * in second param.
     *
     * @param  array  $action
     * @param  bool   $specialPermission
     * @return void
     */
    public function addAction($action, $specialPermission = false)
    {
        $this->checkRequiredActionKeys($action);

        $this->checkPermissions($action, $specialPermission, function ($action, $eventName) {
            $this->fireEvent('action.before.' . $eventName);

            $action['key'] = Str::slug($action['title'], '_');

            $this->actions[] = $action;

            $this->enableAction = true;

            $this->fireEvent('action.after.' . $eventName);
        });
    }

    /**
     * Add mass action. Some datagrids are used in shops also. So extra
     * parameters is their. If needs to give an access just pass true
     * in second param.
     *
     * @param  array  $massAction
     * @param  bool   $specialPermission
     * @return void
     */
    public function addMassAction($massAction, $specialPermission = false)
    {
        $massAction['route'] = $this->getRouteNameFromUrl($massAction['action'], $massAction['method']);

        $this->checkPermissions($massAction, $specialPermission, function ($action, $eventName) {
            $this->fireEvent('mass.action.before.' . $eventName);

            $this->massActions[] = $action;
            $this->enableMassAction = true;

            $this->fireEvent('mass.action.after.' . $eventName);
        }, 'label');
    }

    /**
     * Prepare data for json response.
     *
     * @return array
     */
    public function prepareData()
    {
        return [
            'index'             => $this->index,
            'export'            => $this->export,
            'className'         => Crypt::encryptString(get_called_class()),
            'records'           => $this->collection,
            'columns'           => $this->completeColumnDetails,
            'tabFilters'        => $this->tabFilters,
            'customTabFilters'  => $this->customTabFilters,
            'enableActions'     => $this->enableAction,
            'actions'           => $this->actions,
            'enableMassActions' => $this->enableMassAction,
            'massActions'       => $this->massActions,
            'paginated'         => $this->paginate,
            'itemsPerPage'      => $this->itemsPerPage,
            'enableSearch'      => $this->enableSearch,
            'enablePerPage'     => $this->enablePerPage,
            'enableFilters'     => $this->enableFilters,
        ];
    }

    /**
     * Get json data.
     *
     * @return object
     */
    public function toJson()
    {
        $this->init();

        $this->addColumns();

        $this->prepareTabFilters();

        $this->prepareActions();

        $this->prepareMassActions();

        $this->prepareQueryBuilder();

        $this->getCollection();

        $this->formatCollection();

        return response()->json($this->prepareData());
    }
}
