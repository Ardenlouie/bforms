<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GatePassItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'gate_pass_items';

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

     protected $fillable = [
        'gate_pass_id',
        'item_description',
        'uom',
        'quantity',
        'quantity_release',
        'balance',
        'remarks',
    ];

    public function gate_pass() {
        return $this->belongsTo('App\Models\GatePass', 'gate_pass_id', 'id');
    }
}
