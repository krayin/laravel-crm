@extends('admin::components.layouts.superadmin')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8">
        <!-- Header com total de tenants -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tenants</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Total: <span class="font-semibold text-blue-600 dark:text-blue-400">{{ count($tenants) }}</span> tenants
            </p>
        </div>

        <!-- Tabela de tenants -->
        <div class="bg-white dark:bg-gray-900 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="overflow-auto max-h-[500px]">
                <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0 z-10 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Domínio</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Multi ID</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($tenants as $tenant)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                            <td class="px-6 py-4 text-base font-medium text-gray-900 dark:text-gray-100">
                                {{ $tenant['id'] }}
                            </td>
                            <td class="px-6 py-4 text-base text-gray-900 dark:text-gray-100">
                                {{ $tenant['data']['name'] ?? 'Sem nome' }}
                            </td>
                            <td class="px-6 py-4 text-base text-gray-600 dark:text-gray-300">
                                {{ collect($tenant['domains'])->pluck('domain')->join(', ') ?: 'Sem domínio' }}
                            </td>
                            <td class="px-6 py-4 text-base text-gray-600 dark:text-gray-300">
                                {{ $tenant['multiatendedor_id'] ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a class="inline-flex items-center px-3 py-1.5 text-base font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:text-blue-400 dark:hover:text-blue-300 dark:hover:bg-blue-900/20 rounded-md transition-all duration-200 border border-transparent hover:border-blue-200 dark:hover:border-blue-800"
                                href="{{ route('superAdmin.tenants.edit', $tenant['id']) }}">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editar
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <p class="text-lg font-medium">Nenhum tenant encontrado</p>
                                    <p class="text-base">Não há tenants cadastrados no sistema.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
@endsection