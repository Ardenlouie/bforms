<?php

use Livewire\Component;
use App\Models\ProductSample;
use App\Models\ProductSampleItem;
use App\Models\Company;
use App\Models\AllForm;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Form;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

use App\Notifications\SubmitFormNotification;

new class extends Component
{
    public $control_number, $company_id, $forms, $user, $form_id=1, $total_amount = 0;
    public $data = [], $items = [];

    protected $listeners = [
        'loadPsrfSummary' => 'loadData',
        'brandForm' => 'brandForm',
        'groupBrandForm' => 'groupBrandForm',
    ];

    public function loadData($data, $items)
    {
        $this->user = Auth::user();

        $this->data = $data;
        $this->items = $items;
        $this->company_id = $data['company_id'];
        $this->form_id = $data['form_id'];

        $this->forms = Form::findOrFail(decrypt($data['form_id']));
        $this->total_amount = collect($items)->sum('amount');
        
        if(!empty($data['control_number'])){
            $this->control_number = $data['control_number'];

        } else {
            $this->control_number = $this->generateControlNumber();
        }

        Session::put('psrf_item', [
            'items' => $this->items,
            'control_number' => $this->control_number,
            'data' => $this->data,
            'total_amount' => $this->total_amount,
        ]);

    }

    private function generateControlNumber() {
        do {
            $control_number = 'PSRF-0'.$this->company_id.'-001';

            $psrf = ProductSample::withTrashed()->orderBy('control_number', 'DESC')->where('company_id', $this->company_id)
                ->first();
            if(!empty($psrf->control_number)) {
                $latest_control_number = $psrf->control_number;
                list(, $prev_company_id, $last_number) = explode('-', $latest_control_number);

                $number = ('0'.$this->company_id == $prev_company_id) ? ((int)$last_number + 1) : 1;

                $formatted_number = str_pad($number, 3, '0', STR_PAD_LEFT);

                $control_number = "PSRF-0$this->company_id-$formatted_number";
            }

        } while(ProductSample::withTrashed()->where('control_number', $control_number)->where('company_id', $this->company_id)->exists());

        return $control_number;
    }

    public function brandForm($all_form)
    {
        $all_forms = AllForm::findOrFail($all_form);

        $date_confirming = date('Y-m-d');

        $current_bm = $all_forms->bm_signs ?? [];

        $bm_to_remove = Auth::user()->id;

        $updated_bm = array_filter($current_bm, function($name) use ($bm_to_remove) {
            return trim($name) !== trim($bm_to_remove);
        });

        $updated_bm = array_values($updated_bm);

        $all_forms->update([
            'bm_signs' => $updated_bm,
        ]);

        if (empty($all_forms->bm_signs)) {
            $all_forms->update([
                'status' => 'confirmed',
                'date_confirming' => $date_confirming,
            ]);
            // $all_forms->endorsed->notify(new SubmitFormNotification($all_forms));

            $group_brands = User::whereIn('id', $all_forms->group_brands ?? [])->get();

            if ($group_brands->isNotEmpty()) {
                Notification::send($group_brands, new SubmitFormNotification($all_forms));
            }
        } 

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;

        activity('approved')
            ->performedOn($all_forms)
            ->log(':causer.name has approve '.$form_name.' ['.$control_number.']');

        return redirect()->route('approver.show', encrypt($all_forms->id))->with([
            'message_success' => $form_name.' ['.$control_number.'] was approved'
        ]);
    }

    public function groupBrandForm($all_form)
    {
        $all_forms = AllForm::findOrFail($all_form);

        $date_confirmed = date('Y-m-d');

        $current_gbm = $all_forms->gbm_signs ?? [];

        $gbm_to_remove = Auth::user()->id;

        $updated_gbm = array_filter($current_gbm, function($name) use ($gbm_to_remove) {
            return trim($name) !== trim($gbm_to_remove);
        });

        $updated_gbm = array_values($updated_gbm);

        $all_forms->update([
            'gbm_signs' => $updated_gbm,
        ]);

        if (empty($all_forms->gbm_signs)) {
            $all_forms->update([
                'status' => 'endorsement',
                'date_confirmed' => $date_confirmed,
            ]);
            // $all_forms->endorsed->notify(new SubmitFormNotification($all_forms));

            $endorsers = User::whereIn('id', $all_forms->endorser ?? [])->get();

            if ($endorsers->isNotEmpty()) {
                Notification::send($endorsers, new SubmitFormNotification($all_forms));
            }
        } 

        $control_number = $all_forms->model->control_number;
        $form_name = $all_forms->form->name;

        activity('approved')
            ->performedOn($all_forms)
            ->log(':causer.name has approve '.$form_name.' ['.$control_number.']');

        return redirect()->route('approver.show', encrypt($all_forms->id))->with([
            'message_success' => $form_name.' ['.$control_number.'] was approved'
        ]);
    }
};
?>

