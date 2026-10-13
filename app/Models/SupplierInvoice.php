<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierInvoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'supplier_invoices';

    protected $fillable = [
        'control_number',
        'form_id',
        'company_id',
        'department_id',
        'date_received',
        'supplier_name',
        'invoice_number',
        'invoice_amount',
        'payment_terms',
        'due_date',
        'date_forwarded',
        'receive_id',
        'rfp_form_id',
        'finance_submit',
        'check_date',
        'bank_branch',
        'date_posted',
        'department_date',
        'department_status',
        'ap_date',
        'ap_status',
        'date_submitted',
    ];

    public function form() {
        return $this->belongsTo('App\Models\Form');
    }

    public function department() {
        return $this->belongsTo('App\Models\Department');
    }

    public function receive() {
        return $this->belongsTo('App\Models\User');
    }
    
    public function company() {
        return $this->belongsTo('App\Models\Company');
    }

    public function rfp_form() {
        return $this->belongsTo('App\Models\RequestPayment');
    }
}
