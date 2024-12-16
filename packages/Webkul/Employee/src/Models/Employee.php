<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Employee\Contracts\Employee as EmployeeContract;

class Employee extends Model implements EmployeeContract
{
    protected $fillable = [
        'name',
        'personal_email',
        'personal_mobile',
        'official_email',
        'official_mobile',
        'employee_id',
        'date_of_joining',
        'last_date_of_appraisal',
        'coming_appraisal_date',
        'date_of_reliving',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    // employee_name is equivalent to name





}
