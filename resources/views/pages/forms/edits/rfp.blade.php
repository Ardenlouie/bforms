<form action="{{ route('update.rfp',encrypt($all_form->id)) }}" method="POST" id="update_rfp">
    <div class="card-body">
        @csrf          
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Company</label>
                    {{ html()->select('company_id', $companies, $all_form->model->company_id)->class(['form-control', 'form-control', 'is-invalid' => $errors->has('company_id')]) }}
                    <small class="text-danger">{{$errors->first('company_id')}}</small>
                </div>
            </div>
        <input type="hidden" name="form_id"  value="{{ encrypt($form->id) }}">
        <input type="hidden" name="control_number"  value="{{ $all_form->model->control_number }}">
        <input type="hidden" name="date_submitted"  value="{{ date('Y-m-d') }}">

        </div>  
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="mb-0">Payable to</label>
                    <input type="text" class="form-control" name="payable" form="update_rfp" value="{{ $all_form->model->payable }}"> 
                    <small class="text-danger">{{$errors->first('payable')}}</small>
                </div>
            </div>
            <div class="col-lg-2"></div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Department</label>
                    {{ html()->select('department_id', $departments ,$all_form->model->department_id)->class(['form-control', 'form-control text-uppercase', 'is-invalid' => $errors->has('department_id')]) }}
                    <small class="text-danger">{{$errors->first('department_id')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Amount</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" id="currency_toggle">
                                ₱ (PHP)
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item currency-select" href="#" data-symbol="₱" data-code="PHP">₱ Pesos (PHP)</a>
                                <a class="dropdown-item currency-select" href="#" data-symbol="$" data-code="USD">$ Dollars (USD)</a>
                            </div>
                        </div>
                        
                        <input type="hidden" name="currency" id="currency_code" value="{{ $all_form->model->currency }}" form="update_rfp">
                        
                        <input type="number" 
                            class="form-control" 
                            name="amount" 
                            id="amount_input"
                            form="update_rfp" 
                            step="0.01" 
                            min="0" 
                            value="{{ $all_form->model->amount }}" 
                            placeholder="0.00">
                    </div>
                    @if($errors->has('amount'))
                        <small class="text-danger">{{ $errors->first('amount') }}</small>
                    @endif
                </div>
            </div>
            <div class="col-lg-3"></div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Cost Center</label>
                    <select id="cost_center" name="cost_center" class="form-control" style="width: 100%;" form="update_rfp" value="{{$all_form->model->cost_center}}"></select>
                    <small class="text-danger">{{$errors->first('cost_center')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">
                    <label class="mb-0">Purpose</label>
                    <input type="text" class="form-control" name="purpose" form="update_rfp" value="{{$all_form->model->purpose}}"> 
                    <small class="text-danger">{{$errors->first('purpose')}}</small>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="form-group">
                    <label class="mb-0">Instructions</label>
                    <input type="text" class="form-control" name="instructions" form="update_rfp" value="{{$all_form->model->instructions}}"> 
                    <small class="text-danger">{{$errors->first('instructions')}}</small>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Requested By</label>
                    <input type="text" class="form-control" name="user_id" value="{{$all_form->user->name}}" form="update_rfp" disabled> 
                    <input type="hidden" name="user_id" value="{{$all_form->user_id}}" form="update_rfp"> 
                    <small class="text-danger">{{$errors->first('user_id')}}</small>
                </div>
            </div>
            <div class="col-lg-4">
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Approved By</label>
                    <select id="user_select" name="approver" class="form-control" style="width: 100%;" form="update_rfp" value="{{$all_form->approver}}"></select>
                    <small class="text-danger">{{$errors->first('approver')}}</small>
                </div>

                
            </div>
        </div>
    </div>
    <div class="card-footer text-right">
        <input type="hidden" id="status" name="status" form="update_rfp" value="pending">
        <a class="btn-draft btn btn-secondary">Save as Draft</a>

        <a href="#" title="preview" class="btn-preview btn btn-primary">Preview</a>

        <div class="modal fade" id="modal-preview">
            <div class="modal-dialog modal-xl">
                <livewire:summary.request-payment  />
            </div>
        </div>
    </div>
</form>


@push('js')
<script>
$(function() {
    $('.currency-select').on('click', function(e) {
        e.preventDefault();
        
        let symbol = $(this).data('symbol');
        let code = $(this).data('code');
        
        // Update the button display
        $('#currency_toggle').text(symbol + ' (' + code + ')');
        
        // Update the hidden input value for the backend
        $('#currency_code').val(code);
        
        // Optional: Focus back on the amount input for better UX
        $('#amount_input').focus();
    });
});
</script>
<script>
    $(function() {
        $('body').on('click', '.btn-draft', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Saving Draft...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    $('#status').val('draft');
                    $('#update_rfp').submit();
                }
            });
        });
    });
</script>

<script>
    $(function() {
        $('body').on('click', '.btn-confirm', function(e) {
            e.preventDefault();

            Swal.fire({
                title: "Final Confirmation",
                text: "Are you sure you want to submit this Request for Payment Form?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#0ba236",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, submit it!",
                cancelButtonText: "No",
                }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                    allowOutsideClick: false,
                    title: "Submitted!",
                    text: "Your form has been submitted.",
                    icon: "success"
                    });

                    Swal.showLoading();
                    $('#status').val('approval');
                    $('#update_rfp').submit();

                }
                });
        });
    });
</script>

<script>
    $(function() {
        $('body').on('click', '.btn-preview', function(e) {
            e.preventDefault();

            let data = {
                control_number: document.querySelector('input[name="control_number"]').value,
                form_id: document.querySelector('input[name="form_id"]').value || "-",
                company_id: document.querySelector('select[name="company_id"]').value || "-",
                department_id: document.querySelector('select[name="department_id"]').value || "-",
                payable: document.querySelector('input[name="payable"]').value || "-",
                amount: document.querySelector('input[name="amount"]').value || 0.00,
                cost_center: document.querySelector('select[name="cost_center"]').value || "-",
                purpose: document.querySelector('input[name="purpose"]').value || "-",
                instructions: document.querySelector('input[name="instructions"]').value || "-",
                approver: document.querySelector('select[name="approver"]').value || "-",
                currency: document.querySelector('input[name="currency"]').value || "-",
            };
   
            Livewire.dispatch('loadRfpSummary',{ data });
            $('#modal-preview').modal('show');
        });
    });
</script>
@endpush