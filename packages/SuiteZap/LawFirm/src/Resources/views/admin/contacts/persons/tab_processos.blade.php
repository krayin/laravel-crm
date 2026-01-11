@push('scripts')
    <script type="text/javascript">
        window.addEventListener('load', function () {
            setTimeout(function () {
                // Tenta injetar na lista de abas se existir (Persons View geralmente tem)
                var tabList = document.querySelector('.tabs ul');
                var tabContentContainer = document.querySelector('.tabs-content');

                // Se não achar .tabs ul (ex: Person View pode usar componentes diferentes), 
                // o conteúdo ficará onde foi injetado (via ServiceProvider), o que é aceitável.
                if (tabList && tabContentContainer) {
                    var newTab = document.getElementById('processos-person-tab-li');
                    var newContent = document.getElementById('processos-person-tab-content');

                    if (newTab) tabList.appendChild(newTab);
                    if (newContent) tabContentContainer.appendChild(newContent);
                }
            }, 500);
        });
    </script>
@endpush

<!-- Estrutura da Tab (Link) -->
<li id="processos-person-tab-li" class="tab-item">
    <a href="#processos-person">
        {{ trans('lawfirm::app.processos.title') }}
    </a>
</li>

<!-- Estrutura do Conteúdo -->
<div id="processos-person-tab-content" class="tab-pane">
    <x-admin::datagrid :src="route('admin.contacts.persons.processos', $person->id)" />
</div>