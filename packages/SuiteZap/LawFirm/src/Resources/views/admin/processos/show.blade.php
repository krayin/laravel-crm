<x-admin::layouts>
    <x-slot:title>
        @lang('lawfirm::app.processos.view')
    </x-slot>

    <style>
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; }
        .modal-box { background: #fff; padding: 20px; width: 400px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .show-modal { display: flex !important; }
    </style>

        <div class="flex flex-col gap-4">

            <!-- Header -->
            <div
                class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <div class="flex cursor-pointer items-center gap-2">
                        <x-admin::breadcrumbs name="lawfirm.processos.show" :entity="$processo" />
                    </div>
                    <div class="text-xl font-bold dark:text-white">
                        {{ $processo->titulo }}
                        <span class="ml-2 text-sm font-normal text-gray-500">#{{ $processo->id }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-x-2.5">
                    <a href="{{ route('lawfirm.documents.procuration', $processo->id) }}" class="secondary-button" target="_blank">
                        ⚖️ Gerar Procuração
                    </a>
                    <a href="{{ route('lawfirm.documents.contract', $processo->id) }}" class="secondary-button" target="_blank">
                        📄 Gerar Contrato
                    </a>
                    <a href="{{ route('admin.processos.edit', $processo->id) }}" class="primary-button">
                        @lang('lawfirm::app.processos.edit')
                    </a>
                    <a href="{{ route('admin.processos.index') }}" class="secondary-button">
                        Voltar
                    </a>
                </div>
            </div>

            <!-- 1. Prazos -->
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center gap-2 mb-4">
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        Gestão de Prazos
                    </p>
                    @if($processo->data_audiencia)
                        <span class="text-base {{ $processo->audiencia_alert_class }}">
                            - Audiência: {{ \Carbon\Carbon::parse($processo->data_audiencia)->format('d/m/Y H:i') }}
                        </span>
                    @endif
                </div>

                <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800">
                    <table class="min-w-full w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead
                            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b dark:border-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 min-w-[350px]">Título</th>
                                <th scope="col" class="px-6 py-3 w-[160px]">Vencimento</th>
                                <th scope="col" class="px-6 py-3 min-w-[150px]">Status</th>
                                <th scope="col" class="px-6 py-3 w-full">Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($processo->prazos as $prazo)
                                <tr class="{{ $prazo->row_class }}">
                                    <td class="px-6 py-4 whitespace-nowrap min-w-[200px] {{ $prazo->text_class }}">
                                        {{ $prazo->titulo }}
                                    </td>
                                    <td class="px-6 py-4 {{ $prazo->text_class }}">
                                        {{ $prazo->data_vencimento ? $prazo->data_vencimento->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 {{ $prazo->text_class }}">
                                        {{ $prazo->status }}
                                    </td>
                                    <td class="px-6 py-4 {{ $prazo->text_class }}">
                                        {{ $prazo->descricao ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                            @if($processo->prazos->isEmpty())
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        Nenhum prazo cadastrado.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Financeiro -->
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        Gestão Financeira
                        @if($processo->valor_causa)
                            <span class="text-sm font-normal text-gray-500 ml-2">
                                - Valor da Causa: R$ {{ number_format((float) $processo->valor_causa, 2, ',', '.') }}
                            </span>
                        @endif
                    </p>
                </div>

                <!-- Dashboard KPIs -->
                <div class="flex flex-row flex-wrap gap-4 w-full items-stretch mb-6">
                    <!-- Receita -->
                    <div
                        class="flex-1 min-w-[150px] flex flex-col justify-center rounded-lg border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <span class="text-[11px] font-bold uppercase text-green-600 tracking-wider">Total
                            Receitas</span>
                        <span class="mt-1 text-lg font-bold text-green-600 dark:text-green-400">
                            {{ core()->formatBasePrice($processo->receita_total) }}
                        </span>
                    </div>
                    <!-- Despesas -->
                    <div
                        class="flex-1 min-w-[150px] flex flex-col justify-center rounded-lg border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <span class="text-[11px] font-bold uppercase text-red-800 tracking-wider">Total Despesas</span>
                        <span class="mt-1 text-lg font-bold text-red-800 dark:text-red-400">
                            {{ core()->formatBasePrice($processo->despesas_totais) }}
                        </span>
                    </div>
                    <!-- Saldo -->
                    <div
                        class="flex-1 min-w-[150px] flex flex-col justify-center rounded-lg border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <span
                            class="text-[11px] font-bold uppercase text-gray-500 dark:text-gray-400 tracking-wider">Saldo
                            Líquido</span>
                        <span
                            class="mt-1 text-lg font-bold {{ $processo->lucro_liquido >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ core()->formatBasePrice($processo->lucro_liquido) }}
                        </span>
                    </div>
                    <!-- Margem -->
                    <div
                        class="flex-1 min-w-[150px] flex flex-col justify-center rounded-lg border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <span class="text-[11px] font-bold uppercase text-blue-600 tracking-wider">Margem Lucro</span>
                        <span class="mt-1 text-lg font-bold text-gray-600 dark:text-gray-300">
                            {{ number_format($processo->margem_lucratividade, 2, ',', '.') }}%
                        </span>
                    </div>
                </div>

                <!-- Table -->
                <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800">
                    <table class="min-w-full w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead
                            class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b dark:border-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3">Tipo</th>
                                <th scope="col" class="px-6 py-3">Nome/Descrição</th>
                                <th scope="col" class="px-6 py-3">Valor</th>
                                <th scope="col" class="px-6 py-3">Vencimento</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($processo->financeiros as $item)
                                <tr
                                    class="bg-white border-b hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-600">
                                    <td
                                        class="px-6 py-4 font-medium {{ $item->tipo === 'receita' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ ucfirst($item->tipo) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-white">
                                        {{ $item->nome }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-white">
                                        R$ {{ number_format($item->valor, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-white">
                                        {{ $item->data_vencimento ? $item->data_vencimento->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 dark:text-white">
                                        {{ ucfirst($item->status) }}
                                    </td>
                                </tr>
                            @endforeach
                            @if($processo->financeiros->isEmpty())
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Nenhum lançamento financeiro.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. GED -->
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-4 text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="icon-file text-xl text-blue-600"></span>
                    Documentos Anexados
                </p>
                @include('lawfirm::admin.processos.partials.anexos', ['readOnly' => true, 'anexos' => $processo->anexos])
            </div>

            <!-- 4. Checklist de Documentos -->
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-4 text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="icon-menu text-xl text-purple-600"></span>
                    Checklist de Documentos
                </p>

                <!-- Barra de Ferramentas: Importar Template -->
                <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <form action="{{ route('lawfirm.documents.import', $processo->id) }}" method="POST" class="flex flex-wrap gap-3 items-center">
                        @csrf
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Importar Kit:</label>
                        <select name="template_id" class="rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm" style="min-width: 280px;">
                            <option value="">Selecione um Modelo...</option>
                            @foreach(\SuiteZap\LawFirm\Models\ChecklistTemplate::all() as $tpl)
                                <option value="{{ $tpl->id }}">{{ $tpl->name }} ({{ $tpl->area }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="primary-button">
                            Importar Kit
                        </button>
                    </form>
                </div>

                <!-- Lista de Documentos -->
                <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800">
                    <table class="min-w-full w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b dark:border-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 w-[120px]">Status</th>
                                <th scope="col" class="px-6 py-3">Documento Necessário</th>
                                <th scope="col" class="px-6 py-3">Observações</th>
                                <th scope="col" class="px-6 py-3 w-[180px]">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $documents = \SuiteZap\LawFirm\Models\ProcessDocument::where('processo_id', $processo->id)->get(); 
                            @endphp

                            @forelse($documents as $doc)
                            <tr class="bg-white border-b hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-600">
                                <td class="px-6 py-4">
                                    @if($doc->status == 'received')
                                        <span class="inline-flex px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Recebido</span>
                                    @elseif($doc->status == 'approved')
                                        <span class="inline-flex px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Validado</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pendente</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">{{ $doc->name }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $doc->notes ?? '-' }}</td>
                                <td class="actions" style="width: 100px; min-width: 100px;">
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <!-- Botão Editar (Ícone Lápis) -->
                                        <button type="button" class="transition text-blue-600 hover:text-blue-800" 
                                            title="Analisar / Editar Nota"
                                            data-id="{{ $doc->id }}"
                                            data-status="{{ $doc->status }}"
                                            data-notes="{{ $doc->notes ?? '' }}"
                                            onclick="openDocModal(this)">
                                            <span class="icon icon-edit text-2xl"></span>
                                        </button>

                                        <!-- Botão Excluir (Ícone Lixeira) -->
                                        <form action="{{ route('lawfirm.documents.delete', $doc->id) }}" method="POST" style="margin:0;" onsubmit="return confirm('Remover este documento?');">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="button" class="transition flex items-center justify-center text-red-600 hover:text-red-800" title="Remover" onclick="if(confirm('Remover este documento?')) this.closest('form').submit();">
                                                <span class="icon icon-delete text-2xl"></span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Nenhum documento solicitado. Use a importação acima para adicionar um kit.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. Identification & Process Details (2 Cols) -->
            <div class="flex gap-4">
                <!-- Left: Basic Info -->
                <div
                    class="w-1/2 flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        @lang('lawfirm::app.processos.form.group-info')
                    </p>

                    <!-- Status Badge -->
                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                            @lang('lawfirm::app.processos.form.status')
                        </p>
                        <span class="inline-flex px-2 py-1 text-xs rounded-full {{ 
                        $processo->status == 'Ativo' ? 'bg-green-100 text-green-800' :
    ($processo->status == 'Suspenso' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') 
                    }}">
                            {{ $processo->status ?? '-' }}
                        </span>
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                            @lang('lawfirm::app.processos.form.titulo')
                        </p>
                        <p class="text-base text-gray-900 dark:text-white font-medium">{{ $processo->titulo ?? '-' }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                            @lang('lawfirm::app.processos.form.cnj')
                        </p>
                        <p class="text-base text-gray-900 dark:text-white">{{ $processo->numero_cnj ?? '-' }}</p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Protocolo de Distribuição</p>
                        <p class="text-base text-gray-900 dark:text-white">
                            {{ $processo->protocolo_distribuicao ?? '-' }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                            @lang('lawfirm::app.processos.form.link')
                        </p>
                        @if($processo->link_acesso)
                            <a href="{{ $processo->link_acesso }}" target="_blank"
                                class="text-blue-600 hover:underline break-all">
                                {{ $processo->link_acesso }}
                            </a>
                        @else
                            <p class="text-base text-gray-900 dark:text-white">-</p>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                            @lang('lawfirm::app.processos.form.valor')
                        </p>
                        <p class="text-base text-gray-900 dark:text-white">R$
                            {{ number_format((float) $processo->valor_causa, 2, ',', '.') }}
                        </p>
                    </div>
                </div>

                <!-- Right: Details -->
                <div
                    class="w-1/2 flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        @lang('lawfirm::app.processos.form.group-details')
                    </p>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                @lang('lawfirm::app.processos.form.tribunal')
                            </p>
                            <p class="text-base text-gray-900 dark:text-white">{{ $processo->tribunal ?? '-' }}</p>
                        </div>

                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                @lang('lawfirm::app.processos.form.comarca')
                            </p>
                            <p class="text-base text-gray-900 dark:text-white">{{ $processo->comarca ?? '-' }}</p>
                        </div>

                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                @lang('lawfirm::app.processos.form.vara')
                            </p>
                            <p class="text-base text-gray-900 dark:text-white">{{ $processo->vara ?? '-' }}</p>
                        </div>

                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Juiz(a) Atual</p>
                            <p class="text-base text-gray-900 dark:text-white">{{ $processo->juiz_atual ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                            @lang('lawfirm::app.processos.form.fase')
                        </p>
                        <p class="text-base text-gray-900 dark:text-white">{{ $processo->fase_processual ?? '-' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                @lang('lawfirm::app.processos.form.area')
                            </p>
                            <p class="text-base text-gray-900 dark:text-white">{{ $processo->area_direito ?? '-' }}</p>
                        </div>

                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                @lang('lawfirm::app.processos.form.subarea')
                            </p>
                            <p class="text-base text-gray-900 dark:text-white">{{ $processo->subarea_direito ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Strategic Data -->
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="text-lg font-bold text-gray-800 dark:text-white mb-2">
                    Dados Estratégicos
                </p>
                <div class="space-y-1">
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                        @lang('lawfirm::app.processos.form.probabilidade')
                    </p>
                    <p class="text-base text-gray-900 dark:text-white">{{ $processo->probabilidade_exito ?? '-' }}</p>
                </div>
            </div>

            <!-- 6. Parts Management (2 Cols) -->
            <div class="flex gap-4">
                <!-- Left: Client Info -->
                <div
                    class="w-1/2 flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        @lang('lawfirm::app.processos.form.group-parts')
                    </p>

                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                            @lang('lawfirm::app.processos.form.person') (Cliente)
                        </p>
                        <p class="text-base text-gray-900 dark:text-white font-medium">
                            {{ $processo->person->name ?? '-' }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Responsável Interno (CRM)</p>
                        <p class="text-base text-gray-900 dark:text-white">
                            {{ $processo->responsavel->name ?? $processo->user->name ?? '-' }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Advogado Responsável (Peça)</p>
                        <p class="text-base text-gray-900 dark:text-white">
                            {{ $processo->advogado_responsavel_nome ?: 'Não informado' }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">OAB (Responsável)</p>
                        <p class="text-base text-gray-900 dark:text-white">
                            {{ $processo->advogado_responsavel_oab ?: '-' }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Qualificação da Parte</p>
                        <p class="text-base text-gray-900 dark:text-white capitalize">{{ $processo->tipo_parte ?? '-' }}
                        </p>
                    </div>
                </div>

                <!-- Right: Opposing Party -->
                <!-- Right: Opposing Party -->
                <div
                    class="w-1/2 flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-lg font-bold text-gray-800 dark:text-white mb-2">
                        Parte Contrária (Oponente)
                    </p>

                    <!-- Dados do Oponente -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="flex flex-col">
                            <span class="text-gray-500 text-xs font-semibold uppercase mb-1 dark:text-gray-400">Nome do
                                Oponente</span>
                            <span
                                class="text-gray-800 font-medium dark:text-white break-words">{{ $processo->opposing_party_name ?? $processo->parte_contraria ?? '-' }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-gray-500 text-xs font-semibold uppercase mb-1 dark:text-gray-400">Tipo da
                                Parte</span>
                            <span
                                class="text-gray-800 font-medium dark:text-white">{{ $processo->opposing_party_type ?? '-' }}</span>
                        </div>

                        <div class="flex flex-col col-span-2">
                            <span
                                class="text-gray-500 text-xs font-semibold uppercase mb-1 dark:text-gray-400">Documento
                                (CPF/CNPJ)</span>
                            <span
                                class="text-gray-800 font-medium dark:text-white">{{ $processo->opposing_party_document ?? '-' }}</span>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700 mb-4">

                    <!-- Advogado do Oponente -->
                    <p class="text-sm font-bold text-gray-800 dark:text-white mb-3">
                        Advogado do Oponente
                    </p>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <span class="text-gray-500 text-xs font-semibold uppercase mb-1 dark:text-gray-400">Nome do
                                Advogado</span>
                            <span
                                class="text-gray-800 font-medium dark:text-white break-words">{{ $processo->advogado_parte_contraria ?? '-' }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span
                                class="text-gray-500 text-xs font-semibold uppercase mb-1 dark:text-gray-400">OAB</span>
                            <span
                                class="text-gray-800 font-medium dark:text-white">{{ $processo->advogado_oab ?? '-' }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span
                                class="text-gray-500 text-xs font-semibold uppercase mb-1 dark:text-gray-400">E-mail</span>
                            <span
                                class="text-gray-800 font-medium dark:text-white break-all">{{ $processo->email_advogado_contrario ?? '-' }}</span>
                        </div>

                        <div class="flex flex-col">
                            <span
                                class="text-gray-500 text-xs font-semibold uppercase mb-1 dark:text-gray-400">WhatsApp</span>
                            <span
                                class="text-gray-800 font-medium dark:text-white">{{ $processo->whatsapp_advogado_contrario ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7. Dates & Description -->
            <div class="flex gap-4">
                <!-- Left: Dates -->
                <div
                    class="w-1/2 flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-lg font-bold text-gray-800 dark:text-white">
                        @lang('lawfirm::app.processos.form.group-dates')
                    </p>

                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                            @lang('lawfirm::app.processos.form.data_distribuicao')
                        </p>
                        <p class="text-base text-gray-900 dark:text-white">
                            {{ $processo->data_distribuicao ? $processo->data_distribuicao->format('d/m/Y') : '-' }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                            @lang('lawfirm::app.processos.form.data_audiencia')
                        </p>
                        <p class="text-base text-gray-900 dark:text-white">
                            {{ $processo->data_audiencia ? $processo->data_audiencia->format('d/m/Y H:i') : '-' }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                            @lang('lawfirm::app.processos.form.link_audiencia')
                        </p>
                        @if($processo->link_audiencia)
                            <a href="{{ $processo->link_audiencia }}" target="_blank"
                                class="text-blue-600 hover:underline break-all">
                                {{ $processo->link_audiencia }}
                            </a>
                        @else
                            <p class="text-base text-gray-900 dark:text-white">-</p>
                        @endif
                    </div>
                </div>

                <!-- Right: Description -->
                <div
                    class="w-1/2 flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-lg font-bold text-gray-800 dark:text-white mb-2">
                        @lang('lawfirm::app.processos.form.desc')
                    </p>
                    <div class="flex flex-col h-full">
                        <span
                            class="text-gray-800 font-medium dark:text-white whitespace-pre-wrap leading-relaxed">{!! nl2br(e($processo->descricao ?? 'Sem observações.')) !!}</span>
                    </div>
                </div>
            </div>

        </div>

    <!-- Modal HTML (Moved inside layout to ensure rendering) -->
    <div id="docModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99999; align-items: center; justify-content: center;">
        <div class="bg-white dark:bg-gray-800" style="padding: 25px; width: 500px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
            <h3 class="text-gray-800 dark:text-white" style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; font-weight: bold; font-size: 1.125rem;">
                Analisar Documento
            </h3>

            <form id="docForm" method="POST" action="">
                @csrf 
                <input type="hidden" name="_method" value="PUT">
                
                <div class="form-group" style="margin-bottom: 15px;">
                    <label class="text-gray-700 dark:text-gray-300" style="font-weight: bold; display: block; margin-bottom: 5px;">Status</label>
                    <select name="status" id="modalStatus" class="rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" style="width: 100%; height: 40px; padding: 0 10px;">
                        <option value="pending">🟡 Pendente</option>
                        <option value="received">🔵 Recebido</option>
                        <option value="approved">🟢 Validado</option>
                        <option value="rejected">🔴 Rejeitado</option>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="text-gray-700 dark:text-gray-300" style="font-weight: bold; display: block; margin-bottom: 5px;">Observações</label>
                    <textarea name="notes" id="modalNotes" class="rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" rows="4" style="width: 100%; padding: 10px;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="secondary-button" onclick="closeDocModal()">Cancelar</button>
                    <button type="submit" class="primary-button">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Função Global atrelada ao Window
        window.openDocModal = function(button) {
            // Pega os dados direto do botão clicado (Seguro contra aspas)
            var id = button.getAttribute('data-id');
            var status = button.getAttribute('data-status');
            var notes = button.getAttribute('data-notes');

            var modal = document.getElementById('docModal');
            var form = document.getElementById('docForm');
            var fieldStatus = document.getElementById('modalStatus');
            var fieldNotes = document.getElementById('modalNotes');

            // Define a rota
            var baseUrl = "{{ route('lawfirm.documents.update', '') }}"; 
            form.action = baseUrl + "/" + id;
            
            fieldStatus.value = status;
            fieldNotes.value = notes;
            
            modal.style.display = 'flex';
        }

        window.closeDocModal = function() {
            document.getElementById('docModal').style.display = 'none';
        }
    </script>
</x-admin::layouts>