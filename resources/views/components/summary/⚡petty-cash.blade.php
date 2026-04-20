<?php

use Livewire\Component;

use App\Models\Company;
use App\Models\PettyCash;
use App\Models\PettyCashItem;
use App\Models\Form;
use App\Models\User;
use App\Models\Department;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

new class extends Component
{
    public $control_number, $company_id, $forms, $user, $form_id=1, $cost_center, $total_amount = 0;
    public $data = [], $items = [];

    protected $listeners = ['loadPcaSummary' => 'loadData'];

    public function loadData($data, $items)
    {
        $this->user = Auth::user();

        $this->data = $data;
        $this->items = $items;
        $this->company_id = $data['company_id'];
        $this->form_id = $data['form_id'];

        $this->forms = Form::findOrFail(decrypt($data['form_id']));
        $this->cost_center = User::where('id', $data['cost_center'])->first();

        $this->total_amount = collect($items)->sum('amount');
        
        if(!empty($data['control_number'])){
            $this->control_number = $data['control_number'];

        } else {
            $this->control_number = $this->generateControlNumber();
        }

        Session::put('pca_item', [
            'control_number' => $this->control_number,
            'data' => $this->data,
            'items' => $this->items,
            'total_amount' => $this->total_amount,
        ]);

    }

    private function generateControlNumber() {
        $date_code = date('Y');
        do {
            $control_number = 'PCA-0'.$this->company_id.'-'.$date_code.'-001';

            $rca = PettyCash::withTrashed()->orderBy('control_number', 'DESC')->where('company_id', $this->company_id)->first();
            
            if(!empty($rca->control_number)) {
                $latest_control_number = $rca->control_number;
                list(,$prev_company_id, $prev_year, $last_number) = explode('-', $latest_control_number);

                $number = ('0'.$this->company_id == $prev_company_id && $prev_year == $date_code) ? ((int)$last_number + 1) : 1;

                $formatted_number = str_pad($number, 3, '0', STR_PAD_LEFT);

                $control_number = "PCA-0$this->company_id-$date_code-$formatted_number";
            }

        } while(PettyCash::withTrashed()->where('control_number', $control_number)->where('company_id', $this->company_id)->exists());

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
                    <h4>Name: <b>{{ ($data['name'] ?? '' )}}</b></h4>
                </div>
                <div class="col-6">
   
                    <h4>Cost Center: <b>{{ $cost_center->name ?? '' }}</b></h4>
                    <h4 class="mb-3">Date Submitted: <b>{{ date('F d, Y') }}</b></h4>
                    <h2>Total Amount:
                        <b>₱{{  number_format($total_amount ?? 0.00 , 2) }}</b>
                    </h2>
                </div>
         
            </div>
        

            <table class="table table-striped text-center" id="summaryTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Decription</th>
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
                        <th colspan="2" class="text-end">TOTAL</th>
                        <th>₱{{ number_format($total_amount, 2) }}</th>
                    </tr>
                </tfoot>

            </table>
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