<form action="{{ route('store.psst',encrypt($form->id)) }}" method="POST" id="add_psst">
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
                    <label class="mb-0">Objective</label>
                    <input type="text" class="form-control" name="objective" form="add_psst"> 
                    <small class="text-danger">{{$errors->first('objective')}}</small>
                </div>
            </div>
            <div class="col-lg-3"></div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Point of Origin</label>
                        <select class="form-control" name="point_origin" form="add_psst">
                            <option value="BEVMI Warehouse">BEVMI Warehouse</option>
                            <option value="Maersk Calamba Warehouse">Maersk Calamba Warehouse</option>
                        </select>
                    <small class="text-danger">{{$errors->first('point_origin')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-7">
                <div class="form-group">
                    <label class="mb-0">Delivery Instructions</label>
                    <input type="text" class="form-control" name="delivery_instructions" form="add_psst"> 
                    <small class="text-danger">{{$errors->first('delivery_instructions')}}</small>
                </div>
            </div>
            <div class="col-lg-1"></div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Delivery Date</label>
                    <input type="date" class="form-control" name="delivery_date" form="add_psst"> 
                    <small class="text-danger">{{$errors->first('delivery_date')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-responsive table-bordered text-center" id="dynamicTable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th style="min-width: 100px;">Item Code</th>
                            <th  style="min-width: 300px;">Item Description</th>
                            <th>UOM</th>
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
                            <td><input type="text" name="items[0][sku]" placeholder="Enter Item Code" class="form-control text-center sku" /></td>             
                            <td ><input type="text" name="items[0][desc]" placeholder="Enter Item Description" class="form-control text-center desc" /></td>             
                            <td ><input type="text" name="items[0][uom]" placeholder="Enter UOM" class="form-control text-center uom" /></td>
                            <td><input type="number" name="items[0][qty]" placeholder="Enter Qty" class="form-control text-center qty" value="0"/></td>
                            <td><input type="text" name="items[0][remarks]" placeholder="Enter Remarks" class="form-control text-center remarks" /></td>
                            <td><button type="button" class="btn btn-danger removeRow">x</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card-footer text-right">
        <input type="hidden" id="status" name="status" form="add_psst" value="pending">
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

    document.getElementById("addBtn").addEventListener("click", function () {
        i++;
        let table = document.querySelector("#dynamicTable tbody");
        let newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td class="row-number"></td>
            <td><input type="text" name="items[${i}][sku]" placeholder="Enter Item Code" class="form-control text-center sku" /></td>
            <td><input type="text" name="items[${i}][desc]" placeholder="Enter Item Description" class="form-control text-center desc" /></td>
            <td><input type="text" name="items[${i}][uom]" placeholder="Enter UOM" class="form-control text-center uom" /></td>
            <td><input type="number" name="items[${i}][qty]" placeholder="Enter Qty" class="form-control text-center qty" value="0" /></td>
            <td><input type="text" name="items[${i}][remarks]" placeholder="Enter Remarks" class="form-control text-center remarks" /></td>
            <td><button type="button" class="btn btn-danger removeRow">x</button></td>
        `;
        table.appendChild(newRow);
        updateRowNumbers();
        emitPSRF();
    });

    document.addEventListener("click", function (e) {
        if (e.target && e.target.classList.contains("removeRow")) {
            e.target.closest("tr").remove();
            updateRowNumbers();
            emitPSRF();

        }
    });


    document.addEventListener("input", function (e) {
        if (e.target.name.includes("[sku]")) {
                checkDuplicateNames();
        }
    });

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
            let sku = row.querySelector(".sku").value || "-";
            let desc = row.querySelector(".desc").value || "-";
            let uom = row.querySelector(".uom").value || "-";
            let qty = parseFloat(row.querySelector(".qty").value) || 0;
            let remarks = row.querySelector(".remarks").value || "-";

            items.push({ sku, desc, uom, qty, remarks });
        });

        Livewire.dispatch('loadPsstSummary',{ data, items });
    }

    function updateRowNumbers() {
        document.querySelectorAll("#dynamicTable tbody tr").forEach((row, index) => {
            row.querySelector(".row-number").textContent = index + 1;
        });
    }

    function checkDuplicateNames() {
        const names = [];
        const inputs = document.querySelectorAll('input[name*="[sku]"]');
        let hasDuplicate = false;

        inputs.forEach(input => {
            const name = input.value.trim().toLowerCase();
            if (name !== "") {
                if (names.includes(name)) {
                    input.classList.add("is-invalid");
                    hasDuplicate = true;
                } else {
                    input.classList.remove("is-invalid");
                    names.push(name);
                }
            }
        });

        if (hasDuplicate) {
            alert("Duplicate Item Code are not allowed in the same form!");
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
                    $('#add_psst').submit();
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
                    $('#add_psst').submit();

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
                point_origin: document.querySelector('select[name="point_origin"]').value || "-",
                delivery_date: document.querySelector('input[name="delivery_date"]').value || "-",
                objective: document.querySelector('input[name="objective"]').value || "-",
                delivery_instructions: document.querySelector('input[name="delivery_instructions"]').value || "-",
            };

            let items = [];
            document.querySelectorAll('#dynamicTable tbody tr').forEach(row => {
                let sku = row.querySelector(".sku").value || "-";
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
@endpush