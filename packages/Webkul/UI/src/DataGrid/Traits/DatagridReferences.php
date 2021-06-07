<?php

namespace Webkul\UI\DataGrid\Traits;

trait DatagridReferences
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

    /**
     * Situation handling property when working with custom columns in datagrid, helps abstaining
     * aliases on custom column.
     *
     * @var bool
     */
    protected $enableFilterMap = false;

    /**
     * This is array where aliases and custom column's name are passed
     *
     * @var array
     */
    protected $filterMap = [];

    abstract public function prepareQueryBuilder();

    abstract public function addColumns();

    public function prepareActions() {}

    public function prepareMassActions() {}
}