<?php

namespace Webkul\Core\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\Core\Contracts\CoreConfig;
use Webkul\Core\Eloquent\Repository;

class CoreConfigRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return CoreConfig::class;
    }

    /**
     * Get the configuration title.
     */
    protected function getTranslatedTitle(mixed $configuration): string
    {
        if (
            method_exists($configuration, 'getTitle')
            && ! is_null($configuration->getTitle())
        ) {
            return trans($configuration->getTitle());
        }

        if (
            method_exists($configuration, 'getName')
            && ! is_null($configuration->getName())
        ) {
            return trans($configuration->getName());
        }

        return '';
    }

    /**
     * Get children and fields.
     */
    protected function getChildrenAndFields(mixed $configuration, string $searchTerm, array $path, array &$results): void
    {
        if (
            method_exists($configuration, 'getChildren')
            || method_exists($configuration, 'getFields')
        ) {
            $children = $configuration->haveChildren()
                ? $configuration->getChildren()
                : $configuration->getFields();

            $tempPath = array_merge($path, [[
                'key'   => $configuration->getKey() ?? null,
                'title' => $this->getTranslatedTitle($configuration),
            ]]);

            $results = array_merge($results, $this->search($children, $searchTerm, $tempPath));
        }
    }

    /**
     * Search configuration.
     *
     * @param  array  $items
     */
    public function search(Collection $items, string $searchTerm, array $path = []): array
    {
        $results = [];

        foreach ($items as $configuration) {
            $title = $this->getTranslatedTitle($configuration);

            if (
                stripos($title, $searchTerm) !== false
                && count($path)
            ) {
                $queryParam = $path[1]['key'] ?? $configuration->getKey();

                $results[] = [
                    'title' => implode(' > ', [...Arr::pluck($path, 'title'), $title]),
                    'url'   => route('admin.configuration.index', Str::replace('.', '/', $queryParam)),
                ];
            }

            $this->getChildrenAndFields($configuration, $searchTerm, $path, $results);
        }

        return $results;
    }

    /**
     * Create core configuration.
     */
    public function create(array $data): void
    {
        unset($data['_token']);

        $preparedData = [];

        foreach ($data as $method => $fieldData) {
            $recursiveData = $this->recursiveArray($fieldData, $method);

            foreach ($recursiveData as $fieldName => $value) {
                if (
                    is_array($value)
                    && isset($value['delete'])
                ) {
                    $coreConfigValues = $this->model->where('code', $fieldName)->get();

                    if ($coreConfigValues->isNotEmpty()) {
                        foreach ($coreConfigValues as $coreConfig) {
                            if (! empty($coreConfig['value'])) {
                                Storage::delete($coreConfig['value']);
                            }

                            parent::delete($coreConfig['id']);
                        }
                    }

                    continue;
                }
            }

            foreach ($recursiveData as $fieldName => $value) {
                if (is_array($value)) {
                    foreach ($value as $key => $val) {
                        $fieldNameWithKey = $fieldName.'.'.$key;

                        $coreConfigValues = $this->model->where('code', $fieldNameWithKey)->get();

                        if (request()->hasFile($fieldNameWithKey)) {
                            $val = request()->file($fieldNameWithKey)->store('configuration');
                        }

                        if ($coreConfigValues->isNotEmpty()) {
                            foreach ($coreConfigValues as $coreConfig) {
                                if (request()->hasFile($fieldNameWithKey)) {
                                    Storage::delete($coreConfig['value']);
                                }

                                parent::update(['code' => $fieldNameWithKey, 'value' => $val], $coreConfig->id);
                            }
                        } else {
                            parent::create(['code' => $fieldNameWithKey, 'value' => $val]);
                        }
                    }
                } else {
                    if (request()->hasFile($fieldName)) {
                        $value = request()->file($fieldName)->store('configuration');
                    }

                    $preparedData[] = [
                        'code'  => $fieldName,
                        'value' => $value,
                    ];
                }
            }
        }

        if (! empty($preparedData)) {
            foreach ($preparedData as $dataItem) {
                $coreConfigValues = $this->model->where('code', $dataItem['code'])->get();

                if ($coreConfigValues->isNotEmpty()) {
                    foreach ($coreConfigValues as $coreConfig) {
                        parent::update($dataItem, $coreConfig->id);
                    }
                } else {
                    parent::create($dataItem);
                }
            }
        }

        Event::dispatch('core.configuration.save.after');
    }

    /**
     * Recursive array.
     */
    public function recursiveArray(array $formData, string $method): array
    {
        static $data = [];

        static $recursiveArrayData = [];

        foreach ($formData as $form => $formValue) {
            $value = $method.'.'.$form;

            if (is_array($formValue)) {
                $dim = $this->countDim($formValue);

                if ($dim > 1) {
                    $this->recursiveArray($formValue, $value);
                } elseif ($dim == 1) {
                    $data[$value] = $formValue;
                }
            }
        }

        foreach ($data as $key => $value) {
            $field = core()->getConfigField($key);

            if ($field) {
                $recursiveArrayData[$key] = $value;
            } else {
                foreach ($value as $key1 => $val) {
                    $recursiveArrayData[$key.'.'.$key1] = $val;
                }
            }
        }

        return $recursiveArrayData;
    }

    /**
     * Return dimension of the array.
     */
    public function countDim(array|string $array): int
    {
        if (is_array(reset($array))) {
            $return = $this->countDim(reset($array)) + 1;
        } else {
            $return = 1;
        }

        return $return;
    }
}
