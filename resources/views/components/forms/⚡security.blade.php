<?php

use Livewire\Component;
use App\Models\GatePass;
use App\Models\GatePassItem;
use App\Models\Company;
use App\Models\Form;
use App\Models\AllForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Notifications\CheckedFormNotification;

new class extends Component
{
    public $forms, $user, $form_id;
    public $data = [];
    public $items = [];

    protected $listeners = [
        'checkedForm' => 'loadData',
        'submitForm' => 'submit',
    ];


    public function loadData($data)
    {
        $this->user = Auth::user();

        $this->form_id = $data['id'];
        $this->forms= AllForm::where('id', $this->form_id)->first();
        $this->items = $this->forms->model->gate_pass_item()->get()
        ->map(function($item) {
            return [
                'id' => $item->id,
                'item_description' => $item->item_description,
                'uom' => $item->uom,
                'remarks' => $item->remarks,
                'balance' => $item->balance,
                'total_requested' => $item->quantity, // The original amount
                'quantity_release' => $item->quantity_release, // The user input
            ];
        })->toArray();

    }


    public function submit($data)
    {
        $rules = [];
        foreach ($this->items as $index => $item) {
            // Ensure they can't release more than the total requested
            $rules["items.{$index}.quantity_release"] = "required|numeric|min:0|max:{$item['balance']}";
        }
        
        $this->validate($rules);

        if (!empty($data['imageUrl'])) {
            $image = $data['imageUrl'];
            $receiver = $data['receiverName'];
            
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            
            $imageName = $receiver.'-capture_' . time() . '.png';
            
            Storage::disk('uploads')->put('gate-pass-images/to-release/' .$this->forms->model->id. '/' . $imageName, base64_decode($image));
        
        }

        $allBalancesZero = true;

        foreach ($this->items as $index => $item) {
            $remaining_balance = $item['balance'] - $item['quantity_release'];

            \App\Models\GatePassItem::where('id', $item['id'])->update([
                'quantity_release' => $item['quantity_release'],
                'balance' => $remaining_balance
            ]);

            if ($remaining_balance > 0) {
                $allBalancesZero = false;
            }
        }

        if ($allBalancesZero) {
            $this->forms->update(['status' => 'checked']);

            $all_forms = $this->forms;

            $all_forms->user->notify(new CheckedFormNotification($all_forms));
            $all_forms->approved->notify(new CheckedFormNotification($all_forms));

        } else {
            $this->forms->update(['status' => 'partially_released']);
        }

        $control_number = $this->forms->model->control_number;
        $form_name = $this->forms->form->name;

        activity('checked')
            ->performedOn($this->forms)
            ->log('Security has check '.$form_name.' ['.$control_number.']');

        return redirect()->route('security', encrypt($this->forms->id))->with([
            'message_success' => $form_name.' ['.$control_number.'] quantity was released!'
        ]);
    }

};
?>

<div>
    <div class="modal-content">
        <div class="modal-header bg-success">
            <h4 class="modal-title text-bold">CHECKING</h4>
        </div>
        @if(!empty($forms))
            <form wire:submit.prevent="submit">
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="text-center">
                            <tr>
                                <th>#</th>
                                <th>RELEASE ITEM</th>
                                <th>UOM</th>
                                <th>QTY</th>
                                <th>REMAINING QTY</th>
                                <th>ENTER QTY TO RELEASE</th>
                                <th>-</th>
                            </tr>
                        </thead>
                        @if($forms->form->prefix == 'pgp')
                        <tbody class="align-middle text-center text-uppercase">
                            @foreach($items as $index => $item)
                            @php
                                list($sku, $desc, $size) = explode(' - ', $item['item_description']);
                            @endphp
                            <tr wire:key="item-{{ $item['id'] }}">
                                <td class="align-middle">{{ $index + 1 }}</td>
                                <td class="align-middle">
                                    <img src="{{ asset('images/AllProducts/'.$sku.'.png') }}" alt="SKU IMAGE" height="100" width="100"><br>
                                    {{ $item['item_description'] }}
                                </td>
                                <td class="align-middle">{{ $item['uom'] }}</td>
                                <td class="align-middle">{{ $item['total_requested'] }}</td>
                                <td class="text-bold text-success align-middle">
                                    {{ $item['balance'] }}
                                </td> 
                                <td class="align-middle">
                                    <input type="number" wire:model.lazy="items.{{ $index }}.quantity_release" class="form-control text-center qty" 
                                        min="0" max="{{ $items[$index]['balance'] }}" 
                                        oninput="if(parseInt(this.value) > parseInt(this.max)) this.value = this.max;">
                                    @if(($item['quantity_release'] ?? 0) < 0)
                                        <span class="text-danger small">Must be greater than or equal to 0</span>
                                    @endif
                                </td>
                                <td class="text-bold text-danger align-middle">
                                    {{ (int)$item['balance'] - (int)$item['quantity_release'] }}
                                </td> 
                            </tr>

                            @endforeach
                        </tbody>
                        @elseif($forms->form->prefix == 'gate')
                        <tbody class="align-middle text-center text-uppercase">
                            @foreach($items as $index => $item)
                            <tr wire:key="item-{{ $item['id'] }}">
                                <td class="align-middle">{{ $index + 1 }}</td>
                                <td class="align-middle">{{ $item['item_description'] }}</td>
                                <td class="align-middle">{{ $item['uom'] }}</td>
                                <td class="align-middle">{{ $item['total_requested'] }}</td>
                                <td class="text-bold text-success align-middle">
                                    {{ $item['balance'] }}
                                </td> 
                                <td class="align-middle">
                                    <input type="number" wire:model.live="items.{{ $index }}.quantity_release" class="form-control text-center">
                                    @error("items.{$index}.quantity_release")
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td class="text-bold text-danger align-middle">
                                    {{ (int)$item['balance'] - (int)$item['quantity_release'] }}
                                </td> 
                            </tr>

                            @endforeach
                        </tbody>
                        @endif
                    </table>
                </div>
            </div>
                <div class="modal-footer text-right">
                    <a href="#" title="checked" class="btn-checked btn btn-success float-right btn-lg"> 
                        <i class="fas fa-check-circle"></i> Release Items</a>
                    @if (session()->has('message'))
                        <span class="text-success mr-3 float-right animate__animated animate__fadeIn">
                            <i class="fas fa-check"></i> {{ session('message') }}
                        </span>
                    @endif
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    
                    
                </div>
        @endif
    </div>
</div>
