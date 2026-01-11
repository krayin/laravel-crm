@php
    $prazos = isset($processo) ? $processo->prazos()->orderBy('data_vencimento', 'asc')->get() : collect([]);

    // Lógica de Cor para Audiência
    $audienceColorClass = "text-gray-600 dark:text-gray-400"; // Padrão
    if (isset($processo) && $processo->data_audiencia) {
        $audiencia = \Carbon\Carbon::parse($processo->data_audiencia)->startOfDay();
        $hoje = \Carbon\Carbon::now()->startOfDay();
        $diffDetails = $hoje->diffInDays($audiencia, false);

        // Verifica se processo está ativo para aplicar cores de alerta
        if ($processo->status === 'Ativo' || $processo->status === 'Suspenso') {
            if ($diffDetails <= 0) {
                // HOJE ou PASSADA: Vermelho Pastel
                $audienceColorClass = "text-red-800 bg-red-100 px-2 py-0.5 rounded font-bold animate-pulse";
            } elseif ($diffDetails <= 5) {
                // PRÓXIMOS 5 DIAS: Laranja
                $audienceColorClass = "text-orange-600 font-bold";
            } else {
                // FUTURO SEGURO: Verde
                $audienceColorClass = "text-emerald-600 font-medium";
            }
        }
    }
@endphp

<div class="flex items-center justify-between mb-4 mt-6">
    <div class="flex items-center gap-2">
        <p class="text-lg font-bold text-gray-800 dark:text-white">
            {{ __('lawfirm::app.prazos.section-title') ?? 'Gestão de Prazos' }}
        </p>
        @if(isset($processo) && $processo->data_audiencia)
            <span class="text-base {{ $audienceColorClass }}">
                - Audiência: {{ \Carbon\Carbon::parse($processo->data_audiencia)->format('d/m/Y H:i') }}
            </span>
        @endif
    </div>

    <div class="flex gap-2">


        <button type="button" class="primary-button" onclick="adicionarPrazo()">
            <span class="icon-plus text-lg inline-block align-middle mr-1"></span>
            {{ __('lawfirm::app.prazos.new-btn') ?? 'Novo Prazo' }}
        </button>
    </div>
</div>

<div class="overflow-x-auto rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <table class="min-w-full text-left text-sm text-gray-500 dark:text-gray-400" id="tabela-prazos">
        <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-800 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3 min-w-[400px] required">@lang('lawfirm::app.prazos.title-table')</th>
                <th scope="col" class="px-6 py-3 w-[160px] required">@lang('lawfirm::app.prazos.due-date')</th>
                <th scope="col" class="px-6 py-3 min-w-[150px] required">@lang('lawfirm::app.prazos.status')</th>
                <th scope="col" class="px-6 py-3 w-full">Descrição</th>
                <th scope="col" class="px-6 py-3 w-[50px] text-center"></th>
            </tr>
        </thead>
        <tbody id="container-prazos">
            @foreach ($prazos as $index => $prazo)
                @php
                    $vencimento = \Carbon\Carbon::parse($prazo->data_vencimento)->startOfDay();
                    $hoje = \Carbon\Carbon::now()->startOfDay();
                    $diff = $hoje->diffInDays($vencimento, false); // false mantem negativo se venceu
                    $status = $prazo->status;

                    // Classes Padrão
                    $colorClass = "bg-white border-gray-300 text-gray-600"; // Neutro

                    if ($status !== 'Concluído' && $status !== 'concluido') {
                        if ($diff <= 0) {
                            // HOJE ou VENCIDO: Vermelho Piscante
                            $colorClass = "bg-red-100 border-red-500 text-red-800 font-bold animate-pulse";
                        } elseif ($diff <= 5) {
                            // ATENÇÃO (5 dias): Laranja
                            $colorClass = "bg-orange-100 border-orange-500 text-orange-800 font-medium";
                        } else {
                            // NO PRAZO: Verde Esmeralda
                            $colorClass = "bg-emerald-50 border-emerald-500 text-emerald-800";
                        }
                    } else {
                        // CONCLUÍDO: Cinza/Verde suave (Visual de tarefa feita)
                        $colorClass = "bg-gray-50 border-gray-200 text-gray-400 line-through";
                    }
                @endphp
                <tr class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:hover:bg-gray-800"
                    id="prazo-row-{{ $index }}">
                    <!-- Hidden ID -->
                    <input type="hidden" name="prazos[{{ $index }}][id]" value="{{ $prazo->id }}">

                    <!-- Título -->
                    <td class="px-1 py-1 min-w-[350px]" style="min-width: 350px;">
                        <input type="text" name="prazos[{{ $index }}][titulo]" value="{{ $prazo->titulo }}"
                            class="w-full rounded-md border px-3 py-2.5 text-sm font-normal transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 {{ $colorClass }}"
                            style="width: 100%; box-sizing: border-box;" required>
                    </td>

                    <!-- Data -->
                    <td class="px-1 py-1">
                        <input type="date" name="prazos[{{ $index }}][data_vencimento]"
                            value="{{ $prazo->data_vencimento ? $prazo->data_vencimento->format('Y-m-d') : '' }}"
                            class="w-full rounded-md border px-3 py-2.5 text-sm font-normal transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 {{ $colorClass }}"
                            required>
                    </td>

                    <!-- Status -->
                    <td class="px-1 py-1">
                        <select name="prazos[{{ $index }}][status]"
                            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                            required>
                            <option value="Pendente" {{ $prazo->status === 'Pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="Concluído" {{ $prazo->status === 'Concluído' ? 'selected' : '' }}>Concluído
                            </option>
                        </select>
                    </td>

                    <!-- Descrição -->
                    <td class="px-1 py-1">
                        <input type="text" name="prazos[{{ $index }}][descricao]" value="{{ $prazo->descricao }}"
                            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                    </td>

                    <!-- Actions -->
                    <td class="px-1 py-1 text-center">
                        <button type="button" class="text-red-600 hover:text-red-900 cursor-pointer"
                            onclick="removerPrazo('prazo-row-{{ $index }}')">
                            <span class="icon-delete text-xl"></span>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
    <script>
        function adicionarPrazo() {
            const index = Date.now();
            const container = document.getElementById('container-prazos');
            const rowId = `prazo-row-${index}`;

            const row = document.createElement('tr');
            row.id = rowId;
            row.className = 'border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:hover:bg-gray-800';

            row.innerHTML = `
                                <td class="px-1 py-1 min-w-[350px]" style="min-width: 350px;">
                                    <input type="text" name="prazos[${index}][titulo]" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300" style="width: 100%; box-sizing: border-box;" placeholder="Título" required>
                                </td>
                                <td class="px-1 py-1">
                                     <input type="date" name="prazos[${index}][data_vencimento]" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300" required>
                                </td>
                                <td class="px-1 py-1">
                                    <select name="prazos[${index}][status]" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                        <option value="Pendente">Pendente</option>
                                        <option value="Concluído">Concluído</option>
                                    </select>
                                </td>
                                <td class="px-1 py-1">
                                     <input type="text" name="prazos[${index}][descricao]" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300" placeholder="Descrição">
                                </td>
                                <td class="px-1 py-1 text-center">
                                    <button type="button" class="text-red-600 hover:text-red-900 cursor-pointer" onclick="removerPrazo('${rowId}')">
                                        <span class="icon-delete text-xl"></span>
                                    </button>
                                </td>
                            `;

            container.appendChild(row);
        }

        function removerPrazo(rowId) {
            const row = document.getElementById(rowId);
            if (row) {
                row.remove();
            }
        }
    </script>
@endpush