<?php

namespace Webkul\Admin\Traits;

/**
 * Single place for all dropdown options. Sets of sorted dropdown
 * options methods. Use as per your need.
 */
trait ProvideDropdownOptions
{
    /**
     * Dropdown choices.
     *
     * @var array
     */
    public $booleanDropdownChoices = [
        'active_inactive',
        'yes_no',
    ];

    /**
     * Is boolean dropdown choice exists.
     *
     * @param  string  $choice
     */
    public function isBooleanDropdownChoiceExists($choice): bool
    {
        return in_array($choice, $this->booleanDropdownChoices);
    }

    /**
     * Get boolean dropdown options.
     *
     * @param  string  $choice
     */
    public function getBooleanDropdownOptions($choice = 'active_inactive'): array
    {
        return $this->isBooleanDropdownChoiceExists($choice) && $choice == 'active_inactive'
            ? $this->getActiveInactiveDropdownOptions()
            : $this->getYesNoDropdownOptions();
    }

    /**
     * Get active/inactive dropdown options.
     */
    public function getActiveInactiveDropdownOptions(): array
    {
        return [
            [
                'value'    => '',
                'label'    => __('admin::app.common.select-options'),
                'disabled' => true,
                'selected' => true,
            ],
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

    /**
     * Get yes/no dropdown options.
     */
    public function getYesNoDropdownOptions(): array
    {
        return [
            [
                'value'    => '',
                'label'    => __('admin::app.common.select-options'),
                'disabled' => true,
                'selected' => true,
            ],
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
            ],
        ];
    }

    /**
     * Get user dropdown options.
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
            ...$options,
        ];
    }

    /**
     * Get lead source options.
     */
    public function getLeadSourcesOptions(): array
    {
        $options = app(\Webkul\Lead\Repositories\SourceRepository::class)
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
            ...$options,
        ];
    }

    /**
     * Get organization dropdown options.
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
            ...$options,
        ];
    }

    /**
     * Get role dropdown options.
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
     */
    public function getActivityTypeDropdownOptions(): array
    {
        return [
            [
                'label'    => trans('admin::app.common.select-type'),
                'value'    => '',
                'disabled' => true,
                'selected' => true,
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

    /**
     * Get attribute type dropdown options.
     */
    public function getAttributeTypeDropdownOptions(): array
    {
        return [
            [
                'label'    => trans('admin::app.common.select-options'),
                'value'    => '',
                'disabled' => true,
                'selected' => true,
            ],
            [
                'label'    => trans('admin::app.common.system_attribute'),
                'value'    => '0',
                'disabled' => false,
                'selected' => false,
            ],
            [
                'label'    => trans('admin::app.common.custom_attribute'),
                'value'    => '1',
                'disabled' => false,
                'selected' => false,
            ],
        ];
    }

    /**
     * Get organization dropdown options.
     */
    public function getWarehouseDropdownOptions(): array
    {
        $options = app(\Webkul\Warehouse\Repositories\WarehouseRepository::class)
            ->get(['id as value', 'name as label'])
            ->map(function ($item, $key) {
                $item->disabled = false;

                $item->selected = false;

                return $item;
            })
            ->toArray();

        return [
            [
                'label'    => __('admin::app.common.select-warehouse'),
                'value'    => '',
                'disabled' => true,
                'selected' => true,
            ],
            ...$options,
        ];
    }
}
