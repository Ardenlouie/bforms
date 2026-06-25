<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceCode extends Model
{
    protected $connection = 'mysql2';
    protected $table='price_codes';
}
