<form action="{{ route('store.pcl',encrypt($form->id)) }}" method="POST" id="add_pcl" enctype="multipart/form-data">
    <div class="card-body">
        @csrf          
        <div class="row">
            
        <input type="hidden" name="form_id"  value="{{ encrypt($form->id) }}">
        <input type="hidden" name="company_id"  value="{{ $all_form->model->company_id }}">
        <input type="hidden" name="pca_form_id"  value="{{ $all_form->model->id }}">
        <input type="hidden" name="all_form_id"  value="{{ $all_form->id }}">
 
        </div>  
        <div class="card">
            <div class="card-header">
                <h4><b>Petty Cash Advance [{{ $all_form->model->control_number }}] Details</b></h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <h4>Name: <b>{{ ($all_form->model->name ?? '' )}}</b></h4>
                        <h4>Date Receive: <b>{{ date('F d, Y', strtotime($all_form->date_approved ?? '' ))}}</b></h4>
                        <h4>Cost Center: <b>{{ ($all_form->model->costcenter->name ?? '' )}}</b></h4>
                    </div>
                    <div class="col-lg-6 text-right">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($all_form->model->pca_form_item()->get() as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item['item_description'] }}</td>
                                            <td>{{ $item['amount'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <h2>Total Amount of Petty Cash Advance: <b>₱{{  number_format($all_form->model->total_amount ?? 0.00 , 2) }}</b></h2>
                        <input type="hidden" name="pca_amount" value="{{ $all_form->model->total_amount }}"> 

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="dynamicTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Disbursement Particulars</th>
                                <th>Amount</th>
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
                                <td><button type="button" class="btn btn-danger removeRow">x</button></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th colspan="1" class="text-right">TOTAL LIQUIDATION AMOUNT</th>
                                <th id="totalAmount">₱{{ number_format(0.00 , 2) }}</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th colspan="1" class="text-right">AMOUNT RETURNED</th>
                                <th id="totalBalance">₱{{ number_format(0.00 , 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    {{ html()->label(__('Upload Receipts'), 'file_name')->class(['mb-0']) }}
                    <input
                        form="add_pcl"
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
                    <b>Receipts Preview</b>
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
        <input type="hidden" id="status" name="status" form="add_pcl" value="pending">
        <a class="btn-draft btn btn-secondary">Save as Draft</a>

        <a href="#" title="preview" class="btn-preview btn btn-primary">Preview</a>

        <div class="modal fade" id="modal-preview">
            <div class="modal-dialog modal-xl">
                <livewire:summary.petty-liquid  />
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
            all_form_id: document.querySelector('input[name="all_form_id"]').value || "-",
            company_id: document.querySelector('input[name="company_id"]').value || "-",
            pca_form_id: document.querySelector('input[name="pca_form_id"]').value || "-",
            file_name: document.querySelector('input[name="file_name"]').value || "-",
            pca_amount: document.querySelector('input[name="pca_amount"]').value || 0,
        };

        let items = [];
        document.querySelectorAll('#dynamicTable tbody tr').forEach(row => {
            let desc = row.querySelector(".desc").value || "-";
            let amount = parseFloat(row.querySelector(".amount").value) || 0;

            items.push({ desc, amount, date, area });
        });

        Livewire.dispatch('loadLcaSummary',{ data, items });
    }

    function updateRowNumbers() {
        document.querySelectorAll("#dynamicTable tbody tr").forEach((row, index) => {
            row.querySelector(".row-number").textContent = index + 1;
        });
    }

    function calculateTotals() {
        let totalAmount = 0;
        let totalBalance = 0;
        let totalPca = document.querySelector('input[name="pca_amount"]').value;

        document.querySelectorAll("#dynamicTable tbody tr").forEach(row => {
            let amount = parseFloat(row.querySelector(".amount").value) || 0;

            totalAmount += amount;
        });

        totalBalance = totalPca - totalAmount;

        document.getElementById("totalAmount").textContent = totalAmount.toFixed(2);
        document.getElementById("totalBalance").textContent = totalBalance.toFixed(2);
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
                    $('#add_pcl').submit();
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
                text: "Are you sure you want to submit this Request for Petty Cash Liquidation Form?",
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
                    $('#add_pcl').submit();

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
                all_form_id: document.querySelector('input[name="all_form_id"]').value || "-",
                pca_form_id: document.querySelector('input[name="pca_form_id"]').value || "-",
                pca_amount: document.querySelector('input[name="pca_amount"]').value || 0,
                file_name: document.querySelector('input[name="file_name"]').value || "-",

            };

            let items = [];
            document.querySelectorAll('#dynamicTable tbody tr').forEach(row => {
                let desc = row.querySelector(".desc").value || "-";
                let amount = parseFloat(row.querySelector(".amount").value) || 0;

                items.push({ desc, amount });
            });

   
            Livewire.dispatch('loadPclSummary',{ data, items });
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