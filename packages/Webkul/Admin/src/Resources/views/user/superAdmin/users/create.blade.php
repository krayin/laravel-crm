@extends('admin::components.layouts.superadmin')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Criar Novo Usuário</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Preencha os dados abaixo para criar um novo usuário</p>
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

            <form action="{{ route('superAdmin.users.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Multiatendedor User ID</span></label>
                    <input type="text" name="multiatendedor_id"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Senha <span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" minlength="6" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar Senha <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" minlength="6" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Super Admin<span class="text-red-500">*</span></label>
                    <select name="is_super" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            <option value="0" >Não</option>
                            <option value="1" > Sim</option>
                    </select>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex space-x-3">
                        <a href="{{ route('superAdmin.users.index') }}"
                           class="px-4 py-2 text-sm font-medium bg-white dark:bg-gray-700 border rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 dark:text-gray-100">
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

@endsection
