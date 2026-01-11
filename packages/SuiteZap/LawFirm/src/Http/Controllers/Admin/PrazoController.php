<?php

namespace SuiteZap\LawFirm\Http\Controllers\Admin;

use Illuminate\Http\Request;
use SuiteZap\LawFirm\DataGrids\PrazoDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use SuiteZap\LawFirm\Models\Prazo;
use Carbon\Carbon;

class PrazoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(PrazoDataGrid::class)->toJson();
        }

        return view('lawfirm::admin.prazos.index');
    }

    /**
     * Store a newly created deadline in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'processo_id' => 'required|exists:processos,id',
            'titulo' => 'required|string|max:255',
            'data_vencimento' => 'required|date',
            'tipo' => 'required|in:fatal,comum',
            'descricao' => 'nullable|string',
        ]);

        Prazo::create([
            'processo_id' => $request->processo_id,
            'titulo' => $request->titulo,
            'data_vencimento' => $request->data_vencimento,
            'tipo' => $request->tipo,
            'descricao' => $request->descricao,
            'status' => 'pendente',
        ]);

        session()->flash('success', trans('lawfirm::app.prazos.create-success'));

        return back();
    }

    /**
     * Show the form for editing the specified deadline.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $prazo = Prazo::findOrFail($id);

        return view('lawfirm::admin.prazos.edit', compact('prazo'));
    }

    /**
     * Update the specified deadline in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $prazo = Prazo::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'data_vencimento' => 'required|date',
            'tipo' => 'required|in:fatal,comum',
            'status' => 'required|in:pendente,concluido',
            'descricao' => 'nullable|string',
        ]);

        $prazo->update([
            'titulo' => $request->titulo,
            'data_vencimento' => $request->data_vencimento,
            'tipo' => $request->tipo,
            'status' => $request->status,
            'descricao' => $request->descricao,
            'concluido_em' => $request->status === 'concluido' && $prazo->getOriginal('status') !== 'concluido' ? Carbon::now() : ($request->status === 'pendente' ? null : $prazo->concluido_em),
        ]);

        session()->flash('success', trans('lawfirm::app.processos.update-success'));

        return redirect()->route('admin.processos.edit', $prazo->processo_id);
    }

    /**
     * Mark the specified deadline as concluded.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function concluir($id)
    {
        $prazo = Prazo::findOrFail($id);

        $prazo->update([
            'status' => 'concluido',
            'concluido_em' => Carbon::now(),
        ]);

        session()->flash('success', trans('lawfirm::app.prazos.conclude-success'));

        return back();
    }

    /**
     * Remove the specified deadline from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $prazo = Prazo::findOrFail($id);

        $prazo->delete();

        session()->flash('success', trans('lawfirm::app.prazos.delete-success'));

        return back();
    }
}
