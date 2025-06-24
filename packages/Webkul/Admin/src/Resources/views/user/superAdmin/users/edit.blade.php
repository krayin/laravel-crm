@extends('admin::components.layouts.superadmin')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                Editar Usuário #{{ $user->id }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Atualize os dados abaixo
            </p>
        </div>

        <div class="px-6 py-4">
            {{-- Exibição de erros de validação --}}
            <div id="validationErrors" class="hidden bg-red-50 dark:bg-red-900/10 text-red-700 dark:text-red-400 px-4 py-3 rounded-md mb-6">
                <div class="font-medium">Por favor, corrija os seguintes erros:</div>
                <ul id="errorsList" class="mt-1 list-disc list-inside text-sm"></ul>
            </div>

            {{-- Formulário principal de edição --}}
            <form id="editUserForm" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Nome --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nome <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required
                           value="{{ old('name', $user->name) }}"
                           class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                {{-- E-mail --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        E-mail <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" required
                           value="{{ old('email', $user->email) }}"
                           class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                {{-- Multiatendedor ID --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Multiatendedor ID <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="multiatendedor_id" required
                           value="{{ old('multiatendedor_id', $user->multiatendedor_id) }}"
                           class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                {{-- Nova Senha --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nova Senha
                    </label>
                    <input type="password" name="password" id="password" minlength="6"
                           placeholder="Deixe em branco para manter a atual"
                           class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                {{-- Confirmar Nova Senha --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Confirmar Nova Senha
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" minlength="6"
                           placeholder="Deixe em branco para manter a atual"
                           class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                {{-- Super Admin --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Super Admin <span class="text-red-500">*</span>
                    </label>
                    <select name="is_super" required
                            class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <option value="0" {{ !$user->is_super ? 'selected' : '' }}>Não</option>
                        <option value="1" {{ $user->is_super ? 'selected' : '' }}>Sim</option>
                    </select>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <button type="button" onclick="confirmUserDelete()"
                            class="px-4 py-2 text-sm font-medium bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500
                                dark:text-white dark:bg-red-600 dark:hover:bg-red-700">
                            Excluir Usuário
                        </button>
                    </div>
                    <div class="flex items-center">
                        <a href="{{ route('superAdmin.users.index') }}"
                           class="px-4 py-2 text-sm font-medium bg-white dark:bg-gray-700 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 dark:text-gray-100">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="ml-3 px-4 py-2 text-sm font-medium bg-blue-600 rounded-md hover:bg-blue-700 dark:text-white">
                            Salvar Alterações
                        </button>
                    </div>
                </div>
            </form>
            <form action="{{ route('superAdmin.users.destroy',  $user->id) }}" method="POST" id="deleteUserForm" class="hidden">
                @csrf
                @method('DELETE')
            </form>

            {{-- Seção para Adicionar Novo Tenant --}}
            <div class="mt-8 mb-8 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Adicionar Tenant
                </h3>
              
                <form id="tenantForm" method="POST">
                    @csrf
                    <div>
                        <label for="tenant_select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Selecione o Tenant
                        </label>
                        <select id="tenant_select" name="tenant_id" required
                                class="block w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 select2">
                            <option value="" disabled selected>Selecione um Tenant</option>
                            @foreach ($availableTenants as $tenant)
                                <option value="{{ $tenant['id'] }}">
                                    #{{ $tenant['id'] }} — {{ data_get($tenant, 'data.name') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                  
                    <div class="mt-4">
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium bg-white dark:bg-gray-700 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 dark:text-gray-100">
                            Adicionar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Listagem de Tenants Associados --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Tenants Associados
                </label>
            
                @if (!empty($user->tenants))
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="py-3 px-6">
                                        ID da Conexão
                                    </th>
                                    <th scope="col" class="py-3 px-6">
                                        ID do Tenant
                                    </th>
                                    <th scope="col" class="py-3 px-6">
                                        Nome do Tenant
                                    </th>
                                    <th scope="col" class="py-3 px-6">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->tenants as $tenant)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $tenant->connection_id }}
                                        </td>
                                        <td class="py-4 px-6">
                                            {{ $tenant->id }}
                                        </td>
                                        <td class="py-4 px-6">
                                            {{ $tenant->name }}
                                        </td>
                                        <td class="py-4 px-6">
                                            <form action="{{ route('superAdmin.users.tenants.destroy', [$tenant->connection_id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja desvincular este tenant do usuário?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline">
                                                    Remover
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400">Nenhum tenant associado a este usuário.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            height: 42px;
            padding: 0.5rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151;
            line-height: 1.5;
        }
        .dark .select2-container--default .select2-selection--single {
            background-color: #374151;
            border-color: #4b5563;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #f3f4f6;
        }
    </style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function confirmUserDelete() {
    if (confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
        document.getElementById('deleteUserForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Formulário de edição do usuário
    const editForm = document.getElementById('editUserForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validação de senha
            const pwd = document.getElementById('password').value;
            const cpwd = document.getElementById('password_confirmation').value;
            if ((pwd || cpwd) && (pwd.length < 6 || pwd !== cpwd)) {
                const errDiv = document.getElementById('validationErrors');
                const errList = document.getElementById('errorsList');
                errList.innerHTML = '<li>As senhas devem ter pelo menos 6 caracteres e serem iguais.</li>';
                errDiv.classList.remove('hidden');
                return;
            }

            // Envio do formulário via Fetch API
            const url = "{{ route('superAdmin.users.update', $user->id) }}";
            const formData = new FormData(this);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: formData
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.json().then(json => {
                        if (json.errors) {
                            const errDiv = document.getElementById('validationErrors');
                            const errList = document.getElementById('errorsList');
                            errList.innerHTML = '';
                            Object.values(json.errors).forEach(messages => {
                                messages.forEach(msg => {
                                    const li = document.createElement('li');
                                    li.textContent = msg;
                                    errList.appendChild(li);
                                });
                            });
                            errDiv.classList.remove('hidden');
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
            });
        });
    }

    // Formulário de adição de tenant
    const tenantForm = document.getElementById('tenantForm');
    if (tenantForm) {
        tenantForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const select = document.getElementById('tenant_select');
            if (!select) {
                alert('Seletor de tenant não encontrado');
                return;
            }
            
            const tenantId = select.value;
            if (!tenantId) {
                alert('Selecione um tenant');
                return;
            }
            
            this.action = `{{ url("super-admin/users/{$user->id}/tenants") }}/${tenantId}`;
            this.submit();
        });
    }
});

$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Selecione um Tenant",
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Nenhum resultado encontrado";
            },
            searching: function() {
                return "Pesquisando...";
            }
        }
    });
    
    // Adiciona suporte para o modo dark
    const observer = new MutationObserver(function(mutations) {
        if (document.documentElement.classList.contains('dark')) {
            $('.select2-container--default').addClass('dark');
        } else {
            $('.select2-container--default').removeClass('dark');
        }
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
});
</script>
@endpush