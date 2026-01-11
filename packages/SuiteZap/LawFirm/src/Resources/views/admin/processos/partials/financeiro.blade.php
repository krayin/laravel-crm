@php
    $financeiros = isset($processo) ? $processo->financeiros : collect([]);
    $valorCausa = isset($processo) && $processo->valor_causa ? ' - Valor da Causa: R$ ' . number_format((float) $processo->valor_causa, 2, ',', '.') : '';

    // Categorias por tipo
    $categoriasReceita = ['honorario' => 'Honorários', 'sucumbencia' => 'Sucumbência', 'reembolso' => 'Reembolso', 'consultoria' => 'Consultoria'];
    $categoriasDespesa = ['custas' => 'Custas Processuais', 'deslocamento' => 'Deslocamento', 'taxas' => 'Taxas', 'diligencias' => 'Diligências'];
    $formasPagamento = ['boleto' => 'Boleto', 'pix' => 'PIX', 'transferencia' => 'Transferência', 'cartao' => 'Cartão'];
@endphp

<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-2">
        <p class="text-lg font-bold text-gray-800 dark:text-white">
            Gestão Financeira {{ $valorCausa }}
        </p>
    </div>

    <div class="flex gap-2">
        <!-- Botão Salvar -->
        <button type="submit"
            class="flex items-center justify-center gap-2 rounded border border-emerald-600 bg-white px-3 py-1.5 text-emerald-600 hover:bg-emerald-50 transition-colors"
            title="Salvar Alterações">
            <span class="icon-save text-lg"></span>
            <span class="text-sm font-medium">Salvar</span>
        </button>

        <button type="button" class="primary-button" onclick="adicionarFinanceiro()">
            <span class="icon-plus text-lg inline-block align-middle mr-1"></span>
            Novo Lançamento
        </button>
    </div>
</div>

