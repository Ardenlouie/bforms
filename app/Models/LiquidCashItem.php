<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LiquidCashItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lca_form_items';

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

    protected $fillable = [
        'lca_form_id',
        'date',
        'item_description',
        'amount',
        'area',
    ];

    public function lca_form() {
        return $this->belongsTo('App\Models\LiquidCash', 'lca_form_id', 'id');
    }
}
