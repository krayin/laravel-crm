<?php

namespace Webkul\UI\DataGrid;

use Carbon\Carbon;
use Webkul\UI\DataGrid\Traits\DatagridCollection;

abstract class DataGrid
{
    use DatagridCollection;

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
        $eventName = null;

        if (isset($action['title'])) {
            $eventName = strtolower($action['title']);
            $eventName = explode(' ', $eventName);
            $eventName = implode('.', $eventName);
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
                case 'scheduled':
                    $collection = $this->prepareTabFilter($collection, $key, $info);
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
