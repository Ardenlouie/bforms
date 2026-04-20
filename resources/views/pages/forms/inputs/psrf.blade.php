<form action="{{ route('store.psrf',encrypt($form->id)) }}" method="POST" id="add_psrf">
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
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Recipient</label>
                    <input type="text" class="form-control" name="recipient" form="add_psrf"> 
                    <small class="text-danger">{{$errors->first('recipient')}}</small>
                </div>
            </div>
            <div class="col-lg-3"></div>
            <!-- <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Products</label>
                    <select id="products" name="products" class="form-control" style="width: 100%;" form="add_rca"></select>
                    <small class="text-danger">{{$errors->first('products')}}</small>
                </div>
            </div> -->
        </div>
        <div class="row">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Activity Name</label>
                    <input type="text" class="form-control" name="activity_name" form="add_psrf"> 
                    <small class="text-danger">{{$errors->first('activity_name')}}</small>
                </div>
            </div>
            <div class="col-lg-3"></div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Program Date</label>
                    <input type="date" class="form-control" name="program_date" form="add_psrf" value="{{ date('Y-m-d') }}"> 
                    <small class="text-danger">{{$errors->first('program_date')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Objective</label>
                    <input type="text" class="form-control" name="objective" form="add_psrf"> 
                    <small class="text-danger">{{$errors->first('objective')}}</small>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="form-group">
                    <label class="mb-0">Special Instructions</label>
                    <input type="text" class="form-control" name="special_instructions" form="add_psrf"> 
                    <small class="text-danger">{{$errors->first('special_instructions')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 table-responsive">
                <table class="table table-bordered text-center" id="dynamicTable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th style="min-width: 200px;">Item Code</th>
                            <th style="min-width: 350px;">Item Description</th>
                            <th style="min-width: 100px;">UOM</th>
                            <th>Qty</th>
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
                                    <option value="IN">IN</option>
                                </select>
                            </td>
                            <td><input type="number" name="items[0][qty]" placeholder="Enter Qty" class="form-control text-center qty" value="1" min="1"/></td>
                            <td><input type="text" name="items[0][remarks]" placeholder="Enter Remarks" class="form-control text-center remarks" /></td>
                            <td><button type="button" class="btn btn-danger removeRow">x</button></td>
                        </tr>
                    </tbody>
                    
                </table>
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
                    <option value="IN">IN</option>
                </select>
            </td>
            <td><input type="number" name="items[${i}][qty]" placeholder="Enter Qty" class="form-control text-center qty" value="1" min="1" /></td>
            <td><input type="text" name="items[${i}][remarks]" placeholder="Enter Remarks" class="form-control text-center remarks" /></td>
            <td><button type="button" class="btn btn-danger removeRow">x</button></td>
        `;

        table.appendChild(newRow);
        let $newSelect = $(newRow).find('.sku-select');
        initSelect2($newSelect);
        updateRowNumbers();
        emitPSRF();

    });

    $(document).on('input', '.qty', function() {
        let val = parseFloat($(this).val());
        
        if (val <= 0 || isNaN(val)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });


    document.addEventListener("click", function (e) {
        if (e.target && e.target.classList.contains("removeRow")) {
            e.target.closest("tr").remove();

            updateRowNumbers();
            emitPSRF();
        }
    });


    function initSelect2(element) {
        element.select2({
            placeholder: "Select Product",
            allowClear: true,
            theme: "classic",
            ajax: {
                url: "{{ route('products.ajax') }}", 
                dataType: 'json',
                delay: 250, 
                data: function (params) {
                    return {
                        search: params.term 
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

            $row.find('.desc').val(data.description);
            
            emitPSRF(); 
        });
    }

    function emitPSRF() {
        let data = {
            form_id: document.querySelector('input[name="form_id"]').value || "-",
            company_id: document.querySelector('select[name="company_id"]').value || "-",
            recipient: document.querySelector('input[name="recipient"]').value || "-",
            activity: document.querySelector('input[name="activity_name"]').value || "-",
            program: document.querySelector('input[name="program_date"]').value || "-",
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

            items.push({ sku, desc, uom, qty, remarks });
        });

        Livewire.dispatch('loadPsrfSummary',{ data, items });
    }

    function updateRowNumbers() {
        document.querySelectorAll("#dynamicTable tbody tr").forEach((row, index) => {
            row.querySelector(".row-number").textContent = index + 1;
        });
    }

    updateRowNumbers();
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
            e.preventDefault();

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
                    $('#status').val('endorsement');
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
                e.preventDefault();
                e.stopPropagation(); 

                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Input',
                    text: errorMessage,
                    confirmButtonColor: '#d33'
                });

                return false; 
            }

            let data = {
                form_id: document.querySelector('input[name="form_id"]').value || "-",
                company_id: document.querySelector('select[name="company_id"]').value || "-",
                recipient: document.querySelector('input[name="recipient"]').value || "-",
                activity: document.querySelector('input[name="activity_name"]').value || "-",
                program: document.querySelector('input[name="program_date"]').value || "-",
                objective: document.querySelector('input[name="objective"]').value || "-",
                special: document.querySelector('input[name="special_instructions"]').value || "-",
            };

            let items = [];
            document.querySelectorAll('#dynamicTable tbody tr').forEach(row => {
                let sku = row.querySelector(".sku-select").value || "-";
                let desc = row.querySelector(".desc").value || "-";
                let uom = row.querySelector(".uom").value || "-";
                let qty = parseFloat(row.querySelector(".qty").value) || 1;;
                let remarks = row.querySelector(".remarks").value || "-";

                items.push({ sku, desc, uom, qty, remarks });
            });
   
            Livewire.dispatch('loadPsrfSummary',{ data, items });
            $('#modal-preview').modal('show');
        });
    });
</script>
@endpush