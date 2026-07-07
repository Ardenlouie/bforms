@extends('layouts.out')

@section('content_header')
<div class="row">
    <div class="col-md-6">
        <h1></h1>
    </div>

</div>
@endsection

@section('content_body')
@if($forms->status == 'checked' || $forms->status == 'received')
<div class="card">
    <div class="card-header bg-gradient-navy">
        <h3 class="card-title float-none text-center text-bold">FOR RECEIVING</h3>
    </div>
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
                <span class="badge bg-purple"><b>Checked</b></span>
            @elseif($forms->status == 'received')
                <span class="badge bg-lime"><b>Received</b></span>
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
                
                <h3><b>AKNOWLEDGEMENT RECEIPT</b></h3>
                
            </div>
            <div class="col-6">
                <h4>Purpose: <b>{{ ($forms->model->purpose ?? '' )}}</b></h4>
                @if(!empty($forms->model->psrf_form_id))
                <h4>PSRF Ref No.: <b>{{ ($forms->model->psrf_form->control_number ?? '' )}}</b></h4>
                @endif
            </div>
            <div class="col-6 text-right">

                <h4>Category: <b>{{ ($forms->model->category ?? '' )}}</b></h4>

              
                <h4>Note: <b>{{ ($forms->model->note ?? '' )}}</b></h4>


            </div>
        </div>
        @if($forms->form->prefix == 'pgp')
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
                        list($sku, $desc) = explode(' - ', $item['item_description']);
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
        @elseif($forms->form->prefix == 'gate')
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
                        <tr >
                            <td class="align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle">
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
        @endif
        
    </div>
    <div class="card-footer text-center">
        <div class="row ">
            <!-- <div class="col-6">
                <h4><span class="badge bg-navy"><b>SIGNED</b></span></h4>
                
                <h6>{{ ($forms->model->date_submitted ?? '' )}}</h6>
                <h3><b>{{ ($forms->user->name ?? '' )}}</b></h3>

                <div class="line"></div>
                <h4>Released By</h4>
            </div> -->
            <div class="col-12">
                @if($forms->status == 'received')
                    <img src="{{ asset('uploads/gate-pass-images/receiver-signature/'.$forms->model->control_number.'-receiver-signature.png') }}" height="100" width="150">
                    <h6>{{ $forms->date_received }}</h6>
                @else
                    <button type="button" class="btn-sign btn bg-success btn-lg" data-toggle="modal" data-target="#signatureModal">
                        <i class="fa fa-pencil-alt"></i> SIGN HERE
                    </button>
                    <h6>{{ date('Y-m-d') }}</h6>
                @endif
                    <h3><b>{{ is_array($forms->model->received_by) ? implode(', ', $forms->model->received_by) : $forms->model->received_by }}</b></h3>
                <div class="line"></div>
                <h4>Received By</h4>
            </div>
            
        </div>

        <form action="{{ route('form.receive',encrypt($forms->id)) }}" method="POST" id="receive">
        @csrf 
            <input type="hidden" id="status" name="status" value="checked">
            <input type="hidden" name="signature" id="signature_input">
        </form>

  
        <div class="modal fade" id="signatureModal" tabindex="-1" role="dialog" aria-labelledby="signatureModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="signatureModalLabel">Draw Your Signature</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="signature-pad-wrapper" style="border: 2px dashed #ccc; background: #f9f9f9; border-radius: 4px;">
                            <canvas id="signature-canvas" style="width: 100%; height: 200px; display: block;"></canvas>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" id="clear-signature">Clear</button>
                        <button type="button" class="btn btn-success" id="save-signature">Apply Signature</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
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

<script>
document.addEventListener("DOMContentLoaded", function () {
    const canvas = document.getElementById("signature-canvas");
    const clearButton = document.getElementById("clear-signature");
    const saveButton = document.getElementById("save-signature");
    let signaturePad;

    // Adjust canvas resolution dynamically so drawing tracking lines look crisp
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        
        if (signaturePad) {
            signaturePad.clear(); // Resizing clears canvas state
        }
    }

    // Initialize Signature Pad when modal becomes fully visible
    $('#signatureModal').on('shown.bs.modal', function () {
        resizeCanvas();
        
        signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)', // Transparent background
            penColor: 'rgb(0, 0, 0)' // Black ink color
        });
    });

    // Clear Button Handler
    clearButton.addEventListener("click", function () {
        if (signaturePad) {
            signaturePad.clear();
        }
    });

    // Save/Apply Button Handler
    saveButton.addEventListener("click", function () {
        if (!signaturePad || signaturePad.isEmpty()) {
            alert("Please provide a signature first.");
            return;
        }

        const base64ImageData = signaturePad.toDataURL("image/png");

        document.getElementById("signature_input").value = base64ImageData;

        Swal.fire({
            title: "Final Confirmation",
            text: "Are you sure you want to sign this Aknowledgement Receipt?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#0ba236",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, sign it!",
            cancelButtonText: "No",
            }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                allowOutsideClick: false,
                title: "Signed!",
                text: "Form has been signed.",
                icon: "success"
                });

                Swal.showLoading();

                $('#status').val('received');
                $('#receive').submit();

            }
            });
    });
});
</script>

@endpush