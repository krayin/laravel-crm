<?php

namespace Webkul\Automation\Helpers;

class Validator
{
    /**
     * Validate workflow for condition
     *
     * @param  \Webkul\Automation\Contracts\Workflow  $workflow
     * @param  mixed  $entity
     * @return bool
     */
    public function validate($workflow, $entity)
    {
        if (! $workflow->conditions) {
            return true;
        }

        $validConditionCount = $totalConditionCount = 0;

        foreach ($workflow->conditions as $condition) {
            if (! $condition['attribute']
                || ! isset($condition['value'])
                || is_null($condition['value'])
                || $condition['value'] == ''
            ) {
                continue;
            }

            $totalConditionCount++;

            if ($workflow->condition_type == 'and') {
                if (! $this->validateEntity($condition, $entity)) {
                    return false;
                } else {
                    $validConditionCount++;
                }
            } else {
                if ($this->validateEntity($condition, $entity)) {
                    return true;
                }
            }
        }

        return $validConditionCount == $totalConditionCount ? true : false;
    }

    /**
     * Validate object
     *
     * @param  array  $condition
     * @param  mixed  $entity
     * @return bool
     */
    private function validateEntity($condition, $entity)
    {
        return $this->validateAttribute(
            $condition,
            $this->getAttributeValue($condition, $entity)
        );
    }

    /**
     * Return value for the attribute
     *
     * @param  array  $condition
     * @param  mixed  $entity
     * @return bool
     */
    public function getAttributeValue($condition, $entity)
    {
        $value = $entity->{$condition['attribute']};

        if (! in_array($condition['attribute_type'], ['multiselect', 'checkbox'])) {
            return $value;
        }

        return $value ? explode(',', $value) : [];
    }

    /**
     * Validate attribute value for condition
     *
     * @param  array  $condition
     * @param  mixed  $attributeValue
     * @return bool
     */
    public function validateAttribute($condition, $attributeValue)
    {
        switch ($condition['operator']) {
            case '==': case '!=':
                if (is_array($condition['value'])) {
                    if (! is_array($attributeValue)) {
                        return false;
                    }

                    $result = ! empty(array_intersect($condition['value'], $attributeValue));
                } elseif (is_object($attributeValue)) {
                    $result = $attributeValue->value == $condition['value'];
                } else {
                    if (is_array($attributeValue)) {
                        $result = count($attributeValue) == 1 && array_shift($attributeValue) == $condition['value'];
                    } else {
                        $result = $attributeValue == $condition['value'];
                    }
                }

                break;

            case '<=': case '>':
                if (! is_scalar($attributeValue)) {
                    return false;
                }

                $result = $attributeValue <= $condition['value'];

                break;

            case '>=': case '<':
                if (! is_scalar($attributeValue)) {
                    return false;
                }

                $result = $attributeValue >= $condition['value'];

                break;

            case '{}': case '!{}':
                if (is_scalar($attributeValue) && is_array($condition['value'])) {
                    foreach ($condition['value'] as $item) {
                        if (stripos($attributeValue, $item) !== false) {
                            $result = true;

                            break;
                        }
                    }
                } elseif (is_array($condition['value'])) {
                    if (! is_array($attributeValue)) {
                        return false;
                    }

                    $result = ! empty(array_intersect($condition['value'], $attributeValue));
                } else {
                    if (is_array($attributeValue)) {
                        $result = self::validateArrayValues($attributeValue, $condition['value']);
                    } else {
                        $result = strpos($attributeValue, $condition['value']) !== false;
                    }
                }

                break;
        }

        if (in_array($condition['operator'], ['!=', '>', '<', '!{}'])) {
            $result = ! $result;
        }

        return $result;
    }

    /**
     * Validate the condition value against a multi dimensional array recursively
     */
    private static function validateArrayValues(array $attributeValue, string $conditionValue): bool
    {
        if (in_array($conditionValue, $attributeValue, true) === true) {
            return true;
        }

        foreach ($attributeValue as $subValue) {
            if (! is_array($subValue)) {
                continue;
            }

            if (self::validateArrayValues($subValue, $conditionValue) === true) {
                return true;
            }
        }

        return false;
    }
}
