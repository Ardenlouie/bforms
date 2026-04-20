<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'approver_ids' => 'array', 
    ];

    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

    protected $fillable = [
        'prefix',
        'name',
        'head_id',
        'admin_id',
        'approver_ids',
    ];

    public function head() {
        return $this->belongsTo('App\Models\User');
    }

    public function admin() {
        return $this->belongsTo('App\Models\User');
    }
    
    public function hasApprover($userId)
    {
        return in_array($userId, $this->approver_ids ?? []);
    }
}
