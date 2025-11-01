<x-admin::layouts>
    <x-slot:title>
        Feature Flags Test
    </x-slot:title>

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Feature Flags Test</h1>
                <p class="page-title-description">Demonstration of Laravel Pennant Feature Flags</p>
            </div>
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Current User Information</h4>
                        </div>
                        <div class="card-body">
                            @if($user)
                                <p><strong>Name:</strong> {{ $user->name }}</p>
                                <p><strong>Email:</strong> {{ $user->email }}</p>
                                <p><strong>ID:</strong> {{ $user->id }}</p>
                                @if($user->role)
                                    <p><strong>Role:</strong> {{ $user->role->name }}</p>
                                @endif
                            @else
                                <p>No authenticated user found</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Global Feature Status</h4>
                        </div>
                        <div class="card-body">
                            @foreach($features as $feature => $enabled)
                                <div class="feature-item mb-2">
                                    <span class="badge {{ $enabled ? 'badge-success' : 'badge-danger' }}">
                                        {{ $enabled ? 'ON' : 'OFF' }}
                                    </span>
                                    <strong>{{ str_replace('-', ' ', ucwords($feature)) }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">User-Specific Feature Status</h4>
                            <p class="card-description">These features are evaluated specifically for the current user</p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($userFeatures as $feature => $enabled)
                                    <div class="col-md-4 mb-3">
                                        <div class="feature-card border rounded p-3 {{ $enabled ? 'border-success' : 'border-danger' }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6>{{ str_replace('-', ' ', ucwords($feature)) }}</h6>
                                                    <span class="badge {{ $enabled ? 'badge-success' : 'badge-danger' }}">
                                                        {{ $enabled ? 'ENABLED' : 'DISABLED' }}
                                                    </span>
                                                </div>
                                                <button class="btn btn-sm btn-outline-primary toggle-feature" 
                                                        data-feature="{{ $feature }}">
                                                    Toggle
                                                </button>
                                            </div>
                                            <div class="mt-2">
                                                @switch($feature)
                                                    @case('test-feature')
                                                        <small class="text-muted">Always enabled for demonstration</small>
                                                        @break
                                                    @case('advanced-feature')
                                                        <small class="text-muted">Enabled for 50% of users (even IDs)</small>
                                                        @break
                                                    @case('admin-only-feature')
                                                        <small class="text-muted">Enabled only for administrators</small>
                                                        @break
                                                @endswitch
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Feature Flag Usage Examples</h4>
                        </div>
                        <div class="card-body">
                            <h6>In Blade Templates:</h6>
                            <pre><code>@feature('test-feature')
    &lt;div&gt;This content shows when test-feature is enabled&lt;/div&gt;
@endfeature

@feature('test-feature', $user)
    &lt;div&gt;This content shows when test-feature is enabled for specific user&lt;/div&gt;
@endfeature</code></pre>

                            <h6 class="mt-3">In Controllers:</h6>
                            <pre><code>use Laravel\Pennant\Feature;

// Check if feature is active
if (Feature::active('test-feature')) {
    // Feature is enabled
}

// Check for specific user
if (Feature::for($user)->active('test-feature')) {
    // Feature is enabled for this user
}</code></pre>

                            <h6 class="mt-3">Defining Features:</h6>
                            <pre><code>Feature::define('new-feature', function ($user) {
    return $user->isAdmin();
});

// Or with a class
Feature::define('new-feature', NewFeature::class);</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.toggle-feature');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const feature = this.dataset.feature;
                const url = "{{ route('admin.feature-flags.toggle', ':feature') }}".replace(':feature', feature);
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload the page to show updated status
                        location.reload();
                    } else {
                        alert('Error toggling feature');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error toggling feature');
                });
            });
        });
    });
    </script>
    @endpush

    @push('styles')
    <style>
    .feature-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .feature-card {
        background-color: #f8f9fa;
    }

    .badge-success {
        background-color: #28a745;
    }

    .badge-danger {
        background-color: #dc3545;
    }

    pre {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border: 1px solid #e9ecef;
    }
    </style>
    @endpush
</x-admin::layouts>