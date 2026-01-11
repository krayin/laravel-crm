@push('scripts')
    <script type="text/javascript">
        // Script simples para tentar mover a aba para dentro da lista de abas padrão
        // Caso a estrutura seja de abas HTML simples
        window.addEventListener('load', function () {
            setTimeout(function () {
                var tabList = document.querySelector('.tabs ul'); // Seletor genérico corre para Krayin
                var tabContentContainer = document.querySelector('.tabs-content');

                if (tabList && tabContentContainer) {
                    var newTab = document.getElementById('processos-tab-li');
                    var newContent = document.getElementById('processos-tab-content');

                    if (newTab) tabList.appendChild(newTab);
                    if (newContent) tabContentContainer.appendChild(newContent);
                }
            }, 500);
        });
    </script>
@endpush

<!-- Estrutura da Tab (Link) -->
<li id="processos-tab-li" class="tab-item">
    <a href="#processos">
        {{ trans('lawfirm::app.processos.title') }}
    </a>
</li>

<!-- Estrutura do Conteúdo -->
<div id="processos-tab-content" class="tab-pane">
    <x-admin::datagrid :src="route('admin.leads.processos', $lead->id)" />
</div>