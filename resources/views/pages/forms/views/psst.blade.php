
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
            @elseif($forms->status == 'checked')
                <span class="badge bg-purple"><b>Received & Checked</b></span>
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
                <h4>Objective: <b>{{ ($forms->model->objective ?? '' )}}</b></h4>
                <h4>Delivery Instructions: <b>{{ ($forms->model->delivery_instructions ?? '' )}}</b></h4>
            </div>
            <div class="col-6 text-right">
                <h4>Delivery Date: <b>{{ date('F d, Y', strtotime($forms->model->delivery_date ?? '')) }}</b></h4>
                <h4>Point of Origin: 
                    <b>
                        @if($forms->model->point_origin == 'VIR')
                            BEVMI Warehouse
                        @elseif($forms->model->point_origin == 'CAL')
                            Maersk Calamba
                        @elseif($forms->model->point_origin == 'CAL-SDI')
                            Maersk Calamba - SDI
                        @else
                            {{ $forms->model->point_origin }}
                        @endif
                    </b>
                </h4>
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
                        <th>Item Code</th>
                        <th>Item Description</th>
                        <th>UOM</th>
                        <th>QTY</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($forms->model->psst_form_item()->get() as $index => $item)
                        <tr>
                            <td class="align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle">
                                <div class="gallery text-center">
                                    <img class="popup-image" src="{{ asset('images/AllProducts/'.$item['item_code'].'.png') }}" alt="SKU IMAGE" height="150" width="150">
                                </div>
                                {{ $item['item_code'] }}
                            </td>
                            <td class="align-middle">{{ $item['item_description'] }}</td>
                            <td class="align-middle">{{ $item['uom'] }}</td>
                            <td class="align-middle">{{ $item['quantity'] }}</td>
                            <td class="align-middle">{{ $item['remarks'] }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
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
                    @can('bforms scm')
                    @if(!empty($sct_number))
                        <small class="form-text text-muted mb-3">
                            SCT(Supply Chain Transfer) has been created: 
                        </small>
                        <label class="form-text text-bold text-xl">
                            {{$sct_number}}
                        </label>
                    @else
                        <small class="form-text text-muted mb-3">
                            This Form has been APPROVED. <br>Click the button below to download XML for SCT.
                        </small>
                        <a type="button" href="{{route( 'psst.xml', encrypt($forms->model->id) )}}" class="btn bg-gradient-lightblue" style="margin-right: 5px;">
                            <i class="fas fa-file-download"></i> Download XML for SCT
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
                        {{$forms->remarks}}
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
                        {{ $forms->remarks }} -  {{ $forms->declined->name ?? ''}}
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
                    
        </div>
    </div>
    <div class="card-footer text-center">
        @if($forms->status == 'approved')
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
                <h3><b>{{ ($forms->noted->name ?? '') }}</b></h3>

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