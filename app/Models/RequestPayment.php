<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RequestPayment extends Model
{
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'rfp_forms';

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

     protected $fillable = [
        'control_number',
        'form_id',
        'company_id',
        'department_id',
        'payable',
        'amount',
        'cost_center',
        'purpose',
        'instructions',
        'date_submitted',
        'currency',
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

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

}
