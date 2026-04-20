<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PettyLiquidItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'pcl_form_items';

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

    protected $fillable = [
        'pcl_form_id',
        'item_description',
        'amount',
    ];

    public function pcl_form() {
        return $this->belongsTo('App\Models\PettyLiquid', 'pcl_form_id', 'id');
    }
}
