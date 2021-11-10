<?php

namespace Webkul\UI\DataGrid\Traits;

use Illuminate\Support\Str;

trait ProvideCollection
{
    use ProvideTabFilters, ProvideQueryResolver, ProvideQueryStringParser;

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
                    /**
                 * All sorting related method will go here.
                 */
                case 'sort':
                    $this->sortCollection($collection, $info);
                    break;

                    /**
                     * All search related method will go here.
                     */
                case 'search':
                    $this->searchCollection($collection, $info);
                    break;

                    /**
                     *  Default case is for filter. All filter related method will go here.
                     */
                default:
                    if ($this->exceptionCheckInColumns($columnName)) {
                        return $collection;
                    }

                    $this->attachColumnValues($columnName, $info);

                    if (in_array($key, $this->customTabFilters)) {
                        $this->filterCollectionFromTabFilter($collection, $key, $info);
                        break;
                    }

                    $this->filterCollection($collection, $info, $columnType, $columnName);
                    break;
            }
        }

        return $collection;
    }

    /**
     * Finalyze your collection here. If you want to manipulate actions, then
     * go to action method or if you want to manipulate columns then go to columns method.
     *
     * @return void
     */
    public function formatCollection()
    {
        $this->collection->transform(function ($record) {
            $this->transformRows($record);

            $this->transformActions($record);

            $this->transformColumns($record);

            return $record;
        });

        /**
         * To Do (@devansh-webkul): Need to handle from record's column. For this frontend also needs to adjust.
         */
        foreach ($this->columns as $index => $column) {
            if (! isset($this->completeColumnDetails[$index]['searchable'])) {
                $this->completeColumnDetails[$index]['searchable'] = true;
            }

            if (! isset($this->completeColumnDetails[$index]['filterable'])) {
                $this->completeColumnDetails[$index]['filterable'] = true;
            }

            if (isset($column['type']) && $column['type'] == 'date_range') {
                if (! isset($this->completeColumnDetails[$index]['values'])) {
                    $this->completeColumnDetails[$index]['values'] = ['', ''];
                }
            }
        }
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
     * Prepare column filtered values.
     *
     * @return collection
     */
    public function attachColumnValues($columnName, $info)
    {
        foreach ($this->completeColumnDetails as $index => $column) {
            if ($column['index'] == $columnName) {
                $this->completeColumnDetails[$index]['values'] = explode(',', array_values($info)[0]);
            }
        }

        return $this->completeColumnDetails;
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
            $this->filterMap[$columnName[1]] ?? $columnName[1],
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
                    /**
                     * If searchable key not found, then searching will work because default will be true.
                     *
                     * Or if searchable key found, then it should be `true` for working or `false` for disabling.
                     */
                    if (! isset($column['searchable']) || (isset($column['searchable']) && $column['searchable'])) {
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
        foreach ($info as $condition => $filterValue) {
            switch (array_keys($info)[0]) {
                case 'like':
                case 'nlike':
                    $this->resolve($collection, $columnName, $condition, '%' . $filterValue . '%');
                    break;

                case 'in':
                    $collection->where(function ($query) use ($columnName, $filterValue) {
                        foreach (explode(',', $filterValue) as $value) {
                            $query->orWhere(function ($query) use ($columnName, $value) {
                                $this->resolve($query, $columnName, 'like', "%{$value}%", 'orWhere');
                            });
                        }
                    });
                    break;

                case 'bw':
                    $dates = explode(',', $filterValue);

                    if (! empty($dates) && count($dates) == 2) {
                        if ($dates[0] != '') {
                            $this->resolve($collection, $columnName, 'gte', $dates[0], 'whereDate');
                        }

                        if ($dates[1] != '') {
                            $this->resolve($collection, $columnName, 'lte', $dates[1], 'whereDate');
                        }
                    }
                    break;

                default:
                    $condition = ($condition === 'undefined') ? '=' : $condition;

                    if ($columnType === 'datetime') {
                        $this->resolve($collection, $columnName, $condition, $filterValue, 'whereDate');
                    } else if ($columnType === 'boolean') {
                        $this->resolve($collection, $columnName, $condition, $filterValue, 'where', 'resolveBooleanQuery');
                    } else {
                        $this->resolve($collection, $columnName, $condition, $filterValue);
                    }
                    break;
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
        foreach ($this->columns as $index => $column) {
            if (isset($column['closure'])) {
                $record->{$column['index']} = $column['closure']($record);
            } else {
                if ($column['type'] == 'price') {
                    if (isset($column['currencyCode'])) {
                        $record->{$column['index']} = htmlspecialchars(core()->formatPrice($record->{$column['index']}, $column['currencyCode']));
                    } else {
                        $record->{$column['index']} = htmlspecialchars(core()->formatBasePrice($record->{$column['index']}));
                    }
                } else {
                    $record->{$column['index']} = htmlspecialchars($record->{$column['index']});
                }
            }
        }
    }

    /**
     * Transform your rows.
     *
     * @parma  object  $record
     * @return void
     */
    private function transformRows($record)
    {
        $record->rowProperties = isset($this->rowProperties['condition']) && $this->rowProperties['condition']($record)
            ? $record->rowProperties = $this->rowProperties
            : [];
    }

    /**
     * Transform your actions.
     *
     * @parma  object  $record
     * @return void
     */
    private function transformActions($record)
    {
        foreach ($this->actions as $action) {
            $toDisplay = (isset($action['condition']) && gettype($action['condition']) == 'object') ? $action['condition']($record) : true;

            $toDisplayKey = $this->generateKeyFromActionTitle($action['title'], '_to_display');
            $record->$toDisplayKey = $toDisplay;

            if ($toDisplay) {
                $urlKey = $this->generateKeyFromActionTitle($action['title'], '_url');

                if (isset($action['params']) && ! empty($action['params'])) {
                    $action['params'][] = $record->{$action['index'] ?? $this->index};

                    $record->$urlKey = route($action['route'], $action['params']);
                } else {
                    $record->$urlKey = route($action['route'], $record->{$action['index'] ?? $this->index});
                }
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
            if ($column['index'] === $columnName) {
                /**
                 * If tab filter is activated then filterable should be done.
                 */
                if (collect($this->tabFilters)->contains('key', $columnName)) {
                    return false;
                }

                /**
                 * After passing from tab filter, it will check for the filerable
                 * properties in column.
                 */
                if (isset($column['filterable']) && ! $column['filterable']) {
                    return true;
                }
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
