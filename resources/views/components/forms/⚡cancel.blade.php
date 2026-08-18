<?php

use Livewire\Component;
use App\Models\ProductSample;
use App\Models\GatePass;
use App\Models\ProductSampleItem;
use App\Models\Company;
use App\Models\Form;
use App\Models\User;
use App\Models\AllForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $forms, $user, $form_id, $psrf_gate_pass;
    public $data = [];

    protected $listeners = ['cancelForm' => 'loadData'];

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
        <div class="modal-header bg-danger">
            <h5 class="modal-title text-white">
                <i class="fas fa-exclamation-triangle mr-2"></i> Confirm Cancellation
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @if(!empty($forms))
        <form action="{{ route('myforms.cancel',encrypt($forms->id)) }}" method="POST" id="cancel_form">
            @csrf
            <div class="modal-body text-center p-4">
                <i class="fas fa-times-circle text-danger fa-4x mb-3"></i>
                
                <h4 class="mb-2">Are you sure you want to cancel this form request?</h4>
                <p class="text-muted">This action cannot be undone once confirmed.</p>

                <!-- Optional: Reason for cancellation -->
                <div class="form-group text-left mt-4">
                    <label for="cancellation_reason" class="font-weight-bold">
                        Reason for Cancellation <span class="text-danger">*</span>
                    </label>
                    <textarea 
                        name="remarks" 
                        id="cancellation_reason" 
                        rows="3" 
                        class="form-control" 
                        placeholder="Please state why you are cancelling this form..." 
                        required></textarea>
                </div>
            </div>

            <div class="modal-footer justify-content-between bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-ban mr-1"></i> No, Keep It
                </button>
                
                <button type="submit" class="btn btn-danger btn-confirm-cancel">
                    <i class="fas fa-check mr-1"></i> Yes, Cancel Request
                </button>
            </div>
        </form>
        @endif

    </div>
</div>