<x-admin::layouts>
    <x-slot:title>
        {{ __('lawfirm::app.processos.create') }}
        </x-slot>

        <x-admin::form :action="route('admin.processos.store')" method="POST">
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
                {{-- COLUNA ESQUERDA: Informações Gerais --}}
                <div class="flex flex-col gap-4 w-1/2">
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                        <h4 class="font-semibold mb-4 text-gray-800 dark:text-white">
                            {{ __('lawfirm::app.processos.form.group-info') }}
                        </h4>

                        {{-- 1. Título da Ação --}}
                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label class="required">
                                {{ __('lawfirm::app.processos.form.titulo') }}
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="text" name="titulo" :value="old('titulo')"
                                rules="required" :label="__('lawfirm::app.processos.form.titulo')" />
                            <x-admin::form.control-group.error control-name="titulo" />
                        </x-admin::form.control-group>

                        {{-- 2. Número do Processo/CNJ --}}
                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label class="required">
                                {{ __('lawfirm::app.processos.form.cnj') }}
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="text" name="numero_cnj"
                                :value="old('numero_cnj')" rules="required"
                                :label="__('lawfirm::app.processos.form.cnj')" />
                            <x-admin::form.control-group.error control-name="numero_cnj" />
                        </x-admin::form.control-group>

                        {{-- 3. Vara/Tribunal | Comarca (Lado a Lado) --}}
                        <div class="flex gap-4">
                            <x-admin::form.control-group class="w-1/2 mb-4">
                                <x-admin::form.control-group.label>
                                    {{ __('lawfirm::app.processos.form.tribunal') }}
                                </x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="tribunal"
                                    :value="old('tribunal')" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group class="w-1/2 mb-4">
                                <x-admin::form.control-group.label>
                                    {{ __('lawfirm::app.processos.form.comarca') }}
                                </x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="comarca"
                                    :value="old('comarca')" />
                            </x-admin::form.control-group>
                        </div>

                        {{-- 4. Link do Processo --}}
                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label>
                                {{ __('lawfirm::app.processos.form.link') }}
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="text" name="link_acesso"
                                :value="old('link_acesso')" placeholder="https://..." />
                        </x-admin::form.control-group>

                        {{-- 5. Fase Processual (SELECT COM TITLE CASE) --}}
                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label>
                                {{ __('lawfirm::app.processos.form.fase') }}
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="select" name="fase_processual"
                                class="cursor-pointer">
                                <option value="">Selecione...</option>
                                <option value="Inicial" {{ old('fase_processual') == 'Inicial' ? 'selected' : '' }}>
                                    Inicial</option>
                                <option value="Contestação" {{ old('fase_processual') == 'Contestação' ? 'selected' : '' }}>Contestação</option>
                                <option value="Réplica" {{ old('fase_processual') == 'Réplica' ? 'selected' : '' }}>
                                    Réplica</option>
                                <option value="Instrução" {{ old('fase_processual') == 'Instrução' ? 'selected' : '' }}>
                                    Instrução</option>
                                <option value="Sentença" {{ old('fase_processual') == 'Sentença' ? 'selected' : '' }}>
                                    Sentença</option>
                                <option value="Recurso" {{ old('fase_processual') == 'Recurso' ? 'selected' : '' }}>
                                    Recurso</option>
                                <option value="Execução" {{ old('fase_processual') == 'Execução' ? 'selected' : '' }}>
                                    Execução</option>
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>
                    </div>
                </div>

                {{-- COLUNA DIREITA: Detalhes e Partes --}}
                <div class="flex flex-col gap-4 w-1/2">
                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4">
                        <h4 class="font-semibold mb-4 text-gray-800 dark:text-white">
                            {{ __('lawfirm::app.processos.form.group-details') }}
                        </h4>

                        {{-- 1. Cliente (SELECT COM COMPARAÇÃO CORRETA) --}}
                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label class="required">
                                {{ __('lawfirm::app.processos.form.person') }}
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="select" name="person_id" class="cursor-pointer"
                                rules="required">
                                <option value="">{{ __('lawfirm::app.processos.form.select-person') }}</option>
                                @foreach($persons as $person)
                                    <option value="{{ $person->id }}" {{ (isset($preSelectedLead) && $preSelectedLead->person_id == $person->id) || old('person_id') == $person->id ? 'selected' : '' }}>
                                        {{ $person->name }}
                                    </option>
                                @endforeach
                            </x-admin::form.control-group.control>
                            <x-admin::form.control-group.error control-name="person_id" />
                        </x-admin::form.control-group>

                        {{-- 2. Lead de Origem (SELECT COM COMPARAÇÃO CORRETA) --}}
                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label>
                                {{ __('lawfirm::app.processos.form.lead') }}
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="select" name="lead_id" class="cursor-pointer">
                                <option value="">{{ __('lawfirm::app.processos.form.select-lead') }}</option>
                                @foreach($leads as $lead)
                                    <option value="{{ $lead->id }}" {{ (isset($preSelectedLead) && $preSelectedLead->id == $lead->id) || old('lead_id') == $lead->id ? 'selected' : '' }}>
                                        {{ $lead->title }}
                                    </option>
                                @endforeach
                            </x-admin::form.control-group.control>
                        </x-admin::form.control-group>

                        {{-- 3. Parte Contrária --}}
                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label>
                                {{ __('lawfirm::app.processos.form.adversary') }}
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="text" name="parte_contraria"
                                :value="old('parte_contraria')" />
                        </x-admin::form.control-group>

                        {{-- 4. Advogado da Parte Contrária --}}
                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label>
                                {{ __('lawfirm::app.processos.form.advogado_adversary') }}
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="text" name="advogado_parte_contraria"
                                :value="old('advogado_parte_contraria')" />
                        </x-admin::form.control-group>

                        {{-- 5. Área do Direito | Probabilidade de Êxito (SELECT COM TITLE CASE - Lado a Lado) --}}
                        <div class="flex gap-4">
                            <x-admin::form.control-group class="w-1/2 mb-4">
                                <x-admin::form.control-group.label>
                                    {{ __('lawfirm::app.processos.form.area') }}
                                </x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="select" name="area_direito"
                                    class="cursor-pointer">
                                    <option value="">Selecione...</option>
                                    <option value="Civil" {{ old('area_direito') == 'Civil' ? 'selected' : '' }}>Civil
                                    </option>
                                    <option value="Trabalhista" {{ old('area_direito') == 'Trabalhista' ? 'selected' : '' }}>Trabalhista</option>
                                    <option value="Penal" {{ old('area_direito') == 'Penal' ? 'selected' : '' }}>Penal
                                    </option>
                                    <option value="Tributário" {{ old('area_direito') == 'Tributário' ? 'selected' : '' }}>Tributário</option>
                                    <option value="Família" {{ old('area_direito') == 'Família' ? 'selected' : '' }}>
                                        Família</option>
                                    <option value="Consumidor" {{ old('area_direito') == 'Consumidor' ? 'selected' : '' }}>Consumidor</option>
                                    <option value="Previdenciário" {{ old('area_direito') == 'Previdenciário' ? 'selected' : '' }}>Previdenciário</option>
                                </x-admin::form.control-group.control>
                            </x-admin::form.control-group>

                            <x-admin::form.control-group class="w-1/2 mb-4">
                                <x-admin::form.control-group.label>
                                    {{ __('lawfirm::app.processos.form.probabilidade') }}
                                </x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="select" name="probabilidade_exito"
                                    class="cursor-pointer">
                                    <option value="">Selecione...</option>
                                    <option value="Alta" {{ old('probabilidade_exito') == 'Alta' ? 'selected' : '' }}>Alta
                                    </option>
                                    <option value="Média" {{ old('probabilidade_exito') == 'Média' ? 'selected' : '' }}>
                                        Média</option>
                                    <option value="Baixa" {{ old('probabilidade_exito') == 'Baixa' ? 'selected' : '' }}>
                                        Baixa</option>
                                </x-admin::form.control-group.control>
                            </x-admin::form.control-group>
                        </div>

                        {{-- 6 e 7. Data da Distribuição | Data da Audiência (LADO A LADO - DATETIME NATIVO KRAYIN V2)
                        --}}
                        <div class="flex gap-4">
                            <x-admin::form.control-group class="w-1/2 mb-4">
                                <x-admin::form.control-group.label>
                                    {{ __('lawfirm::app.processos.form.data_distribuicao') }}
                                </x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="date" name="data_distribuicao"
                                    :value="old('data_distribuicao')" />
                                <x-admin::form.control-group.error control-name="data_distribuicao" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group class="w-1/2 mb-4">
                                <x-admin::form.control-group.label>
                                    {{ __('lawfirm::app.processos.form.data_audiencia') }}
                                </x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="datetime" name="data_audiencia"
                                    :value="old('data_audiencia')" placeholder="Selecione Data e Hora" />
                                <x-admin::form.control-group.error control-name="data_audiencia" />
                            </x-admin::form.control-group>
                        </div>

                        {{-- 8. Status Atual (SELECT COM TITLE CASE - Required) --}}
                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label class="required">
                                {{ __('lawfirm::app.processos.form.status') }}
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="select" name="status" rules="required"
                                class="cursor-pointer">
                                <option value="Ativo" {{ old('status') == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                                <option value="Suspenso" {{ old('status') == 'Suspenso' ? 'selected' : '' }}>Suspenso
                                </option>
                                <option value="Arquivado" {{ old('status') == 'Arquivado' ? 'selected' : '' }}>Arquivado
                                </option>
                            </x-admin::form.control-group.control>
                            <x-admin::form.control-group.error control-name="status" />
                        </x-admin::form.control-group>

                        {{-- 9. Valor da Causa --}}
                        <x-admin::form.control-group class="mb-4">
                            <x-admin::form.control-group.label>
                                {{ __('lawfirm::app.processos.form.valor') }}
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control type="text" name="valor_causa"
                                :value="old('valor_causa')" placeholder="R$ 0,00" />
                        </x-admin::form.control-group>
                    </div>
                </div>
            </div>

            {{-- RODAPÉ: Descrição/Observações (Full Width) --}}
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-4 mt-4">
                <x-admin::form.control-group class="mb-4">
                    <x-admin::form.control-group.label>
                        {{ __('lawfirm::app.processos.form.desc') }}
                    </x-admin::form.control-group.label>
                    <x-admin::form.control-group.control type="textarea" name="descricao" :value="old('descricao')"
                        rows="4" />
                </x-admin::form.control-group>
            </div>
        </x-admin::form>
</x-admin::layouts>