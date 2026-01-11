<?php

namespace SuiteZap\LawFirm\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialDashboardService
{
    /**
     * Retorna todas as métricas do dashboard consolidadas.
     */
    public function getAllMetrics(?string $startDate = null, ?string $endDate = null): array
    {
        $kpis = $this->getKpis($startDate, $endDate);

        return [
            'totalReceitas' => $kpis['totalReceitas'],
            'totalDespesas' => $kpis['totalDespesas'],
            'saldoLiquido' => $kpis['saldoLiquido'],
            'margemPercent' => $kpis['margemPercent'],
            'pendenteReceber' => $this->getPendenteReceber($startDate, $endDate),
            'collectionRate' => $this->getCollectionRate($startDate, $endDate),
            'dso' => $this->getDso($startDate, $endDate),
            'aging' => $this->getAgingList(),
        ];
    }

    /**
     * Retorna Query Base com filtros de Segurança (ACL) e Filtros Manuais.
     */
    private function getBaseQuery()
    {
        // Join com processos para acessar o user_id (dono do processo)
        // FK verificada: processo_id (law_financials) -> id (processos)
        $query = DB::table('law_financials')
            ->join('processos', 'law_financials.processo_id', '=', 'processos.id')
            ->select('law_financials.*'); // Evita conflito de ID

        $user = auth()->guard('user')->user();
        $responsibleId = request('responsible_id');

        // 1. Aplica Segurança (ACL)
        if ($user->view_permission !== 'global') {
            if ($user->view_permission == 'group') {
                // Filtra por grupo
                $userIds = $user->groups->mapMany(function ($group) {
                    return $group->users->pluck('id');
                })->flatten()->unique();
                $query->whereIn('processos.user_id', $userIds);
            } else {
                // Individual
                $query->where('processos.user_id', $user->id);
            }
        }
        // 2. Aplica Filtro Manual (Apenas se for Global e escolheu alguém)
        elseif ($responsibleId) {
            $query->where('processos.user_id', $responsibleId);
        }

        return $query;
    }

