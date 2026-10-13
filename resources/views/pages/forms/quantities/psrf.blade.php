<form action="{{ route('scm.psrf', encrypt($all_form->id)) }}" method="POST" id="update" enctype="multipart/form-data">
    <div class="card-body">
        @csrf          
        
        <div class="col-lg-12 text-center">
            <h3 class="text-right text-uppercase">
                @if($all_form->status == 'approved')
                    <span class="badge badge-success"><b>Approved</b></span>
                @endif
            </h3>
            <div class="form-group">
                <h4><b>{{ $all_form->model->control_number }}</b></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Company: </label>
                    <h4><b>{{ $all_form->model->company->name }}</b></h4>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Requested By: </label>
                    <h4><b>{{ $all_form->model->requested_by }}</b></h4>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Customer: </label>
                    <h4><b>{{ $all_form->model->customer }}</b></h4>
                </div>
            </div>
        <input type="hidden" name="form_id"  value="{{ encrypt($form->id) }}">
        <input type="hidden" name="control_number"  value="{{ $all_form->model->control_number }}">
        <input type="hidden" name="date_submitted"  value="{{ date('Y-m-d') }}">
        <input type="hidden" name="activity_name"  value="{{ $all_form->model->activity_name }}">
        <input type="hidden" name="warehouse"  value="{{ $all_form->model->warehouse }}">
        <input type="hidden" name="requested_by"  value="{{ $all_form->model->requested_by }}">
        <input type="hidden" name="customer"  value="{{ $all_form->model->customer }}">
        <input type="hidden" name="recipient"  value="{{ $all_form->model->recipient }}">
        <input type="hidden" name="program_date"  value="{{ $all_form->model->program_date }}">
        <input type="hidden" name="objective"  value="{{ $all_form->model->objective }}">
        <input type="hidden" name="special_instructions"  value="{{ $all_form->model->special_instructions }}">
        <input type="hidden" name="company_id"  value="{{ $all_form->model->company_id }}">

        </div>  
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Activity Name: </label>
                    <h4><b>{{ $all_form->model->activity_name }}</b></h4> 
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Warehouse: </label>
                    <h4><b>{{ $all_form->model->warehouse }}</b></h4>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Recipient: </label>
                    <h4><b>{{ $all_form->model->recipient }}</b></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Program Date: </label>
                    <h4><b>{{ date('F d, Y', strtotime($all_form->model->program_date ?? '')) }}</b></h4>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Objective: </label>
                    <h4><b>{{ $all_form->model->objective }}</b></h4>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Special Instructions: </label>
                    <h4><b>{{ $all_form->model->special_instructions }}</b></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <input type="hidden" id="department" name="department" value="{{ $all_form->user->department->name }}">
            
            <div class="col-md-12">
                <label class="mb-0">Items <small class="text-danger font-italic text-bold">(required)</small></label>
                <table class="table table-responsive table-bordered text-center" id="dynamicTable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th style="min-width: 200px;">Item Code</th>
                            <th style="min-width: 350px;">Item Description</th>
                            <th>UOM</th>
                            <th>Qty</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    @php
                        $num = 1;
                    @endphp
                    <tbody >
                        @foreach ($all_form->model->psrf_form_item()->get() as $index => $item)
                        <tr>
                            <td class="row-number">{{ $index + 1 }}</td>
                            <td ><input type="text" name="items[{{ $index }}][sku-select]" form="update" value="{{ $item['item_code'] }}" style="width: 100%;" class="form-control text-center sku-select" disabled/></td>             
                            <td ><input type="text" name="items[{{ $index }}][desc]" value="{{ $item['item_description'] }}" class="form-control text-center desc" disabled/></td>             
                            <td ><input type="text" name="items[{{ $index }}][uom]" value="{{ $item['uom'] }}" class="form-control text-center uom" disabled/></td>             
                            <td>
                                <input type="number" name="items[{{ $index }}][qty]" form="update" value="{{ $item['quantity'] }}" class="form-control text-center qty" value="1" min="1"/>
                            </td>
                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}" form="update">
                            <input type="hidden" name="items[{{ $index }}][item_code]" value="{{ $item['item_code'] }}" form="update">
                            
                            <td><input type="text" name="items[{{ $index }}][remarks]" value="{{ $item['remarks'] }}" class="form-control text-center remarks" disabled/></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card-footer text-right">
        <input type="hidden" id="status" name="status" form="update" value="pending">

        <a href="#" title="update" class="btn-update btn btn-primary">Update</a>

    </div>
</form>


