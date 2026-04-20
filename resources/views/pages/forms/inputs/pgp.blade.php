<form action="{{ route('store.gate',encrypt($form->id)) }}" method="POST" id="add_gate">
    <div class="card-body">
        @csrf          
        <div class="row">

        <input type="hidden" name="form_id"  value="{{ encrypt($form->id) }}">
        <input type="hidden" name="company_id"  value="{{ $all_form->model->company_id }}">
        <input type="hidden" name="received_by"  value="{{ $all_form->model->recipient }}">
        <input type="hidden" name="purpose"  value="{{ $all_form->model->activity_name }}">
        <input type="hidden" name="psrf_form_id"  value="{{ $all_form->model->id }}">

        </div>  
        <div class="row">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Purpose</label>
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
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Receive By</label>
                    <input type="text" class="form-control" name="received_by" form="add_gate" value="{{ ($all_form->model->recipient ?? '' )}}" disabled> 
                    <small class="text-danger">{{$errors->first('received_by')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
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
    </div>
    <div class="card-footer text-right">
        <input type="hidden" id="status" name="status" form="add_gate" value="pending">
        <a class="btn-draft btn btn-secondary">Save as Draft</a>

        <a href="#" title="preview" class="btn-preview btn btn-primary">Preview</a>

        <div class="modal fade" id="modal-preview">
            <div class="modal-dialog modal-xl">
                <livewire:summary.gate-pass  />
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
        emitPSRF();
    });

    document.addEventListener("click", function (e) {
        if (e.target && e.target.classList.contains("removeRow")) {
            e.target.closest("tr").remove();
            updateRowNumbers();
            emitPSRF();

        }
    });


    function emitPSRF() {
        let data = {
            form_id: document.querySelector('input[name="form_id"]').value || "-",
            company_id: document.querySelector('select[name="company_id"]').value || "-",
            purpose: document.querySelector('input[name="purpose"]').value || "-",
            receive_by: document.querySelector('input[name="receive_by"]').value || "-",
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
        $('body').on('click', '.btn-preview', function(e) {
            e.preventDefault();

            let data = {
                form_id: document.querySelector('input[name="form_id"]').value || "-",
                company_id: document.querySelector('input[name="company_id"]').value || "-",
                purpose: document.querySelector('input[name="purpose"]').value || "-",
                received_by: document.querySelector('input[name="received_by"]').value || "-",
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
@endpush