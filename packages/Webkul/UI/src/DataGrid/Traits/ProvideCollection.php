<?php

namespace Webkul\UI\DataGrid\Traits;

use Carbon\Carbon;
use Illuminate\Support\Str;

trait ProvideCollection
{
    use ProvideQueryResolver, ProvideQueryStringParser;

    /**
     * Get collections.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCollection()
    {
        $queryStrings = $this->getQueryStrings();

        if (count($queryStrings)) {
            $filteredOrSortedCollection = $this->sortOrFilterCollection(
                $this->queryBuilder,
                $queryStrings
            );

            return $this->collection = $this->generateResults($filteredOrSortedCollection);
        }

        return $this->collection = $this->generateResults($this->queryBuilder);
    }

    /**
     * Sort or filter collection.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @param  array                           $parseInfo
     * @return \Illuminate\Support\Collection
     */
    public function sortOrFilterCollection($collection, $parseInfo)
    {
        foreach ($parseInfo as $key => $info) {
            $columnType = $this->findColumnType($key)[0] ?? null;
            $columnName = $this->findColumnType($key)[1] ?? null;

            switch ($key) {
                case 'type':
                case 'duration':
                case 'scheduled':
                    $this->prepareTabFilter($collection, $key, $info);
                    break;

                case 'sort':
                    $this->sortCollection($collection, $info);
                    break;

                case 'search':
                    $this->searchCollection($collection, $info);
                    break;

                default:
                    if ($this->exceptionCheckInColumns($columnName)) {
                        return $collection;
                    }

                    $this->filterCollection($collection, $info, $columnType, $columnName);
                    break;
            }
        }

        return $collection;
    }

    /**
     * Finalyze your collection here.
     *
     * @return void
     */
    public function formatCollection()
    {
        $this->collection->transform(function ($record) {
            $this->transformActions($record);

            $this->transformColumns($record);

            return $record;
        });
    }

    /**
     * To find the alias of the column and by taking the column name.
     *
     * @param  array  $columnAlias
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
     * Generate full results.
     *
     * @param  object  $queryBuilderOrCollection
     * @return \Illuminate\Support\Collection
     */
    private function generateResults($queryBuilderOrCollection)
    {
        if ($this->paginate) {
            if ($this->itemsPerPage > 0) {
                return $this->paginatedResults($queryBuilderOrCollection);
            }
        } else {
            return $this->defaultResults($queryBuilderOrCollection);
        }
    }

    /**
     * Generate paginated results.
     *
     * @param  object  $queryBuilderOrCollection
     * @return \Illuminate\Support\Collection
     */
    private function paginatedResults($queryBuilderOrCollection)
    {
        return $queryBuilderOrCollection->orderBy(
            $this->index,
            $this->sortOrder
        )->paginate($this->itemsPerPage)->appends(request()->except('page'));
    }

    /**
     * Generate default results.
     *
     * @param  object  $queryBuilderOrCollection
     * @return \Illuminate\Support\Collection
     */
    private function defaultResults($queryBuilderOrCollection)
    {
        return $queryBuilderOrCollection->orderBy($this->index, $this->sortOrder)->get();
    }

    /**
     * Prepare tab filter.
     *
     * @return void
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

                if ($key === "duration") {
                    $column = $this->filterMap["created_at"] ?? "created_at";
                } else if ($key === "scheduled") {
                    $column = "schedule_from";
                } else {
                    $column = $key;
                }

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
    }

    /**
     * Sort collection.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @param  array                           $info
     * @return void
     */
    private function sortCollection($collection, $info)
    {
        $availableOptions = ['asc', 'desc'];

        $selectedSortOption = strtolower(array_values($info)[0]);

        $countKeys = count(array_keys($info));

        if ($countKeys > 1) {
            throw new \Exception(__('ui::app.datagrid.error.multiple-sort-keys-error'));
        }

        $columnName = $this->findColumnType(array_keys($info)[0]);

        $collection->orderBy(
            $columnName[1],
            in_array($selectedSortOption, $availableOptions) ? $selectedSortOption : 'asc'
        );
    }

