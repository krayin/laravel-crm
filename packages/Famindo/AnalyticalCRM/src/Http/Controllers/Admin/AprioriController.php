<?php

namespace Famindo\AnalyticalCRM\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webkul\Admin\Http\Controllers\Controller;
use Famindo\AnalyticalCRM\DataGrids\AprioriRulesDataGrid;

class AprioriController extends Controller
{
    public function index(): View|JsonResponse|BinaryFileResponse
    {
        if (request()->ajax() || request()->boolean('export') || request()->has('format')) {
            return datagrid(AprioriRulesDataGrid::class)->process();
        }

        return view('analyticalcrm::admin.analytics.market-basket.index');
    }

    public function run(): RedirectResponse
    {
        $data = request()->validate([
            'from'        => ['nullable', 'date'],
            'to'          => ['nullable', 'date', 'after_or_equal:from'],
            'support'     => ['required', 'numeric', 'min:0', 'max:1'],
            'confidence'  => ['required', 'numeric', 'min:0', 'max:1'],
            'min_items'   => ['nullable', 'integer', 'min:1'],
            'persist'     => ['nullable', 'boolean'],
            'save'        => ['nullable', 'boolean'],
        ]);

        $options = [
            '--support'    => (string) ($data['support'] ?? 0.05),
            '--confidence' => (string) ($data['confidence'] ?? 0.6),
            '--min-items'  => (string) ($data['min_items'] ?? 2),
            '--save'       => true,
        ];

        if (! empty($data['from'])) {
            $options['--from'] = Carbon::parse($data['from'])->toDateString();
        }

        if (! empty($data['to'])) {
            $options['--to'] = Carbon::parse($data['to'])->toDateString();
        }

        if (! empty($data['persist'])) {
            $options['--persist'] = true;
        }

        if (auth()->guard('user')->check()) {
            $options['--created_by'] = (string) auth()->guard('user')->id();
        }

        Artisan::call('analytics:apriori', $options);

        session()->flash('success', 'Apriori analysis completed and rules saved.');

        return redirect()->route('admin.analytics.market_basket.index');
    }
}
