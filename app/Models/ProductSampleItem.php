<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductSampleItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'psrf_form_items';

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

     protected $fillable = [
        'psrf_form_id',
        'item_code',
        'item_description',
        'uom',
        'quantity',
        'remarks',
    ];

    public function psrf_form() {
        return $this->belongsTo('App\Models\ProductSample', 'psrf_form_id', 'id');
    }
    

}
