<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PettyLiquid extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'pcl_forms';

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

    protected $fillable = [
        'control_number',
        'form_id',
        'company_id',
        'pca_form_id',
        'total_amount',
        'balance',
        'date_submitted',
        'file_name',
        'path',
    ];

    public function form() {
        return $this->belongsTo('App\Models\Form');
    }

    public function company() {
        return $this->belongsTo('App\Models\Company');
    }

    public function pca_form() {
        return $this->belongsTo('App\Models\PettyCash');
    }

    public function pcl_form_item() {
        return $this->hasMany('App\Models\PettyLiquidItem', 'pcl_form_id', 'id');
    }
}
