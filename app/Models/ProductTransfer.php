<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductTransfer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'psst_forms';

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

    protected $fillable = [
        'form_id',
        'company_id',
        'point_origin',
        'activity_name',
        'objective',
        'delivery_date',
        'date_submitted',
        'delivery_instructions',
        'control_number',
    ];

    public function form() {
        return $this->belongsTo('App\Models\Form');
    }
    
    public function company() {
        return $this->belongsTo('App\Models\Company');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function psst_form_item() {
        return $this->hasMany('App\Models\ProductTransferItem', 'psst_form_id', 'id');
    }
}
