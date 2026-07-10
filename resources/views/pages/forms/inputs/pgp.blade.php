<form action="{{ route('store.gate',encrypt($form->id)) }}" method="POST" id="add_gate">
    <div class="card-body">
        @csrf          
        <div class="row">

        <input type="hidden" name="form_id"  value="{{ encrypt($form->id) }}">
        <input type="hidden" name="company_id"  value="{{ $all_form->model->company_id }}">
        <input type="hidden" name="received_by"  value="{{ $all_form->model->recipient }}">
        <input type="hidden" name="purpose"  value="{{ $all_form->model->activity_name }}">
        <input type="hidden" name="psrf_form_id"  value="{{ $all_form->model->id }}">
        <input type="hidden" name="category"  value="Product Sample">

        </div>  
        <div class="row">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Purpose <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="text" class="form-control" name="purpose" form="add_gate" value="{{ ($all_form->model->activity_name ?? '' )}}" disabled> 
                    <small class="text-danger">{{$errors->first('purpose')}}</small>
                </div>
            </div>
            <div class="col-lg-2"></div>
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Product Sample Request Form:</label>
                    <h4><b>[{{ $all_form->model->control_number }}]</b></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label class="mb-0" >Note <small class=" font-italic text-bold">(optional)</small></label>
                    <input type="text" class="form-control" name="note" form="add_gate" > 
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Release Date<small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="date" class="form-control" name="release_date" form="add_gate" value="{{ date('Y-m-d') }}"> 
                    <small class="text-danger">{{$errors->first('release_date')}}</small>
                </div>
            </div>  
        </div>
        <div class="row">
            <div class="col-md-12 table-responsive">
                <label class="mb-0">Items <small class="text-danger font-italic text-bold">(required)</small></label>
                <table class="table table-bordered text-center" id="dynamicTable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th style="min-width: 300px;">Release Item</th>
                            <th>UOM</th>
                            <th>Qty</th>
                            <th>Remarks</th>
                           
                        </tr>
                    </thead>
                    @php
                        $num = 1;
                    @endphp
                    <tbody >
                        @foreach($all_form->model->psrf_form_item()->get() as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td ><input type="text" name="items[0][desc]" value="{{ $item['item_code'] }} - {{$item['item_description'] }}" class="form-control text-center desc" disabled/></td>             
                            <td ><input type="text" name="items[0][uom]" value="{{ $item['uom'] }}" class="form-control text-center uom" disabled/></td>
                            <td><input type="number" name="items[0][qty]" value="{{ $item['quantity'] }}" class="form-control text-center qty" disabled/></td>
                            <td><input type="text" name="items[0][remarks]" value="{{ $item['remarks'] }}" class="form-control text-center remarks" disabled/></td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Number of Receiver/s <small class="text-danger font-italic text-bold">(required)</small></label>
                    {{-- This input controls how many fields appear --}}
                    <input type="number" id="numberof" class="form-control" form="add_gate" name="numberof" min="1" value="1">
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="form-group">
                    <label>Received By <small class="text-danger font-italic text-bold">(required)</small></label>
                    <div id="receivers-container">
                        <input type="text" class="form-control mb-2" name="received_by[]" form="add_gate" value="{{ ($all_form->model->recipient ?? '' )}}" required placeholder="Receiver 1 Name"> 

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-right">

        <input type="hidden" id="status" name="status" form="add_gate" value="pending">
        <a class="btn-draft btn btn-secondary">Save as Draft</a>
        <!-- <a href="#" title="multiple" class="btn-multiple btn bg-success">Multiple Receivers</a> -->

        <a href="#" title="preview" class="btn-preview btn btn-primary">Preview</a>

        <div class="modal fade" id="modal-preview">
            <div class="modal-dialog modal-xl">
                <livewire:summary.gate-pass  />
            </div>
        </div>
        <div class="modal fade" id="modal-multiple">
            <div class="modal-dialog modal-xl">
                <livewire:inputs.multiple-gate  />
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
            <td><input type="text" name="items[${i}][desc]" placeholder="Enter Item Description" class="form-control text-center desc" /></td>
            <td><input type="text" name="items[${i}][uom]" placeholder="Enter UOM" class="form-control text-center uom" /></td>
            <td><input type="number" name="items[${i}][qty]" placeholder="Enter Qty" class="form-control text-center qty" value="0" /></td>
            <td><input type="text" name="items[${i}][remarks]" placeholder="Enter Remarks" class="form-control text-center remarks" /></td>
            <td><button type="button" class="btn btn-danger removeRow">x</button></td>
        `;
        table.appendChild(newRow);
        updateRowNumbers();
    });

    document.addEventListener("click", function (e) {
        if (e.target && e.target.classList.contains("removeRow")) {
            e.target.closest("tr").remove();
            updateRowNumbers();

        }
    });
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
                    $('#add_gate').submit();
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
                text: "Are you sure you want to submit this Gate Pass Form?",
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
                    $('#add_gate').submit();

                }
                });
        });
    });
</script>
<script>
    $(function() {
        $('body').on('click', '.btn-confirm-multiple', function(e) {
            let hasError = false;
            let totalQty = 0;
            let errorMessage = "";
            let isValid = true;

            $('.qty').each(function() {
                let val = parseFloat($(this).val()) || 0;

                if (val < 0) {

                    hasError = true;
                    $(this).addClass('is-invalid');
                    errorMessage = "Quantity cannot be negative.";
                } else {
                    $(this).removeClass('is-invalid');
                    totalQty += val; 
                }
            });

            if (!hasError && totalQty === 0) {
                hasError = true;
                $('.qty').addClass('is-invalid'); 
                errorMessage = "At least one item must have a quantity greater than 0.";
            }

            let totalRowsInTable = $('#summaryTable tbody tr').length; 

            for (let i = 0; i < totalRowsInTable; i++) {

                let originalTotalQty = parseInt($(`input[name="receivers[0][items][${i}][quantity_release]"]`).attr('max')) || 0;

                let totalAllocatedQty = 0;

                $(`input[name$="[items][${i}][quantity_release]"]`).each(function() {
                    totalAllocatedQty += parseInt($(this).val()) || 0;
                });

                let remainingQty = originalTotalQty - totalAllocatedQty;

                if (remainingQty !== 0) {
                    isValid = false;
                    errorMessage = "At least one item must have a quantity greater than 0.";
                    break; 
                }
            }


            if (hasError || !isValid) {
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
                text: "Are you sure you want to submit this Gate Pass Form?",
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
                    $('#add_gate').submit();

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
                company_id: document.querySelector('input[name="company_id"]').value || "-",
                purpose: document.querySelector('input[name="purpose"]').value || "-",
                numberof: document.querySelector('input[name="numberof"]').value || "-",
                category: document.querySelector('input[name="category"]').value || "-",
                release_date: document.querySelector('input[name="release_date"]').value || "-",
                others: "",
                note: document.querySelector('input[name="note"]').value || "-",
                received_by: Array.from(document.querySelectorAll('input[name="received_by[]"]'))
                  .map(input => input.value.trim() || "-")
                  .join(', '),
            };

            let items = [];
            document.querySelectorAll('#dynamicTable tbody tr').forEach(row => {
                let desc = row.querySelector(".desc").value || "-";
                let uom = row.querySelector(".uom").value || "-";
                let qty = parseFloat(row.querySelector(".qty").value) || 0;
                let remarks = row.querySelector(".remarks").value || "-";

                items.push({ desc, uom, qty, remarks });
            });
   
            Livewire.dispatch('loadGateSummary',{ data, items });
            $('#modal-preview').modal('show');
        });
    });
</script>
<script>
    $(function() {
        $('body').on('click', '.btn-multiple', function(e) {
            e.preventDefault();

            let data = {
                form_id: document.querySelector('input[name="form_id"]').value || "-",
                company_id: document.querySelector('input[name="company_id"]').value || "-",
                purpose: document.querySelector('input[name="purpose"]').value || "-",
                received_by: document.querySelector('input[name="received_by[]"]').value || "-",
                psrf_form_id: document.querySelector('input[name="psrf_form_id"]').value || "-",
            };

            let items = [];
            document.querySelectorAll('#dynamicTable tbody tr').forEach(row => {
                let desc = row.querySelector(".desc").value || "-";
                let uom = row.querySelector(".uom").value || "-";
                let qty = parseFloat(row.querySelector(".qty").value) || 0;
                let quantity_release = parseFloat(row.querySelector(".qty").value) || 1;
                let remarks = row.querySelector(".remarks").value || "-";

                items.push({ desc, uom, qty, remarks, quantity_release });
            });
   
            Livewire.dispatch('multipleGateSummary',{ data, items });
            $('#modal-multiple').modal('show');
        });
    });
</script>

<script>

$(document).ready(function() {
    $('#numberof').on('input change', function() {
        let count = parseInt($(this).val()) || 1;
        let container = $('#receivers-container');
        
        // Safety guard: Don't allow less than 1
        if (count < 1) {
            $(this).val(1);
            count = 1;
        }

        // Get the current number of inputs inside the container
        let currentInputs = container.find('input[name="received_by[]"]').length;

        if (count > currentInputs) {
            // Add more fields if count increased
            for (let i = currentInputs + 1; i <= count; i++) {
                container.append(`
                    <input type="text" 
                           class="form-control mb-2 extra-receiver" 
                           name="received_by[]" 
                           required 
                           placeholder="Receiver ${i} Name">
                `);
            }
        } else if (count < currentInputs) {
            // Remove fields from the bottom if count decreased
            for (let i = currentInputs; i > count; i--) {
                container.find('input[name="received_by[]"]').last().remove();
            }
        }
    });
});
</script>

@endpush