<div class="overflow-x-auto rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <table class="min-w-full text-left text-sm text-gray-500 dark:text-gray-400" id="tabela-financeiros">
        <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-800 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-4 py-3 min-w-[120px] required">Tipo</th>
                <th scope="col" class="px-4 py-3 min-w-[140px]">Categoria</th>
                <th scope="col" class="px-4 py-3 min-w-[220px] required">Nome/Descrição</th>
                <th scope="col" class="px-4 py-3 min-w-[120px] required">Valor (R$)</th>
                <th scope="col" class="px-4 py-3 w-[140px] required">Vencimento</th>
                <th scope="col" class="px-4 py-3 w-[140px]">Competência</th>
                <th scope="col" class="px-4 py-3 min-w-[120px] required">Status</th>
                <th scope="col" class="px-4 py-3 min-w-[130px]">Forma Pgto.</th>
                <th scope="col" class="px-4 py-3 w-[140px]">Data Pgto.</th>
                <th scope="col" class="px-4 py-3 w-[50px] text-center"></th>
            </tr>
        </thead>
        <tbody id="container-financeiros">
            @foreach ($financeiros as $index => $item)
                @php
                    $colorClass = $item->tipo === 'receita' ? 'text-green-600' : 'text-red-600';
                    $categoriasAtuais = $item->tipo === 'receita' ? $categoriasReceita : $categoriasDespesa;
                @endphp
                <tr class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:hover:bg-gray-800"
                    id="financeiro-row-{{ $index }}">
                    <input type="hidden" name="financeiros[{{ $index }}][id]" value="{{ $item->id }}">

                    <!-- Tipo -->
                    <td class="px-1 py-1">
                        <select name="financeiros[{{ $index }}][tipo]"
                            class="w-full rounded-md border border-gray-300 bg-white px-2 py-2 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                            onchange="atualizarCategorias(this, {{ $index }})" required>
                            <option value="receita" {{ $item->tipo === 'receita' ? 'selected' : '' }}>Receita</option>
                            <option value="despesa" {{ $item->tipo === 'despesa' ? 'selected' : '' }}>Despesa</option>
                        </select>
                    </td>

                    <!-- Categoria -->
                    <td class="px-1 py-1">
                        <select name="financeiros[{{ $index }}][category]" id="category-{{ $index }}"
                            class="w-full rounded-md border border-gray-300 bg-white px-2 py-2 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">- Selecione -</option>
                            @foreach ($categoriasAtuais as $key => $label)
                                <option value="{{ $key }}" {{ $item->category === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </td>

                    <!-- Nome -->
                    <td class="px-1 py-1">
                        <input type="text" name="financeiros[{{ $index }}][nome]" value="{{ $item->nome }}"
                            class="w-full rounded-md border px-2 py-2 text-sm font-normal transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 {{ $colorClass }}"
                            required>
                    </td>

                    <!-- Valor -->
                    <td class="px-1 py-1">
                        <input type="number" step="0.01" name="financeiros[{{ $index }}][valor]" value="{{ $item->valor }}"
                            class="w-full rounded-md border px-2 py-2 text-sm font-normal transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                            required>
                    </td>

                    <!-- Vencimento -->
                    <td class="px-1 py-1">
                        <input type="date" name="financeiros[{{ $index }}][data_vencimento]"
                            value="{{ $item->data_vencimento ? $item->data_vencimento->format('Y-m-d') : '' }}"
                            class="w-full rounded-md border px-2 py-2 text-sm font-normal transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                            required>
                    </td>

                    <!-- Data Competência/Emissão -->
                    <td class="px-1 py-1">
                        <input type="date" name="financeiros[{{ $index }}][issued_at]"
                            value="{{ $item->issued_at ? $item->issued_at->format('Y-m-d') : '' }}"
                            class="w-full rounded-md border px-2 py-2 text-sm font-normal transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                    </td>

                    <!-- Status -->
                    <td class="px-1 py-1">
                        <select name="financeiros[{{ $index }}][status]"
                            class="w-full rounded-md border border-gray-300 bg-white px-2 py-2 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                            onchange="togglePaymentDate(this, {{ $index }})" required>
                            <option value="pendente" {{ $item->status === 'pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="pago" {{ $item->status === 'pago' ? 'selected' : '' }}>Pago</option>
                            <option value="cancelado" {{ $item->status === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </td>

                    <!-- Forma Pagamento -->
                    <td class="px-1 py-1">
                        <select name="financeiros[{{ $index }}][payment_method]"
                            class="w-full rounded-md border border-gray-300 bg-white px-2 py-2 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">- Selecione -</option>
                            @foreach ($formasPagamento as $key => $label)
                                <option value="{{ $key }}" {{ $item->payment_method === $key ? 'selected' : '' }}>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    <!-- Data Pagamento -->
                    <td class="px-1 py-1">
                        <input type="date" name="financeiros[{{ $index }}][payment_date]" id="payment-date-{{ $index }}"
                            value="{{ $item->payment_date ? $item->payment_date->format('Y-m-d') : '' }}"
                            class="w-full rounded-md border px-2 py-2 text-sm font-normal transition-all hover:border-gray-400 focus:border-blue-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 {{ $item->status !== 'pago' ? 'bg-gray-100' : '' }}">
                    </td>

                    <!-- Actions -->
                    <td class="px-1 py-1 text-center">
                        <button type="button" class="text-red-600 hover:text-red-900 cursor-pointer"
                            onclick="removerFinanceiro('financeiro-row-{{ $index }}')">
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
        const categoriasReceita = {!! json_encode($categoriasReceita) !!};
        const categoriasDespesa = {!! json_encode($categoriasDespesa) !!};
        const formasPagamento = {!! json_encode($formasPagamento) !!};

        function atualizarCategorias(selectTipo, index) {
            const tipo = selectTipo.value;
            const categorias = tipo === 'receita' ? categoriasReceita : categoriasDespesa;
            const selectCategoria = document.getElementById(`category-${index}`);

            if (selectCategoria) {
                selectCategoria.innerHTML = '<option value="">- Selecione -</option>';
                for (const [key, label] of Object.entries(categorias)) {
                    selectCategoria.innerHTML += `<option value="${key}">${label}</option>`;
                }
            }
        }

        function togglePaymentDate(selectStatus, index) {
            const paymentDateInput = document.getElementById(`payment-date-${index}`);
            if (!paymentDateInput) return;

            if (selectStatus.value === 'pago') {
                paymentDateInput.classList.remove('bg-gray-100');
                // Auto-fill with today if empty
                if (!paymentDateInput.value) {
                    paymentDateInput.value = new Date().toISOString().split('T')[0];
                }
            } else {
                paymentDateInput.classList.add('bg-gray-100');
            }
        }

        function adicionarFinanceiro() {
            const index = Date.now();
            const container = document.getElementById('container-financeiros');
            const rowId = `financeiro-row-${index}`;

            const row = document.createElement('tr');
            row.id = rowId;
            row.className = 'border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:hover:bg-gray-800';

            // Build category options for receita (default)
            let categoryOptions = '<option value="">- Selecione -</option>';
            for (const [key, label] of Object.entries(categoriasReceita)) {
                categoryOptions += `<option value="${key}">${label}</option>`;
            }

            // Build payment method options
            let paymentMethodOptions = '<option value="">- Selecione -</option>';
            for (const [key, label] of Object.entries(formasPagamento)) {
                paymentMethodOptions += `<option value="${key}">${label}</option>`;
            }

            row.innerHTML = `
                        <input type="hidden" name="financeiros[${index}][id]" value="">

                        <!-- Campos principais em linha -->
                        <td class="px-1 py-1" colspan="10">
                            <div class="flex flex-col gap-2 p-2 bg-blue-50 rounded border border-blue-200 dark:bg-gray-800 dark:border-gray-700">
                                <!-- Linha 1: Campos Básicos -->
                                <div class="flex gap-2 w-full flex-wrap">
                                    <!-- Tipo -->
                                    <div class="w-[120px]">
                                        <label class="text-xs text-gray-500 mb-1 block">Tipo *</label>
                                        <select name="financeiros[${index}][tipo]" 
                                            class="w-full rounded-md border border-gray-300 bg-white px-2 py-2 text-sm" 
                                            onchange="atualizarCategorias(this, ${index})"
                                            required>
                                            <option value="receita">Receita</option>
                                            <option value="despesa">Despesa</option>
                                        </select>
                                    </div>

                                    <!-- Categoria -->
                                    <div class="w-[140px]">
                                        <label class="text-xs text-gray-500 mb-1 block">Categoria</label>
                                        <select name="financeiros[${index}][category]" id="category-${index}"
                                            class="w-full rounded-md border border-gray-300 bg-white px-2 py-2 text-sm">
                                            ${categoryOptions}
                                        </select>
                                    </div>

                                    <!-- Nome -->
                                    <div class="flex-grow min-w-[200px]">
                                        <label class="text-xs text-gray-500 mb-1 block">Descrição *</label>
                                        <input type="text" name="financeiros[${index}][nome]" 
                                            class="w-full rounded-md border border-gray-300 bg-white px-2 py-2 text-sm" 
                                            placeholder="Descrição do lançamento" required>
                                    </div>

                                    <!-- Valor -->
                                    <div class="w-[120px]">
                                        <label class="text-xs text-gray-500 mb-1 block">Valor *</label>
                                        <input type="number" step="0.01" name="financeiros[${index}][valor]" 
                                            class="w-full rounded-md border border-gray-300 bg-white px-2 py-2 text-sm" 
                                            placeholder="0,00" required>
                                    </div>

                                    <!-- Vencimento -->
                                    <div class="w-[140px]">
                                        <label class="text-xs text-gray-500 mb-1 block">Vencimento *</label>
                                        <input type="date" name="financeiros[${index}][data_vencimento]" 
                                            class="w-full rounded-md border border-gray-300 bg-white px-2 py-2 text-sm" required>
                                    </div>

                                    <!-- Competência -->
                                    <div class="w-[140px]">
                                        <label class="text-xs text-gray-500 mb-1 block">Competência</label>
                                        <input type="date" name="financeiros[${index}][issued_at]" 
                                            class="w-full rounded-md border border-gray-300 bg-white px-2 py-2 text-sm">
                                    </div>
                                </div>

                                <!-- Linha 2: Status e Pagamento -->
                                <div class="flex gap-2 w-full flex-wrap">
                                    <!-- Status -->
                                    <div class="w-[120px]">
                                        <label class="text-xs text-gray-500 mb-1 block">Status *</label>
                                        <select name="financeiros[${index}][status]" 
                                            class="w-full rounded-md border border-gray-300 bg-white px-2 py-2 text-sm"
                                            onchange="togglePaymentDate(this, ${index})"
                                            required>
                                            <option value="pendente">Pendente</option>
                                            <option value="pago">Pago</option>
                                            <option value="cancelado">Cancelado</option>
                                        </select>
                                    </div>

                                    <!-- Forma Pagamento -->
                                    <div class="w-[130px]">
                                        <label class="text-xs text-gray-500 mb-1 block">Forma Pgto.</label>
                                        <select name="financeiros[${index}][payment_method]" 
                                            class="w-full rounded-md border border-gray-300 bg-white px-2 py-2 text-sm">
                                            ${paymentMethodOptions}
                                        </select>
                                    </div>

                                    <!-- Data Pagamento -->
                                    <div class="w-[140px]">
                                        <label class="text-xs text-gray-500 mb-1 block">Data Pgto.</label>
                                        <input type="date" name="financeiros[${index}][payment_date]" 
                                            id="payment-date-${index}"
                                            class="w-full rounded-md border border-gray-300 bg-white px-2 py-2 text-sm bg-gray-100">
                                    </div>

                                    <!-- Área de Parcelamento (Toggle) -->
                                    <div x-data="{ parcelar: false }" class="flex-grow bg-gray-50 p-2 rounded border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" x-model="parcelar" name="financeiros[${index}][parcelar]" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Parcelar?</span>
                                        </label>

                                        <div x-show="parcelar" class="flex gap-4 mt-2">
                                            <div class="flex flex-col gap-1">
                                                <label class="text-xs text-gray-600 dark:text-gray-400">Qtd. Parcelas</label>
                                                <input type="number" min="2" max="60" name="financeiros[${index}][parcelas_qtd]" value="2" class="w-20 rounded-md border border-gray-300 px-2 py-1 text-sm focus:border-blue-500 dark:border-gray-700 dark:bg-gray-900">
                                            </div>
                                            <div class="flex flex-col gap-1">
                                                <label class="text-xs text-gray-600 dark:text-gray-400">Frequência</label>
                                                <select name="financeiros[${index}][parcelas_frequencia]" class="rounded-md border border-gray-300 px-2 py-1 text-sm focus:border-blue-500 dark:border-gray-700 dark:bg-gray-900">
                                                    <option value="30">Mensal</option>
                                                    <option value="15">Quinzenal</option>
                                                    <option value="7">Semanal</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete -->
                                    <div class="flex items-end">
                                        <button type="button" class="text-red-600 hover:text-red-900 cursor-pointer p-2" onclick="removerFinanceiro('${rowId}')">
                                            <span class="icon-delete text-xl"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    `;

            container.appendChild(row);
        }

        function removerFinanceiro(rowId) {
            const row = document.getElementById(rowId);
            if (row) {
                row.remove();
            }
        }
    </script>
@endpush