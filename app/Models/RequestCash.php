<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestCash extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'rca_forms';

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

     protected $fillable = [
        'control_number',
        'form_id',
        'company_id',
        'department_id',
        'name',
        'total_amount',
        'cost_center',
        'purpose',
        'travel',
        'rca_date',
        'date_submitted',
        'itenerary',
        'location',
    ];

    public function form() {
        return $this->belongsTo('App\Models\Form');
    }

    public function department() {
        return $this->belongsTo('App\Models\Department');
    }

    public function costcenter() {
        return $this->belongsTo('App\Models\User', 'cost_center', 'id');
    }
    
    public function company() {
        return $this->belongsTo('App\Models\Company');
    }

    public function rca_form_item() {
        return $this->hasMany('App\Models\RequestCashItem', 'rca_form_id', 'id');
    }
}
