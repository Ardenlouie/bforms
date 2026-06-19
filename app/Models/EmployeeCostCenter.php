<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;

class EmployeeCostCenter extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table='employee_cost_centers';


    protected $fillable = [
        'company_id',
        'gl_code',
        'gl_description',
        'analysis_category',
        'department_code',
        'department',
        'employee_code',
        'employee_name',
    ];
}