    /**
     * Search collection.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @param  array                           $info
     * @return void
     */
    private function searchCollection($collection, $info)
    {
        $countKeys = count(array_keys($info));

        if ($countKeys > 1) {
            throw new \Exception(__('ui::app.datagrid.error.multiple-search-keys-error'));
        }

        if ($countKeys == 1) {
            $collection->where(function ($collection) use ($info) {
                foreach ($this->completeColumnDetails as $column) {
                    if ($column['searchable'] == true) {
                        $this->resolve($collection, $column['index'], 'like', '%' . $info['all'] . '%', 'orWhere');
                    }
                }
            });
        }
    }

    /**
     * Filter collection.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @param  array                           $info
     * @param  string                          $columnType
     * @param  string                          $columnName
     * @return void
     */
    private function filterCollection($collection, $info, $columnType, $columnName)
    {
        if (array_keys($info)[0] === 'like' || array_keys($info)[0] === 'nlike') {
            foreach ($info as $condition => $filterValue) {
                $this->resolve($collection, $columnName, $condition, '%' . $filterValue . '%');
            }
        } else {
            foreach ($info as $condition => $filterValue) {

                $condition = ($condition === 'undefined') ? '=' : $condition;

                if ($columnType === 'datetime') {
                    $this->resolve($collection, $columnName, $condition, $filterValue, 'whereDate');
                } else if ($columnType === 'boolean') {
                    $this->resolve($collection, $columnName, $condition, $filterValue, 'where', 'resolveBooleanQuery');
                } else {
                    $this->resolve($collection, $columnName, $condition, $filterValue);
                }
            }
        }
    }

    /**
     * Transform your columns.
     *
     * @parma  object  $record
     * @return void
     */
    private function transformColumns($record)
    {
        foreach($this->columns as $column) {
            if (isset($column['wrapper'])) {
                if (isset($column['closure']) && $column['closure'] == true) {
                    $record->{$column['index']} = $column['wrapper']($record);
                } else {
                    $record->{$column['index']} = htmlspecialchars($column['wrapper']($record));
                }
            } else {
                if ($column['type'] == 'price') {
                    if (isset($column['currencyCode'])) {
                        $record->{$column['index']} = htmlspecialchars(core()->formatPrice($record->{$column['index']}, $column['currencyCode']));
                    } else {
                        $record->{$column['index']} = htmlspecialchars(core()->formatBasePrice($record->{$column['index']}));
                    }
                }
            }
        }
    }

    /**
     * Transform your actions.
     *
     * @parma  object  $record
     * @return void
     */
    private function transformActions($record)
    {
        foreach($this->actions as $action) {
            $toDisplay = (isset($action['condition']) && gettype($action['condition']) == 'object') ? $action['condition']($record) : true;

            $toDisplayKey = $this->generateKeyFromActionTitle($action['title'], '_to_display');
            $record->$toDisplayKey = $toDisplay;

            if ($toDisplay) {
                $urlKey = $this->generateKeyFromActionTitle($action['title'], '_url');
                $record->$urlKey = route($action['route'], $record->{$action['index'] ?? $this->index});
            }
        }
    }

    /**
     * Some exceptions check in column details.
     *
     * @param  string  $columnName
     * @return bool
     */
    private function exceptionCheckInColumns($columnName)
    {
        foreach ($this->completeColumnDetails as $column) {
            if ($column['index'] === $columnName && ! $column['filterable']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate unique key from title.
     *
     * @param  string  $title
     * @param  string  $suffix
     * @return string
     */
    private function generateKeyFromActionTitle($title, $suffix)
    {
        $validatedStrings = Str::slug($title, '_');

        return strtolower($validatedStrings) . $suffix;
    }
}
