@extends('admin::components.layouts.superadmin')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                Editar Usuário #{{ $user['id'] }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Atualize as informações do usuário abaixo
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

            {{-- Formulário de Edição --}}
            <form action="{{ route('superAdmin.users.update', $user['id']) }}" method="POST" class="space-y-6" id="editUserForm">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nome <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $user['name']) }}"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        E-mail <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $user['email']) }}"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Multiatendedor ID <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="multiatendedor_id"
                        value="{{ old('multiatendedor_id', $user['multiatendedor_id']) }}"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nova Senha
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        minlength="6"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300"
                        placeholder="Deixe em branco para manter a senha atual"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Confirmar Nova Senha
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        minlength="6"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300"
                        placeholder="Deixe em branco para manter a senha atual"
                    >
                </div>

                {{-- Mensagem de erro customizada --}}
                <div id="passwordError" class="hidden text-red-600 text-sm">
                    As senhas devem ter pelo menos 6 caracteres e serem iguais.
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                    {{-- Botão de Exclusão --}}
                    <button type="button" onclick="confirmUserDelete()"
                        class="px-4 py-2 text-sm font-medium bg-red-600 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500
                            dark:text-white dark:bg-red-600 dark:hover:bg-red-700">
                        Excluir Usuário
                    </button>

                    <div class="flex space-x-3">
                        <a href="{{ route('superAdmin.users.index') }}"
                           class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800">
                            Cancelar
                        </a>
                        <button type="submit" form="editUserForm"
                            class="px-4 py-2 text-sm font-medium bg-blue-600 rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                dark:text-white dark:bg-blue-600 dark:hover:bg-blue-700">
                            Salvar Alterações
                        </button>
                    </div>
                </div>
            </form>

            {{-- Formulário de Exclusão (hidden) --}}
            <form action="{{ route('superAdmin.users.destroy', $user['id']) }}" method="POST" id="deleteUserForm" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmUserDelete() {
        if (confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
            document.getElementById('deleteUserForm').submit();
        }
    }

    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        const pwd = document.getElementById('password').value;
        const cpwd = document.getElementById('password_confirmation').value;
        const errDiv = document.getElementById('passwordError');

        if ((pwd || cpwd) && (pwd.length < 6 || pwd !== cpwd)) {
            e.preventDefault();
            errDiv.classList.remove('hidden');
        } else {
            errDiv.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection
