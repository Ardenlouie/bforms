<form action="{{ route('store.rfp',encrypt($form->id)) }}" method="POST" id="add_rfp" enctype="multipart/form-data">
    <div class="card-body">
        @csrf          
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Company <small class="text-danger font-italic text-bold">(required)</small></label>
                    {{ html()->select('company_id', $companies,'')->class(['form-control', 'form-control', 'is-invalid' => $errors->has('company_id')]) }}
                    <small class="text-danger">{{$errors->first('company_id')}}</small>
                </div>
            </div>
            <div class="col-lg-4"></div>
            
            
        <input type="hidden" name="form_id"  value="{{ encrypt($form->id) }}">
        <!-- <input type="hidden" name="department_id"  value="{{ $requestor->department->id }}"> -->
        <!-- <input type="hidden" name="cost_center" id="cost_center_input" value="{{ strtoupper($requestor->department->head->name ?? '') }}"> -->
        
        </div>  
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Date of Access <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="date" class="form-control" name="date_access" value="{{ date('Y-m-d') }}"> 
                    <small class="text-danger">{{$errors->first('date_access')}}</small>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Actual Time-In <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="time" class="form-control" name="time_in" value="{{ date('H:i') }}"> 
                    <small class="text-danger">{{$errors->first('time_in')}}</small>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Actual Time-Out <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="time" class="form-control" name="time_out" value="{{ date('H:i') }}"> 
                    <small class="text-danger">{{$errors->first('time_out')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">
                    <label class="mb-0">Purpose of Access <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="text" class="form-control" name="purpose" form="add_rfp"> 
                    <small class="text-danger">{{$errors->first('purpose')}}</small>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    {{ html()->label(__('Upload Attachment'), 'file_name')->class(['mb-0']) }} <small class=" font-italic text-bold">(optional)</small>
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
        <input type="hidden" id="status" name="status" form="add_warf" value="pending">
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
<!-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Map department IDs to their respective Head names using Blade
        // Assumes $departmentList contains Department models with their 'head' relationship loaded
        const departmentHeads = @json(
            \App\Models\Department::with('head')->get()->pluck('head.name', 'id')
        );

        const departmentSelect = document.getElementById('department_select');
        const costCenterInput = document.getElementById('cost_center_input');

        // 2. Listen for dropdown changes
        departmentSelect.addEventListener('change', function () {
            const selectedDeptId = this.value;
            
            // Get the head name from our lookup map, or fallback to empty string
            const headName = departmentHeads[selectedDeptId] || '';
            
            // Set the uppercase value into cost_center input
            costCenterInput.value = headName.toUpperCase();
        });
    });
</script> -->
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

<script>
    $(document).ready(function() {

        let company = document.querySelector('select[name="company_id"]').value;

        $('#customer_cost_center_cash').select2({
            placeholder: "Select Employee Cost Center",
            allowClear: true,
            theme: "classic",
            ajax: {
                url: "{{ route('customer_cost_cash.ajax') }}", // Create this route in web.php
                dataType: 'json',
                delay: 250, // Wait 250ms before sending request (debounce)
                data: function (params) {
                    return {
                        search: params.term,
                        company_id: $('select[name="company_id"]').val()
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });
});
</script>
@endpush