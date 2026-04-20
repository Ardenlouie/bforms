<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PettyCash extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'pca_forms';

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

     protected $fillable = [
        'control_number',
        'form_id',
        'company_id',
        'name',
        'total_amount',
        'cost_center',
        'date_submitted',

    ];

    public function form() {
        return $this->belongsTo('App\Models\Form');
    }

    public function costcenter() {
        return $this->belongsTo('App\Models\User', 'cost_center', 'id');
    }
    
    public function company() {
        return $this->belongsTo('App\Models\Company');
    }

    public function pca_form_item() {
        return $this->hasMany('App\Models\PettyCashItem', 'pca_form_id', 'id');
    }
}
