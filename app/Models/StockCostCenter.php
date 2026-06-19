<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;

class StockCostCenter extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table='stock_cost_centers';


    protected $fillable = [
        'company_id',
        'gl_code',
        'gl_description',
        'analysis_category',
        'stock_code',
        'stock_description',
        'brand_code',
        'brand_description',
    ];
}
