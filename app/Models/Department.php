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
        'bevi_approver' => 'array', 
        'beva_approver' => 'array', 
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
        'admin2_id',
        'approver_ids',
        'bevi_approver',
        'beva_approver',
    ];

    public function head() {
        return $this->belongsTo('App\Models\User');
    }

    public function admin() {
        return $this->belongsTo('App\Models\User');
    }

    public function admin2() {
        return $this->belongsTo('App\Models\User');
    }
    
    public function hasApprover($userId)
    {
        return in_array($userId, $this->approver_ids ?? []);
    }

    public function hasBevi($userId)
    {
        return in_array($userId, $this->bevi_approver ?? []);
    }

    public function hasBeva($userId)
    {
        return in_array($userId, $this->beva_approver ?? []);
    }
}
