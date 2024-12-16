<?php

namespace Webkul\Employee\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\Employee\DataGrids\EmployeeDataGrid;
use Webkul\Employee\Repositories\EmployeeRepository;
use Webkul\Admin\Http\Requests\AttributeForm;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\JsonResponse;
use Webkul\Admin\Http\Requests\MassDestroyRequest;


class EmployeeController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(protected EmployeeRepository $employeeRepository)
    {
        request()->request->add(['entity_type' => 'employees']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(EmployeeDataGrid::class)->toJson();
        }

        return view('employee::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('employee::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        $this->employeeRepository->create(request()->all());

        return redirect()->route('admin.employee.index')->with('success',"Employee created successfully");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $employee = $this->employeeRepository->findOrFail($id);

        return view('employee::edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        $this->employeeRepository->update(request()->all(), $id);

        return redirect()->route('admin.employee.index')->with('success',"Employee updated successfully");

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): JsonResponse
    {
        $employee = $this->employeeRepository->findOrFail($id);

        try {
            Event::dispatch('employee.delete.before', $id);

            $employee->delete($id);

            Event::dispatch('employee.delete.after', $id);

            return response()->json([
                'message' => trans('employee::app.employees.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('employee::app.employees.index.delete-failed'),
            ], 400);
        }
    }

    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $indices = $massDestroyRequest->input('indices');

        foreach ($indices as $index) {
            Event::dispatch('employee.delete.before', $index);

            $this->employeeRepository->delete($index);

            Event::dispatch('employee.delete.after', $index);
        }

        return new JsonResponse([
            'message' => trans('admin::app.employees.index.delete-success'),
        ]);
    }
}
