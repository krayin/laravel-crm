{{--
MÓDULO JURÍDICO: Dados Empresariais Avançados (PJ)
Este blade é injetado via evento nas telas de Create e Edit de Organizações.
--}}
@php
    $orgId = isset($organization) ? $organization->id : request()->route('id');
    $isEdit = !empty($orgId);

    $lawDetails = $isEdit
        ? \SuiteZap\LawFirm\Models\LawOrganizationDetail::where('organization_id', $orgId)->first()
        : null;

    $ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
@endphp

<div class="mt-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">

    <!-- Cabeçalho da Seção -->
    <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3 dark:border-gray-800">
        <div class="flex items-center gap-2">
            <span class="icon-settings text-xl text-purple-600"></span>
            <p class="text-lg font-bold text-gray-800 dark:text-white">Dados Empresariais Avançados</p>
        </div>
        <span class="text-xs text-gray-400">Módulo Jurídico (PJ)</span>
    </div>

    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- CNPJ -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">CNPJ</label>
                <input type="text" name="law_org_details[cnpj]"
                    value="{{ old('law_org_details.cnpj', $lawDetails->cnpj ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 @error('law_org_details.cnpj') border-red-500 @enderror"
                    placeholder="00.000.000/0000-00" maxlength="18" oninput="maskCNPJ(this)">
                @error('law_org_details.cnpj')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Razão Social -->
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Razão Social</label>
                <input type="text" name="law_org_details[razao_social]"
                    value="{{ old('law_org_details.razao_social', $lawDetails->razao_social ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Inscrição Estadual -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Inscrição
                    Estadual</label>
                <input type="text" name="law_org_details[inscricao_estadual]"
                    value="{{ old('law_org_details.inscricao_estadual', $lawDetails->inscricao_estadual ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>

            <!-- Inscrição Municipal -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Inscrição
                    Municipal</label>
                <input type="text" name="law_org_details[inscricao_municipal]"
                    value="{{ old('law_org_details.inscricao_municipal', $lawDetails->inscricao_municipal ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>

            <!-- CNAE -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">CNAE</label>
                <input type="text" name="law_org_details[cnae]"
                    value="{{ old('law_org_details.cnae', $lawDetails->cnae ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>
        </div>

        <!-- Representante Legal -->
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Representante Legal</label>
            <input type="text" name="law_org_details[representante_legal]"
                value="{{ old('law_org_details.representante_legal', $lawDetails->representante_legal ?? '') }}"
                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
        </div>
    </div>

    <!-- ==================== ENDEREÇO (PJ) ==================== -->
    <div class="mt-6 space-y-4 pt-4 border-t border-gray-100 dark:border-gray-800">
        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Endereço Empresarial Completo</p>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- CEP -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">CEP</label>
                <input type="text" name="law_org_details[cep]"
                    value="{{ old('law_org_details.cep', $lawDetails->cep ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                    placeholder="00000-000">
            </div>

            <!-- Logradouro -->
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Logradouro</label>
                <input type="text" name="law_org_details[logradouro]"
                    value="{{ old('law_org_details.logradouro', $lawDetails->logradouro ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>

            <!-- Número -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Número</label>
                <input type="text" name="law_org_details[numero]"
                    value="{{ old('law_org_details.numero', $lawDetails->numero ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Complemento -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Complemento</label>
                <input type="text" name="law_org_details[complemento]"
                    value="{{ old('law_org_details.complemento', $lawDetails->complemento ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>

            <!-- Bairro -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Bairro</label>
                <input type="text" name="law_org_details[bairro]"
                    value="{{ old('law_org_details.bairro', $lawDetails->bairro ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>

            <!-- Cidade -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Cidade</label>
                <input type="text" name="law_org_details[cidade]"
                    value="{{ old('law_org_details.cidade', $lawDetails->cidade ?? '') }}"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
            </div>

            <!-- UF -->
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">UF</label>
                <select name="law_org_details[uf]"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <option value="">-</option>
                    @foreach($ufs as $uf)
                        <option value="{{ $uf }}" {{ old('law_org_details.uf', $lawDetails->uf ?? '') === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<script>
    function maskCNPJ(i) {
        var v = i.value;
        if (isNaN(v[v.length - 1])) { // impede entrar outro caractere que não seja número
            i.value = v.substring(0, v.length - 1);
            return;
        }

        i.setAttribute("maxlength", "18");
        v = v.replace(/\D/g, "") //Remove tudo o que não é dígito
        v = v.replace(/^(\d{2})(\d)/, "$1.$2") //Coloca ponto entre o segundo e o terceiro dígitos
        v = v.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3") //Coloca ponto entre o quinto e o sexto dígitos
        v = v.replace(/\.(\d{3})(\d)/, ".$1/$2") //Coloca uma barra entre o oitavo e o nono dígitos
        v = v.replace(/(\d{4})(\d)/, "$1-$2") //Coloca um hífen depois do bloco de quatro dígitos
        i.value = v;
    }

</script>