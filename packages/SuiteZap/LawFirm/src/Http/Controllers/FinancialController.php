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
        // 1. Buscar lançamento com relacionamentos
        $recibo = \SuiteZap\LawFirm\Models\Financial::with('processo.person')->findOrFail($id);

        // 2. Validar status
        if (strtolower($recibo->status) !== 'pago') {
            return back()->with('error', 'Apenas lançamentos pagos podem gerar recibo.');
        }

        // 3. Preparar dados
        $pagador = $recibo->processo?->person?->name ?? 'Ao Portador';
        $processoTitulo = $recibo->processo?->titulo ?? 'N/A';

        // Formata data por extenso em português
        $dataExtenso = '';
        if ($recibo->payment_date) {
            $meses = [
                1 => 'Janeiro',
                2 => 'Fevereiro',
                3 => 'Março',
                4 => 'Abril',
                5 => 'Maio',
                6 => 'Junho',
                7 => 'Julho',
                8 => 'Agosto',
                9 => 'Setembro',
                10 => 'Outubro',
                11 => 'Novembro',
                12 => 'Dezembro'
            ];
            $date = \Carbon\Carbon::parse($recibo->payment_date);
            $dataExtenso = $date->day . ' de ' . $meses[$date->month] . ' de ' . $date->year;
        }

        $data = [
            'recibo' => $recibo,
            'pagador' => $pagador,
            'processoTitulo' => $processoTitulo,
            'dataExtenso' => $dataExtenso,
        ];

        // 4. Gerar PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('lawfirm::financial.pdf.receipt', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream("recibo-{$id}.pdf");
    }
}
