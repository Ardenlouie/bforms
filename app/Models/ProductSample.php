<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductSample extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'psrf_forms';

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

    protected $fillable = [
        'form_id',
        'company_id',
        'recipient',
        'activity_name',
        'objective',
        'special_instructions',
        'date_submitted',
        'program_date',
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

    public function psrf_form_item() {
        return $this->hasMany('App\Models\ProductSampleItem', 'psrf_form_id', 'id');
    }
    
    
}
