<form action="{{ route('store.rfp',encrypt($form->id)) }}" method="POST" id="add_rfp" enctype="multipart/form-data">
    <div class="card-body">
        @csrf          
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Company</label>
                    {{ html()->select('company_id', $companies,'')->class(['form-control', 'form-control', 'is-invalid' => $errors->has('company_id')]) }}
                    <small class="text-danger">{{$errors->first('company_id')}}</small>
                </div>
            </div>
        <input type="hidden" name="form_id"  value="{{ encrypt($form->id) }}">

        </div>  
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="mb-0">Payable to</label>
                    <input type="text" class="form-control" name="payable" form="add_rfp"> 
                    <small class="text-danger">{{$errors->first('payable')}}</small>
                </div>
            </div>
            <div class="col-lg-2"></div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Department</label>
                    {{ html()->select('department_id', $departments,'')->class(['form-control', 'form-control text-uppercase', 'is-invalid' => $errors->has('department_id')]) }}
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
                        
                        <input type="hidden" name="currency" id="currency_code" value="PHP" form="add_rfp">
                        
                        <input type="number" 
                            class="form-control" 
                            name="amount" 
                            id="amount_input"
                            form="add_rfp" 
                            step="0.01" 
                            min="0" 
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
                    <select id="cost_center" name="cost_center" class="form-control" style="width: 100%;" form="add_rfp"></select>
                    <small class="text-danger">{{$errors->first('cost_center')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">
                    <label class="mb-0">Purpose</label>
                    <input type="text" class="form-control" name="purpose" form="add_rfp"> 
                    <small class="text-danger">{{$errors->first('purpose')}}</small>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="form-group">
                    <label class="mb-0">Instructions</label>
                    <input type="text" class="form-control" name="instructions" form="add_rfp"> 
                    <small class="text-danger">{{$errors->first('instructions')}}</small>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    {{ html()->label(__('Upload Attachment'), 'file_name')->class(['mb-0']) }}
                    <input
                        form="add_rfp"
                        type="file"
                        id="file_name"
                        name="file_name"
                        accept="application/pdf"
                        class="form-control {{ $errors->has('file_name') ? 'is-invalid' : '' }}"
                    > 
                    <small class="text-danger">{{$errors->first('file_name')}}</small>
                </div>
                
            </div>
            
            <div class="col-lg-6">
                <div class="form-group">
                    <b>Attachment Preview</b>
                    <iframe
                        id="pdfPreview"
                        width="100%"
                        height="500"
                        style="border:1px solid #ccc;"
                    ></iframe>
                </div>
            </div>
        </div>
        
    </div>
    <div class="card-footer text-right">
        <input type="hidden" id="status" name="status" form="add_rfp" value="pending">
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
                    $('#add_rfp').submit();
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
                    $('#add_rfp').submit();

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
                form_id: document.querySelector('input[name="form_id"]').value || "-",
                company_id: document.querySelector('select[name="company_id"]').value || "-",
                department_id: document.querySelector('select[name="department_id"]').value || "-",
                payable: document.querySelector('input[name="payable"]').value || "-",
                file_name: document.querySelector('input[name="file_name"]').value || "-",
                amount: document.querySelector('input[name="amount"]').value || 0.00,
                cost_center: document.querySelector('select[name="cost_center"]').value || "-",
                purpose: document.querySelector('input[name="purpose"]').value || "-",
                instructions: document.querySelector('input[name="instructions"]').value || "-",
                currency: document.querySelector('input[name="currency"]').value || "-",
            };
   
            Livewire.dispatch('loadRfpSummary',{ data });
            $('#modal-preview').modal('show');
        });
    });
</script>

<script>
    document.getElementById('file_name').addEventListener('change', function () {
        const file = this.files[0];
        const iframe = document.getElementById('pdfPreview');

        if (file && file.type === 'application/pdf') {
            iframe.src = URL.createObjectURL(file);
        } else {
            iframe.src = '';
        }
    });
</script>

@endpush