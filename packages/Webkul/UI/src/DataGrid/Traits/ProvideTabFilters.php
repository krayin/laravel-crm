<?php

namespace Webkul\UI\DataGrid\Traits;

use Carbon\Carbon;

trait ProvideTabFilters
{
    /**
     * Tab filters.
     *
     * @var array
     */
    protected $tabFilters = [];

    /**
     * Custom tab filter listing.
     *
     * @var array
     */
    protected $customTabFilters = ['type', 'entity_type', 'duration', 'scheduled'];

    /**
     * Prepare tab filters. Optional method.
     *
     * @return array
     */
    public function prepareTabFilters()
    {
    }

    /**
     * Add tab filter.
     *
     * @param  array  $filterConfig
     * @return void
     */
    public function addTabFilter($filterConfig)
    {
        if (! (($filterConfig['value_type'] ?? false) == 'lookup')) {
            foreach ($filterConfig['values'] as $valueIndex => $value) {
                $filterConfig['values'][$valueIndex]['name'] = trans($filterConfig['values'][$valueIndex]['name']);
            }
        }

        $this->tabFilters[] = $filterConfig;
    }

    /**
     * Resolve custom tab filters column.
     *
     * @param  string  $key
     * @return string
     */
    public function resolveCustomTabFiltersColumn($key)
    {
        switch ($key) {
            case 'scheduled':
                return $this->filterMap['schedule_from'] ?? 'schedule_from';

            default:
                return $this->filterMap[$key] ?? $key;
        }
    }

    /**
     * Resolve custom tab filter query.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @param  string                          $key
     * @param  array                           $info
     * @return void
     */
    public function resolveCustomTabFiltersQuery($collection, $key, $info)
    {
        $value = array_values($info)[0];

        $column = $this->resolveCustomTabFiltersColumn($key);

        switch ($value) {
            case 'yesterday':
                $collection->whereDate(
                    $column,
                    Carbon::yesterday()->format('Y-m-d')
                );
                break;

            case 'today':
                $collection->whereDate(
                    $column,
                    Carbon::today()->format('Y-m-d')
                );
                break;

            case 'tomorrow':
                $collection->whereDate(
                    $column,
                    Carbon::tomorrow()->format('Y-m-d')
                );
                break;

            case 'this_week':
                $collection->whereBetween(
                    $column,
                    [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
                );
                break;

            case 'this_month':
                $collection
                    ->whereMonth($column, Carbon::now()->format('m'))
                    ->whereYear($column, Carbon::now()->format('Y'));
                break;

            default:
                if ($value != 'all') {
                    if ($key == 'duration') {
                        $dates = explode(',', $value);

                        if (! empty($dates) && count($dates) == 2) {
                            if ($dates[1] == '') {
                                $dates[1] = Carbon::today()->format('Y-m-d');
                            }

                            $collection->whereDate('schedule_from', '>=', $dates[0]);

                            $collection->whereDate('schedule_to', '<=', $dates[1]);
                        }
                    } else {
                        $collection->where($column, $value);
                    }
                }
                break;
        }
    }

    /**
     * Filter collection from tab filter.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @param  string                          $key
     * @param  array                           $info
     * @return void
     */
    public function filterCollectionFromTabFilter($collection, $key, $info)
    {
        foreach ($this->tabFilters as $filterIndex => $filter) {
            if (in_array($key, $this->customTabFilters)) {
                foreach ($filter['values'] as $filterValueIndex => $filterValue) {
                    if (
                        (array_keys($info)[0] == 'bw' && $filterValue['key'] == 'custom') ||
                        $filterValue['key'] == array_values($info)[0]
                    ) {
                        $this->tabFilters[$filterIndex]['values'][$filterValueIndex]['isActive'] = true;
                    } else if ($filterValue['key'] == 'all') {
                        $this->tabFilters[$filterIndex]['values'][$filterValueIndex]['isActive'] = false;
                    }
                }

                $this->resolveCustomTabFiltersQuery($collection, $key, $info);
            }
        }
    }
}
