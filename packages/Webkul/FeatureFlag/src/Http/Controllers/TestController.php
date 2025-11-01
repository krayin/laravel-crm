<?php

namespace Webkul\FeatureFlag\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Pennant\Feature;
use Webkul\Admin\Http\Controllers\Controller as BaseController;

class TestController extends BaseController
{
    /**
     * Display the feature flag test page.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get current user for feature flag checking
        $user = auth()->user();

        // Define some test features
        Feature::define('test-feature', function ($user) {
            return true; // Always enabled for demo
        });

        Feature::define('advanced-feature', function ($user) {
            // Enable for 50% of users based on ID
            return $user && $user->id % 2 === 0;
        });

        Feature::define('admin-only-feature', function ($user) {
            // Enable only for admin users
            return $user && $user->role && $user->role->name === 'Administrator';
        });

        // Check feature flags
        $features = [
            'test-feature' => Feature::active('test-feature'),
            'advanced-feature' => Feature::active('advanced-feature'),
            'admin-only-feature' => Feature::active('admin-only-feature'),
        ];

        // Get feature values with user context
        $userFeatures = [
            'test-feature' => Feature::for($user)->active('test-feature'),
            'advanced-feature' => Feature::for($user)->active('advanced-feature'),
            'admin-only-feature' => Feature::for($user)->active('admin-only-feature'),
        ];

        return view('featureflag::test', compact('features', 'userFeatures', 'user'));
    }

    /**
     * Toggle a feature flag for testing purposes.
     *
     * @param  Request  $request
     * @param  string  $feature
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request, $feature)
    {
        $user = auth()->user();
        
        // For demo purposes, we'll simulate toggling by storing in session
        $sessionKey = "feature_override_{$feature}";
        $currentValue = $request->session()->get($sessionKey, false);
        $newValue = !$currentValue;
        
        $request->session()->put($sessionKey, $newValue);

        return response()->json([
            'success' => true,
            'feature' => $feature,
            'enabled' => $newValue,
            'message' => "Feature '{$feature}' " . ($newValue ? 'enabled' : 'disabled') . ' for this session'
        ]);
    }
}