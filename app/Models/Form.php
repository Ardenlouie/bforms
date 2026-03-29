<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Form extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

    protected $fillable = [
        'prefix',
        'name',
        'department_id',
        'category_id',
        'approver_id',
    ];

    public function department() {
        return $this->belongsTo('App\Models\Department');
    }

    public function category() {
        return $this->belongsTo('App\Models\Category');
    }

    public function approver() {
        return $this->belongsTo('App\Models\User');
    }

    public function psrf() {
        return $this->hasMany('App\Models\ProductSample');
    }
}
