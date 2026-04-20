
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
            @elseif($forms->status == 'partially_released')
                <span class="badge bg-orange"><b>Partially Released</b></span>
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
                @if(!empty($forms->model->psrf_form_id))
                <h4>PSRF Ref No.: <b>{{ ($forms->model->psrf_form->control_number ?? '' )}}</b></h4>
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
                    @php
                        list($sku, $desc, $size) = explode(' - ', $item['item_description']);
                    @endphp
                        <tr >
                            <td class="align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle">
                                <div class="gallery text-center">
                                    <img class="popup-image" src="{{ asset('images/AllProducts/'.$sku.'.png') }}" alt="SKU IMAGE" height="150" width="150">
                                </div>
                            
                                {{ $item['item_description'] ?? '' }}
                            </td>
                            <td class="align-middle">{{ $item['uom'] ?? '' }}</td>
                            <td class="align-middle">{{ number_format($item['quantity'] ?? 0, 0) }}</td>
                            <td class="align-middle">{{ $item['remarks'] ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
        @if($forms->status == 'checked' || $forms->status == 'partially_released')
            @if(!empty($images))
            <h4>Security Released Item/s: </h4><br>
            <div class="row gallery">
            @forelse($images as $imageUrl)
            @php
                list($name, $png) = explode('-', $imageUrl);
            @endphp
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body p-1">
                            <h4>{{ $name }}</h4>
                            <img src="{{ asset($folderPath .'/' . $imageUrl) }}"
                                class="img-fluid rounded image-preview" 
                                style="height: 150px; width: 100%; object-fit: cover; cursor: pointer;"
                                onclick="showFullImage('{{ asset($folderPath .'/' . $imageUrl) }}')">
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="callout callout-info">
                        <h5><i class="fas fa-info"></i> Note:</h5>
                        No images uploaded for this Gate Pass ID.
                    </div>
                </div>
            @endforelse
            </div>
            @endif
        @endif
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
                            @elseif(in_array($user->id, $forms->approver ?? []) && $forms->status == 'approval')
                                You are approver of this Form.
                            @else
                                
                            @endif
                        </small>
                        <label>
                            @if($forms->endorser == $user->id && $forms->status == 'endorsement')
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
                @if($forms->status == 'approved' || $forms->status == 'partially_released')
                <div class="form-group">    
                    <small class="form-text text-muted mb-3">
                        This Form has been APPROVED!<br>For Security Checking, Please show the QR CODE below
                    </small>
                    <div class="mb-3">
                        {!! DNS2D::getBarcodeSVG(route('security', encrypt($forms->id)), 'QRCODE') !!}
                    </div>

                    <a href="data:image/png+xml;base64,{{ base64_encode(DNS2D::getBarcodePNG(route('security', encrypt($forms->id)), 'QRCODE', 10, 10)) }}" 
                        download="QR_Code_{{ $forms->model->control_number }}.png" 
                        class="btn bg-green">
                            <i class="fas fa-download"></i> Download QR Code
                    </a>
                </div>
                @endif
            </div>
            
        </div>
    </div>
    <div class="card-footer text-center">
        @if($forms->status == 'approved' || $forms->status == 'partially_released' || $forms->status == 'checked')
        <div class="row ">
            <div class="col-4">
                <img src="{{ asset($forms->user->signature ?? 'images/nosign.png' )}}" height="100" width="150">
                <h4><span class="badge badge-success"><b>SIGNED</b></span></h4>
                
                <h6>{{ ($forms->model->date_submitted ?? '' )}}</h6>
                <h3><b>{{ ($forms->user->name ?? '' )}}</b></h3>

                <div class="line"></div>
                <h4>Prepared By</h4>
            </div>
            <div class="col-4">
                <img src="{{ asset($forms->signed->signature ?? $forms->approved->signature ?? 'images/nosign.png') }}" height="100" width="150">
                <h4><span class="badge badge-success"><b>SIGNED</b></span></h4>

                <h6>{{ ($forms->date_approved ?? '' )}}</h6>
                <h3><b>{{ ($forms->signed->name ?? $forms->approved->name )}}</b></h3>

                <div class="line"></div>
                <h4>Approved By</h4>
            </div>
        </div>
        @endif
    </div>
</div>

@push('js')

<script>
function showFullImage(url) {
    Swal.fire({
        imageUrl: url,
        imageAlt: 'Gate Pass Attachment',
        width: '80%',
        backdrop: `rgba(0, 0, 71, 0.4)`,
        showCloseButton: true,
        showConfirmButton: false
    });
}
</script>

@endpush