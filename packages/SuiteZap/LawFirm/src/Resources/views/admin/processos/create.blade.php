<x-admin::layouts>
    <x-slot:title>
        {{ __('lawfirm::app.processos.create') }}
        </x-slot>

        <form method="POST" action="{{ route('admin.processos.store') }}">
            @csrf

            <div class="flex gap-4 justify-between items-center mb-4">
                <div class="flex-grow">
                    <h1 class="text-xl font-bold text-gray-800">{{ __('lawfirm::app.processos.create') }}</h1>
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
                <!-- Painel Esquerdo: Dados Técnicos -->
                <div class="flex flex-col gap-4 w-1/2">
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                        <h4 class="font-semibold mb-4 text-gray-800 dark:text-white">
                            {{ __('lawfirm::app.processos.form.group-info') }}</h4>

                        <!-- Título e CNJ -->
                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label
                                class="required">{{ __('lawfirm::app.processos.form.titulo') }}</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="text" name="titulo" :value="old('titulo')"
                                rules="required" :label="__('lawfirm::app.processos.form.titulo')" />
                            <x-admin::form.control-group.error control-name="titulo" />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label
                                class="required">{{ __('lawfirm::app.processos.form.cnj') }}</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="text" name="numero_cnj"
                                :value="old('numero_cnj')" rules="required"
                                :label="__('lawfirm::app.processos.form.cnj')" />
                            <x-admin::form.control-group.error control-name="numero_cnj" />
                        </x-admin::form.control-group>

                        <!-- Tribunal e Comarca -->
                        <div class="flex gap-4">
                            <x-admin::form.control-group class="w-1/2 mb-4">
                                <x-admin::form.control-group.label>{{ __('lawfirm::app.processos.form.tribunal') }}</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="tribunal"
                                    :value="old('tribunal')" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group class="w-1/2 mb-4">
                                <x-admin::form.control-group.label>{{ __('lawfirm::app.processos.form.comarca') }}</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="comarca"
                                    :value="old('comarca')" />
                            </x-admin::form.control-group>
                        </div>

                        <!-- Link e Fase (Novos Campos) -->
                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label>{{ __('lawfirm::app.processos.form.link') }}</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="text" name="link_acesso"
                                :value="old('link_acesso')" placeholder="https://..." />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label>{{ __('lawfirm::app.processos.form.fase') }}</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="select" name="fase_processual">
                                <option value="inicial">Inicial</option>
                                <option value="citacao">Citação</option>
                                <option value="instrucao">Instrução/Audiência</option>
                                <option value="sentenca">Sentença/Decisão</option>
                                <option value="recurso">Recursal</option>
                                <option value="execucao">Execução/Cumprimento</option>
                                <option value="arquivado">Arquivado</option>
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>
                    </div>
                </div>

                <!-- Painel Direito: Partes e Detalhes -->
                <div class="flex flex-col gap-4 w-1/2">
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                        <h4 class="font-semibold mb-4 text-gray-800 dark:text-white">
                            {{ __('lawfirm::app.processos.form.group-details') }}</h4>

                        <!-- Lead e Cliente -->
                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label>{{ __('lawfirm::app.processos.form.lead') }}</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="select" name="lead_id" class="cursor-pointer">
                                <option value="">{{ __('lawfirm::app.processos.form.select-lead') }}</option>
                                @foreach($leads as $lead)
                                    <option value="{{ $lead->id }}" {{ (isset($preSelectedLead) && $preSelectedLead->id == $lead->id) ? 'selected' : '' }}>
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
                                    <option value="{{ $person->id }}" {{ (isset($preSelectedLead) && $preSelectedLead->person_id == $person->id) ? 'selected' : '' }}>
                                        {{ $person->name }}
                                    </option>
                                @endforeach
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>

                        <!-- Parte Contrária (Novo Campo) -->
                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label>{{ __('lawfirm::app.processos.form.adversary') }}</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="text" name="parte_contraria"
                                :value="old('parte_contraria')" />
                        </x-admin::form.control-group>

                        <!-- Status e Valor -->
                        <div class="flex gap-4">
                            <x-admin::form.control-group class="w-1/2 mb-4">
                                <x-admin::form.control-group.label
                                    class="required">{{ __('lawfirm::app.processos.form.status') }}</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="select" name="status" rules="required">
                                    <option value="ativo">Ativo</option>
                                    <option value="suspenso">Suspenso</option>
                                    <option value="arquivado">Arquivado</option>
                                </x-admin::form.control-group.control>
                                <x-admin::form.control-group.error control-name="status" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group class="w-1/2 mb-4">
                                <x-admin::form.control-group.label>{{ __('lawfirm::app.processos.form.valor') }}</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="valor_causa"
                                    :value="old('valor_causa')" />
                            </x-admin::form.control-group>
                        </div>

                        <!-- Descrição (Novo Campo) -->
                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label>{{ __('lawfirm::app.processos.form.desc') }}</x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="textarea" name="descricao"
                                :value="old('descricao')" rows="4" />
                        </x-admin::form.control-group>
                    </div>
                </div>
            </div>
        </form>
</x-admin::layouts>