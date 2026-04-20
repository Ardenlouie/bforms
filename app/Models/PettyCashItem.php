<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PettyCashItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'pca_form_items';

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

     protected $fillable = [
        'pca_form_id',
        'item_description',
        'amount',
    ];

    public function pca_form() {
        return $this->belongsTo('App\Models\PettyCash', 'pca_form_id', 'id');
    }
}
