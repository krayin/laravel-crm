<x-admin::layouts>
    <x-slot:title>
        @lang('lawfirm::app.processos.create-title')
    </x-slot>

    @inject('userRepository', 'Webkul\User\Repositories\UserRepository')

    <x-admin::form :action="route('admin.processos.store')" enctype="multipart/form-data">
        <div class="flex flex-col gap-4">
            
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <div class="flex cursor-pointer items-center gap-2">
                         <x-admin::breadcrumbs name="lawfirm.processos.create" />
                    </div>
                </div>
                <div class="flex items-center gap-x-2.5">
                    <button
                        type="submit"
                        class="primary-button"
                    >
                        @lang('lawfirm::app.processos.save-btn')
                    </button>
                </div>
            </div>

            <!-- BLOCO 0: PRAZOS (TOPO) -->
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                @include('lawfirm::admin.processos.partials.prazos', ['prazos' => []])
            </div>

            <!-- BLOCO 0.5: FINANCEIRO -->
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                @include('lawfirm::admin.processos.partials.financeiro')
            </div>

            <!-- BLOCO 0.7: GED / DOCUMENTOS -->
            @include('lawfirm::admin.processos.partials.anexos', ['editable' => true])

            <!-- BLOCO 1: CABEÇALHO -->
            <div class="flex gap-4">
                <!-- COLUNA ESQUERDA: Informações Básicas -->
                <div class="flex w-1/2 flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        @lang('lawfirm::app.processos.form.group-info')
                    </p>

                    <!-- Titulo -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            @lang('lawfirm::app.processos.form.titulo')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="text"
                            name="titulo"
                            rules="required"
                             :value="old('titulo')"
                            :label="trans('lawfirm::app.processos.form.titulo')"
                            :placeholder="trans('lawfirm::app.processos.form.titulo')"
                        />
                        <x-admin::form.control-group.error control-name="titulo" />
                    </x-admin::form.control-group>

                    <!-- CNJ -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('lawfirm::app.processos.form.cnj')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="text"
                            name="numero_cnj"
                             :value="old('numero_cnj')"
                            :label="trans('lawfirm::app.processos.form.cnj')"
                            :placeholder="trans('lawfirm::app.processos.form.cnj')"
                        />
                         <x-admin::form.control-group.error control-name="numero_cnj" />
                    </x-admin::form.control-group>

                    <!-- Protocolo de Distribuição -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            Protocolo de Distribuição
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="text"
                            name="protocolo_distribuicao"
                             :value="old('protocolo_distribuicao')"
                            label="Protocolo de Distribuição"
                            placeholder="Caso não tenha CNJ ainda"
                        />
                         <x-admin::form.control-group.error control-name="protocolo_distribuicao" />
                    </x-admin::form.control-group>

                    <!-- Link -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('lawfirm::app.processos.form.link')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="text"
                            name="link_acesso"
                             :value="old('link_acesso')"
                            :label="trans('lawfirm::app.processos.form.link')"
                            placeholder="https://"
                        />
                         <x-admin::form.control-group.error control-name="link_acesso" />
                    </x-admin::form.control-group>

                    <!-- Status -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            @lang('lawfirm::app.processos.form.status')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="select"
                            name="status"
                            rules="required"
                            :label="trans('lawfirm::app.processos.form.status')"
                        >
                             @foreach(['Ativo', 'Suspenso', 'Arquivado', 'Encerrado'] as $status)
                                <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                    {{ trans('lawfirm::app.processos.status-options.' . strtolower($status)) }}
                                </option>
                            @endforeach
                        </x-admin::form.control-group.control>
                        <x-admin::form.control-group.error control-name="status" />
                    </x-admin::form.control-group>

                    <!-- Valor da Causa -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('lawfirm::app.processos.form.valor')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="text"
                            name="valor_causa"
                             :value="old('valor_causa')"
                            :label="trans('lawfirm::app.processos.form.valor')"
                            placeholder="R$ 0,00"
                        />
                         <x-admin::form.control-group.error control-name="valor_causa" />
                    </x-admin::form.control-group>
                </div>

                <!-- COLUNA DIREITA: Detalhes Processuais -->
                <div class="flex w-1/2 flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        @lang('lawfirm::app.processos.form.group-details')
                    </p>

                    <!-- Tribunal -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('lawfirm::app.processos.form.tribunal')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="text"
                            name="tribunal"
                             :value="old('tribunal')"
                            :label="trans('lawfirm::app.processos.form.tribunal')"
                            :placeholder="trans('lawfirm::app.processos.form.tribunal')"
                        />
                         <x-admin::form.control-group.error control-name="tribunal" />
                    </x-admin::form.control-group>

                    <!-- Comarca -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('lawfirm::app.processos.form.comarca')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="text"
                            name="comarca"
                             :value="old('comarca')"
                            :label="trans('lawfirm::app.processos.form.comarca')"
                            :placeholder="trans('lawfirm::app.processos.form.comarca')"
                        />
                         <x-admin::form.control-group.error control-name="comarca" />
                    </x-admin::form.control-group>

                    <!-- Vara -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('lawfirm::app.processos.form.vara')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="text"
                            name="vara"
                             :value="old('vara')"
                        :label="trans('lawfirm::app.processos.form.vara')"
                            :placeholder="trans('lawfirm::app.processos.form.placeholder-vara')"
                        />
                         <x-admin::form.control-group.error control-name="vara" />
                    </x-admin::form.control-group>

                    <!-- Juiz Atual -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            Juiz(a) Atual
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="text"
                            name="juiz_atual"
                             :value="old('juiz_atual')"
                            label="Juiz(a) Atual"
                            placeholder="Juiz(a) Atual"
                        />
                         <x-admin::form.control-group.error control-name="juiz_atual" />
                    </x-admin::form.control-group>

                    <!-- Fase Processual (SPLIT) -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('lawfirm::app.processos.form.fase')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="select"
                            name="fase_processual"
                            :label="trans('lawfirm::app.processos.form.fase')"
                        >
                            <option value="">@lang('lawfirm::app.processos.form.select-choose')</option>
                            @foreach(['Inicial', 'Contestação', 'Réplica', 'Instrução', 'Julgamento', 'Sentença', 'Recurso', 'Execução'] as $phase)
                                <option value="{{ $phase }}" {{ old('fase_processual') == $phase ? 'selected' : '' }}>
                                    {{ $phase }}
                                </option>
                            @endforeach
                        </x-admin::form.control-group.control>
                         <x-admin::form.control-group.error control-name="fase_processual" />
                    </x-admin::form.control-group>

                     <!-- Linha Interna: Área e Sub-área -->
                    <div class="flex gap-4">
                        <!-- Area -->
                        <x-admin::form.control-group class="w-1/2">
                            <x-admin::form.control-group.label>
                                @lang('lawfirm::app.processos.form.area')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="select"
                                name="area_direito"
                                :label="trans('lawfirm::app.processos.form.area')"
                            >
                                <option value="">@lang('lawfirm::app.processos.form.select-choose')</option>
                                @foreach(['Civil', 'Trabalhista', 'Penal', 'Tributário', 'Família', 'Consumidor', 'Previdenciário'] as $area)
                                    <option value="{{ $area }}" {{ old('area_direito') == $area ? 'selected' : '' }}>
                                        {{ $area }}
                                    </option>
                                @endforeach
                            </x-admin::form.control-group.control>
                             <x-admin::form.control-group.error control-name="area_direito" />
                        </x-admin::form.control-group>

                        <!-- Sub-Area -->
                        <x-admin::form.control-group class="w-1/2">
                            <x-admin::form.control-group.label>
                                @lang('lawfirm::app.processos.form.subarea')
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="text"
                                name="subarea_direito"
                                 :value="old('subarea_direito')"
                                :label="trans('lawfirm::app.processos.form.subarea')"
                                :placeholder="trans('lawfirm::app.processos.form.placeholder-subarea')"
                            />
                             <x-admin::form.control-group.error control-name="subarea_direito" />
                        </x-admin::form.control-group>
                    </div>
                </div>
            </div>
            
            <!-- NEW CARD: DADOS ESTRATÉGICOS -->
             <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="text-lg font-bold text-gray-800 dark:text-white mb-4">
                    Dados Estratégicos
                </p>
                 <!-- Probabilidade (MOVED) -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label>
                        @lang('lawfirm::app.processos.form.probabilidade')
                    </x-admin::form.control-group.label>
                    <x-admin::form.control-group.control
                        type="select"
                        name="probabilidade_exito"
                        :label="trans('lawfirm::app.processos.form.probabilidade')"
                    >
                        <option value="">@lang('lawfirm::app.processos.form.select-choose')</option>
                            @foreach(['Alta', 'Média', 'Baixa', 'Muito Baixa', 'Muito Alta'] as $prob)
                            <option value="{{ $prob }}" {{ old('probabilidade_exito') == $prob ? 'selected' : '' }}>
                                {{ $prob }}
                            </option>
                        @endforeach
                    </x-admin::form.control-group.control>
                        <x-admin::form.control-group.error control-name="probabilidade_exito" />
                </x-admin::form.control-group>
            </div>


            <!-- BLOCO 2: GESTÃO DAS PARTES -->
            <div class="flex gap-4">
                <!-- COLUNA ESQUERDA: Partes Envolvidas -->
                <div class="flex w-1/2 flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        @lang('lawfirm::app.processos.form.group-parts')
                    </p>

                    <!-- Pessoa (Cliente) LookUp -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            @lang('lawfirm::app.processos.form.person')
                        </x-admin::form.control-group.label>
                        <x-admin::lookup 
                            src="{{ route('admin.contacts.persons.search') }}" 
                            name="person_id" 
                            :placeholder="trans('lawfirm::app.processos.form.search-client')"
                        />
                        <x-admin::form.control-group.error control-name="person_id" />
                    </x-admin::form.control-group>

                    <!-- Tipo Parte (RENAMED) -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            Qualificação da Parte
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="select"
                            name="tipo_parte"
                            :label="'Qualificação da Parte'"
                        >
                             <option value="autor" {{ old('tipo_parte') == 'autor' ? 'selected' : '' }}>Autor</option>
                             <option value="reu" {{ old('tipo_parte') == 'reu' ? 'selected' : '' }}>Réu</option>
                        </x-admin::form.control-group.control>
                         <x-admin::form.control-group.error control-name="tipo_parte" />
                    </x-admin::form.control-group>


                    <!-- Advogado Responsável -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            Advogado Responsável
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="select"
                            name="user_id"
                            :label="'Advogado Responsável'"
                        >
                            <option value="">@lang('lawfirm::app.processos.form.select-choose')</option>
                            @foreach($userRepository->all() as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', auth()->id()) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </x-admin::form.control-group.control>
                         <x-admin::form.control-group.error control-name="user_id" />
                    </x-admin::form.control-group>
                </div>

                <!-- COLUNA DIREITA: Parte Contrária (STRUCTURED) -->
                <div class="flex w-1/2 flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        Parte Contrária (Oponente)
                    </p>

                     <!-- NEW: Nome / Razão Social -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            Nome / Razão Social
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="text"
                            name="opposing_party_name"
                             :value="old('opposing_party_name')"
                            label="Nome / Razão Social"
                        />
                         <x-admin::form.control-group.error control-name="opposing_party_name" />
                    </x-admin::form.control-group>
                    
                    <div class="flex gap-4">
                        <!-- NEW: Tipo de Pessoa -->
                         <x-admin::form.control-group class="w-1/3">
                            <x-admin::form.control-group.label>
                                Tipo
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="select"
                                name="opposing_party_type"
                                id="opposing_party_type"
                                label="Tipo"
                                onchange="toggleMask()"
                            >
                                <option value="PF" {{ old('opposing_party_type') == 'PF' ? 'selected' : '' }}>PF</option>
                                <option value="PJ" {{ old('opposing_party_type') == 'PJ' ? 'selected' : '' }}>PJ</option>
                            </x-admin::form.control-group.control>
                             <x-admin::form.control-group.error control-name="opposing_party_type" />
                        </x-admin::form.control-group>

                        <!-- NEW: CPF/CNPJ -->
                        <x-admin::form.control-group class="w-2/3">
                            <x-admin::form.control-group.label>
                                CPF / CNPJ
                            </x-admin::form.control-group.label>
                            <x-admin::form.control-group.control
                                type="text"
                                name="opposing_party_document"
                                id="opposing_party_document"
                                 :value="old('opposing_party_document')"
                                label="CPF / CNPJ"
                                oninput="applyMask()"
                            />
                             <x-admin::form.control-group.error control-name="opposing_party_document" />
                        </x-admin::form.control-group>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-800 my-2">
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Dados do Advogado do Oponente</p>

                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            Nome do Advogado
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="text"
                            name="advogado_parte_contraria"
                             :value="old('advogado_parte_contraria')"
                            label="Nome do Advogado"
                        />
                         <x-admin::form.control-group.error control-name="advogado_parte_contraria" />
                    </x-admin::form.control-group>

                    <!-- OAB -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('lawfirm::app.processos.form.oab')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="text"
                            name="advogado_oab"
                             :value="old('advogado_oab')"
                            :label="trans('lawfirm::app.processos.form.oab')"
                        />
                         <x-admin::form.control-group.error control-name="advogado_oab" />
                    </x-admin::form.control-group>
                    
                     <!-- WhatsApp -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('lawfirm::app.processos.form.whatsapp')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="text"
                            name="whatsapp_advogado_contrario"
                             :value="old('whatsapp_advogado_contrario')"
                            :label="trans('lawfirm::app.processos.form.whatsapp')"
                        />
                         <x-admin::form.control-group.error control-name="whatsapp_advogado_contrario" />
                    </x-admin::form.control-group>

                     <!-- Email -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('lawfirm::app.processos.form.email_advogado')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="email"
                            name="email_advogado_contrario"
                             :value="old('email_advogado_contrario')"
                            :label="trans('lawfirm::app.processos.form.email_advogado')"
                        />
                         <x-admin::form.control-group.error control-name="email_advogado_contrario" />
                    </x-admin::form.control-group>
                </div>
            </div>

            <!-- BLOCO 3: DATAS E OBSERVAÇÕES -->
            <div class="flex gap-4">
                <!-- COLUNA ESQUERDA: Datas -->
                <div class="flex w-1/2 flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        @lang('lawfirm::app.processos.form.group-dates')
                    </p>

                    <!-- Data Distribuição -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('lawfirm::app.processos.form.data_distribuicao')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="date"
                            name="data_distribuicao"
                             :value="old('data_distribuicao')"
                            :label="trans('lawfirm::app.processos.form.data_distribuicao')"
                        />
                         <x-admin::form.control-group.error control-name="data_distribuicao" />
                    </x-admin::form.control-group>
                    
                    <!-- Data Audiencia -->
                     <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('lawfirm::app.processos.form.data_audiencia')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="datetime"
                            name="data_audiencia"
                             :value="old('data_audiencia')"
                            :label="trans('lawfirm::app.processos.form.data_audiencia')"
                        />
                         <x-admin::form.control-group.error control-name="data_audiencia" />
                    </x-admin::form.control-group>

                     <!-- Link Audiencia -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                             @lang('lawfirm::app.processos.form.link_audiencia')
                        </x-admin::form.control-group.label>
                        <x-admin::form.control-group.control
                            type="text"
                            name="link_audiencia"
                             :value="old('link_audiencia')"
                            :label="trans('lawfirm::app.processos.form.link_audiencia')"
                            placeholder="Zoom/Meet Link"
                        />
                         <x-admin::form.control-group.error control-name="link_audiencia" />
                    </x-admin::form.control-group>
                </div>

                <!-- COLUNA DIREITA: Observações -->
                <div class="flex w-1/2 flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        @lang('lawfirm::app.processos.form.desc')
                    </p>
                    
                    <x-admin::form.control-group class="!mb-0 h-full">
                         <x-admin::form.control-group.control
                            type="textarea"
                            name="descricao"
                            class="!h-full min-h-[150px]"
                            rows="6"
                             :value="old('descricao')"
                            :label="trans('lawfirm::app.processos.form.desc')"
                        />
                         <x-admin::form.control-group.error control-name="descricao" />
                    </x-admin::form.control-group>
                </div>
            </div>

        </div>
    </x-admin::form>

    @push('scripts')
        <script>
            function maskCPF(value) {
                return value
                    .replace(/\D/g, '')
                    .replace(/(\d{3})(\d)/, '$1.$2')
                    .replace(/(\d{3})(\d)/, '$1.$2')
                    .replace(/(\d{3})(\d{1,2})/, '$1-$2')
                    .replace(/(-\d{2})\d+?$/, '$1');
            }

            function maskCNPJ(value) {
                return value
                    .replace(/\D/g, '')
                    .replace(/(\d{2})(\d)/, '$1.$2')
                    .replace(/(\d{3})(\d)/, '$1.$2')
                    .replace(/(\d{3})(\d)/, '$1/$2')
                    .replace(/(\d{4})(\d)/, '$1-$2')
                    .replace(/(-\d{2})\d+?$/, '$1');
            }

            function applyMask() {
                const type = document.getElementById('opposing_party_type').value;
                const input = document.getElementById('opposing_party_document');
                if (type === 'PF') {
                    input.value = maskCPF(input.value);
                    input.maxLength = 14;
                } else {
                    input.value = maskCNPJ(input.value);
                    input.maxLength = 18;
                }
            }

            function toggleMask() {
                const input = document.getElementById('opposing_party_document');
                input.value = ''; // clear on switch
            }
        </script>
    @endpush
</x-admin::layouts>