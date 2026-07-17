<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

    protected $fillable = [
        'brand',
        'bm_id',
        'gbm_id',
    ];

    public function bm() {
        return $this->belongsTo('App\Models\User');
    }

    public function gbm() {
        return $this->belongsTo('App\Models\User');
    }
}
