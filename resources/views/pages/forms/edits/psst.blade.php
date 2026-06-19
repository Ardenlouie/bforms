<form action="{{ route('update.psst', encrypt($all_form->id)) }}" method="POST" id="update_psst">
    <div class="card-body">
        @csrf          
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Company <small class="text-danger font-italic text-bold">(required)</small></label>
                    {{ html()->select('company_id', $companies, $all_form->model->company_id)->class(['form-control', 'form-control', 'is-invalid' => $errors->has('company_id')]) }}
                    <small class="text-danger">{{$errors->first('company_id')}}</small>
                </div>
            </div>
        <input type="hidden" name="form_id"  value="{{ encrypt($form->id) }}">
        <input type="hidden" name="control_number"  value="{{ $all_form->model->control_number }}">
        <input type="hidden" name="date_submitted"  value="{{ date('Y-m-d') }}">

        </div>  
        <div class="row">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Objective <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="text" class="form-control" name="objective" form="update_psst" value="{{ $all_form->model->objective }}"> 
                    <small class="text-danger">{{$errors->first('objective')}}</small>
                </div>
            </div>
            <div class="col-lg-3"></div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Point of Origin <small class="text-danger font-italic text-bold">(required)</small></label>
                        <select class="form-control" name="point_origin" form="update_psst" value="{{ $all_form->model->point_origin }}">
                            <option value="VIR">BEVMI Warehouse (VIR)</option>
                            <option value="CAL">Maersk Calamba Warehouse (CAL)</option>
                            <option value="CAL-SDI">Maersk Calamba Warehouse - SDI (CAL-SDI)</option>
                        </select>
                    <small class="text-danger">{{$errors->first('point_origin')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-7">
                <div class="form-group">
                    <label class="mb-0">Delivery Instructions <small class=" font-italic text-bold">(optional)</small></label>
                    <input type="text" class="form-control" name="delivery_instructions" form="update_psst" value="{{ $all_form->model->delivery_instructions }}"> 
                    <small class="text-danger">{{$errors->first('delivery_instructions')}}</small>
                </div>
            </div>
            <div class="col-lg-1"></div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Delivery Date <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="date" class="form-control" name="delivery_date" form="update_psst" value="{{ $all_form->model->delivery_date }}"> 
                    <small class="text-danger">{{$errors->first('delivery_date')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label class="mb-0">Items <small class="text-danger font-italic text-bold">(required)</small></label>
                <table class="table table-responsive table-bordered text-center" id="dynamicTable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th style="min-width: 200px;">Item Code </th>
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
                        @foreach ($all_form->model->psst_form_item()->get() as $index => $item)
                        <tr>
                            <td class="row-number">{{ $index + 1 }}</td>
                            <td><select name="items[{{ $index }}][sku-select]" value="{{ $item['item_code'] }}" style="width: 100%;" class="form-control text-center sku-select"></select></td>
                            <td><input type="text" name="items[{{ $index }}][desc]" value="{{ $item['item_description'] }}" class="form-control text-center desc" disabled/></td>       
                                
                            <td>
                                <select name="items[{{ $index }}][uom]"  value="{{ $item['uom'] }}" class="form-control text-center uom">
                                    <option value="PCS">PCS</option>
                                    <option value="CS">CS</option>
                                </select>
                            </td>
                            
                            <td>
                                <input type="number" name="items[{{ $index }}][qty]" value="{{ $item['quantity'] }}" placeholder="Enter Qty" class="form-control text-center qty" value="1" min="1" max="1" />
                                <small class="form-text text-muted">
                                    Available:
                                </small>
                                <input type="number" class="form-control text-center text-bold text-success totalQty"  disabled />
                            </td>
                            <td><input type="text" name="items[{{ $index }}][remarks]" value="{{ $item['remarks'] }}" placeholder="Enter Remarks" class="form-control text-center remarks" /></td>
                            <td><button type="button" class="btn btn-danger removeRow">x</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    {{ html()->label(__('Upload Attachment'), 'file_name')->class(['mb-0']) }} <small class=" font-italic text-bold">(optional)</small>
                    <h6>{{$all_form->model->file_name}}</h6>
                    <input
                        form="update_psst"
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
                        src="{{ ($all_form->model->file_name == null && '') ? (asset('/'.$all_form->model->path)) : '' }}"
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
        <input type="hidden" id="status" name="status" form="update_psst" value="pending">
        <a class="btn-draft btn btn-secondary">Save as Draft</a>

        <a href="#" title="preview" class="btn-preview btn btn-primary">Preview</a>

        <div class="modal fade" id="modal-preview">
            <div class="modal-dialog modal-xl">
                <livewire:summary.product-transfer  />
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
                <input type="number" name="items[${i}][qty]" placeholder="Enter Qty" class="form-control text-center qty" value="1" min="1" />
                <small class="form-text text-muted">
                    Available:
                </small>
                <input type="number" class="form-control text-center text-bold text-success totalQty"  disabled />
            </td>
            <td><input type="text" name="items[${i}][remarks]" placeholder="Enter Remarks" class="form-control text-center remarks" /></td>
            <td><button type="button" class="btn btn-danger removeRow">x</button></td>
        `;
        table.appendChild(newRow);
        let $newSelect = $(newRow).find('.sku-select');
        initSelect2($newSelect);
        updateRowNumbers();
    });

    $(document).on('input', '.qty', function() {
        let val = parseFloat($(this).val());
        let $row = $(this).closest('tr');
        updateRowQuantity($row);
        
        if (val <= 0 || isNaN(val) || val > available) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    document.addEventListener("click", function (e) {
        if (e.target && e.target.classList.contains("removeRow")) {
            e.target.closest("tr").remove();
            updateRowNumbers();
        }
    });

    function initSelect2(element) {
        let company = document.querySelector('select[name="company_id"]').value;
        let warehouse = document.querySelector('select[name="point_origin"]').value;

        element.select2({
            placeholder: "Select Product",
            allowClear: true,
            theme: "classic",
            ajax: {
                url: "{{ route('lot_detail.ajax') }}", 
                dataType: 'json',
                delay: 250, 
                data: function (params) {
                    return {
                        search: params.term,
                        // Grabbing values dynamically on every search
                        company_id: $('select[name="company_id"]').val(),
                        warehouse: $('select[name="point_origin"]').val()
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
            $row.find('.totalQty').val(data.quantity_pcs);

            updateRowQuantity($row);

        });
    }

    function updateRowNumbers() {
        document.querySelectorAll("#dynamicTable tbody tr").forEach((row, index) => {
            row.querySelector(".row-number").textContent = index + 1;
        });
    }

    $(document).on('change', '.uom', function() {
        let $row = $(this).closest('tr');
        updateRowQuantity($row);
    });

    function updateRowQuantity($row) {
        let data = $row.data('product-info');
        let uom = $row.find('.uom').val(); 
        let $qtyInput = $row.find('.qty'); 
        let $totalQtyDisplay = $row.find('.totalQty'); 
        if (!data) return;


        let available = (uom === 'CS') ? data.quantity_cs : data.quantity_pcs;

        $totalQtyDisplay.val(available);

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
                    $('#update_psst').submit();
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
                text: "Are you sure you want to submit this Product Sample Stock Transfer Form?",
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
                    $('#update_psst').submit();

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
                control_number: document.querySelector('input[name="control_number"]').value,
                company_id: document.querySelector('select[name="company_id"]').value || "-",
                point_origin: document.querySelector('select[name="point_origin"]').value || "-",
                delivery_date: document.querySelector('input[name="delivery_date"]').value || "-",
                objective: document.querySelector('input[name="objective"]').value || "-",
                delivery_instructions: document.querySelector('input[name="delivery_instructions"]').value || "-",
            };

            let items = [];
            document.querySelectorAll('#dynamicTable tbody tr').forEach(row => {
                let sku = row.querySelector(".sku-select").value || "-";
                let desc = row.querySelector(".desc").value || "-";
                let uom = row.querySelector(".uom").value || "-";
                let qty = parseFloat(row.querySelector(".qty").value) || 0;
                let remarks = row.querySelector(".remarks").value || "-";

                items.push({ sku, desc, uom, qty, remarks });
            });
   
            Livewire.dispatch('loadPsstSummary',{ data, items });
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