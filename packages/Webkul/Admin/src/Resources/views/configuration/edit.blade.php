@php
    $activeConfiguration = system_config()->getActiveConfigurationItem();
@endphp

<x-admin::layouts>
    <x-slot:title>
        {{ $name = $activeConfiguration->getName() }}
    </x-slot>

    <!-- Configuration form fields -->
    <x-admin::form
        action=""
        enctype="multipart/form-data"
    >
        <!-- Save Inventory -->
        <div class="mt-3.5 flex items-center justify-between gap-4 max-sm:flex-wrap">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                {{ $name }}
            </p>

            <!-- Save Inventory -->
            <div class="flex items-center gap-x-2.5">
                <!-- Back Button -->
                <a
                    href="{{ route('admin.configuration.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    @lang('admin::app.configuration.index.back')
                </a>

                <button
                    type="submit"
                    class="primary-button"
                >
                    @lang('admin::app.configuration.index.save-btn')
                </button>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-[1fr_2fr] gap-10 max-xl:flex-wrap">
            @foreach ($activeConfiguration->getChildren() as $child)
                <div class="grid content-start gap-2.5">
                    <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                        {{ $child->getName() }}
                    </p>

                    <p class="leading-[140%] text-gray-600 dark:text-gray-300">
                        {!! $child->getInfo() !!}
                    </p>
                </div>

                <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                    @foreach ($child->getFields() as $field)
                        @if (
                            $field->getType() == 'blade'
                            && view()->exists($path = $field->getPath())
                        )
                            {!! view($path, compact('field', 'child'))->render() !!}
                        @else 
                            @include ('admin::configuration.field-type')
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </x-admin::form>
</x-admin::layouts>