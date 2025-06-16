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
            {{-- Onde exibiremos erros de validação vindos do JSON --}}
            <div id="validationErrors" class="hidden bg-red-50 dark:bg-red-900/10 text-red-700 dark:text-red-400 px-4 py-3 rounded-md mb-6">
                <div class="font-medium">Por favor, corrija os seguintes erros:</div>
                <ul id="errorsList" class="mt-1 list-disc list-inside text-sm"></ul>
            </div>

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

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" required
                            class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <option value="1" {{ $user->status ? 'selected' : '' }}>1 (Ativo)</option>
                        <option value="0" {{ !$user->status ? 'selected' : '' }}>0 (Inativo)</option>
                    </select>
                </div>

                {{-- Tenant ID --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tenant ID <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="tenant_id" required
                           value="{{ old('tenant_id', $user->tenant_id) }}"
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

                {{-- Role ID --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Role ID <span class="text-red-500">*</span>
                    </label>
                    <select name="role_id" required
                            class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <option value="1" {{ $user->role_id==1 ? 'selected' : '' }}>1 (Padrão)</option>
                        {{-- outras roles --}}
                    </select>
                </div>

                {{-- View Permission --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        View Permission <span class="text-red-500">*</span>
                    </label>
                    <select name="view_permission" required
                            class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <option value="global" {{ $user->view_permission=='global' ? 'selected' : '' }}>global</option>
                        <option value="group" {{ $user->view_permission=='group' ? 'selected' : '' }}>group</option>
                        <option value="individual" {{ $user->view_permission=='individual' ? 'selected' : '' }}>individual</option>
                    </select>
                </div>

                {{-- Super Admin --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Super Admin <span class="text-red-500">*</span>
                    </label>
                    <select name="is_super" required
                            class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <option value="0" {{ !$user->is_super ? 'selected' : '' }}>0 (Inativo)</option>
                        <option value="1" {{ $user->is_super ? 'selected' : '' }}>1 (Ativo)</option>
                    </select>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('superAdmin.users.index') }}"
                       class="px-4 py-2 text-sm font-medium bg-white dark:bg-gray-700 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium bg-blue-600 rounded-md hover:bg-blue-700 dark:text-white">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
  document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // validação de senha (se preenchida)
    const pwd = document.getElementById('password').value;
    const cpwd = document.getElementById('password_confirmation').value;
    if ((pwd || cpwd) && (pwd.length < 6 || pwd !== cpwd)) {
      const errDiv = document.getElementById('validationErrors');
      const errList = document.getElementById('errorsList');
      errList.innerHTML = '<li>As senhas devem ter pelo menos 6 caracteres e serem iguais.</li>';
      errDiv.classList.remove('hidden');
      return;
    }

    // monta dados
    const url = "{{ route('superAdmin.users.update', $user->id) }}";
    const form = this;
    const data = {
      name: form.name.value,
      email: form.email.value,
      status: form.status.value,
      tenant_id: parseInt(form.tenant_id.value,10),
      multiatendedor_id: parseInt(form.multiatendedor_id.value, 10),
      password: pwd,
      password_confirmation: cpwd,
      role_id: form.role_id.value,
      view_permission: form.view_permission.value,
      is_super: form.is_super.value === '1'
    };

    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    };

    fetch(url, { method: 'PUT', headers, body: JSON.stringify(data) })
      .then(response => {
        if (response.redirected) {
          window.location.href = response.url;
        } else {
          return response.json().then(json => {
            if (json.errors) {
              const errDiv = document.getElementById('validationErrors');
              const errList = document.getElementById('errorsList');
              errList.innerHTML = '';
              json.errors.forEach(msg => {
                const li = document.createElement('li');
                li.textContent = msg;
                errList.appendChild(li);
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
</script>
@endpush
@endsection
