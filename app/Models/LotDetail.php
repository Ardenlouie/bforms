<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;

class LotDetail extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table='lot_detail';


    protected $fillable = [
        'company_id',
        'stock_code',
        'description',
        'quantity_pcs',
        'quantity_cs',
        'warehouse',
    ];
}
