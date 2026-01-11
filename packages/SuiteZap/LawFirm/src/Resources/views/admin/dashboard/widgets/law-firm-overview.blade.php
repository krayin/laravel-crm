{!! view_render_event('admin.dashboard.index.law_firm_widget.before') !!}

<!-- LawFirm Widget - Card Layout -->
<div
    class="flex flex-col gap-4 rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm">

    <!-- Header: Título e Link Novo Caso -->
    <div class="flex items-center justify-between border-b border-gray-200 p-4 dark:border-gray-800">
        <div class="flex items-center gap-2">
            <p class="text-gray-600 font-semibold text-lg dark:text-gray-300">
                {{ __('lawfirm::app.dashboard.title') }}
            </p>
        </div>
        <a href="{{ route('admin.processos.create') }}" class="text-blue-600 text-sm font-medium hover:underline">
            + {{ __('lawfirm::app.dashboard.new-case') }}
        </a>
    </div>

    <!-- Corpo do Card -->
    <div class="flex flex-col gap-4 p-4 pt-2">

        {{-- SEÇÃO 1: Casos Ativos --}}
        <div class="flex items-center gap-4">
            <div
                class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 dark:bg-gray-800">
                <span class="icon-briefcase text-2xl"></span>
            </div>
            <div class="flex flex-col">
                <p class="text-3xl font-bold text-gray-800 dark:text-white leading-none">{{ $activeCount ?? 0 }}</p>
                <a href="{{ route('admin.processos.index') }}" class="text-blue-600 text-sm hover:underline mt-1">
                    {{ __('lawfirm::app.dashboard.active-processes') }}
                </a>
            </div>
        </div>

        {{-- SEÇÃO 2: Financeiro (GRID 2 COLUNAS) --}}
        <div class="grid grid-cols-2 gap-4">
            <!-- Portfólio Ativo -->
            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                <p class="text-xs text-gray-500 uppercase font-semibold dark:text-gray-400">Portfólio Ativo</p>
                <p class="text-lg font-bold text-gray-800 mt-1 dark:text-white">
                    R$ {{ number_format($totalValorCausa ?? 0, 2, ',', '.') }}
                </p>
            </div>

            <!-- Volume Encerrado -->
            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                <p class="text-xs text-gray-500 uppercase font-semibold dark:text-gray-400">Volume Encerrado</p>
                <p class="text-lg font-bold text-gray-500 mt-1 dark:text-green-400">
                    R$ {{ number_format($totalValorGanho ?? 0, 2, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- SEÇÃO 3: Audiências --}}
        <div class="border-t border-gray-200 pt-4 mt-2 dark:border-gray-700">
            <p class="text-gray-700 font-semibold mb-3 dark:text-gray-300 text-sm">
                {{ __('lawfirm::app.dashboard.upcoming-hearings') }}
            </p>

            @if(isset($upcomingHearings) && $upcomingHearings->count() > 0)
                <div class="flex flex-col gap-3">
                    @foreach($upcomingHearings as $hearing)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col items-center min-w-[50px] leading-tight">
                                    <span class="font-bold text-gray-800 dark:text-white text-base">
                                        {{ \Carbon\Carbon::parse($hearing->data_audiencia)->format('d/m') }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($hearing->data_audiencia)->format('H:i') }}
                                    </span>
                                </div>
                                <div class="flex flex-col">
                                    <a href="{{ route('admin.processos.edit', $hearing->id) }}"
                                        class="font-medium text-gray-700 hover:text-blue-600 truncate max-w-[180px] dark:text-gray-300">
                                        {{ $hearing->titulo }}
                                    </a>
                                    <span class="text-xs text-gray-500 truncate max-w-[180px]">
                                        {{ $hearing->vara ?? 'Fórum não informado' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-4 text-gray-400 opacity-70">
                    <span class="icon-calendar text-2xl mb-1"></span>
                    <p class="text-xs">{{ __('lawfirm::app.dashboard.no-hearings') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

{!! view_render_event('admin.dashboard.index.law_firm_widget.after') !!}