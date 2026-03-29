
<div class="card">
    
    <div class="card-header">
        <h3 class="text-right text-uppercase">
            @if($forms->status == 'draft')
                <span class="badge badge-secondary"><b>DRAFT</b></span>
            @elseif($forms->status == 'endorsement')
                <span class="badge badge-info"><b>For Endorsement</b></span>
            @elseif($forms->status == 'approval')
                <span class="badge badge-primary"><b>For Final Approval</b></span>
            @elseif($forms->status == 'approved')
                <span class="badge badge-success"><b>Approved</b></span>
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
                <h4>Purpose: <b>{{ ($forms->model->purpose ?? '' )}}</b></h4>
                <h4>Received By: <b>{{ ($forms->model->received_by ?? '' )}}</b></h4>
            </div>
            <div class="col-6 text-right">
                @if(!empty($forms->model->date_submitted))
                <h4>Date Submitted: <b>{{ date('F d, Y', strtotime($forms->model->date_submitted ?? '')) }}</b></h4>
                @endif
            </div>
        </div>
        <div class="table-responsive mb-3">
            <table class="table table-striped text-center" id="summaryTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Description</th>
                        <th>UOM</th>
                        <th>QTY</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($forms->model->gate_pass_item()->get() as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['item_description'] }}</td>
                            <td>{{ $item['uom'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ $item['remarks'] }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
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
                            @else

                            @endif
                        </label>
                    </div>
                    <input type="hidden" id="remarks" name="remarks" form="approve" >
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
            <div class="col-12 text-center">
                @if($forms->status == 'approved')
                <div class="form-group">    
                    <small class="form-text text-muted mb-3">
                        This Form has been APPROVED!<br>For Security Checking, Please show the QR CODE below
                    </small>
                    <div class="mb-3">
                        {!! DNS2D::getBarcodeSVG(route('security', encrypt($forms->id)), 'QRCODE') !!}
                    </div>

                    <a href="data:image/svg+xml;base64,{{ base64_encode(DNS2D::getBarcodeSVG(route('security', encrypt($forms->id)), 'QRCODE', 10, 10)) }}" 
                        download="QR_Code_{{ $forms->model->control_number }}.svg" 
                        class="btn bg-green">
                            <i class="fas fa-download"></i> Download QR Code
                    </a>
                </div>
                @endif
            </div>
            
        </div>
    </div>
    <div class="card-footer text-center">
        @if($forms->status == 'approved')
        <div class="row ">
            <div class="col-4">
                <img src="{{ asset($forms->user->signature ?? 'images/nosign.png' )}}" height="100" width="150">
                <h6>{{ ($forms->model->date_submitted ?? '' )}}</h6>
                <h3><b>{{ ($forms->user->name ?? '' )}}</b></h3>

                <div class="line"></div>
                <h4>Prepared By</h4>
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