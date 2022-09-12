<?php

namespace Webkul\UI\DataGrid\Imports;

use Webkul\Contact\Models\Person;
use Maatwebsite\Excel\Concerns\ToModel;

class DataGridImport implements ToModel{

    /**
     * Method to create a blade view for export.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function model(array $row)
    {
        // dd($row);
        return new Person([
            'name'            => $row[0],
            'emails'          => $row[1],
            'contact_numbers' => $row[2],
            'organization_id' => $row[3],
        ]);
    }
}
