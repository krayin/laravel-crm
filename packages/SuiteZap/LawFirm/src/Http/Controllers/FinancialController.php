<?php

namespace SuiteZap\LawFirm\Http\Controllers;

use Illuminate\Http\Request;
use SuiteZap\LawFirm\DataGrids\FinancialDataGrid;
use SuiteZap\LawFirm\Services\FinancialDashboardService;
use Webkul\Admin\Http\Controllers\Controller;

class FinancialController extends Controller
{
    /**
     * @var FinancialDashboardService
     */
    protected $dashboardService;

    /**
     * Create a new controller instance.
     */
    public function __construct(FinancialDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the financial dashboard.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Handle AJAX request for DataGrid
        if ($request->ajax()) {
            return app(FinancialDataGrid::class)->process();
        }

        // Filtros (Datas e Responsável)
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Passa usuários para o filtro de responsável
        $users = \Webkul\User\Models\User::all();

        // Obtém todas as métricas do Service
        $metrics = $this->dashboardService->getAllMetrics($startDate, $endDate);

        return view('lawfirm::financial.index', [
            'totalReceitas' => $metrics['totalReceitas'],
            'totalDespesas' => $metrics['totalDespesas'],
            'saldoLiquido' => $metrics['saldoLiquido'],
            'margemPercent' => $metrics['margemPercent'],
            'pendenteReceber' => $metrics['pendenteReceber'],
            'collectionRate' => $metrics['collectionRate'],
            'dso' => $metrics['dso'],
            'aging' => $metrics['aging'],
            'startDate' => $startDate,
            'endDate' => $endDate,
            'users' => $users, // Injetado
        ]);
    }

    /**
     * Realiza a baixa rápida (Quick Pay) de um lançamento financeiro.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function quickPay(Request $request, $id)
    {
        // 1. Validação
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
        ]);

        // 2. Busca e Atualização
        $financial = \SuiteZap\LawFirm\Models\Financial::findOrFail($id);

        $financial->update([
            'status' => 'pago',
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
        ]);

        return response()->json([
            'message' => 'Baixa realizada com sucesso!',
            'data' => $financial
        ]);
    }

    /**
     * Gera um recibo em PDF para um lançamento financeiro pago.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadReceipt($id)
    {
        $transaction = \SuiteZap\LawFirm\Models\Financial::with('processo.person')->findOrFail($id);

        // 1. Busca as Configurações Globais do Escritório (definidas no system.php)
        $companyName = core()->getConfigData('lawfirm.settings.general.company_name') ?? 'Escritório de Advocacia';
        $logoPath = core()->getConfigData('lawfirm.settings.general.logo');
        $whatsapp = core()->getConfigData('lawfirm.settings.general.contact_whatsapp');
        $email = core()->getConfigData('lawfirm.settings.general.contact_email');
        $address = core()->getConfigData('lawfirm.settings.general.address');
        $website = core()->getConfigData('lawfirm.settings.general.website');

        // 2. Tratamento da Logo para PDF (DomPDF precisa do caminho físico)
        $realLogoPath = null;
        if ($logoPath) {
            $realLogoPath = public_path('storage/' . $logoPath);
        }

        // 3. Envia tudo para a View
        // Nota: A view correta baseada no sistema de arquivos é 'lawfirm::financial.pdf.receipt'
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('lawfirm::financial.pdf.receipt', compact(
            'transaction',
            'companyName',
            'realLogoPath',
            'whatsapp',
            'email',
            'address',
            'website'
        ));

        return $pdf->download('recibo_' . $transaction->id . '.pdf');
    }
}
