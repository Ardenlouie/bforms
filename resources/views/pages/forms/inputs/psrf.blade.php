<form action="{{ route('store.psrf',encrypt($form->id)) }}" method="POST" id="add_psrf" enctype="multipart/form-data">
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
            <div class="col-lg-3"></div>
            
        <input type="hidden" name="form_id"  value="{{ encrypt($form->id) }}">

        </div>  
        <div class="row">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Requested By <small class="text-danger font-italic text-bold">(required)</small></label>
                    <select id="employee_cost_center" name="requested_by" class="form-control" style="width: 100%;" form="add_psrf"></select>
                    <small class="text-danger">{{$errors->first('requested_by')}}</small>
                </div>
            </div>
            <div class="col-lg-2"></div>
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Customer <small class="text-danger font-italic text-bold">(required)</small></label>
                    <select id="customer_cost_center" name="customer" class="form-control" style="width: 100%;" form="add_psrf"></select>
                    <small class="text-danger">{{$errors->first('customer')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Activity Name <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="text" class="form-control" name="activity_name" form="add_psrf"> 
                    <small class="text-danger">{{$errors->first('activity_name')}}</small>
                </div>
            </div>
            <div class="col-lg-3"></div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Warehouse <small class="text-danger font-italic text-bold">(required)</small></label>
                    <select class="form-control" name="warehouse" id="warehouse" form="add_psrf">
                        <option value="" disabled selected>-- Select Warehouse --</option>
                        <option value="">Office Warehouse</option>
                        <option value="CE-SDI">Cebu Warehouse</option>
                        <option value="DO-SDI">Davao Warehouse</option>
                    </select>
                    <small class="text-danger">{{$errors->first('warehouse')}}</small>
                </div>
            </div>
            
        </div>
        <div class="row">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Recipient <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="text" class="form-control" name="recipient" form="add_psrf"> 
                    <small class="text-danger">{{$errors->first('recipient')}}</small>
                </div>
            </div>
            <div class="col-lg-3"></div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Program Date <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="date" class="form-control" name="program_date" form="add_psrf" value="{{ date('Y-m-d') }}"> 
                    <small class="text-danger">{{$errors->first('program_date')}}</small>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Objective <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="text" class="form-control" name="objective" form="add_psrf"> 
                    <small class="text-danger">{{$errors->first('objective')}}</small>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="form-group">
                    <label class="mb-0">Special Instructions <small class=" font-italic text-bold">(optional)</small></label>
                    <input type="text" class="form-control" name="special_instructions" form="add_psrf"> 
                    <small class="text-danger">{{$errors->first('special_instructions')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <input type="hidden" id="department" name="department" value="{{ $requestor->department->name }}">
            <div class="col-md-12 table-responsive">
                <label class="mb-0">Items <small class="text-danger font-italic text-bold">(required)</small></label>
                <table class="table table-bordered text-center" id="dynamicTable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th style="min-width: 200px;">Item Code</th>
                            <th style="min-width: 300px;">Item Description</th>
                            <th style="min-width: 100px;">UOM</th>
                            <th>Qty</th>
                            <th style="min-width: 100px;">Amount</th>
                            <th>Remarks</th>
                            <th><button type="button" name="add" id="addBtn" class="btn btn-success"><i class="fa fa-plus"></i></button></th>
                        </tr>
                    </thead>
                    @php
                        $num = 1;
                    @endphp
                    <tbody >
                        <tr>
                            <td class="row-number">1</td>
                            <td><select name="items[0][sku-select]" style="width: 100%;" class="form-control text-center sku-select"></select></td>
                            <td><input type="text" name="items[0][desc]" class="form-control text-center desc" disabled/></td>             
                            <td>
                                <select name="items[0][uom]" class="form-control text-center uom">
                                    <option value="PCS">PCS</option>
                                    <option value="CS">CS</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[0][qty]" class="form-control text-center qty" value="1" min="1"/>
                                <small class="form-text text-muted">
                                    Available:
                                </small>
                                <input type="number" class="form-control text-center text-bold text-success totalQty"  disabled />
                            </td>
                            <td>
                                <input placeholder="0.00" type="number" class="form-control text-center text-bold text-danger totalPrice" disabled />
                                <small class="form-text text-muted">
                                    SRP /pc:
                                </small>
                                <input type="number" name="items[0][price]" class="form-control text-center price" disabled/>
                            </td>             
          
                            <td><input type="text" name="items[0][remarks]" placeholder="Remarks" class="form-control text-center remarks" /></td>
                            <td><button type="button" class="btn btn-danger removeRow">x</button></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th colspan="4" class="text-right">TOTAL</th>
                            <th id="totalAmount">₱{{ number_format(0.00 , 2) }}</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    {{ html()->label(__('Upload Attachment'), 'file_name')->class(['mb-0']) }} <small class=" font-italic text-bold">(optional)</small>
                    <input
                        form="add_psrf"
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
        <input type="hidden" id="status" name="status" form="add_psrf" value="pending">
        <a class="btn-draft btn btn-secondary">Save as Draft</a>

        <a href="#" title="preview" class="btn-preview btn btn-primary">Preview</a>

        <div class="modal fade" id="modal-preview">
            <div class="modal-dialog modal-xl">
                <livewire:summary.product-sample  />
            </div>
        </div>
    </div>
</form>


@push('js')
<script>
    let i = 0;

    $('.sku-select').each(function() {
        initSelect2($(this));
    });

    document.getElementById("addBtn").addEventListener("click", function () {
        i++;
        let table = document.querySelector("#dynamicTable tbody");
        let newRow = document.createElement("tr");
        
        newRow.innerHTML = `
            <td class="row-number"></td>
            <td><select name="items[${i}][sku-select]" class="form-control sku-select" style="width: 100%;"></select></td>
            <td><input type="text" name="items[${i}][desc]" class="form-control text-center desc" disabled/></td>
            <td>
                <select name="items[${i}][uom]" class="form-control text-center uom">
                    <option value="PCS">PCS</option>
                    <option value="CS">CS</option>
                </select>
            </td>
            <td>
                <input type="number" name="items[${i}][qty]" class="form-control text-center qty" value="1" min="1" />
                <small class="form-text text-muted">
                    Available:
                </small>
                <input type="number" class="form-control text-center text-bold text-success totalQty"  disabled />
            </td>
            <td>
                <input placeholder="0.00" type="number" class="form-control text-center text-bold text-danger totalPrice" disabled />
                <small class="form-text text-muted">
                    SRP /pc:
                </small>
                <input type="number" name="items[${i}][price]" class="form-control text-center price" disabled/>
            </td>  
            <td><input type="text" name="items[${i}][remarks]" placeholder="Enter Remarks" class="form-control text-center remarks" /></td>
            <td><button type="button" class="btn btn-danger removeRow">x</button></td>
        `;

        table.appendChild(newRow);
        let $newSelect = $(newRow).find('.sku-select');
        initSelect2($newSelect);
        updateRowNumbers();
        calculateTotals();


    });

    $(document).on('input', '.qty', function() {
        let val = parseFloat($(this).val());
        let $row = $(this).closest('tr');
        updateRowQuantity($row);
        calculateTotals();
        
        if (val > available) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }

    });


    document.addEventListener("click", function (e) {
        if (e.target && e.target.classList.contains("removeRow")) {
            e.target.closest("tr").remove();

            updateRowNumbers();
            calculateTotals();

        }
    });


    function initSelect2(element) {
        let company = document.querySelector('select[name="company_id"]').value;
        let warehouse = document.querySelector('select[name="warehouse"]').value;

        element.select2({
            placeholder: "Select Product",
            allowClear: true,
            theme: "classic",
            ajax: {
                url: "{{ route('sample_product.ajax') }}", 
                dataType: 'json',
                delay: 250, 
                data: function (params) {
                    return {
                        search: params.term,
                        company_id: $('select[name="company_id"]').val(),
                        warehouse: $('select[name="warehouse"]').val()

                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        }).on('select2:select', function (e) {
            let data = e.params.data; 
            let $row = $(this).closest('tr');


            $row.data('product-info', data);
            $row.find('.desc').val(data.description);
            $row.find('.price').val(data.price);
            $row.find('.totalQty').val(data.quantity_pcs);

            updateRowQuantity($row);
            calculateTotals();

        });
    }

    $(document).on('change', '.uom', function() {
        let $row = $(this).closest('tr');
        updateRowQuantity($row);
        calculateTotals();

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

    updateRowNumbers();
    calculateTotals();
</script>
<script>
    $(document).ready(function() {

        let company = document.querySelector('select[name="company_id"]').value;

        $('#employee_cost_center').select2({
            placeholder: "Select Employee Cost Center",
            allowClear: true,
            theme: "classic",
            ajax: {
                url: "{{ route('employee_cost.ajax') }}", // Create this route in web.php
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
<script>
    $(document).ready(function() {

        let company = document.querySelector('select[name="company_id"]').value;

        $('#customer_cost_center').select2({
            placeholder: "Select Customer Cost Center",
            allowClear: true,
            theme: "classic",
            ajax: {
                url: "{{ route('customer_cost.ajax') }}", // Create this route in web.php
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
                    $('#add_psrf').submit();
                }
            });
        });
    });
</script>

<script>
    $(function() {
        $('body').on('click', '.btn-confirm', function(e) {
            let hasError = false;
            let errorMessage = "";
            const objectiveInput = document.querySelector('input[name="objective"]');
            const department = document.querySelector('input[name="department"]'); 
            const dept = department.value.trim();

            if (objectiveInput) {
                const val = objectiveInput.value.trim();
                
                if (!val) { 
                    hasError = true;
                    objectiveInput.classList.add('is-invalid');
                    errorMessage = "Please enter the Objective before proceeding.";
                } else {
                    objectiveInput.classList.remove('is-invalid');
                }
            }

            if (hasError) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: errorMessage
                });
                return false;
            }

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
                    $('#add_psrf').submit();

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
                company_id: document.querySelector('select[name="company_id"]').value || "-",
                requested_by: document.querySelector('select[name="requested_by"]').value || "-",
                customer: document.querySelector('select[name="customer"]').value || "-",
                department: document.querySelector('input[name="department"]').value || "-",
                recipient: document.querySelector('input[name="recipient"]').value || "-",
                activity: document.querySelector('input[name="activity_name"]').value || "-",
                warehouse: document.querySelector('select[name="warehouse"]').value || "-",
                program: document.querySelector('input[name="program_date"]').value || "-",
                objective: document.querySelector('input[name="objective"]').value || "-",
                special: document.querySelector('input[name="special_instructions"]').value || "-",
                file_name: document.querySelector('input[name="file_name"]').value || "-",

            };

            let items = [];
            document.querySelectorAll('#dynamicTable tbody tr').forEach(row => {
                let sku = row.querySelector(".sku-select").value || "-";
                let desc = row.querySelector(".desc").value || "-";
                let uom = row.querySelector(".uom").value || "-";
                let qty = parseFloat(row.querySelector(".qty").value) || 1;
                let amount = parseFloat(row.querySelector(".totalPrice").value) || 1;
                let remarks = row.querySelector(".remarks").value || "-";

                items.push({ sku, desc, uom, qty, remarks, amount });
            });
   
            Livewire.dispatch('loadPsrfSummary',{ data, items });
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