    /**
     * Calcula KPIs gerais: Total Receita, Total Despesa, Saldo Líquido, Margem %.
     */
    public function getKpis(?string $startDate = null, ?string $endDate = null): array
    {
        $query = $this->getBaseQuery();

        if ($startDate) {
            $query->where('law_financials.data_vencimento', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('law_financials.data_vencimento', '<=', $endDate);
        }

        $totalReceitas = (clone $query)->where('law_financials.tipo', 'receita')->sum('law_financials.valor') ?? 0;
        $totalDespesas = (clone $query)->where('law_financials.tipo', 'despesa')->sum('law_financials.valor') ?? 0;
        $saldoLiquido = $totalReceitas - $totalDespesas;

        // Margem % = (Saldo / Receitas) * 100 - Proteção contra divisão por zero
        $margemPercent = $totalReceitas > 0
            ? round(($saldoLiquido / $totalReceitas) * 100, 2)
            : 0;

        return [
            'totalReceitas' => $totalReceitas,
            'totalDespesas' => $totalDespesas,
            'saldoLiquido' => $saldoLiquido,
            'margemPercent' => $margemPercent,
        ];
    }

    /**
     * Calcula Collection Rate: (Recebido / Faturado) * 100.
     * Considera apenas receitas que têm issued_at preenchido.
     */
    public function getCollectionRate(?string $startDate = null, ?string $endDate = null): float
    {
        $query = $this->getBaseQuery()
            ->where('law_financials.tipo', 'receita')
            ->whereNotNull('law_financials.issued_at');

        if ($startDate) {
            $query->where('law_financials.issued_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('law_financials.issued_at', '<=', $endDate);
        }

        $totalFaturado = (clone $query)->sum('law_financials.valor') ?? 0;
        $totalRecebido = (clone $query)->where('law_financials.status', 'pago')->sum('law_financials.valor') ?? 0;

        // Proteção contra divisão por zero
        if ($totalFaturado <= 0) {
            return 0;
        }

        return round(($totalRecebido / $totalFaturado) * 100, 2);
    }

    /**
     * Calcula DSO (Days Sales Outstanding): Média de dias entre issued_at e payment_date.
     * Considera apenas receitas pagas com ambas as datas preenchidas.
     */
    public function getDso(?string $startDate = null, ?string $endDate = null): float
    {
        $query = $this->getBaseQuery()
            ->where('law_financials.tipo', 'receita')
            ->where('law_financials.status', 'pago')
            ->whereNotNull('law_financials.payment_date')
            // Aceita registros com issued_at OU data_vencimento (fallback)
            ->where(function ($q) {
                $q->whereNotNull('law_financials.issued_at')
                    ->orWhereNotNull('law_financials.data_vencimento');
            });

        if ($startDate) {
            $query->where(DB::raw('COALESCE(law_financials.issued_at, law_financials.data_vencimento)'), '>=', $startDate);
        }
        if ($endDate) {
            $query->where(DB::raw('COALESCE(law_financials.issued_at, law_financials.data_vencimento)'), '<=', $endDate);
        }

        $records = $query->select(
            'law_financials.issued_at',
            'law_financials.data_vencimento',
            'law_financials.payment_date'
        )->get();

        if ($records->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($records as $record) {
            // Data Inicial: Usa issued_at, se nulo usa data_vencimento (due_date)
            $start = $record->issued_at ?? $record->data_vencimento;

            if (!$start)
                continue; // Should be covered by query, but safe check

            $startDateObj = Carbon::parse($start);
            $paymentDateObj = Carbon::parse($record->payment_date);

            // false = não absoluto, permite negativos para validação
            $days = $startDateObj->diffInDays($paymentDateObj, false);

            // Filtro de Sanidade: Ignora erros de data (negativos) ou outliers (> 365 anos)
            if ($days < 0 || $days > 365) {
                continue;
            }

            $totalDays += $days;
            $count++;
        }

        // Proteção contra divisão por zero
        if ($count <= 0) {
            return 0;
        }

        return round($totalDays / $count, 1);
    }

    /**
     * Retorna Aging List: Contas a receber vencidas agrupadas em buckets.
     * Buckets: 0-30 dias, 31-60 dias, 61-90 dias, >90 dias.
     * Baseado em snapshot da data atual.
     */
    public function getAgingList(): array
    {
        $today = Carbon::today();

        $aging = [
            '0_30' => 0,
            '31_60' => 0,
            '61_90' => 0,
            'over_90' => 0,
        ];

        // Busca receitas pendentes com vencimento no passado (vencidas)
        $overdueRecords = $this->getBaseQuery()
            ->where('law_financials.tipo', 'receita')
            ->where('law_financials.status', 'pendente')
            ->where('law_financials.data_vencimento', '<', $today)
            ->select('law_financials.data_vencimento', 'law_financials.valor')
            ->get();

        foreach ($overdueRecords as $record) {
            $vencimento = Carbon::parse($record->data_vencimento);
            $daysOverdue = $vencimento->diffInDays($today);
            $valor = (float) $record->valor;

            if ($daysOverdue <= 30) {
                $aging['0_30'] += $valor;
            } elseif ($daysOverdue <= 60) {
                $aging['31_60'] += $valor;
            } elseif ($daysOverdue <= 90) {
                $aging['61_90'] += $valor;
            } else {
                $aging['over_90'] += $valor;
            }
        }

        return $aging;
    }

    /**
     * Calcula o total pendente a receber: Receitas com status 'pendente'.
     */
    public function getPendenteReceber(?string $startDate = null, ?string $endDate = null): float
    {
        $query = $this->getBaseQuery()
            ->where('law_financials.tipo', 'receita')
            ->where('law_financials.status', 'pendente');

        if ($startDate) {
            $query->where('law_financials.data_vencimento', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('law_financials.data_vencimento', '<=', $endDate);
        }

        return $query->sum('law_financials.valor') ?? 0;
    }
}
