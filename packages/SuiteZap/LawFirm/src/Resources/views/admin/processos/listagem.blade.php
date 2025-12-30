<x-admin::layouts>
    <x-slot:title>
        {{ __('lawfirm::app.processos.index') }}
    </x-slot>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <div class="grid gap-1.5">
            <div class="flex items-center gap-1.5 font-bold text-gray-800 dark:text-white text-xl leading-none">
                <h1>{{ __('lawfirm::app.processos.index') }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-1.5">
            <a href="{{ route('admin.processos.create') }}" class="primary-button">
                {{ __('lawfirm::app.processos.create') }}
            </a>
        </div>
    </div>

    <div class="mt-8">
        <!-- AQUI ESTÁ A CORREÇÃO: Usando o componente x-admin::datagrid -->
        <x-admin::datagrid :src="route('admin.processos.index')"></x-admin::datagrid>
    </div>
</x-admin::layouts>