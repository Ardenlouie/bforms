<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseAccount extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'ledger_code',
        'name',
    ];

}
