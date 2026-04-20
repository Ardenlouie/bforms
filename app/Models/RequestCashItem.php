<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestCashItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'rca_form_items';

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

     protected $fillable = [
        'rca_form_id',
        'item_description',
        'amount',
        'days',
        'remarks',
    ];

    public function rca_form() {
        return $this->belongsTo('App\Models\RequestCash', 'rca_form_id', 'id');
    }
}
