<x-admin::layouts>
    <x-slot:title>
        {{ __('lawfirm::app.processos.edit') }}
        </x-slot>

        <form method="POST" action="{{ route('admin.processos.update', $processo->id) }}">
            @csrf
            @method('PUT')

            <div class="flex gap-4 justify-between items-center mb-4">
                <div class="flex-grow">
                    <h1 class="text-xl font-bold text-gray-800">{{ __('lawfirm::app.processos.edit') }}</h1>
                </div>
                <div class="flex gap-x-2.5 items-center">
                    <a href="{{ route('admin.processos.index') }}"
                        class="transparent-button hover:bg-gray-200 dark:hover:bg-gray-800 py-1.5 px-2.5 rounded-md text-gray-600 font-semibold">
                        {{ __('lawfirm::app.processos.cancel') }}
                    </a>
                    <button type="submit" class="primary-button">
                        {{ __('lawfirm::app.processos.save') }}
                    </button>
                </div>
            </div>

            <div class="flex gap-4">
                <!-- Painel Esquerdo: Dados Principais -->
                <div class="flex flex-col gap-4 w-1/2">
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                        <h4 class="font-semibold mb-4 text-gray-800 dark:text-white">
                            {{ __('lawfirm::app.processos.form.group-info') }}</h4>

                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label
                                class="required">{{ __('lawfirm::app.processos.form.titulo') }}</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="text" name="titulo" :value="old('titulo', $processo->titulo)" rules="required"
                                :label="__('lawfirm::app.processos.form.titulo')" />
                            <x-admin::form.control-group.error control-name="titulo" />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label
                                class="required">{{ __('lawfirm::app.processos.form.cnj') }}</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="text" name="numero_cnj" :value="old('numero_cnj', $processo->numero_cnj)" rules="required"
                                :label="__('lawfirm::app.processos.form.cnj')" />
                            <x-admin::form.control-group.error control-name="numero_cnj" />
                        </x-admin::form.control-group>

                        <div class="flex gap-4">
                            <x-admin::form.control-group class="w-1/2 mb-4">
                                <x-admin::form.control-group.label>{{ __('lawfirm::app.processos.form.tribunal') }}</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="tribunal" :value="old('tribunal', $processo->tribunal)" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group class="w-1/2 mb-4">
                                <x-admin::form.control-group.label>{{ __('lawfirm::app.processos.form.comarca') }}</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="comarca" :value="old('comarca', $processo->comarca)" />
                            </x-admin::form.control-group>
                        </div>
                    </div>
                </div>

                <!-- Painel Direito: Detalhes -->
                <div class="flex flex-col gap-4 w-1/2">
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                        <h4 class="font-semibold mb-4 text-gray-800 dark:text-white">
                            {{ __('lawfirm::app.processos.form.group-details') }}</h4>

                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label>{{ __('lawfirm::app.processos.form.lead') }}</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="select" name="lead_id" class="cursor-pointer">
                                <option value="">{{ __('lawfirm::app.processos.form.select-lead') }}</option>
                                @foreach($leads as $lead)
                                    <option value="{{ $lead->id }}" {{ (isset($processo) && $processo->lead_id == $lead->id) ? 'selected' : '' }}>
                                        {{ $lead->title }}
                                    </option>
                                @endforeach
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>

                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label>{{ __('lawfirm::app.processos.form.person') }}</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="select" name="person_id" class="cursor-pointer">
                                <option value="">{{ __('lawfirm::app.processos.form.select-person') }}</option>
                                @foreach($people as $person)
                                    <option value="{{ $person->id }}" {{ (isset($processo) && $processo->person_id == $person->id) ? 'selected' : '' }}>
                                        {{ $person->name }}
                                    </option>
                                @endforeach
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>

                        <div class="flex gap-4">
                            <x-admin::form.control-group class="w-1/2 mb-4">
                                <x-admin::form.control-group.label
                                    class="required">{{ __('lawfirm::app.processos.form.status') }}</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="select" name="status" rules="required">
                                    <option value="ativo" {{ $processo->status == 'ativo' ? 'selected' : '' }}>Ativo
                                    </option>
                                    <option value="suspenso" {{ $processo->status == 'suspenso' ? 'selected' : '' }}>
                                        Suspenso</option>
                                    <option value="arquivado" {{ $processo->status == 'arquivado' ? 'selected' : '' }}>
                                        Arquivado</option>
                                </x-admin::form.control-group.control>
                                <x-admin::form.control-group.error control-name="status" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group class="w-1/2 mb-4">
                                <x-admin::form.control-group.label>{{ __('lawfirm::app.processos.form.valor') }}</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="valor_causa"
                                    :value="old('valor_causa', $processo->valor_causa)" />
                            </x-admin::form.control-group>
                        </div>
                    </div>
                </div>
            </div>
        </form>
</x-admin::layouts>