<?php

namespace Webkul\FeatureFlag\Features;

use Laravel\Pennant\Feature;

class TestFeature
{
    /**
     * Resolve the feature's initial value.
     *
     * @param  mixed  $scope
     * @return mixed
     */
    public function resolve($scope)
    {
        // For demo purposes, we'll enable the feature for all users
        // In a real implementation, you might check user roles, 
        // percentage rollouts, or other business logic
        return true;
    }

    /**
     * Register the feature in Laravel Pennant.
     *
     * @return void
     */
    public static function register()
    {
        Feature::define('test-feature', new static);
    }

    /**
     * A sample feature that demonstrates different activation logic.
     * This could be used for A/B testing or gradual rollouts.
     *
     * @param  mixed  $scope
     * @return mixed
     */
    public static function advancedFeature($scope)
    {
        // Example: Enable for 50% of users based on user ID
        if ($scope && method_exists($scope, 'getKey')) {
            return $scope->getKey() % 2 === 0;
        }

        // Default to disabled if no scope
        return false;
    }
}