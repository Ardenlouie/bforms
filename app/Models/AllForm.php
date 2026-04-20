<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AllForm extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

    protected $casts = [
        'approver' => 'array', 
    ];

    protected $fillable = [
        'form_id',
        'user_id',
        'model_id',
        'model_type',
        'admin_id',
        'processor',
        'endorser',
        'approver',
        'signed_id',
        'date_confirmed',
        'date_processed',
        'date_endorsed',
        'date_approved',
        'date_checked',
        'status',
        'remarks',
    ];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function form() {
        return $this->belongsTo('App\Models\Form');
    }

    public function model() {
        return $this->morphTo();
    }

    public function admin() {
        return $this->belongsTo('App\Models\User');
    }

    public function signed() {
        return $this->belongsTo('App\Models\User');
    }

    public function processed() {
        return $this->belongsTo('App\Models\User', 'processor', 'id');
    }

    public function approved() {
        return $this->belongsTo('App\Models\User', 'approver', 'id');
    }

    public function hasApprover($userId)
    {
        return in_array($userId, $this->approver ?? []);
    }

    public function endorsed() {
        return $this->belongsTo('App\Models\User', 'endorser', 'id');
    }

}
