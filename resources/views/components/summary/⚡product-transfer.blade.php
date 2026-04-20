<?php

use Livewire\Component;
use App\Models\ProductTransfer;
use App\Models\ProductTransferItem;
use App\Models\Company;
use App\Models\Form;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $control_number, $company_id, $forms, $user, $form_id=1;
    public $data = [], $items = [];

    protected $listeners = ['loadPsstSummary' => 'loadData'];

    public function loadData($data, $items)
    {
        $this->user = Auth::user();

        $this->data = $data;
        $this->items = $items;
        $this->company_id = $data['company_id'];
        $this->form_id = $data['form_id'];

        $this->forms = Form::findOrFail(decrypt($data['form_id']));
        
        if(!empty($data['control_number'])){
            $this->control_number = $data['control_number'];

        } else {
            $this->control_number = $this->generateControlNumber();
        }

        Session::put('psst_item', [
            'items' => $this->items,
            'control_number' => $this->control_number,
            'data' => $this->data,
        ]);

    }

    private function generateControlNumber() {
        do {
            $control_number = 'PSST-0'.$this->company_id.'-001';

            $psst = ProductTransfer::withTrashed()->orderBy('control_number', 'DESC')->where('company_id', $this->company_id)
                ->first();
            if(!empty($psst->control_number)) {
                $latest_control_number = $psst->control_number;
                list(, $prev_company_id, $last_number) = explode('-', $latest_control_number);

                $number = ('0'.$this->company_id == $prev_company_id) ? ((int)$last_number + 1) : 1;

                $formatted_number = str_pad($number, 3, '0', STR_PAD_LEFT);

                $control_number = "PSST-0$this->company_id-$formatted_number";
            }

        } while(ProductTransfer::withTrashed()->where('control_number', $control_number)->where('company_id', $this->company_id)->exists());

        return $control_number;
    }
};
?>

<div>
    <div class="modal-content">
        <div class="modal-header bg-primary">
            <h4 class="modal-title text-uppercase" >{{ $forms->name ?? '' }} SUMMARY</h4>
        </div>
        <div class="modal-body">
            <div class="row mb-3 text-left">
                <div class="col-6">
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
                <div class="col-6">
                    <h4>Ref. No.: <b>{{ $control_number }}</b></h4>
                </div>
                
            </div>
            <div class="row text-left">
                <div class="col-6">
                    <h4>Objective: <b>{{ ($data['objective'] ?? '' )}}</b></h4>
                    <h4>Delivery Instructions: <b>{{ ($data['delivery_instructions'] ?? '' )}}</b></h4>
                </div>
                <div class="col-6">
                    <h4>Point of Origin: <b>{{ ($data['point_origin'] ?? '' )}}</b></h4>
                    <h4>Delivery Date: <b>{{ date('F d, Y', strtotime($data['delivery_date'] ?? '')) }}</b></h4>
                    <h4>Date Submitted: <b>{{ date('F d, Y') }}</b></h4>

                </div>
         
            </div>
        

            <table class="table table-striped text-center" id="summaryTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Code</th>
                        <th>Item Description</th>
                        <th>UOM</th>
                        <th>Qty</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['sku'] }}</td>
                            <td>{{ $item['desc'] }}</td>
                            <td>{{ $item['uom'] }}</td>
                            <td>{{ $item['qty'] }}</td>
                            <td>{{ $item['remarks'] }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
            <div class="row text-center">
                <div class="col-4">
                    <h4>Prepared By: <br><b>{{ ($user->name ?? '' )}}</b></h4>
                </div>
                <div class="col-4">
                    <h4>Endorsed By: <br><b>{{ ($user->department->head->name ?? '' )}}</b></h4>
                </div>
                <div class="col-4">
                    <h4>Approved By: <br><b>{{ ($forms->approver->name ?? '' )}}</b></h4>
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