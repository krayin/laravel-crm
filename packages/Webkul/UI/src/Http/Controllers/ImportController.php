<?php

namespace Webkul\UI\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use Webkul\UI\DataGrid\Imports\DataGridImport;

class ImportController extends Controller
{
    /**
     * Import datagrid.
     *
     * @return \Illuminate\Http\Response
     */
    public function import()
    {
        // dd(request()->file);
        $requestedInfo = request()->validate([
            //'format' => 'required',
        ]);
        
        $path = request()->file;

    //    dd($path);
    
        Excel::Import(new DataGridImport, $path);

       // Session::flash('success', 'Leave Records Imported Successfully');

        return redirect()->back();
           
    }
}
