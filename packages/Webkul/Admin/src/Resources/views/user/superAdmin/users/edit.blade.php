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

            <form action="{{ route('superAdmin.users.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nome <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required
                           value="{{ old('name', $user->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        E-mail <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" required
                           value="{{ old('email', $user->email) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Multiatendedor User ID</span>
                    </label>
                    <input type="text" name="multiatendedor_id"
                           value="{{ old('multiatendedor_id', $user->multiatendedor_id) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nova Senha
                    </label>
                    <input type="password" name="password" id="password" minlength="6"
                           placeholder="Deixe em branco para manter a atual"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Confirmar Nova Senha
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" minlength="6"
                           placeholder="Deixe em branco para manter a atual"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Super Admin <span class="text-red-500">*</span>
                    </label>
                    <select name="is_super" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <option value="0" {{ !$user->is_super ? 'selected' : '' }}>Não</option>
                        <option value="1" {{ $user->is_super ? 'selected' : '' }}>Sim</option>
                    </select>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <button type="button" onclick="confirmUserDelete()"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:text-white dark:bg-red-600 dark:hover:bg-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Excluir Usuário
                            </button>
                    </div>
                    <div class="flex items-center">
                        <a href="{{ route('superAdmin.users.index') }}"
                           class="px-4 py-2 text-sm font-medium bg-white dark:bg-gray-700 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 dark:text-gray-100">
                            Cancelar
                        </a>
                        <button type="submit"
                                    class="inline-flex items-center px-4 py-2 ml-3 px-4 py-2 text-sm font-medium bg-blue-600 rounded-md hover:bg-blue-700 dark:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Salvar Alterações
                            </button>
                    </div>
                </div>
            </form>
            <form action="{{ route('superAdmin.users.destroy',  $user->id) }}" method="POST" id="deleteUserForm" class="hidden">
                @csrf
                @method('DELETE')
            </form>

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
                        <label for="role_select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Selecione o Cargo
                        </label>
                        <select id="role_select" name="role_id" required
                                class="block w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 select2">
                            <option value="3">Agent</option>
                            <option value="2">Manager</option>
                            <option value="1">Administrator</option>
                        </select>
                    </div>
                  
                    <div class="mt-4">
                        <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Adicionar Tenant
                            </button>
                    </div>
                </form>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Tenants Associados
                </label>
            
                @if (!empty($user->tenants))
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-800 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        ID da Conexão
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        ID do Tenant
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nome do Tenant
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Cargo
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($user->tenants as $tenant)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $tenant->connection_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $tenant->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $tenant->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            @php
                                                $roles = [
                                                    1 => 'Administrator',
                                                    2 => 'Manager',
                                                    3 => 'Agent',
                                                ];
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $tenant->role_id == 1 ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 
                                                   ($tenant->role_id == 2 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                                   'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200') }}">
                                                {{ $roles[$tenant->role_id] ?? 'Desconhecido' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form action="{{ route('superAdmin.users.tenants.destroy', [$tenant->connection_id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja desvincular este tenant do usuário?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
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

        /* fundo e borda da dropdown */
        .select2-dropdown.dark {
        background-color: #374151;
        border: 1px solid #4b5563;
        border-radius: 0.375rem;
        }
        .select2-dropdown.dark .select2-results__option {
        color: #f3f4f6;
        }
        .select2-dropdown.dark .select2-results__option--highlighted {
        background-color: #4b5563;
        color: #f3f4f6;
        }

        .dark .select2-dropdown.dark .select2-results__option--selected {
        background-color: #4b5563;  /* mesmo tom das opções destacadas */
        color: #f3f4f6;             /* texto claro */
        }

        /* campo de busca dentro do dropdown */
        .dark .select2-dropdown.dark .select2-search__field {
        background-color: #374151;  /* mesmo tom do input principal */
        color: #f3f4f6;             /* texto claro */
        border: 1px solid #4b5563;  /* igual borda dark */
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

$(function() {
  // Inicializa cada Select2 usando o texto do <label> correspondente como placeholder
  $('.select2').each(function() {
    const $sel = $(this);
    const placeholderText = $(`label[for="${$sel.attr('id')}"]`).text().trim();

    $sel.select2({
      placeholder: placeholderText,
      allowClear: true,
      width: '100%',
      dropdownCssClass: document.documentElement.classList.contains('dark') ? 'dark' : '',
      language: {
        noResults: () => "Nenhum resultado encontrado",
        searching:  () => "Pesquisando..."
      }
    });
  });

  $(document).on('select2:open select2:close', () => {
    const isDark = document.documentElement.classList.contains('dark');
    $('.select2-dropdown').toggleClass('dark', isDark);
  });

  new MutationObserver(() => {
    const isDark = document.documentElement.classList.contains('dark');
    $('.select2-dropdown').toggleClass('dark', isDark);
  }).observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class']
  });
});
</script>
@endpush