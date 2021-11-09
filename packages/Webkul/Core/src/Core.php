<?php

namespace Webkul\Core;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Webkul\Core\Repositories\CountryRepository;
use Webkul\Core\Repositories\CoreConfigRepository;
use Webkul\Core\Repositories\CountryStateRepository;

class Core
{
    /**
     * CountryRepository class
     *
     * @var \Webkul\Core\Repositories\CountryRepository
     */
    protected $countryRepository;

    /**
     * CountryStateRepository class
     *
     * @var \Webkul\Core\Repositories\CountryStateRepository
     */
    protected $countryStateRepository;

    /**
     * CoreConfigRepository class
     *
     * @var \Webkul\Core\Repositories\CoreConfigRepository
     */
    protected $coreConfigRepository;

    /**
     * Create a new instance.
     *
     * @param \Webkul\Core\Repositories\CountryRepository  $countryRepository
     * @param \Webkul\Core\Repositories\CountryStateRepository  $countryStateRepository
     * @return void
     */
    public function __construct(
        CountryRepository $countryRepository,
        CoreConfigRepository $coreConfigRepository,
        CountryStateRepository $countryStateRepository
    ) {
        $this->countryRepository = $countryRepository;

        $this->countryStateRepository = $countryStateRepository;

        $this->coreConfigRepository = $coreConfigRepository;
    }

    /**
     * Retrieve all timezones.
     *
     * @return array
     */
    public function timezones()
    {
        $timezones = [];

        foreach (timezone_identifiers_list() as $timezone) {
            $timezones[$timezone] = $timezone;
        }

        return $timezones;
    }

    /**
     * Retrieve all locales.
     *
     * @return array
     */
    public function locales()
    {
        return config('app.available_locales');
    }

    /**
     * Retrieve all countries.
     *
     * @return \Illuminate\Support\Collection
     */
    public function countries()
    {
        return $this->countryRepository->all();
    }

    /**
     * Returns country name by code.
     *
     * @param string $code
     *
     * @return string
     */
    public function country_name($code)
    {
        $country = $this->countryRepository->findOneByField('code', $code);

        return $country ? $country->name : '';
    }

    /**
     * Returns state name by code.
     *
     * @param string $code
     *
     * @return string
     */
    public function state_name($code)
    {
        $state = $this->countryStateRepository->findOneByField('code', $code);

        return $state ? $state->name : $code;
    }

    /**
     * Retrieve all country states.
     *
     * @param string $countryCode
     *
     * @return \Illuminate\Support\Collection
     */
    public function states($countryCode)
    {
        return $this->countryStateRepository->findByField('country_code', $countryCode);
    }

    /**
     * Retrieve all grouped states by country code.
     *
     * @return \Illuminate\Support\Collection
     */
    public function groupedStatesByCountries()
    {
        $collection = [];

        foreach ($this->countryStateRepository->all() as $state) {
            $collection[$state->country_code][] = $state->toArray();
        }

        return $collection;
    }

    /**
     * Retrieve all grouped states by country code.
     *
     * @return \Illuminate\Support\Collection
     */
    public function findStateByCountryCode($countryCode = null, $stateCode = null)
    {
        $collection = [];

        $collection = $this->countryStateRepository->findByField([
            'country_code' => $countryCode,
            'code' => $stateCode
        ]);

        if (count($collection)) {
            return $collection->first();
        } else {
            return false;
        }
    }

    /**
     * Method to sort through the acl items and put them in order.
     *
     * @param array $items
     *
     * @return array
     */
    public function sortItems($items)
    {
        foreach ($items as &$item) {
            if (count($item['children'])) {
                $item['children'] = $this->sortItems($item['children']);
            }
        }

        usort($items, function ($a, $b) {
            if ($a['sort'] == $b['sort']) {
                return 0;
            }

            return ($a['sort'] < $b['sort']) ? -1 : 1;
        });

        return $this->convertToAssociativeArray($items);
    }
    
    /**
     * @param array            $items
     * @param string           $key
     * @param string|int|float $value
     *
     * @return array
     */
    public function array_set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);
        $count = count($keys);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $finalKey = array_shift($keys);

        if (isset($array[$finalKey])) {
            $array[$finalKey] = $this->arrayMerge($array[$finalKey], $value);
        } else {
            $array[$finalKey] = $value;
        }

        return $array;
    }

    /**
     * @param array $items
     *
     * @return array
     */
    public function convertToAssociativeArray($items)
    {
        foreach ($items as $key1 => $level1) {
            unset($items[$key1]);
            $items[$level1['key']] = $level1;

            if (count($level1['children'])) {
                foreach ($level1['children'] as $key2 => $level2) {
                    $temp2 = explode('.', $level2['key']);
                    $finalKey2 = end($temp2);
                    unset($items[$level1['key']]['children'][$key2]);
                    $items[$level1['key']]['children'][$finalKey2] = $level2;

                    if (count($level2['children'])) {
                        foreach ($level2['children'] as $key3 => $level3) {
                            $temp3 = explode('.', $level3['key']);
                            $finalKey3 = end($temp3);
                            unset($items[$level1['key']]['children'][$finalKey2]['children'][$key3]);
                            $items[$level1['key']]['children'][$finalKey2]['children'][$finalKey3] = $level3;
                        }
                    }

                }
            }
        }

        return $items;
    }

    /**
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    protected function arrayMerge(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->arrayMerge($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Create singleton object through single facade.
     *
     * @param string $className
     * @return mixed
     */
    public function getSingletonInstance($className)
    {
        static $instances = [];

        if (array_key_exists($className, $instances)) {
            return $instances[$className];
        }

        return $instances[$className] = app($className);
    }

    /**
     * Format date
     *
     * @return string
     */
    public function formatDate($date, $format = 'd M Y h:iA')
    {
        return Carbon::parse($date)->format($format);
    }

    /**
     * Return currency symbol from currency code.
     *
     * @param float $price
     *
     * @return string
     */
    public function currencySymbol($code)
    {
        $formatter = new \NumberFormatter(app()->getLocale() . '@currency=' . $code, \NumberFormatter::CURRENCY);

        return $formatter->getSymbol(\NumberFormatter::CURRENCY_SYMBOL);
    }

    /**
     * Format price with base currency symbol. This method also give ability to encode
     * the base currency symbol and its optional.
     *
     * @param  float $price
     *
     * @return string
     */
    public function formatBasePrice($price)
    {
        if (is_null($price)) {
            $price = 0;
        }

        $formater = new \NumberFormatter(app()->getLocale(), \NumberFormatter::CURRENCY);

        return $formater->formatCurrency($price, config('app.currency'));
    }

    /**
     * @param string $fieldName
     *
     * @return array
     */
    public function getConfigField($fieldName)
    {
        foreach (config('core_config') as $coreData) {
            if (isset($coreData['fields'])) {
                foreach ($coreData['fields'] as $field) {
                    $name = $coreData['key'] . '.' . $field['name'];

                    if ($name == $fieldName) {
                        return $field;
                    }
                }
            }
        }
    }

    /**
     * Retrieve information for configuration
     *
     * @param string          $field
     * @param int|string|null $channelId
     * @param string|null     $locale
     *
     * @return mixed
     */
    public function getConfigData($field)
    {
        $fields = $this->getConfigField($field);

        $coreConfigValue = $this->coreConfigRepository->findOneWhere([
            'code' => $field,
        ]);

        if (! $coreConfigValue) {
            $fields = explode(".", $field);

            array_shift($fields);

            $field = implode(".", $fields);

            return Config::get($field);
        }

        return $coreConfigValue->value;
    }
}