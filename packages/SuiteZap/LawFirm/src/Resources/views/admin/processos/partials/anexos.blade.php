<!-- MÓDULO DE DOCUMENTOS (Padronizado) -->
@if(isset($processo) && $processo->exists)
    <div
        class="mt-4 flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">

        <!-- CABEÇALHO DA SEÇÃO -->
        <div class="flex items-center justify-between">
            <p class="text-lg font-bold text-gray-800 dark:text-white">
                Documentos e Anexos
            </p>
            <span class="text-xs text-gray-500">
                {{ $processo->anexos->count() }} arquivo(s)
            </span>
            @if(!request()->routeIs('*.view') && !request()->routeIs('*.show'))
                <button type="submit"
                    class="ml-auto flex items-center justify-center gap-2 rounded border border-gray-400 bg-white px-3 py-1.5 text-gray-600 hover:bg-gray-100 transition-colors"
                    title="Salvar Anexos">
                    <span class="icon-save text-lg"></span>
                    <span class="text-sm font-medium">Salvar</span>
                </button>
            @endif
        </div>

        <!-- TABELA DE ARQUIVOS -->
        <div class="overflow-x-auto rounded border border-gray-100">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="px-4 py-2 text-center font-medium">Tipo</th>
                        <th class="px-4 py-2 text-left font-medium w-full">Nome do Arquivo</th>
                        <th class="px-4 py-2 text-right font-medium whitespace-nowrap">Tamanho</th>
                        <th class="px-4 py-2 text-center font-medium">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($processo->anexos as $anexo)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <!-- Ícone do Tipo + Extensão -->
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="{{ $anexo->icon }} text-2xl text-gray-500"></span>
                                    <span class="text-[10px] font-bold text-gray-400 mt-1">{{ $anexo->extension }}</span>
                                </div>
                            </td>

                            <!-- Nome com Link -->
                            <td class="px-4 py-3">
                                <a href="{{ $anexo->url }}" target="_blank"
                                    class="font-medium text-blue-600 hover:underline hover:text-blue-800 flex items-center gap-2">
                                    {{ $anexo->nome_original }}
                                </a>
                            </td>

                            <!-- Tamanho -->
                            <td class="px-4 py-3 text-right text-gray-500 whitespace-nowrap">
                                {{ round($anexo->tamanho / 1024, 2) }} KB
                            </td>

                            <!-- Ações (Download / Excluir) -->
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-4">
                                    <!-- Download -->
                                    <a href="{{ $anexo->url }}" target="_blank" title="Baixar"
                                        class="text-gray-500 hover:text-blue-600 cursor-pointer">
                                        <span class="icon-download text-xl"></span>
                                    </a>

                                    <!-- Excluir (Apenas se NÃO for modo leitura) -->
                                    @if(!request()->routeIs('*.view') && !request()->routeIs('*.show'))
                                        <form action="{{ route('admin.processos.delete_attachment', $anexo->id) }}" method="POST"
                                            id="delete-anexo-{{ $anexo->id }}" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <a href="javascript:void(0);"
                                            onclick="if(confirm('Tem certeza que deseja remover este arquivo?')) { document.getElementById('delete-anexo-{{ $anexo->id }}').submit(); }"
                                            class="text-red-500 hover:text-red-700 cursor-pointer" title="Excluir">
                                            <span class="icon-delete text-xl display-block"></span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-400 italic">
                                Nenhum documento anexado a este processo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ÁREA DE UPLOAD DRAG & DROP -->
        @if(!request()->routeIs('*.view') && !request()->routeIs('*.show'))
            <div class="mt-4">
                <label class="mb-2 block text-sm font-medium text-gray-700">Adicionar Novos Arquivos</label>

                <div class="relative flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:bg-gray-50 transition-colors dropzone-container"
                    id="dropzone-edit">

                    <div class="text-center pointer-events-none">
                        <span class="icon-file text-4xl text-gray-400 mb-2 block"></span>
                        <p class="mt-2 text-sm text-gray-600">
                            <span class="font-bold text-blue-600 hover:text-blue-500">Clique para upload</span> ou arraste e
                            solte
                        </p>
                        <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG, DOCX (Max: 20MB)</p>
                    </div>

                    <input type="file" name="anexos[]" multiple
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer file-upload-input"
                        data-list-target="file-list-edit" onchange="handleFileSelect(this)" />
                </div>

                <!-- Lista de arquivos selecionados -->
                <ul id="file-list-edit" class="mt-3 space-y-2"></ul>
            </div>
        @endif

    </div>
