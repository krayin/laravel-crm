<?php

namespace Webkul\Admin\Traits;

/**
 * Single place for all dropdown options. Sets of sorted dropdown
 * options methods. Use as per your need.
 */
trait ProvideDropdownOptions
{
    /**
     * Get boolean dropdown options.
     *
     * @return array
     */
    public function getBooleanDropdownOptions($choice = 'active_inactive'): array
    {
        if ($choice == 'active_inactive') {
            return [
                [
                    'label'    => trans('admin::app.datagrid.active'),
                    'value'    => 1,
                    'disabled' => false,
                    'selected' => false,
                ], [
                    'label'    => trans('admin::app.datagrid.inactive'),
                    'value'    => 0,
                    'disabled' => false,
                    'selected' => false,
                ],
            ];
        }

        return [
            [
                'value'    => 0,
                'label'    => __('admin::app.common.no'),
                'disabled' => false,
                'selected' => false,
            ], [
                'value'    => 1,
                'label'    => __('admin::app.common.yes'),
                'disabled' => false,
                'selected' => false,
            ]
        ];
    }

    /**
     * Get user dropdown options.
     *
     * @return array
     */
    public function getUserDropdownOptions(): array
    {
        $options = app(\Webkul\User\Repositories\UserRepository::class)
            ->get(['id as value', 'name as label'])
            ->map(function ($item, $key) {
                $item->disabled = false;

                $item->selected = false;

                return $item;
            })
            ->toArray();

        return [
            [
                'label'    => __('admin::app.common.select-users'),
                'value'    => '',
                'disabled' => true,
                'selected' => true,
            ],
            ...$options
        ];
    }

    /**
     * Get organization dropdown options.
     *
     * @return array
     */
    public function getOrganizationDropdownOptions(): array
    {
        $options = app(\Webkul\Contact\Repositories\OrganizationRepository::class)
            ->get(['id as value', 'name as label'])
            ->map(function ($item, $key) {
                $item->disabled = false;

                $item->selected = false;

                return $item;
            })
            ->toArray();

        return [
            [
                'label'    => __('admin::app.common.select-organization'),
                'value'    => '',
                'disabled' => true,
                'selected' => true,
            ],
            ...$options
        ];
    }

    /**
     * Get role dropdown options.
     *
     * @return array
     */
    public function getRoleDropdownOptions(): array
    {
        return [
            [
                'label'    => trans('admin::app.settings.roles.all'),
                'value'    => 'all',
                'disabled' => false,
                'selected' => false,
            ], [
                'label'    => trans('admin::app.settings.roles.custom'),
                'value'    => 'custom',
                'disabled' => false,
                'selected' => false,
            ],
        ];
    }

    /**
     * Get activity type dropdown options.
     *
     * @return array
     */
    public function getActivityTypeOptions(): array
    {
        return [
            [
                'label'    => trans('admin::app.common.select-type'),
                'value'    => 'call',
                'disabled' => false,
                'selected' => false,
            ], [
                'label'    => trans('admin::app.common.select-call'),
                'value'    => 'call',
                'disabled' => false,
                'selected' => false,
            ], [
                'label'    => trans('admin::app.common.select-meeting'),
                'value'    => 'meeting',
                'disabled' => false,
                'selected' => false,
            ], [
                'label'    => trans('admin::app.common.select-lunch'),
                'value'    => 'lunch',
                'disabled' => false,
                'selected' => false,
            ],
        ];
    }
}
