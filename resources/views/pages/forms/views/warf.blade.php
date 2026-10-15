
<div class="card">
    
    <div class="card-header">
        <h3 class="text-right text-uppercase">
            @if($forms->status == 'draft')
                <span class="badge badge-secondary"><b>DRAFT</b></span>
            @elseif($forms->status == 'confirmation')
                <span class="badge badge-warning"><b>Admin Confirmation</b></span>
            @elseif($forms->status == 'endorsement')
                <span class="badge badge-info"><b>For Endorsement</b></span>
            @elseif($forms->status == 'approval')
                <span class="badge badge-primary"><b>For Final Approval</b></span>
            @elseif($forms->status == 'approved')
                <span class="badge badge-success"><b>Approved</b></span>
            @elseif($forms->status == 'received')
                <span class="badge bg-lime"><b>Received</b></span>
            @elseif($forms->status == 'declined')
                <span class="badge badge-danger"><b>Declined</b></span>
            @elseif($forms->status == 'cancelled')
                <span class="badge badge-danger"><b>Cancelled</b></span>
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
                <h4>Requested By: <b>{{ ($forms->user->name ?? '' )}}</b></h4>
                <h4>Department: <b>{{ ($forms->user->department->name ?? '' )}}</b></h4>
                <h4>Purpose: <b>{{ ($forms->model->purpose ?? '' )}}</b></h4>
            </div>
            <div class="col-6 text-right">
                <h4>Date of Access: <b>{{ date('F d, Y', strtotime($forms->model->date_access ?? '')) }}</b></h4>
                <h4>Time In: <b>{{ $forms->model->time_in ? date('h:i A', strtotime($forms->model->time_in)) : '' }}</b></h4>
                <h4>Time Out: <b>{{ $forms->model->time_out ? date('h:i A', strtotime($forms->model->time_out)) : '' }}</b></h4>
                @if(!empty($forms->model->date_submitted))
                <h4>Date Submitted: <b>{{ date('F d, Y', strtotime($forms->model->date_submitted ?? '')) }}</b></h4>
                @endif
                
            </div>
        </div>
        <div class="row">
            <div class="col-9 mb-3">
                <h4>Attachment:</h4>
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
                @if($forms->status == 'approved')
                    @can('bforms finance')
                    @if(!empty($sct_number))
                        <small class="form-text text-muted mb-3">
                            APInvoice has been created: 
                        </small>
                        <label class="form-text text-bold text-xl">
                            
                        </label>
                    @else
                        <small class="form-text text-muted mb-3">
                            This Form has been APPROVED. <br>Click the button below to download XML for APInvoice posting.
                        </small>
                        <a type="button" href="{{route( 'rfp.xml', encrypt($forms->model->id) )}}" class="btn bg-gradient-lightblue" style="margin-right: 5px;">
                            <i class="fas fa-file-download"></i> Download XML for APInvoice Posting
                        </a>
                        
                    @endif
                    @endcan
                @endif
                <form action="{{ route('approve.form',encrypt($forms->id)) }}" method="POST" id="approve">
                    @csrf          
                    <div class="form-group">
                        <input type="hidden" id="status" name="status" form="approve" value="endorsement">

                        <small class="form-text text-muted mb-3">
                            @if(in_array($user->id, $forms->endorser ?? []) && $forms->status == 'endorsement')
                                You are endorser of this Form.
                            @elseif(in_array($user->id, $forms->approver ?? []) && $forms->status == 'approval')
                                You are approver of this Form.
                            @else
                                
                            @endif
                        </small>
                        <label>
                            @if(in_array($user->id, $forms->endorser ?? []) && $forms->status == 'endorsement')
                                <a href="#" title="endorse" class="btn-endorse btn bg-success btn-lg">APPROVE</a>
                                <a href="#" title="decline" class="btn-decline btn bg-danger btn-sm">DECLINE</a>
                            @elseif(in_array($user->id, $forms->approver ?? []) && $forms->status == 'approval')
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
                        {{$forms->remarks}} - <b class="text-success">{{ $forms->declined->name ?? ''}}</b>
                    </label>
                    @if($forms->user_id == $user->id)
                    <a type="button" href="{{route( 'myforms.edit', encrypt($forms->id) )}}" class="btn bg-gradient-warning btn-lg">
                        <i class="fas fa-edit"></i> EDIT & RE-SUBMIT
                    </a>
                    @endif
                </div>
                @elseif($forms->status == 'cancelled')
                <div class="form-group">
                    <small class="form-text text-muted mb-3">
                        This Form has been CANCELLED.
                    </small>
                    <label class="form-text text-bold text-xl">
                        {{ $forms->remarks }} - <b class="text-success">{{ $forms->declined->name ?? ''}}</b>
                    </label>
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

                    <div class="mb-0">
                        <small class="text-muted d-block font-weight-bold">GATE PASS URL TO EASILY OPEN ON YOUR PHONE</small>
                        <a href="{{ route('myforms.show', encrypt($forms->id)) }}" class="target-link text-success font-weight-bold" target="_blank">
                            {{ route('myforms.show', encrypt($forms->id)) }}
                        </a>
                    </div>
                    <button type="button" class="btn btn-success mb-3 copy-btn" data-clipboard-text="{{ route('myforms.show', encrypt($forms->id)) }}">
                        <i class="fa fa-copy"></i> Copy Link
                    </button>

                    <div class="mb-3">
                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('security', encrypt($forms->id)), 'QRCODE') }}" alt="barcode" />
                    </div>

                    <a href="data:image/png;base64,{{ DNS2D::getBarcodePNG(route('security', encrypt($forms->id)), 'QRCODE', 10, 10) }}" 
                        download="QR_Code_{{ $forms->model->control_number }}.png" 
                        class="btn bg-green">
                            <i class="fas fa-download"></i> Download QR Code
                    </a>
                </div>
                @elseif($forms->status == 'received')
                <div class="form-group text">    
                    <small class="form-text text-muted mb-3">
                        This Form has been RECEIVED by security!
                    </small>
                    
                    <div class="mb-0">
                        <small class="text-muted d-block font-weight-bold">Security Guard On Duty:</small>
                        <h1>{{ $forms->model->opened_by }}</h1>
                    </div>
                    
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer text-center">
        @if($forms->status == 'approved' || $forms->status == 'received')
        <div class="row ">
            <div class="col-4">
                <img src="{{ asset($forms->user->signature ?? 'images/nosign1.png' )}}" height="50" width="100">
                <h4><span class="badge badge-success"><b>SIGNED</b></span></h4>

                <h6>{{ ($forms->model->date_submitted ?? '' )}}</h6>
                <h3><b>{{ ($forms->user->name ?? '' )}}</b></h3>

                <div class="line"></div>
                <h4>Prepared By</h4>
            </div>

            <div class="col-4">
                <img src="{{ asset($forms->noted->signature ?? 'images/nosign1.png') }}" height="50" width="100">
                <h4><span class="badge badge-success"><b>SIGNED</b></span></h4>

                <h6>{{ ($forms->date_endorsed ?? '' )}}</h6>
                <h3><b>{{ ($forms->noted->name ?? '' )}}</b></h3>

                <div class="line"></div>
                <h4>Endorsed By</h4>
            </div>
       
            <div class="col-4">
                <img src="{{ asset($forms->signed->signature ?? 'images/nosign1.png') }}" height="50" width="100">
                <h4><span class="badge badge-success"><b>SIGNED</b></span></h4>

                <h6>{{ ($forms->date_approved ?? '' )}}</h6>
                <h3><b>{{ ($forms->signed->name ?? '' )}}</b></h3>

                <div class="line"></div>
                <h4>Approved By</h4>
            </div>
        </div>
        @endif
    </div>
</div>