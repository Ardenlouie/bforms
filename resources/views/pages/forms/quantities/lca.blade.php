<form action="{{ route('tax.lca',encrypt($all_form->id)) }}" method="POST" id="update" enctype="multipart/form-data">
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
            
            <input type="hidden" name="form_id"  value="{{ encrypt($form->id) }}">
            <input type="hidden" name="company_id"  value="{{ $all_form->model->company_id }}">
            <input type="hidden" name="rca_amount"  value="{{ $all_form->model->rca_amount }}">
            <input type="hidden" name="control_number"  value="{{ $all_form->model->control_number }}">
            <input type="hidden" name="rca_form_id"  value="{{ $all_form->model->rca_form->id }}">

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
                    <label class="mb-0">Name: </label>
                    <h4><b>{{ $all_form->model->rca_form->name }}</b></h4>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Department: </label>
                    <h4><b>{{ $all_form->model->rca_form->department->name }}</b></h4>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Date: </label>
                    <h4><b>{{ date('F d, Y', strtotime($all_form->model->rca_form->rca_date ?? '')) }}</b></h4>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Cost Center: </label>
                    <h4><b>{{ $all_form->model->rca_form->cost_center }}</b></h4>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Date Submitted: </label>
                    <h4><b>{{ date('F d, Y', strtotime($all_form->model->date_submitted ?? '')) }}</b></h4>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Cash Advance Ref No.: </label>
                    <h4><b>{{ $all_form->model->rca_form->control_number }}</b></h4>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Cash Advance Amount: </label>
                    <h4><b>₱{{  number_format($all_form->model->rca_form->total_amount ?? 0.00 , 2) }}</b></h4>
                </div>
            </div>

        </div>  
  

        <div class="row">
            <div class="col-md-12">
                <label class="mb-0">Details of Expenses</label>
                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="dynamicTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Expense Description</th>
                                <th>Area/Place</th>
                                <th>Amount</th>
                                <th>Tax Code</th>
                                <th>W/T Code</th>

                        </thead>
                        @php
                            $num = 1;
                        @endphp
                        <tbody >
                            @foreach ($all_form->model->lca_form_item()->get() as $index => $item)
                            <tr>
                                <td class="row-number">1</td>       
                                <td><input type="date" name="items[{{ $index }}][date]" value="{{ $item['date'] }}" class="form-control text-center date" disabled/></td>
                                <td><input type="text" name="items[{{ $index }}][desc]" value="{{ $item['item_description'] }}" class="form-control text-center desc" disabled/></td>             
                                <td><input type="text" name="items[{{ $index }}][area]" value="{{ $item['area'] }}" class="form-control text-center area" disabled/></td>
                                <td><input type="number" name="items[{{ $index }}][amount]" value="{{ $item['amount'] }}" class="form-control text-center amount" disabled/></td>
                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}" form="update">

                                <td>
                                    <select name="items[{{ $index }}][tax]" form="update" class="form-control text-center tax">
                                        <option value="Z" {{ $item->tax_code == 'Z' ? 'selected' : '' }}>Z</option>
                                        <option value="G" {{ $item->tax_code == 'G' ? 'selected' : '' }}>G</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="items[{{ $index }}][wt]" form="update" class="form-control text-center wt">
                                        <option value="A" {{ $item->wt_code == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ $item->wt_code == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="C" {{ $item->wt_code == 'C' ? 'selected' : '' }}>C</option>
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        
                    </table>
                </div>
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
    $(function() {
        $('body').on('click', '.btn-draft', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Saving Draft...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    $('#status').val('draft');
                    $('#update_lca').submit();
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
                text: "Are you sure you want to submit this Request for Liquidation of Cash Advance Form?",
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
                    $('#update_lca').submit();

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
                control_number: document.querySelector('input[name="control_number"]').value,
                company_id: document.querySelector('input[name="company_id"]').value || "-",
                rca_form_id: document.querySelector('input[name="rca_form_id"]').value || "-",
                rca_amount: document.querySelector('input[name="rca_amount"]').value || 0,

            };

            let items = [];
            document.querySelectorAll('#dynamicTable tbody tr').forEach(row => {
                let date = row.querySelector(".date").value || "2026-01-01";
                let desc = row.querySelector(".desc").value || "-";
                let area = row.querySelector(".area").value || "-";
                let tax = row.querySelector(".tax").value || "-";
                let wt = row.querySelector(".wt").value || "-";

                let amount = parseFloat(row.querySelector(".amount").value) || 0;

                items.push({ desc, amount, date, area, tax, wt});
            });

   
            Livewire.dispatch('loadLcaSummary',{ data, items });
            $('#modal-preview').modal('show');
        });
    });
</script>

@endpush