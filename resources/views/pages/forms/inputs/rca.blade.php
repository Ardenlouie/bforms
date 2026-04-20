<form action="{{ route('store.rca',encrypt($form->id)) }}" method="POST" id="add_rca">
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
                    <label class="mb-0">Name</label>
                    <input type="text" class="form-control" name="name" form="add_rca"> 
                    <small class="text-danger">{{$errors->first('name')}}</small>
                </div>
            </div>
            <div class="col-lg-3"></div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Cost Center</label>
                    <select id="cost_center" name="cost_center" class="form-control" style="width: 100%;" form="add_rca"></select>
                    <small class="text-danger">{{$errors->first('cost_center')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="mb-0">Purpose</label>
                    <input type="text" class="form-control" name="purpose" form="add_rca"> 
                    <small class="text-danger">{{$errors->first('purpose')}}</small>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    <label class="mb-0">Travel With</label>
                    <input type="text" class="form-control" name="travel" form="add_rca"> 
                    <small class="text-danger">{{$errors->first('travel')}}</small>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Date</label>
                    <input type="date" class="form-control" name="rca_date" form="add_rca" value="{{ date('Y-m-d') }}"> 
                    <small class="text-danger">{{$errors->first('rca_date')}}</small>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Itenerary</label>
                    <input type="text" class="form-control" name="itenerary" form="add_rca"> 
                    <small class="text-danger">{{$errors->first('itenerary')}}</small>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Location</label>
                    <input type="text" class="form-control" name="location" form="add_rca"> 
                    <small class="text-danger">{{$errors->first('location')}}</small>
                </div>
            </div>
   
        </div>
        <div class="row">
            <div class="col-md-12">
                <label class="mb-0">Estimated Details of Allowed Expenses</label>
                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="dynamicTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th  style="min-width: 300px;">Description</th>
                                <th>Budget Amount</th>
                                <th>No. of Days</th>
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
                                <td><input type="text" name="items[0][desc]" placeholder="Enter Description" class="form-control text-center desc" /></td>             
                                <td><input type="number" name="items[0][amount]" placeholder="Enter Amount" class="form-control text-center amount" value="0"/></td>
                                <td><input type="number" name="items[0][days]" placeholder="Enter No. Days" class="form-control text-center days" value="0"/></td>
                                <td><input type="text" name="items[0][remarks]" placeholder="Enter Remarks" class="form-control text-center remarks" /></td>
                                <td><button type="button" class="btn btn-danger removeRow">x</button></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th colspan="1" class="text-right">TOTAL</th>
                                <th id="totalAmount">₱{{ number_format(0.00 , 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
    <div class="card-footer text-right">
        <input type="hidden" id="status" name="status" form="add_rca" value="pending">
        <a class="btn-draft btn btn-secondary">Save as Draft</a>

        <a href="#" title="preview" class="btn-preview btn btn-primary">Preview</a>

        <div class="modal fade" id="modal-preview">
            <div class="modal-dialog modal-xl">
                <livewire:summary.request-cash  />
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
            <td><input type="text" name="items[${i}][desc]" placeholder="Enter Description" class="form-control text-center desc" /></td>
            <td><input type="number" name="items[${i}][amount]" placeholder="Enter Amount" class="form-control text-center amount" value="0"/></td>
            <td><input type="number" name="items[${i}][days]" placeholder="Enter No. Days" class="form-control text-center days" value="0"/></td>
            <td><input type="text" name="items[${i}][remarks]" placeholder="Enter Remarks" class="form-control text-center remarks" /></td>
            <td><button type="button" class="btn btn-danger removeRow">x</button></td>
        `;
        table.appendChild(newRow);
        updateRowNumbers();
        calculateTotals();
        emitPSRF();
    });

    document.addEventListener("click", function (e) {
        if (e.target && e.target.classList.contains("removeRow")) {
            e.target.closest("tr").remove();
            updateRowNumbers();
            calculateTotals();
            emitPSRF();

        }
    });


    document.addEventListener("input", function (e) {
        if (e.target.classList.contains("amount")) {
            calculateTotals();
        }
  
    });

    function emitPSRF() {
        let data = {
            form_id: document.querySelector('input[name="form_id"]').value || "-",
            company_id: document.querySelector('select[name="company_id"]').value || "-",
            cost_center: document.querySelector('select[name="cost_center"]').value || "-",
            name: document.querySelector('input[name="name"]').value || "-",
            purpose: document.querySelector('input[name="purpose"]').value || "-",
            rca_date: document.querySelector('input[name="rca_date"]').value || "-",
            travel: document.querySelector('input[name="travel"]').value || "-",
            itenerary: document.querySelector('input[name="itenerary"]').value || "-",
            location: document.querySelector('input[name="location"]').value || "-",
        };

        let items = [];
        document.querySelectorAll('#dynamicTable tbody tr').forEach(row => {
            let desc = row.querySelector(".desc").value || "-";
            let amount = parseFloat(row.querySelector(".amount").value) || 0;
            let days = parseFloat(row.querySelector(".days").value) || 0;
            let remarks = row.querySelector(".remarks").value || "-";

            items.push({ desc, amount, days, remarks });
        });

        Livewire.dispatch('loadRcaSummary',{ data, items });
    }

    function updateRowNumbers() {
        document.querySelectorAll("#dynamicTable tbody tr").forEach((row, index) => {
            row.querySelector(".row-number").textContent = index + 1;
        });
    }

    function calculateTotals() {
        let totalAmount = 0;

        document.querySelectorAll("#dynamicTable tbody tr").forEach(row => {
            let amount = parseFloat(row.querySelector(".amount").value) || 0;

            totalAmount += amount;
        });

        document.getElementById("totalAmount").textContent = totalAmount.toFixed(2);
    }

    updateRowNumbers();
    calculateTotals();
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
                    $('#add_rca').submit();
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
                text: "Are you sure you want to submit this Request for Cash Advance Form?",
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
                    $('#status').val('confirmation');
                    $('#add_rca').submit();

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
                cost_center: document.querySelector('select[name="cost_center"]').value || "-",
                name: document.querySelector('input[name="name"]').value || "-",
                purpose: document.querySelector('input[name="purpose"]').value || "-",
                rca_date: document.querySelector('input[name="rca_date"]').value || "-",
                travel: document.querySelector('input[name="travel"]').value || "-",
                itenerary: document.querySelector('input[name="itenerary"]').value || "-",
                location: document.querySelector('input[name="location"]').value || "-",
            };

            let items = [];
            document.querySelectorAll('#dynamicTable tbody tr').forEach(row => {
                let desc = row.querySelector(".desc").value || "-";
                let amount = parseFloat(row.querySelector(".amount").value) || 0;
                let days = parseFloat(row.querySelector(".days").value) || 0;
                let remarks = row.querySelector(".remarks").value || "-";

                items.push({ desc, amount, days, remarks });
            });

   
            Livewire.dispatch('loadRcaSummary',{ data, items });
            $('#modal-preview').modal('show');
        });
    });
</script>
@endpush