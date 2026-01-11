{{--
MÓDULO JURÍDICO: Dados Pessoais Avançados (PF)
Este blade é injetado via evento nas telas de Create e Edit de Pessoas.
--}}
@php
    $personId = isset($person) ? $person->id : request()->route('id');
    $isEdit = !empty($personId);

    $lawDetails = $isEdit
        ? \SuiteZap\LawFirm\Models\LawPersonDetail::where('person_id', $personId)->first()
        : null;

    $estadosCivis = ['Solteiro(a)', 'Casado(a)', 'Divorciado(a)', 'Viuvo(a)', 'União Estável', 'Separado(a)'];
    $ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
@endphp

<!-- MÓDULO JURÍDICO: Dados Pessoais Avançados -->
<div class="mt-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">

    <!-- Cabeçalho da Seção -->
    <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3 dark:border-gray-800">
        <div class="flex items-center gap-2">
            <span class="icon-user text-xl text-blue-600"></span>
            <p class="text-lg font-bold text-gray-800 dark:text-white">Dados Pessoais Avançados</p>
        </div>
        <span class="text-xs text-gray-400">Módulo Jurídico (PF)</span>
    </div>

    <!-- Campos Pessoa Física (PF) -->
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- CPF -->
            <!-- CPF -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">CPF</label>
                <input type="text" name="law_details[cpf]" value="{{ old('law_details.cpf', $lawDetails->cpf ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 @error('law_details.cpf') border-red-500 @enderror"
                    placeholder="000.000.000-00" maxlength="14" oninput="maskCPF(this)">
                @error('law_details.cpf')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <!-- RG -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">RG</label>
                <input type="text" name="law_details[rg]" value="{{ old('law_details.rg', $lawDetails->rg ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>
            <!-- RG Órgão / UF -->
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Órgão</label>
                    <input type="text" name="law_details[rg_orgao]"
                        value="{{ old('law_details.rg_orgao', $lawDetails->rg_orgao ?? '') }}"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                        placeholder="SSP">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">UF</label>
                    <select name="law_details[rg_uf]"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">-</option>
                        @foreach($ufs as $uf)
                            <option value="{{ $uf }}" {{ old('law_details.rg_uf', $lawDetails->rg_uf ?? '') === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Data Nascimento -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Data de
                    Nascimento</label>
                <input type="date" name="law_details[data_nascimento]"
                    value="{{ old('law_details.data_nascimento', ($lawDetails->data_nascimento ?? null)?->format('Y-m-d') ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>
            <!-- Estado Civil -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Estado Civil</label>
                <select name="law_details[estado_civil]"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="">Selecione...</option>
                    @foreach($estadosCivis as $ec)
                        <option value="{{ $ec }}" {{ old('law_details.estado_civil', $lawDetails->estado_civil ?? '') === $ec ? 'selected' : '' }}>{{ $ec }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Profissão -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Profissão</label>
                <input type="text" name="law_details[profissao]"
                    value="{{ old('law_details.profissao', $lawDetails->profissao ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Nome da Mãe -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nome da Mãe</label>
                <input type="text" name="law_details[nome_mae]"
                    value="{{ old('law_details.nome_mae', $lawDetails->nome_mae ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>
            <!-- Nome do Pai -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nome do Pai</label>
                <input type="text" name="law_details[nome_pai]"
                    value="{{ old('law_details.nome_pai', $lawDetails->nome_pai ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>
        </div>

        <!-- Nacionalidade -->
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nacionalidade</label>
            <input type="text" name="law_details[nacionalidade]"
                value="{{ old('law_details.nacionalidade', $lawDetails->nacionalidade ?? 'Brasileiro(a)') }}"
                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
        </div>
    </div>

    <!-- Campos de Endereço (Sempre visíveis) -->
    <div class="mt-6 space-y-4 pt-4 border-t border-gray-100 dark:border-gray-800">
        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Endereço Completo</p>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- CEP -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">CEP</label>
                <input type="text" name="law_details[cep]" value="{{ old('law_details.cep', $lawDetails->cep ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                    placeholder="00000-000">
            </div>
            <!-- Logradouro -->
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Logradouro</label>
                <input type="text" name="law_details[logradouro]"
                    value="{{ old('law_details.logradouro', $lawDetails->logradouro ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>
            <!-- Número -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Número</label>
                <input type="text" name="law_details[numero]"
                    value="{{ old('law_details.numero', $lawDetails->numero ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Complemento -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Complemento</label>
                <input type="text" name="law_details[complemento]"
                    value="{{ old('law_details.complemento', $lawDetails->complemento ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>
            <!-- Bairro -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Bairro</label>
                <input type="text" name="law_details[bairro]"
                    value="{{ old('law_details.bairro', $lawDetails->bairro ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>
            <!-- Cidade -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Cidade</label>
                <input type="text" name="law_details[cidade]"
                    value="{{ old('law_details.cidade', $lawDetails->cidade ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>
            <!-- UF -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">UF</label>
                <select name="law_details[uf]"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="">-</option>
                    @foreach($ufs as $uf)
                        <option value="{{ $uf }}" {{ old('law_details.uf', $lawDetails->uf ?? '') === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<script>
    function maskCPF(i) {
        var v = i.value;
        if (isNaN(v[v.length - 1])) { // impede entrar outro caractere que não seja número
            i.value = v.substring(0, v.length - 1);
            return;
        }

        i.setAttribute("maxlength", "14");
        v = v.replace(/\D/g, "") //Remove tudo o que não é dígito
        v = v.replace(/(\d{3})(\d)/, "$1.$2") //Coloca um ponto entre o terceiro e o quarto dígitos
        v = v.replace(/(\d{3})(\d)/, "$1.$2") //Coloca um ponto entre o terceiro e o quarto dígitos
        //de novo (para o segundo bloco de números)
        v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2") //Coloca um hífen entre o terceiro e o quarto dígitos
        i.value = v;
    }

</script>