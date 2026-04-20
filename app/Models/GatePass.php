<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GatePass extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'gate_pass';

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

    protected $fillable = [
        'form_id',
        'company_id',
        'purpose',
        'received_by',
        'date_submitted',
        'control_number',
        'psrf_form_id',
        'image',
        'path',
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

    public function gate_pass_item() {
        return $this->hasMany('App\Models\GatePassItem', 'gate_pass_id', 'id');
    }

    public function psrf_form() {
        return $this->belongsTo('App\Models\ProductSample', 'psrf_form_id', 'id');
    }
}