@else
    <!-- Modo Criação: Apenas área de upload -->
    @if(!request()->routeIs('*.view') && !request()->routeIs('*.show'))
        <div
            class="mt-4 flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <p class="text-lg font-bold text-gray-800 dark:text-white">
                    Documentos e Anexos
                </p>
            </div>
            <div class="rounded bg-white p-3">
                <label class="mb-2 block text-sm font-medium text-gray-700">Adicionar Arquivos</label>

                <div class="relative flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg hover:bg-gray-50 transition-colors dropzone-container"
                    id="dropzone-create">

                    <div class="text-center pointer-events-none">
                        <span class="icon-file text-4xl text-gray-400 mb-2 block"></span>
                        <p class="mt-2 text-sm text-gray-600">
                            <span class="font-bold text-blue-600 hover:text-blue-500">Clique para upload</span> ou arraste e
                            solte
                        </p>
                        <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG, DOCX (Max: 20MB)</p>
                    </div>

                    <input type="file" name="anexos[]" multiple
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer file-upload-input"
                        data-list-target="file-list-create" onchange="handleFileSelect(this)" />
                </div>

                <!-- Lista de arquivos selecionados -->
                <ul id="file-list-create" class="mt-3 space-y-2"></ul>
            </div>
        </div>
    @endif
@endif

<script>
    function handleFileSelect(input) {
        const fileListId = input.getAttribute('data-list-target');
        const fileList = document.getElementById(fileListId);
        const files = input.files;

        fileList.innerHTML = ''; // Limpar lista atual

        if (files.length === 0) {
            return;
        }

        Array.from(files).forEach(file => {
            const li = document.createElement('li');
            li.className = "flex items-center gap-2 text-sm text-gray-600 bg-gray-50 p-2 rounded border border-gray-100";

            // Icone simples baseado na extensão (simplificado)
            let iconClass = 'icon-file';
            if (file.type.includes('image')) iconClass = 'icon-image';
            if (file.type.includes('pdf')) iconClass = 'icon-file';

            li.innerHTML = `
                <span class="${iconClass} text-gray-400"></span>
                <span class="font-medium truncate flex-1">${file.name}</span>
                <span class="text-xs text-gray-400 whitespace-nowrap">${formatBytes(file.size)}</span>
            `;
            fileList.appendChild(li);
        });
    }

    function formatBytes(bytes, decimals = 2) {
        if (!+bytes) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const dropzones = document.querySelectorAll('.dropzone-container');

        dropzones.forEach(dropzone => {
            const input = dropzone.querySelector('input[type="file"]');

            // 1. Ensure Form has enctype
            if (input) {
                const form = input.closest('form');
                if (form && form.getAttribute('enctype') !== 'multipart/form-data') {
                    form.setAttribute('enctype', 'multipart/form-data');
                }
            }

            const highlight = () => {
                dropzone.classList.remove('border-gray-300');
                dropzone.classList.add('border-blue-500', 'bg-blue-50');
            };

            const unhighlight = () => {
                dropzone.classList.remove('border-blue-500', 'bg-blue-50');
                dropzone.classList.add('border-gray-300');
            };

            // Remove default behavior
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            // Visual Feedback
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, unhighlight, false);
            });

            // 2. CRITICAL: Handle Drop to assign files to Input
            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files && files.length > 0 && input) {
                    // Modern Header: Assign files to input
                    input.files = files;

                    // Trigger change event manually to update the list
                    // (The onchange="handleFileSelect(this)" in HTML will catch this)
                    const event = new Event('change', { bubbles: true });
                    input.dispatchEvent(event);
                }
            }, false);
        });
    });
</script>