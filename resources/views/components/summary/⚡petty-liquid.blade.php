<?php

use Livewire\Component;

use App\Models\Company;
use App\Models\PettyCash;
use App\Models\PettyCashItem;
use App\Models\AllForm;
use App\Models\PettyLiquid;
use App\Models\PettyLiquidItem;
use App\Models\Form;
use App\Models\User;
use App\Models\Department;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

new class extends Component
{
    public $control_number, $company_id, $forms, $user, $form_id=1, $cost_center, 
        $department, $pca_form, $total_amount = 0, $pca_amount = 0, $all_form, $balance = 0;
    public $data = [], $items = [];

    protected $listeners = ['loadPclSummary' => 'loadData'];

    public function loadData($data, $items)
    {
        $this->user = Auth::user();

        $this->data = $data;
        $this->items = $items;
        $this->form_id = $data['form_id'];

        $this->pca_form = PettyCash::where('id', $data['pca_form_id'])->first();

        
        $this->company_id = $this->pca_form->company_id;

        $this->forms = Form::findOrFail(decrypt($data['form_id']));
        $this->cost_center = User::where('id', $this->pca_form->cost_center)->first();
        $this->department = Department::where('id', $this->pca_form->department_id)->first();

        $this->total_amount = collect($items)->sum('amount');
        $this->pca_amount = $data['pca_amount'];
        $this->balance =  $this->pca_amount - $this->total_amount;
        
        if(!empty($data['control_number'])){
            $this->control_number = $data['control_number'];

        } else {
            $this->control_number = $this->generateControlNumber();
        }

        Session::put('pcl_item', [
            'control_number' => $this->control_number,
            'data' => $this->data,
            'items' => $this->items,
            'total_amount' => $this->total_amount,
            'balance' => $this->balance,
        ]);

    }

    private function generateControlNumber() {
        $date_code = date('Y');
        do {
            $control_number = 'PCL-0'.$this->company_id.'-'.$date_code.'-001';

            $rca = PettyLiquid::withTrashed()->orderBy('control_number', 'DESC')->first();
            
            if(!empty($rca->control_number)) {
                $latest_control_number = $rca->control_number;
                list(,$prev_company_id, $prev_year, $last_number) = explode('-', $latest_control_number);

                $number = ('0'.$this->company_id == $prev_company_id && $prev_year == $date_code) ? ((int)$last_number + 1) : 1;

                $formatted_number = str_pad($number, 3, '0', STR_PAD_LEFT);

                $control_number = "PCL-0$this->company_id-$date_code-$formatted_number";
            }

        } while(PettyLiquid::withTrashed()->where('control_number', $control_number)->exists());

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
                    <h4>Name: <b>{{ ($pca_form->name ?? '' )}}</b></h4>
                    <h4 class="mb-3">Cost Center: <b>{{ $cost_center->name ?? '' }}</b></h4>
                    <h4>Petty Cash Advance Ref No.: <b>{{ ($pca_form->control_number ?? '' )}}</b></h4>
                </div>
                <div class="col-6">
                    <h4 class="mb-3">Date Submitted: <b>{{ date('F d, Y') }}</b></h4>
                    <h4>Petty Cash Advance Amount:
                        <b>₱{{  number_format($pca_amount ?? 0.00 , 2) }}</b>
                    </h4>
      
                </div>
         
            </div>
        

            <table class="table table-striped text-center" id="summaryTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Disbursement Particulars</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['desc'] }}</td>
                            <td>{{ number_format($item['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-right">TOTAL LIQUIDATION AMOUNT</th>
                        <th><h3>₱{{ number_format($total_amount, 2) }}</h3></th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-right">AMOUNT RETURNED </th>
                        <th><h3>₱{{ number_format($balance, 2) }}</h3></th>
                    </tr>
                </tfoot>

            </table>
            <div class="row text-left mb-3">
                <div class="col-6">
                    <h4>Receipt File Name: <b>{{ ($data['file_name'] ?? '' )}}</b></h4>
                </div>
            </div>
            <div class="row text-center">
                <div class="col-6">
                    <h4>Prepared By: <br><b>{{ ($user->name ?? '' )}}</b></h4>
                </div>
                <div class="col-6">
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