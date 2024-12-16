<?php

namespace Webkul\Expense\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\Expense\DataGrids\ExpenseDataGrid;
use Webkul\Expense\Repositories\ExpenseRepository;
use Webkul\Approval\Repositories\ApprovalRepository;
use Webkul\Admin\Http\Requests\AttributeForm;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\JsonResponse;
use Webkul\Admin\Http\Requests\MassDestroyRequest;

class ExpenseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function __construct(
    protected ExpenseRepository $expenseRepository,
    protected ApprovalRepository $approvalRepository
    )
    {
        request()->request->add(['entity_type' => 'expense']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

        if (request()->ajax()) {
            return app(ExpenseDataGrid::class)->toJson();
        }

        return view('expense::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('expense::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        $expense = $this->expenseRepository->create(request()->all());

        $this->approvalRepository->create([
            'expense_id' => $expense->id,
        ]);

        return redirect()->route('admin.expense.index')->with('success',"Expense created successfully");

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $expense = $this->expenseRepository->findOrFail($id);

        return view('expense::edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        $this->expenseRepository->update(request()->all(), $id);

        return redirect()->route('admin.expense.index')->with('success',"Expense updated successfully");
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $expense = $this->expenseRepository->findOrFail($id);

        try {
            Event::dispatch('expense.delete.before', $id);

            $expense->delete($id);

            Event::dispatch('expense.delete.after', $id);

            return response()->json([
                'message' => trans('expense::app.expenses.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('expense::app.expenses.index.delete-failed'),
            ], 400);
        }

    }

    /**
     * Mass delete the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $indices = $massDestroyRequest->input('indices');

        foreach ($indices as $index) {
            Event::dispatch('expense.delete.before', $index);

            $this->expenseRepository->delete($index);

            Event::dispatch('expense.delete.after', $index);
        }

        return new JsonResponse([
            'message' => trans('admin::app.expenses.index.delete-success'),
        ]);
    }
}
