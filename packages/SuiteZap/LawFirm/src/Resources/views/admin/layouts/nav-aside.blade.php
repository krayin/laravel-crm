<div class="aside-nav">
    <ul>
        <li class="active">
            <a href="{{ route('admin.processos.index') }}">
                <span class="icon icon-note-text"></span>
                <span class="menu-label">Processos</span>
            </a>
        </li>
        {{-- Link de volta para o Dashboard do CRM --}}
        <li>
            <a href="{{ route('admin.dashboard.index') }}">
                <span class="icon icon-dashboard"></span>
                <span class="menu-label">Voltar ao CRM</span>
            </a>
        </li>
    </ul>
</div>