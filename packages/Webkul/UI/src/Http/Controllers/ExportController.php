<?php

namespace Webkul\UI\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Webkul\UI\DataGrid\Exports\DataGridExport;

class ExportController extends Controller
{
    /**
     * Export datagrid.
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        $requestedInfo = request()->validate([
            'gridClassName' => 'required',
            'format' => 'required',
        ]);

        $format = $requestedInfo['format'];
        $gridClassName = Crypt::decryptString($requestedInfo['gridClassName']);

        $gridInstance = app($gridClassName);

        if ($gridInstance instanceof \Webkul\UI\DataGrid\DataGrid) {
            $records = $gridInstance->export();

            if (!count($records)) {
                return response()->json([
                    'message' => trans('admin::app.export.no-records')
                ]);
            }

            if ($format == 'csv') {
                return Excel::download(new DataGridExport($records), Str::random(16) . '.csv');
            }

            if ($format == 'xls') {
                return Excel::download(new DataGridExport($records), Str::random(16) . '.xlsx');
            }
        }

        /**
         * If instance not matched that means datagrid is manipulated and should be aborted.
         */
        abort(404);
    }
}
