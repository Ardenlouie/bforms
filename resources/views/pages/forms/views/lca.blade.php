
<div class="card">
    
    <div class="card-header">
        <h3 class="text-right text-uppercase">
            @if($forms->status == 'draft')
                <span class="badge badge-secondary"><b>DRAFT</b></span>
            @elseif($forms->status == 'confirmation')
                <span class="badge badge-warning"><b>Confirmation</b></span>
            @elseif($forms->status == 'endorsement')
                <span class="badge badge-info"><b>For Endorsement</b></span>
            @elseif($forms->status == 'approval')
                <span class="badge badge-primary"><b>For Final Approval</b></span>
            @elseif($forms->status == 'approved')
                <span class="badge badge-success"><b>Approved</b></span>
            @elseif($forms->status == 'processing')
                <span class="badge bg-navy"><b>For Processing</b></span>
            @elseif($forms->status == 'checked')
                <span class="badge bg-purple"><b>Received & Checked</b></span>
            @elseif($forms->status == 'declined')
                <span class="badge badge-danger"><b>Declined</b></span>
            @else
                <span class="badge bg-dark"><b>Pending</b></span>
            @endif
        </h3>
        <div class="row">
            <div class="col-12">
                @if($forms->model->company->id == 1)
                <img src="{{asset('/images/bevilogonobg.png')}}" alt="product photo" class="product-img" height="50" width="250">
                @elseif($forms->model->company->id == 2)
                <img src="{{asset('/images/bevanobg.png')}}" alt="product photo" class="product-img" height="80" width="120">
                @elseif($forms->model->company->id == 3)
                <img src="{{asset('/images/biginobg.png')}}" alt="product photo" class="product-img" height="80" width="150">
                @elseif($forms->model->company->id == 4)
                <img src="{{asset('/images/bevminobg.png')}}" alt="product photo" class="product-img" height="80" width="220">
                @elseif($forms->model->company->id == 5)
                <img src="{{asset('/images/osp.png')}}" alt="product photo" class="product-img" height="80" width="250">
                @elseif($forms->model->company->id == 6)
                <img src="{{asset('/images/pbb.png')}}" alt="product photo" class="product-img" height="80" width="150">
                @endif
                <h4 class="float-right">Ref. No.: <b>{{ $forms->model->control_number }}</b></h4>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-12 text-center text-uppercase mb-3">
                
                <h3><b>{{ $forms->form->name }}</b></h3>
                
            </div>
            <div class="col-6">
                <h4>Name: <b>{{ ($forms->model->rca_form->name ?? '' )}}</b></h4>
                <h4>Cost Center: <b>{{ ($forms->model->rca_form->costcenter->name ?? '' )}}</b></h4>
                <h4>Cash Advance Ref No.: <b>{{ ($forms->model->rca_form->control_number ?? '' )}}</b></h4>
            </div>
            <div class="col-6 text-right">
                <h4>Department: <b>{{ $forms->model->rca_form->department->name ?? '' }}</b></h4>
                @if(!empty($forms->model->date_submitted))
                <h4>Date Submitted: <b>{{ date('F d, Y', strtotime($forms->model->date_submitted ?? '')) }}</b></h4>
                @endif
                <h4>Cash Advance Amount:
                        <b>₱{{  number_format($forms->model->rca_form->total_amount ?? 0.00 , 2) }}</b>
                    </h4>


            </div>
        </div>
        <div class="col-md-12">
            <label class="mb-0">Details of Expenses</label>
            <div class="table-responsive mb-3">
                <table class="table table-striped text-center" id="summaryTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Expense Description</th>
                            <th>Area/Place</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($forms->model->lca_form_item()->get() as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{  date('F d, Y', strtotime($item['date'] ?? '' )) }}</td>
                                <td>{{ $item['item_description'] }}</td>
                                <td>{{ $item['area'] }}</td>
                                <td>{{ number_format($item['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-3">
                <h2>Total Liquidation Amount: <b>₱{{  number_format($forms->model->total_amount ?? 0.00 , 2) }}</b></h2>
                <h2>Balance: <b>₱{{  number_format($forms->model->balance ?? 0.00 , 2) }}</b></h2>
            </div>
            <div class="col-9 mb-3">
                <h4>Receipt/s:</h4>
                @if(!empty($forms->model->path))
                    <iframe
                        src="{{ asset('/'.$forms->model->path) }}"
                        width="100%"
                        height="600px"
                        style="border: none;">
                    </iframe>
                @else
                    NO ATTACHMENT
                @endif
            </div>

        </div>

        <div class="row">
            <div class="col-6">
                <a type="button" href="{{route( 'printPDF', encrypt($forms->id) )}}" target="_blank" class="btn bg-gradient-navy" style="margin-right: 5px;">
                    <i class="fas fa-download"></i> Generate PDF
                </a>
                
            </div>
            <div class="col-6 text-right">
                
                
                <form action="{{ route('approve.form',encrypt($forms->id)) }}" method="POST" id="approve">
                    @csrf          
                    <div class="form-group">
                        <input type="hidden" id="status" name="status" form="approve" value="endorsement">

                        <small class="form-text text-muted mb-3">
                            @if($forms->endorser == $user->id && $forms->status == 'endorsement')
                                You are endorser of this Form.
                            @elseif($forms->approver == $user->id && $forms->status == 'approval')
                                You are approver of this Form.
                            @elseif($forms->admin_id == $user->id && $forms->status == 'confirmation')
                                You are admin in-charge of this Form.
                            @else
                                
                            @endif
                        </small>
                        <label>
                            @if($forms->endorser == $user->id && $forms->status == 'endorsement')
                                <a href="#" title="endorse" class="btn-endorse btn bg-success btn-lg">APPROVE</a>
                                <a href="#" title="decline" class="btn-decline btn bg-danger btn-sm">DECLINE</a>
                            @elseif($forms->approver == $user->id && $forms->status == 'approval')
                                <a href="#" title="approve" class="btn-approve btn bg-success btn-lg">APPROVE</a>
                                <a href="#" title="decline" class="btn-decline btn bg-danger btn-sm">DECLINE</a>
                            @elseif($forms->admin_id == $user->id && $forms->status == 'confirmation')
                                <a href="#" title="admin" class="btn-admin btn bg-success btn-lg">APPROVE</a>
                                <a href="#" title="decline" class="btn-decline btn bg-danger btn-sm">DECLINE</a>
                            @else

                            @endif
                        </label>
                    </div>
                    <input type="hidden" id="remarks" name="remarks" form="approve" >
                    <input type="hidden" id="processor" name="processor" form="approve" >

                </form>
                @if($forms->status == 'declined')
                <div class="form-group">
                    <small class="form-text text-muted mb-3">
                        This Form has been DECLINED.
                    </small>
                    <label class="form-text text-bold text-xl">
                        {{$forms->remarks}}
                    </label>
                    @if($forms->user_id == $user->id)
                    <a type="button" href="{{route( 'myforms.edit', encrypt($forms->id) )}}" class="btn bg-gradient-warning btn-lg">
                        <i class="fas fa-edit"></i> EDIT & RE-SUBMIT
                    </a>
                    @endif
                </div>
                @endif

                <!-- <div class="form-group">
                    <small class="form-text text-muted mb-3">
                        This Form has been APPROVED. Assign finance personel to process it.
                    </small>
                    <a href="#" title="processor" class="btn-processor btn bg-primary btn-lg">
                        <i class="fas fa-arrow-right"></i> ASSIGN</a>
                    

                </div> -->


                @if($forms->status == 'endorsement' || $forms->status == 'approval')
                    @php
                        $hoursPending = $forms->updated_at->diffInHours(now());
                    @endphp
                    @if($hoursPending >= 24  && $forms->user_id == $user->id) 
                    <div class="form-group">    
                        <small class="form-text text-muted mb-3">
                            This Form has been PENDING for approval, 1 day from submission. Press the button below to follow up your request.
                        </small>
                        <a href="#" data-url="{{ route('follow-up', encrypt($forms->id)) }}" class="btn btn-follow btn-lg btn-outline-danger ml-2 animate__animated animate__pulse animate__infinite" title="Send follow-up notification">
                            <i class="fas fa-bell"></i> Follow-up Approver
                        </a>
                    </div>
                    @endif
                @endif
                
            </div>
        </div>
    </div>
    <div class="card-footer text-center">
        @if($forms->status == 'approved' || $forms->status == 'processing')
        <div class="row ">
            <div class="col-4">
                <img src="{{ asset($forms->user->signature ?? 'images/nosign.png' )}}" height="100" width="150">
                <h6>{{ ($forms->model->date_submitted ?? '' )}}</h6>
                <h3><b>{{ ($forms->user->name ?? '' )}}</b></h3>

                <div class="line"></div>
                <h4>Prepared By</h4>
            </div>

            <div class="col-4">
                <img src="{{ asset($forms->endorsed->signature ?? 'images/nosign.png') }}" height="100" width="150">

                <h6>{{ ($forms->date_endorsed ?? '' )}}</h6>
                <h3><b>{{ ($forms->endorsed->name ?? '' )}}</b></h3>

                <div class="line"></div>
                <h4>Endorsed By</h4>
            </div>
       
            <div class="col-4">
                <img src="{{ asset($forms->approved->signature ?? 'images/nosign.png') }}" height="100" width="150">

                <h6>{{ ($forms->date_approved ?? '' )}}</h6>
                <h3><b>{{ ($forms->approved->name ?? '' )}}</b></h3>

                <div class="line"></div>
                <h4>Approved By</h4>
            </div>
        </div>
        @endif
    </div>
</div>

@push('js')
<!-- <script>
$(function() {
    $('body').on('click', '.btn-processor', function(e) {
        e.preventDefault();

        
        Swal.fire({
        title: 'Select Finance Personnel',
        input: 'select',
        inputOptions: userOptions,
        inputPlaceholder: 'Select a user...',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        }).then((result) => {
        if (result.isConfirmed) {
            $('#processor').val(result.value);
            $('#status').val('processing');
            $('#approve').submit();
        }
        });
    });
});
</script> -->
@endpush