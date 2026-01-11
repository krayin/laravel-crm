@push('styles')
    <style id="lawfirm-datagrid-styles">
        /* 
                 * FIX DEFINITIVO PARA O GRID DO KRAYIN V2 
                 * Atualizado para 10 colunas (Com Vara e Juiz)
                 */
        .table-responsive .row.grid,
        .row.grid {
            /* 
                       1. Checkbox: 40px
                       2. ID: 50px
                       3. Título: 2fr
                       4. CNJ: 150px
                       5. Vara: 1fr
                       6. Juiz: 1fr
                       7. Nome: 1fr
                       8. Data: 150px
                       9. Status: 100px
                       10. Ações: 100px
                    */
            grid-template-columns: 40px 50px 2fr 150px 1fr 1fr 1fr 150px 100px 100px !important;
            column-gap: 10px !important;
        }

        /* Ajustes de Alinhamento e Overflow */
        .row.grid>div {
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        /* 1. Checkbox & 2. ID: Centralizados */
        .row.grid>div:nth-child(1),
        .row.grid>div:nth-child(2) {
            justify-content: center !important;
            text-align: center !important;
        }

        /* 4. CNJ: Sem quebra de linha */
        .row.grid>div:nth-child(4) {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            display: block !important;
            line-height: normal;
            padding-top: 10px;
        }

        /* 8. Data & 9. Status: Centralizados */
        .row.grid>div:nth-child(8),
        .row.grid>div:nth-child(9) {
            justify-content: center !important;
            text-align: center !important;
        }

        /* 10. Ações: Flexrow e Sem quebra */
        .row.grid>div:nth-child(10) {
            justify-content: center !important;
            white-space: nowrap !important;
            display: flex !important;
            gap: 5px;
            /* Espaço entre ícones */
        }

        .row.grid>div:nth-child(10) a {
            display: inline-block;
        }

        /* Ajuste para mobile */
        @media (max-width: 1400px) {

            .table-responsive .row.grid,
            .row.grid {
                /* Em telas menores, permite scroll horizontal ou ajusta */
                min-width: 1200px;
            }
        }
    </style>
@endpush

<x-admin::layouts>
    <x-slot:title>
        @lang('lawfirm::app.processos.title')
        </x-slot>

        <div class="flex flex-col gap-4">
            <!-- Header -->
            <div
                class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <div class="flex cursor-pointer items-center">
                        <x-admin::breadcrumbs name="lawfirm.processos.index" />
                    </div>
                    <div class="text-xl font-bold dark:text-white">
                        @lang('lawfirm::app.processos.title')
                    </div>
                </div>
                <div class="flex items-center gap-x-2.5">
                    <a href="{{ route('admin.processos.create') }}" class="primary-button">
                        @lang('lawfirm::app.processos.create')
                    </a>
                </div>
            </div>

            <!-- DataGrid -->
            <x-admin::datagrid :src="route('admin.processos.index')" />
        </div>
</x-admin::layouts>