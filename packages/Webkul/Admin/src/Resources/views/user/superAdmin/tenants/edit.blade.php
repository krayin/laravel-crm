@extends('admin::components.layouts.superadmin')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Editar Tenant #{{ $tenant['id'] }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Atualize as informações do tenant abaixo</p>
        </div>

        <div class="px-6 py-4">
            @if($errors->any())
                <div class="bg-red-50 dark:bg-red-900/10 text-red-700 dark:text-red-400 px-4 py-3 rounded-md mb-6">
                    <div class="font-medium">Por favor, corrija os seguintes erros:</div>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('superAdmin.tenants.update', $tenant['id']) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome</label>
                    <input
                        type="text"
                        name="data[name]"
                        value="{{ old('data.name', $tenant['name'] ?? '') }}"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300"
                        required
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Multiatendedor ID</label>
                    <input
                        type="text"
                        name="multiatendedor_id"
                        value="{{ old('multiatendedor_id', $tenant['multiatendedor_id'] ?? '') }}"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300"
                        required
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Domínio Atual</label>
                    <input
                        type="text"
                        disabled
                        value="{{ collect($tenant['domains'])->pluck('domain')->join(', ') }}"
                        class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md text-gray-600 dark:text-gray-400"
                    >
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('superAdmin.tenants.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                        Cancelar
                    </a>
                 <button type="submit" class="ml-4 text-sm font-medium text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                    Salvar Alterações
                </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection