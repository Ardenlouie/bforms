<?php

use Livewire\Component;
use App\Models\GatePass;
use App\Models\GatePassItem;
use App\Models\Company;
use App\Models\Form;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $control_number, $company_id, $forms, $user, $form_id=1;
    public $data = [], $items = [];
    public $receivers = [];

    protected $listeners = ['multipleGateSummary' => 'loadData'];

    public function loadData($data, $items)
    {
        $this->reset('receivers');

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

        Session::put('gate_item', [
            'items' => $this->items,
            'control_number' => $this->control_number,
            'data' => $this->data,
        ]);

        $this->receivers[] = [
            'control_number' =>  $this->control_number,
            'name' => '',
            'items' =>  $this->items 
        ];

    }

    public function addReceiver()
    {
        $existingNumbers = collect($this->receivers)->pluck('control_number')->toArray();

        $newControlNumber = $this->getNextIncrementedNumber(end($existingNumbers));

        $newItems = collect($this->receivers[0]['items'])->map(function($item) {
            $item['quantity_release'] = 0; 
            return $item;
        })->toArray();

        $this->receivers[] = [
            'control_number' => $newControlNumber,
            'name' => '',
            'items' => $newItems
        ];
    }

    private function getNextIncrementedNumber($currentNumber)
    {
        $parts = explode('-', $currentNumber);
        
        $lastPart = (int) end($parts);
        $newNumber = $lastPart + 1;
        
        $parts[count($parts) - 1] = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        
        return implode('-', $parts);
    }

    public function removeReceiver($index)
    {
        unset($this->receivers[$index]);
        $this->receivers = array_values($this->receivers);
    }

    private function generateControlNumber() {
        $date_code = date('Y');
        do {
            $control_number = 'GP-0'.$this->company_id.'-'.$date_code.'-001';

            $gate = GatePass::withTrashed()->orderBy('control_number', 'DESC')->where('company_id', $this->company_id)
                ->first();
            if(!empty($gate->control_number)) {
                $latest_control_number = $gate->control_number;
                list(,$prev_company_id, $prev_year, $last_number) = explode('-', $latest_control_number);

                $number = ('0'.$this->company_id == $prev_company_id && $prev_year == $date_code) ? ((int)$last_number + 1) : 1;

                $formatted_number = str_pad($number, 3, '0', STR_PAD_LEFT);

                $control_number = "GP-0$this->company_id-$date_code-$formatted_number";
            }

        } while(GatePass::withTrashed()->where('control_number', $control_number)->where('company_id', $this->company_id)->exists());

        return $control_number;
    }

    public function save()
    {
        foreach ($this->receivers as $receiverData) {
            $gatePass = GatePass::create([
                'control_number' => $receiverData['control_number'],
                'recipient'      => $receiverData['name'],
                'purpose'      => $this->data['purpose'],
                'date_submitted'      => date('Y-m-d'),
                'form_id'      => $this->form_id,
                'psrf_form_id'      => $this->data['psrf_form_id'],
                
            ]);

            foreach ($receiverData['items'] as $item) {
                if ($item['quantity_release'] > 0) {
                    $gate_items = GatePassItem::create([
                        'gate_pass_id' => $gatePass->id,
                        'item_description' => $items['item_description'],
                        'uom' =>  $items['uom'],
                        'quantity' =>  $items['quantity_release'],
                        'quantity_release' =>  $items['quantity_release'],
                        'balance' =>  $items['quantity_release'],
                        'remarks' =>  $items['remarks'],
                    ]);
                }
            }
            
        }

        session()->flash('success', 'All gate passes generated successfully.');
        return redirect()->route('gate-pass.index');
    }
};
?>

<div>
    <div class="modal-content">
        <div class="modal-header bg-success">
            <h4 class="modal-title text-uppercase" >MULTIPLE {{ $forms->name ?? '' }} SUMMARY</h4>
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
            </div>
            <div class="row text-left">
                <div class="col-6">
                    <h4>Purpose: <b>{{ ($data['purpose'] ?? '' )}}</b></h4>
                    
                </div>
                <div class="col-6 text-right">
                    <h4>Date Submitted: <b>{{ date('F d, Y') }}</b></h4>
                </div>
            </div>
            @foreach($receivers as $rIndex => $receiver)
            <div class="card mb-4 border-primary">
                <div class="card-body">
                    <div class="row text-left">
                        <div class="col-6">
                            <h4>Received By:                 
                                <input type="text" class="form-control" name="received_by" form="add_gate" width="50px" value="{{ ($all_form->model->recipient ?? '' )}}"> 
                            </h4>
                        </div>
                        <div class="col-6 text-right">
                            <h4>Ref. No.: <b>{{ $receiver['control_number'] }}</b></h4>
                            @if($rIndex > 0)
                                <a class="btn btn-danger btn-sm" wire:click="removeReceiver({{ $rIndex }})">
                                    <i class="fa fa-trash"></i> Remove Receiver
                                </a>
                            @endif
                        </div>
                    </div>
                    <table class="table table-striped text-center" id="summaryTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Release Item</th>
                                <th>UOM</th>
                                <th>Qty</th>
                                <th>Remaining Qty</th>
                                <th>Qty to Receive</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item['desc'] }}</td>
                                    <td>{{ $item['uom'] }}</td>
                                    <td>{{ $item['qty'] }}</td>
                                    <td class="text-bold text-danger align-middle">
                                        {{-- Total Qty minus the SUM of quantity_release for this item across ALL receivers --}}
                                        {{ 
                                            (int)$item['qty'] - collect($receivers)->sum(function($r) use ($index) {
                                                return (int)($r['items'][$index]['quantity_release'] ?? 0);
                                            }) 
                                        }}
                                    </td> 
                                    <td class="align-middle">
                                        {{-- Update model to match nested receivers structure --}}
                                        <input type="number" 
                                            wire:model.lazy="receivers.{{ $rIndex }}.items.{{ $index }}.quantity_release" 
                                            class="form-control text-center qty" 
                                            min="0" 
                                            {{-- Set dynamic max based on what remains globally --}}
                                            max="{{ 
                                                (int)$item['qty'] - collect($receivers)->where('control_number', '!=', $receiver['control_number'])->sum(function($r) use ($index) {
                                                    return (int)($r['items'][$index]['quantity_release'] ?? 0);
                                                }) 
                                            }}" 
                                            oninput="if(parseInt(this.value) > parseInt(this.max)) this.value = this.max;">

                                        @if(($item['quantity_release'] ?? 0) < 0)
                                            <span class="text-danger small">Must be greater than or equal to 0</span>
                                        @endif
                                    </td>
                                    <td>{{ $item['remarks'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
            <div class="text-center mb-5">
                <button type="button" class="btn btn-primary" wire:click="addReceiver">
                    <i class="fa fa-plus"></i> {{__('Add Another Receiver')}}
                </button>
            </div>
            <div class="row text-center">
                <div class="col-6">
                    <h4>Prepared By: <br><b>{{ ($user->name ?? '' )}}</b></h4>
                </div>
                <div class="col-6">
                    <h4>Approved By: 
                        <br><b>
                            {{ ($forms->department->name ?? '' )}} Department
                        </b></h4>
                </div>
            </div>
        </div>
        

        <div class="modal-footer">
            <a class="btn-draft btn btn-secondary">Save as Draft</a>

            <a class="btn-confirm-multiple btn btn-success">Submit</a>
    
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>