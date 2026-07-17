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
        'endorser' => 'array', 
        'brands' => 'array', 
        'group_brands' => 'array', 
        'bm_signs' => 'array', 
        'gbm_signs' => 'array', 
        'department_id' => 'array', 
    ];

    protected $fillable = [
        'form_id',
        'user_id',
        'department_id',
        'model_id',
        'model_type',
        'admin_id',
        'processor',
        'brands',
        'group_brands',
        'endorser',
        'approver',
        'noted_id',
        'signed_id',
        'date_confirm',
        'date_confirming',
        'date_confirmed',
        'date_processed',
        'date_endorsed',
        'date_approved',
        'date_checked',
        'date_received',
        'status',
        'remarks',
        'bm_signs',
        'gbm_signs',
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

    public function noted() {
        return $this->belongsTo('App\Models\User');
    }

    public function department() {
        return $this->belongsTo('App\Models\Department');
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

    public function hasEndorser($userId)
    {
        return in_array($userId, $this->endorser ?? []);
    }

    public function branded() {
        return $this->belongsTo('App\Models\User', 'brands', 'id');
    }

    public function hasBrand($userId)
    {
        return in_array($userId, $this->brands ?? []);
    }

    public function group_branded() {
        return $this->belongsTo('App\Models\User', 'group_brands', 'id');
    }

    public function hasGroupBrand($userId)
    {
        return in_array($userId, $this->group_brands ?? []);
    }


}
