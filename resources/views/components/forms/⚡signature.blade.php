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

    protected $listeners = ['viewSignatures' => 'loadData'];

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
        <div class="modal-header bg-success">
            <h4 class="modal-title text-bold">SIGNATURES</h4>
        </div>
        @if(!empty($forms))
        <div class="modal-body">
            <table class="table table-bordered">
                <thead class="text-center">
                    <tr>
                        <th></th>
                        <th>NAME</th>
                        <th>DATE</th>
                        <th>REMARKS</th>
                    </tr>
                </thead>
                <tbody class="align-middle text-center text-uppercase">
                    <tr>
                        <td>Prepared By</td>
                        <td>{{ $forms->user->name ?? ''}}</td>
                        <td>{{ $forms->model->date_submitted }}</td>
                        <td>
                            @if(!empty($forms->model->date_submitted))
                            <span class="badge badge-success"><b>Signed</b></span>
                            @endif
                        </td>
                    </tr>
                    @if(!empty($forms->endorser))
                    <tr>
                        <td>Endorsed By</td>
                        <td>{{ $forms->endorsed->name ?? ''}}</td>
                        <td>
                            @if(!empty($forms->date_endorsed) && $forms->status != 'declined')
                            {{ $forms->date_endorsed }}
                            @endif
                        </td>
                        <td>
                            @if(!empty($forms->date_endorsed) && $forms->status != 'declined')
                            <span class="badge badge-success"><b>Signed</b></span>
                            @endif
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td>Approved By</td>
                        <td>{{ $forms->approved->name ?? ''}}</td>
                        <td>
                            @if(!empty($forms->date_approved) && $forms->status != 'declined')
                                {{ $forms->date_approved }}
                            @endif
                        </td>
                        <td>
                            @if(!empty($forms->date_approved) && $forms->status != 'declined')
                            <span class="badge badge-success"><b>Signed</b></span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
        @endif
        <div class="modal-footer text-right">
            <button type="button" class="btn btn-default" data-dismiss="modal">Exit</button>
 
        </div>
    </div>
</div>
