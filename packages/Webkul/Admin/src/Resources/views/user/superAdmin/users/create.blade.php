@extends('admin::components.layouts.superadmin')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Criar Novo Usuário</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Preencha os dados abaixo para criar um novo usuário</p>
        </div>

        <div class="px-6 py-4">
            {{-- Onde exibiremos erros de validação vindos do JSON --}}
            <div id="validationErrors" class="hidden bg-red-50 dark:bg-red-900/10 text-red-700 dark:text-red-400 px-4 py-3 rounded-md mb-6">
                <div class="font-medium">Por favor, corrija os seguintes erros:</div>
                <ul id="errorsList" class="mt-1 list-disc list-inside text-sm"></ul>
            </div>

            <form id="createUserForm" class="space-y-6">
                @csrf

                {{-- Nome --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required
                           class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                {{-- E-mail --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required
                           class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required
                            class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <option value="1" selected>1 (Ativo)</option>
                        <option value="0">0 (Inativo)</option>
                    </select>
                </div>

                {{-- Tenant ID --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tenant ID <span class="text-red-500">*</span></label>
                    <input type="text" name="tenant_id" required
                           class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                {{-- Multiatendedor ID --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Multiatendedor ID <span class="text-red-500">*</span></label>
                    <input type="text" name="multiatendedor_id" required
                           class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                {{-- Senha --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Senha <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" minlength="6" required
                           class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                {{-- Confirmar Senha --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar Senha <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" minlength="6" required
                           class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                {{-- Role ID --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role ID <span class="text-red-500">*</span></label>
                    <select name="role_id" required
                            class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <option value="1" selected>1 (Padrão)</option>
                        {{-- outras roles, se houver --}}
                    </select>
                </div>

                {{-- View Permission --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">View Permission <span class="text-red-500">*</span></label>
                    <select name="view_permission" required
                            class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <option value="global" selected>global</option>
                        <option value="group">group</option>
                        <option value="individual">individual</option>
                    </select>
                </div>

                {{-- Super Admin --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Super Admin<span class="text-red-500">*</span></label>
                    <select name="is_super" required
                            class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <option value=false selected>0 (Inativo)</option>
                        <option value=true >1 (Ativo)</option>
                    </select>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex space-x-3">
                        <a href="{{ route('superAdmin.users.index') }}"
                           class="px-4 py-2 text-sm font-medium bg-white dark:bg-gray-700 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium bg-blue-600 rounded-md hover:bg-blue-700 dark:text-white">
                            Criar Usuário
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
  document.getElementById('createUserForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // validação
    const pwd = document.getElementById('password').value;
    const cpwd = document.getElementById('password_confirmation').value;
    if (pwd.length < 6 || cpwd.length < 6 || pwd !== cpwd) {
      alert('As senhas devem ter pelo menos 6 caracteres e serem iguais.');
      return;
    }

    // monta dados
    const url = "{{ route('superAdmin.users.store') }}";
    const data = {
      name: this.name.value,
      email: this.email.value,
      status: this.status.value,
      tenant_id: this.tenant_id.value,
      multiatendedor_id: this.multiatendedor_id.value,
      password: pwd,
      password_confirmation: cpwd,
      role_id: this.role_id.value,
      view_permission: this.view_permission.value,
      is_super: this.is_super.value === 'true' 
    };
    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    };
    const fetchOptions = { method: 'POST', headers, body: JSON.stringify(data) };

    fetch(url, fetchOptions)
  .then(response => {
    if (response.redirected) {
      // Se o Laravel retornou um redirect, abre a URL de destino no browser:
      window.location.href = response.url;
      return;
    }
    // Caso não tenha sido redirect, espera o JSON com possíveis erros:
    return response.json().then(json => {
      if (json.errors) {
        const errList = document.getElementById('errorsList');
        errList.innerHTML = '';
        json.errors.forEach(msg => {
          const li = document.createElement('li');
          li.textContent = msg;
          errList.appendChild(li);
        });
        document.getElementById('validationErrors').classList.remove('hidden');
      }
    });
  })
  .catch(err => {
  });

  });
</script>
@endpush

@endsection
