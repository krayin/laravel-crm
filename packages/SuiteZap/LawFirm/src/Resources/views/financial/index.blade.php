<x-admin::layouts>
    <x-slot:title>
        Dashboard Financeiro
        </x-slot>

        <!--
        Main Container -->
        <div class="flex flex-col gap-6" style="gap: 1.5rem; display: flex; flex-direction: column;">

            <!-- Header Section -->
            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm dark:bg-gray-900 dark:border-gray-800">

                <!-- Left: Context -->
                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <span class="icon-dashboard text-2xl text-blue-600"></span>
                        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Dashboard Financeiro</h1>
                    </div>
                    <p class="text-sm text-gray-500 mt-1 pl-8">Controladoria jurídica com métricas avançadas.</p>
                </div>

                <!-- Right: Filters -->
                <div class="flex flex-col md:flex-row gap-3 items-end md:items-center">
                    
                    @php
                        $user = auth()->guard('user')->user();
                        $isGlobal = $user->view_permission === 'global';
                    @endphp

                    @if($isGlobal)
                        <form id="filter-form" action="" method="GET" class="flex gap-2">
                            <!-- User Filter -->
                            <select name="responsible_id" onchange="this.form.submit()" 
                                class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 py-2">
                                <option value="">Todos os Advogados</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ request('responsible_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- Date Filters would go here seamlessly -->
                        </form>
                    @else
                        <!-- Individual View Indicator -->
                        <div class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-xs font-medium border border-gray-200">
                             <span class="icon-user text-gray-400 mr-1"></span> {{ $user->name }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Row 1: Main KPIs (5 Columns) - JS Force Applied -->
            <div id="main-kpi-grid">

                <!-- Receitas -->
                <div
                    class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-center dark:bg-gray-900 dark:border-gray-800">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">TOTAL RECEITAS</span>
                    <div class="mt-2 flex items-baseline gap-1">
                        <span class="text-xl font-extrabold text-green-600">
                            R$ {{ number_format($totalReceitas, 2, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Despesas -->
                <div
                    class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-center dark:bg-gray-900 dark:border-gray-800">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">TOTAL DESPESAS</span>
                    <div class="mt-2 flex items-baseline gap-1">
                        <span class="text-xl font-extrabold text-red-600">
                            R$ {{ number_format($totalDespesas, 2, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Saldo -->
                <div
                    class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-center dark:bg-gray-900 dark:border-gray-800">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">SALDO LÍQUIDO</span>
                    <div class="mt-2 flex items-baseline gap-1">
                        <span
                            class="text-xl font-extrabold {{ $saldoLiquido >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            R$ {{ number_format($saldoLiquido, 2, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Margem -->
                <div
                    class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-center dark:bg-gray-900 dark:border-gray-800">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">MARGEM</span>
                    <div class="mt-2 flex items-baseline gap-1">
                        <span
                            class="text-xl font-extrabold {{ $margemPercent >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            {{ number_format($margemPercent, 1, ',', '.') }}%
                        </span>
                    </div>
                </div>

                <!-- Pendente -->
                <div
                    class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-center dark:bg-gray-900 dark:border-gray-800">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">PENDENTE A RECEBER</span>
                    <div class="mt-2 flex items-baseline gap-1">
                        <span class="text-xl font-extrabold text-gray-700 dark:text-gray-300">
                            R$ {{ number_format($pendenteReceber, 2, ',', '.') }}
                        </span>
                    </div>
                </div>

            </div>

            <!-- Row 2: Secondary Performance KPIs -->
            <div id="performance-grid">

                <!-- Collection Rate - Font size standardized to text-xl -->
                <div
                    class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-center dark:bg-gray-900 dark:border-gray-800">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">TAXA DE
                            RECEBIMENTO</span>
                        <span class="icon-sort-amount-up text-gray-300 text-xs"></span>
                    </div>
                    <div class="flex flex-col">
                        <span
                            class="text-xl font-extrabold {{ $collectionRate >= 80 ? 'text-green-600' : ($collectionRate >= 50 ? 'text-orange-600' : 'text-red-600') }}">
                            {{ number_format($collectionRate, 1, ',', '.') }}%
                        </span>
                    </div>
                </div>

                <!-- DSO - Font size standardized to text-xl -->
                <div
                    class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-center dark:bg-gray-900 dark:border-gray-800">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">PRAZO MÉDIO
                            (DSO)</span>
                        <span class="icon-calendar text-gray-300 text-xs"></span>
                    </div>
                    <div class="flex flex-col">
                        <span
                            class="text-xl font-extrabold {{ $dso <= 30 ? 'text-green-600' : ($dso <= 60 ? 'text-orange-600' : 'text-red-600') }}">
                            {{ number_format($dso, 1, ',', '.') }} dias
                        </span>
                    </div>
                </div>

            </div>

            <!-- Row 3: Aging Report - Font size standardized to text-xl -->
            <div id="aging-grid">

                <!-- 0-30 -->
                <div
                    class="bg-green-50 p-5 rounded-xl border border-green-100 flex flex-row items-center justify-between h-24 dark:bg-green-900 dark:border-green-800">
                    <span
                        class="text-[10px] font-bold text-green-700 uppercase tracking-widest bg-green-100 px-2 py-0.5 rounded-full">0-30
                        DIAS</span>
                    <span class="text-xl font-extrabold text-green-600">
                        R$ {{ number_format($aging['0_30'] ?? 0, 2, ',', '.') }}
                    </span>
                </div>

                <!-- 31-60 -->
                <div
                    class="bg-white p-5 rounded-xl border border-gray-200 flex flex-row items-center justify-between h-24 dark:bg-gray-800 dark:border-gray-700">
                    <span
                        class="text-[10px] font-bold text-gray-600 uppercase tracking-widest bg-gray-100 px-2 py-0.5 rounded-full">31-60
                        DIAS</span>
                    <span class="text-xl font-extrabold text-gray-800 dark:text-white">
                        R$ {{ number_format($aging['31_60'] ?? 0, 2, ',', '.') }}
                    </span>
                </div>

                <!-- 61-90 -->
                <div
                    class="bg-orange-50 p-5 rounded-xl border border-orange-100 flex flex-row items-center justify-between h-24 dark:bg-orange-900 dark:border-orange-800">
                    <span
                        class="text-[10px] font-bold text-orange-600 uppercase tracking-widest bg-orange-100 px-2 py-0.5 rounded-full">61-90
                        DIAS</span>
                    <span class="text-xl font-extrabold text-orange-600">
                        R$ {{ number_format($aging['61_90'] ?? 0, 2, ',', '.') }}
                    </span>
                </div>

                <!-- >90 -->
                <div
                    class="bg-red-50 p-5 rounded-xl border border-red-100 flex flex-row items-center justify-between h-24 dark:bg-red-900 dark:border-red-800">
                    <span
                        class="text-[10px] font-bold text-red-600 uppercase tracking-widest bg-red-100 px-2 py-0.5 rounded-full">>90
                        DIAS</span>
                    <span class="text-xl font-extrabold text-red-600">
                        R$ {{ number_format($aging['over_90'] ?? 0, 2, ',', '.') }}
                    </span>
                </div>

            </div>

            <!-- DataGrid -->
            <div
                class="bg-white rounded-xl border border-gray-200 shadow-sm dark:bg-gray-900 dark:border-gray-800 mt-6">
                <x-admin::datagrid :src="route('admin.lawfirm.financial.index')" />
            </div>

        </div>

        <!-- 
        FORCE SCRIPT: Injecting styles via JS to survive re-renders. 
    -->
        @push('scripts')
            <!-- Ensure Alpine.js is loaded -->
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const style = document.createElement('style');
                    style.innerHTML = `
                                        #main-kpi-grid {
                                            display: grid !important;
                                            gap: 1rem !important;
                                            grid-template-columns: 1fr !important;
                                        }
                                        @media (min-width: 768px) {
                                            #main-kpi-grid { grid-template-columns: repeat(2, 1fr) !important; }
                                        }
                                        @media (min-width: 1200px) {
                                            #main-kpi-grid { grid-template-columns: repeat(5, 1fr) !important; }
                                        }

                                        #performance-grid {
                                            display: grid !important;
                                            gap: 1rem !important;
                                            grid-template-columns: 1fr !important;
                                        }
                                        @media (min-width: 1024px) {
                                            #performance-grid { grid-template-columns: 1fr 1.5fr !important; }
                                        }

                                        #aging-grid {
                                            display: grid !important;
                                            gap: 1rem !important;
                                            grid-template-columns: 1fr !important;
                                        }
                                        @media (min-width: 768px) {
                                            #aging-grid { grid-template-columns: repeat(2, 1fr) !important; }
                                        }
                                        @media (min-width: 1200px) {
                                            #aging-grid { grid-template-columns: repeat(4, 1fr) !important; }
                                        }
                                    `;
                    document.head.appendChild(style);
                });
            </script>

            <!-- Quick Pay Modal (Alpine.js Component) -->
            <div x-data="quickPayModal()" 
                 @open-quick-pay.window="openModal($event.detail.id)"
                 x-show="open" 
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto">
                
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>

                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg dark:bg-gray-800">
                        
                        <form @submit.prevent="submitQuickPay">
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 dark:bg-gray-900">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <span class="icon-check text-green-600 text-lg"></span>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Confirmar Recebimento</h3>
                                        <div class="mt-4 flex flex-col gap-4">
                                            
                                            <!-- Data Pagamento -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data do Pagamento</label>
                                                <input type="date" x-model="paymentDate" required
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                            </div>

                                            <!-- Método Pagamento -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Método</label>
                                                <select x-model="paymentMethod" required
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                                    <option value="pix">Pix</option>
                                                    <option value="boleto">Boleto</option>
                                                    <option value="transferencia">Transferência</option>
                                                    <option value="credito">Cartão de Crédito</option>
                                                    <option value="dinheiro">Dinheiro</option>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 dark:bg-gray-800">
                                <button type="submit" :disabled="loading"
                                    class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto disabled:opacity-50">
                                    <span x-show="!loading">Confirmar Baixa</span>
                                    <span x-show="loading">Processando...</span>
                                </button>
                                <button type="button" @click="open = false"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-600">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                // Global function called by DataGrid button
                function openQuickPay(id) {
                    window.dispatchEvent(new CustomEvent('open-quick-pay', { detail: { id: id } }));
                }

                // Alpine.js Component Definition
                document.addEventListener('alpine:init', () => {
                    Alpine.data('quickPayModal', () => ({
                        open: false,
                        id: null,
                        loading: false,
                        paymentDate: '{{ date("Y-m-d") }}',
                        paymentMethod: 'pix',
                        
                        openModal(id) {
                            this.id = id;
                            this.open = true;
                            this.loading = false;
                        },
                        
                        async submitQuickPay() {
                            if (this.loading) return; // Prevent double-submit
                            this.loading = true;
                            
                            try {
                                const formData = new FormData();
                                formData.append('payment_date', this.paymentDate);
                                formData.append('payment_method', this.paymentMethod);
                                
                                const response = await fetch("{{ route('admin.lawfirm.financial.quick_pay', '') }}/" + this.id, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value 
                                            || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                                            || '',
                                        'Accept': 'application/json',
                                    },
                                    body: formData
                                });
                                
                                const data = await response.json();
                                
                                this.loading = false;
                                this.open = false;
                                
                                // Show success message
                                alert(data.message || 'Baixa realizada com sucesso!');
                                
                                // Reload page to refresh grid
                                location.reload();
                                
                            } catch (error) {
                                console.error('Error:', error);
                                this.loading = false;
                                alert('Erro ao realizar baixa. Veja console.');
                            }
                        }
                    }));
                });
            </script>
        @endpush
</x-admin::layouts>