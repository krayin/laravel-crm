<?php

namespace Webkul\Activity\Traits;

use Webkul\Activity\Repositories\ActivityRepository;
use Webkul\Attribute\Contracts\AttributeValue;
use Webkul\Attribute\Repositories\AttributeValueRepository;

trait LogsActivity
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function ($model) {
            if (! method_exists($model->entity ?? $model, 'activities')) {
                return;
            }

            if (! $model instanceof AttributeValue) {
                $activity = app(ActivityRepository::class)->create([
                    'type'    => 'system',
                    'title'   => trans('admin::app.activities.created'),
                    'is_done' => 1,
                    'user_id' => auth()->check()
                        ? auth()->id()
                        : null,
                ]);

                $model->activities()->attach($activity->id);

                return;
            }

            static::logActivity($model);
        });

        static::updated(function ($model) {
            if (! method_exists($model->entity ?? $model, 'activities')) {
                return;
            }

            static::logActivity($model);
        });

        static::deleting(function ($model) {
            if (! method_exists($model->entity ?? $model, 'activities')) {
                return;
            }

            $model->activities()->delete();
        });
    }

    /**
     * Create activity.
     */
    protected static function logActivity($model)
    {
        $customAttributes = [];

        if (method_exists($model, 'getCustomAttributes')) {
            $customAttributes = $model->getCustomAttributes()->pluck('code')->toArray();
        }

        $updatedAttributes = static::getUpdatedAttributes($model);

        foreach ($updatedAttributes as $attributeCode => $attributeData) {
            if (in_array($attributeCode, $customAttributes)) {
                continue;
            }

            $attributeCode = $model->attribute?->name ?: $attributeCode;

            $activity = app(ActivityRepository::class)->create([
                'type'       => 'system',
                'title'      => trans('admin::app.activities.updated', ['attribute' => $attributeCode]),
                'is_done'    => 1,
                'additional' => json_encode([
                    'attribute' => $attributeCode,
                    'new'       => [
                        'value' => $attributeData['new'],
                        'label' => static::getAttributeLabel($attributeData['new'], $model->attribute),
                    ],
                    'old'       => [
                        'value' => $attributeData['old'],
                        'label' => static::getAttributeLabel($attributeData['old'], $model->attribute),
                    ],
                ]),
                'user_id'    => auth()->id(),
            ]);

            if ($model instanceof AttributeValue) {
                $model->entity->activities()->attach($activity->id);
            } else {
                $model->activities()->attach($activity->id);
            }
        }
    }

    /**
     * Get attribute label.
     */
    protected static function getAttributeLabel($value, $attribute)
    {
        return app(AttributeValueRepository::class)->getAttributeLabel($value, $attribute);
    }

    /**
     * Create activity.
     */
    protected static function getUpdatedAttributes($model)
    {
        $updatedAttributes = [];

        foreach ($model->getDirty() as $key => $value) {
            if (in_array($key, [
                'id',
                'attribute_id',
                'entity_id',
                'entity_type',
                'updated_at',
            ])) {
                continue;
            }

            $newValue = static::decodeValueIfJson($value);

            $oldValue = static::decodeValueIfJson($model->getOriginal($key));

            if ($newValue != $oldValue) {
                $updatedAttributes[$key] = [
                    'new' => $newValue,
                    'old' => $oldValue,
                ];
            }
        }

        return $updatedAttributes;
    }

    /**
     * Convert value if json.
     */
    protected static function decodeValueIfJson($value)
    {
        if (
            ! is_array($value)
            && json_decode($value, true)
        ) {
            $value = json_decode($value, true);
        }

        if (! is_array($value)) {
            return $value;
        }

        static::ksortRecursive($value);

        return $value;
    }

    /**
     * Sort array recursively.
     */
    protected static function ksortRecursive(&$array)
    {
        if (! is_array($array)) {
            return;
        }

        ksort($array);

        foreach ($array as &$value) {
            if (! is_array($value)) {
                continue;
            }

            static::ksortRecursive($value);
        }
    }
}