<div>
    <div class="modal-content">
        <div class="modal-header bg-primary">
            <h4 class="modal-title text-uppercase">{{ $forms->name ?? '' }} SUMMARY</h4>
        </div>
        <div class="modal-body">
            <div class="row mb-3 text-left">
                <div class="col-8">
                    @if($company_id== 1)
                    <img src="{{asset('/images/bevilogonobg.png')}}" alt="product photo" class="product-img" height="50" width="250">
                    @elseif($company_id == 2)
                    <img src="{{asset('/images/bevanobg.png')}}" alt="product photo" class="product-img" height="80" width="120">
                    @elseif($company_id == 3)
                    <img src="{{asset('/images/biginobg.png')}}" alt="product photo" class="product-img" height="100" width="200">
                    @elseif($company_id == 4)
                    <img src="{{asset('/images/bevminobg.png')}}" alt="product photo" class="product-img" height="80" width="220">
                    @elseif($company_id == 5)
                    <img src="{{asset('/images/osp.png')}}" alt="product photo" class="product-img" height="80" width="250">
                    @elseif($company_id == 6)
                    <img src="{{asset('/images/pbb.png')}}" alt="product photo" class="product-img" height="80" width="150">
                    @endif
                </div>
                <div class="col-4">
                    <h5>Ref. No.: <b>{{ $control_number }}</b></h5>
                    <h5>Date Submitted: <b>{{ date('F d, Y') }}</b></h5>
                    <h5>Program Date: <b>{{ date('F d, Y', strtotime($data['program'] ?? '')) }}</b></h5>
                </div>
            </div>
            <div class="row text-left">
                <div class="col-8">

                    <h5>Requested By: <b>{{ ($data['requested_by'] ?? '' )}}</b></h5>
                    <h5>Recipient: <b>{{ ($data['recipient'] ?? '' )}}</b></h5>
                    <h5>Objective: <b>{{ ($data['objective'] ?? '' )}}</b></h5>

                </div>
                <div class="col-4">
                    <h5>Customer: <b>{{ ($data['customer'] ?? '' )}}</b></h5>
                    <h5>Activity Name: <b>{{ ($data['activity'] ?? '' )}}</b></h5>
                    <h5>Special Instructions: <b>{{ ($data['special'] ?? '' )}}</b></h5>
                </div>
            </div>
        

            <table class="table table-striped text-center" id="summaryTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Description</th>
                        <th>UOM</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $index => $item)
                        <tr>
                            <td class="align-middle">{{ $index + 1 }}</td>
                            <td>
                                <img src="{{ asset('images/AllProducts/'.$item['sku'].'.png') }}" alt="SKU IMAGE" height="80" width="80"><br>
                                {{ $item['sku'] }} ({{ $item['desc'] }})
                            </td>
                           
                            <td class="align-middle">{{ $item['uom'] }}</td>
                            <td class="align-middle">{{ $item['qty'] }}</td>
                            <td class="align-middle">{{ number_format($item['amount'], 2) }}</td>
                            <td class="align-middle">{{ $item['remarks'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th colspan="1" class="text-end">TOTAL AMOUNT:</th>
                        <th><h3>₱{{ number_format($total_amount, 2) }}</h3></th>
                        <th></th>
                    </tr>
                </tfoot>

            </table>
            <div class="row text-left mb-3">
                <div class="col-6">
                    <h4>Attachment File Name: <b>{{ ($data['file_name'] ?? '' )}}</b></h4>
                </div>
            </div>
            <div class="row text-center">
                <div class="col-4">
                    <h4>Requestor: <br><b>{{ ($user->name ?? '' )}}</b></h4>
                </div>
                <div class="col-4">
                    <h4>Endorser: 
                        <br><b>
                            {{ ($user->department->name ?? '' )}} Department Approvers
                        </b></h4>
                </div>
                <div class="col-4">
                    <h4>Approver: <br><b>Finance Department Approvers</b></h4>
                </div>
            </div>
        </div>
        

        <div class="modal-footer">
            

            <a class="btn-draft btn btn-secondary">Save as Draft</a>

            <a class="btn-confirm btn btn-success">Submit</a>
    
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div>

</div>