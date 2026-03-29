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

    protected $fillable = [
        'form_id',
        'user_id',
        'model_id',
        'model_type',
        'endorser',
        'approver',
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

    public function approved() {
        return $this->belongsTo('App\Models\User', 'approver', 'id');
    }

    public function endorsed() {
        return $this->belongsTo('App\Models\User', 'endorser', 'id');
    }

}