@push('js')
<script>
    let i = 0;

    $(document).on('input', '.qty', function() {
        let val = parseFloat($(this).val());
        let $row = $(this).closest('tr');
        updateRowQuantity($row);
        calculateTotals();
        
        if (val <= 0 || isNaN(val) || val > available) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }

    });


    function updateRowQuantity($row) {
        let data = $row.data('product-info');
        let uom = $row.find('.uom').val(); 
        let qty = $row.find('.qty').val(); 
        let $qtyInput = $row.find('.qty'); 
        let $totalQtyDisplay = $row.find('.totalQty'); 
        let $totalPriceDisplay = $row.find('.totalPrice'); 
        if (!data) return;

        let available = (uom === 'CS') ? data.quantity_cs : data.quantity_pcs;
        let availablePrice = (uom === 'CS') ? (data.conversion * qty) * data.price : data.price * qty;

        $totalQtyDisplay.val(available);
        $totalPriceDisplay.val(availablePrice.toFixed(2));

        $qtyInput.attr('max', available);

        if (parseInt($qtyInput.val()) > available) {
            $qtyInput.val(available);
            errorMessage = "Quantity adjusted to maximum available stock.";

            Swal.fire({
                icon: 'warning',
                title: 'Quantity Limit',
                text: errorMessage
            });
        }
    }

    function updateRowNumbers() {
        document.querySelectorAll("#dynamicTable tbody tr").forEach((row, index) => {
            row.querySelector(".row-number").textContent = index + 1;
        });
    }

    function calculateTotals() {
        let totalAmount = 0;

        document.querySelectorAll("#dynamicTable tbody tr").forEach(row => {
            let qty = parseFloat(row.querySelector(".qty").value) || 0;
            let price = parseFloat(row.querySelector(".totalPrice").value) || 0;

            totalAmount += price;
        });

        document.getElementById("totalAmount").textContent = totalAmount.toFixed(2);
    }
    calculateTotals();
    updateRowNumbers();

</script>

<script>
    $(function() {
        $('body').on('click', '.btn-confirm', function(e) {
            e.preventDefault();
            const department = document.querySelector('input[name="department"]'); 
            const dept = department.value.trim();

            Swal.fire({
                title: "Final Confirmation",
                text: "Are you sure you want to submit this Product Sample Request Form?",
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
                    if (dept === 'MARKETING') {
                        $('#status').val('confirming');
                    } else {
                        $('#status').val('endorsement');
                    }
                    $('#update_psrf').submit();

                }
                });
        });
    });
</script>

<script>
    $(function() {
        $('body').on('click', '.btn-preview', function(e) {
            let hasDuplicate = false;
            let hasError = false;
            let errorMessage = "";
        
            const names = [];
            const inputs = document.querySelectorAll('select[name*="[sku-select]"]');

            inputs.forEach(input => {
                const name = input.value.trim().toLowerCase();
                if (name !== "") {
                    if (names.includes(name)) {
                        input.classList.add("is-invalid");
                        errorMessage = "Duplicate Item Code are not allowed in the same form!";

                        hasDuplicate = true;
                    } else {
                        input.classList.remove("is-invalid");
                        names.push(name);
                    }
                }
            });


            $('.qty').each(function() {
            let val = parseFloat($(this).val());

            if (isNaN(val) || val <= 0) {
                    hasError = true;
                    $(this).addClass('is-invalid'); 
                    errorMessage = "Quantity must be greater than 0.";
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            if (hasError || hasDuplicate) {
                e.stopPropagation(); 

                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Input',
                    text: errorMessage,
                    confirmButtonColor: '#d33'
                });

                return false; 
            }

            e.preventDefault();


            let data = {
                form_id: document.querySelector('input[name="form_id"]').value || "-",
                company_id: document.querySelector('input[name="company_id"]').value || "-",
                control_number: document.querySelector('input[name="control_number"]').value,
                requested_by: document.querySelector('input[name="requested_by"]').value || "-",
                customer: document.querySelector('input[name="customer"]').value || "-",
                department: document.querySelector('input[name="department"]').value || "-",
                recipient: document.querySelector('input[name="recipient"]').value || "-",
                activity: document.querySelector('input[name="activity_name"]').value || "-",
                program: document.querySelector('input[name="program_date"]').value || "-",
                warehouse: document.querySelector('input[name="warehouse"]').value || "-",
                objective: document.querySelector('input[name="objective"]').value || "-",
                special: document.querySelector('input[name="special_instructions"]').value || "-",
            };

            let items = [];
            document.querySelectorAll('#dynamicTable tbody tr').forEach(row => {
                let sku = row.querySelector(".sku-select").value || "-";
                let desc = row.querySelector(".desc").value || "-";
                let uom = row.querySelector(".uom").value || "-";
                let qty = parseFloat(row.querySelector(".qty").value) || 1;
                let remarks = row.querySelector(".remarks").value || "-";
                let amount = parseFloat(row.querySelector(".totalPrice").value) || 1;

                items.push({ sku, desc, uom, qty, remarks, amount });
            });
   
            Livewire.dispatch('loadPsrfSummary',{ data, items });
            $('#modal-preview').modal('show');
        });
    });
</script>

@endpush