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

            {{-- Formulário de Edição --}}
            <form action="{{ route('superAdmin.tenants.update', $tenant['id']) }}" method="POST" class="space-y-6" id="editForm">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="data[name]"
                        value="{{ old('data.name', $tenant['name'] ?? '') }}"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300"
                        required
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Multiatendedor Account ID<span class="text-red-500">*</span></label>
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

                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                    {{-- Botão de Exclusão (separado do formulário de edição) --}}
                    <button type="button" onclick="confirmDelete()"
                        class="px-4 py-2 text-sm font-medium bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500
                            dark:text-white dark:bg-red-600 dark:hover:bg-red-700">
                        Excluir Tenant
                    </button>
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('superAdmin.tenants.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                            Cancelar
                        </a>
                        <button type="submit" form="editForm"
                            class="px-4 py-2 text-sm font-medium bg-blue-600 rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                dark:text-white dark:bg-blue-600 dark:hover:bg-blue-700">
                            Salvar Alterações
                        </button>
                    </div>
                </div>
            </form>

            {{-- Formulário de Exclusão (hidden) --}}
            <form action="{{ route('superAdmin.tenants.destroy', $tenant['id']) }}" method="POST" id="deleteForm" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

<script>
    function confirmDelete() {
        if (confirm('Tem certeza que deseja excluir este tenant? Esta ação não pode ser desfeita.')) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>
@endsection