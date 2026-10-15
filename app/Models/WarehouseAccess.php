<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WarehouseAccess extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'warehouse_access';

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

    protected $fillable = [
        'control_number',
        'form_id',
        'company_id',
        'purpose',
        'date_access',
        'time_in',
        'time_out',
        'date_submitted',
        'file_name',
        'path',
        'opened_by',

    ];

    public function form() {
        return $this->belongsTo('App\Models\Form');
    }

    public function company() {
        return $this->belongsTo('App\Models\Company');
    }

}
