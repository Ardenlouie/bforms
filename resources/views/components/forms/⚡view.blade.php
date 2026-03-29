<?php

use Livewire\Component;
use App\Models\ProductSample;
use App\Models\ProductSampleItem;
use App\Models\Company;
use App\Models\Form;
use App\Models\AllForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $forms, $user, $form_id;
    public $data = [];

    protected $listeners = ['viewForm' => 'loadData'];

    public function loadData($data)
    {
        $this->user = Auth::user();

        $this->form_id = $data['id'];
        $this->forms= AllForm::where('id', $this->form_id)->first();


    }
};
?>

<div>
    <div class="modal-content">
        @if(!empty($forms))
        <div class="modal-body ">
            @include('pages.forms.views.'.$forms->form->prefix ) 
        </div>
        @endif
        <div class="modal-footer text-right bg-gradient-navy">
            <button type="button" class="btn btn-default" data-dismiss="modal">Exit</button>
 
        </div>
    </div>
</div>