@extends('layouts.app')

{{-- Customize layout sections --}}

{{-- Content body: main page content --}}
@section('content_header')
<div class="row">
    <div class="col-md-6">
        <h1></h1>
    </div>

</div>
@endsection
@section('content_body')
<div class="card">
    <div class="card-header bg-gradient-navy">
        <h3 class="card-title float-none text-center text-bold">FOR SECURITY CHECKING</h3>
    </div>
    <div class="invoice p-3 mb-3">
        <h3 class="text-right text-uppercase">
            @if($all_form->status == 'draft')
                <span class="badge badge-secondary"><b>DRAFT</b></span>
            @elseif($all_form->status == 'endorsement')
                <span class="badge badge-info"><b>For Endorsement</b></span>
            @elseif($all_form->status == 'approval')
                <span class="badge badge-primary"><b>For Final Approval</b></span>
            @elseif($all_form->status == 'approved')
                <span class="badge badge-success"><b>Approved</b></span>
            @elseif($all_form->status == 'checked')
                <span class="badge bg-purple"><b>Received & Checked</b></span>
            @elseif($all_form->status == 'declined')
                <span class="badge badge-danger"><b>Declined</b></span>
            @elseif($all_form->status == 'partially_released')
                <span class="badge bg-orange"><b>Partially Released</b></span>
            @else
                <span class="badge bg-dark"><b>Pending</b></span>
            @endif
        </h3>
        <div class="row mb-3">
            <div class="col-12">
                <h4>
                <i class="fas fa-file"></i> {{$all_form->form->name}}
                <b class="float-right">Reference Number: <br>{{$all_form->model->control_number}}</b>
                </h4>
            </div>
        </div>
        @if($all_form->form->prefix == 'psrf')
        <div class="row invoice-info mb-3 text-lg">
            <div class="col-6 invoice-col">
                <b>Recipient: </b><br>{{$all_form->model->recipient}}<br>
                <b>Activity Name: </b><br>{{$all_form->model->activity_name}}<br>
                <b>Program Date: </b><br>{{$all_form->model->program_date}}<br>
                <b>Objective: </b><br>{{$all_form->model->objective}}<br>
                <b>Special Instructions: </b><br>{{$all_form->model->special_instructions}}<br>
            </div>
            <div class="col-6 invoice-col">
                <b>Date Submitted: </b><br>{{$all_form->model->date_submitted}}<br>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12 table-responsive">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Description</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($all_form->model->psrf_form_item()->get() as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['item_code'] ?? '' }}</td>
                            <td>{{ $item['item_description'] ?? '' }}</td>
                            <td>{{ $item['uom'] ?? '' }}</td>
                            <td>{{ number_format($item['quantity'] ?? 0, 0) }}</td>
                            <td>{{ $item['remarks'] ?? '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @elseif($all_form->form->prefix == 'psst')
        <div class="row invoice-info mb-3 text-lg">
            <div class="col-6 invoice-col">
                <b>Objective: </b><br>{{$all_form->model->objective}}<br>
                <b>Delivery Instructions: </b><br>{{$all_form->model->delivery_instructions}}<br>
            </div>
            <div class="col-6 invoice-col">
                <b>Point of Origin: </b><br>{{$all_form->model->point_origin}}<br>
                <b>Delivery Date: </b><br>{{$all_form->model->delivery_date}}<br>
                <b>Date Submitted: </b><br>{{$all_form->model->date_submitted}}<br>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12 table-responsive">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Description</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($all_form->model->psst_form_item()->get() as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['item_code'] ?? '' }}</td>
                            <td>{{ $item['item_description'] ?? '' }}</td>
                            <td>{{ $item['uom'] ?? '' }}</td>
                            <td>{{ number_format($item['quantity'] ?? 0, 0) }}</td>
                            <td>{{ $item['remarks'] ?? '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @elseif($all_form->form->prefix == 'gate')
        <div class="row invoice-info mb-3 text-lg">
            <div class="col-6 invoice-col">
                <b>Purpose: </b><br>{{$all_form->model->purpose}}<br>
                <b>Received By: </b><br>{{$all_form->model->received_by}}<br>
            </div>
            <div class="col-6 invoice-col">
                <b>Date Submitted: </b><br>{{$all_form->model->date_submitted}}<br>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12 table-responsive">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Release Item</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($all_form->model->gate_pass_item()->get() as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['item_description'] ?? '' }}</td>
                            <td>{{ $item['uom'] ?? '' }}</td>
                            <td>{{ number_format($item['quantity'] ?? 0, 0) }}</td>
                            <td>{{ $item['remarks'] ?? '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-9 mb-3">
                <h4>Photo:</h4>
                @if(!empty($all_form->model->path))
                    <div class="gallery text-center">
                        <img
                            class="popup-image"
                            src="{{ asset('/'.$all_form->model->path) }}"
                            width="100%"
                            height="60%"
                            style="border: none;">
                        </img>
                    </div>
                @else
                    NO PHOTO
                @endif
            </div>
        </div>
        @elseif($all_form->form->prefix == 'pgp')
        <div class="row invoice-info mb-3 text-lg">
            <div class="col-6 invoice-col">
                <b>Purpose: </b><br>{{$all_form->model->purpose}}<br>
                <b>Received By: </b><br>{{$all_form->model->received_by}}<br>
            </div>
            <div class="col-6 invoice-col">
                <b>Date Submitted: </b><br>{{$all_form->model->date_submitted}}<br>
            </div>
        </div>
       
        
        <div class="row mb-3">
            <div class="p-0 table-responsive">
                <table class="table table-striped table-hover mb-0 rounded text-center ">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Release Item</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($all_form->model->gate_pass_item()->get() as $index => $item)
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
        </div>
        
        @endif
    </div>
    <div class="card-footer text-center">
        @if($all_form->status == 'approved')
        <div class="row mb-3">
            <div class="col-4">
                <img src="{{ asset($all_form->user->signature ?? 'images/nosign.png' )}}" height="100" width="150">
                <h4><span class="badge badge-success"><b>SIGNED</b></span></h4>
                
                <h6>{{ ($all_form->model->date_submitted ?? '' )}}</h6>
                <h3><b>{{ ($all_form->user->name ?? '' )}}</b></h3>

                <div class="line"></div>
                <h4>Prepared By</h4>
            </div>
            @if( !empty($all_form->date_endorsed) )

            <div class="col-4">
                <img src="{{ asset($all_form->endorsed->signature ?? 'images/nosign.png') }}" height="100" width="150">
                <h4><span class="badge badge-success"><b>SIGNED</b></span></h4>

                <h6>{{ ($all_form->date_endorsed ?? '' )}}</h6>
                <h3><b>{{ ($all_form->endorsed->name ?? '' )}}</b></h3>

                <div class="line"></div>
                <h4>Endorsed By</h4>
            </div>
            @endif
            <div class="col-4">
                <img src="{{ asset($all_form->approved->signature ?? 'images/nosign.png') }}" height="100" width="150">
                <h4><span class="badge badge-success"><b>SIGNED</b></span></h4>

                <h6>{{ ($all_form->date_approved ?? '' )}}</h6>
                <h3><b>{{ ($all_form->approved->name ?? '' )}}</b></h3>

                <div class="line"></div>
                <h4>Approved By</h4>
            </div>
        </div>
        @endif
        
        @if($all_form->status == 'checked')
        <div class="form-group float-right">
            <label class="form-text text-muted mb-3">
                This Form is already checked by Security.
            </label>
        </div>
        @else
        <form action="{{ route('form.check',encrypt($all_form->id)) }}" method="POST" id="check">
        @csrf 
            <input type="hidden" id="status" name="status" form="check" value="approved">

            <div class="col-12">
                <a href="#" title="checking" data-id="{{$all_form->id}}" data-form="{{$all_form->form_id}}" class="btn-checking btn btn-success float-right btn-lg"> 
                    <i class="fas fa-clipboard-check"></i> CHECK</a>

            </div>
        </form>
        @endif

        <div class="modal fade" id="modal-checked">
            <div class="modal-dialog modal-xl">
                <livewire:forms.security />
            </div>
        </div>
    </div>
</div>

    
@stop

{{-- Push extra CSS --}}

@push('css')



@endpush

{{-- Push extra scripts --}}

@push('js')
<script>
$(function() {
    $('body').on('click', '.btn-checked', function(e) {
        let hasError = false;
        let totalQty = 0;
        let errorMessage = "";

        $('.qty').each(function() {
            let val = parseFloat($(this).val()) || 0;

            if (val < 0) {

                hasError = true;
                $(this).addClass('is-invalid');
                errorMessage = "Quantity cannot be negative.";
            } else {
                $(this).removeClass('is-invalid');
                totalQty += val; 
            }
        });

        if (!hasError && totalQty === 0) {
            hasError = true;
            $('.qty').addClass('is-invalid'); 
            errorMessage = "At least one item must have a quantity greater than 0.";
        }

        if (hasError) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessage
            });
            return false;
        }
        takePhoto();

    });
});

async function takePhoto() {
    const { value: name } = await Swal.fire({
        title: "Receiver Name",
        input: "text",
        inputLabel: "Enter receiver name",
        showCancelButton: true,
        inputValidator: (value) => {
            if (!value) return "You must enter a name!";
        }
    });

    if (!name) return;

    const { value: capturedImage } = await Swal.fire({
        title: 'Capture Receiver Photo with Item/s',
        html: `
            <div style="position: relative;">
                <video id="webcam" autoplay playsinline style="width: 100%; border-radius: 5px;"></video>
                <canvas id="snapshot" style="display: none;"></canvas>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fa fa-camera"></i> Capture',
        customClass: { confirmButton: 'btn btn-success' },
        didOpen: () => {
            const video = document.getElementById('webcam');
            // Start the camera
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
                .then(stream => {
                    video.srcObject = stream;
                    window.localStream = stream; // Save to stop later
                })
                .catch(err => {
                    Swal.showValidationMessage(`Camera error: ${err}`);
                });
        },
        willClose: () => {
            // Stop the camera when modal closes
            if (window.localStream) {
                window.localStream.getTracks().forEach(track => track.stop());
            }
        },
        preConfirm: () => {
            const video = document.getElementById('webcam');
            const canvas = document.getElementById('snapshot');
            const context = canvas.getContext('2d');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            return canvas.toDataURL('image/png'); 
        }
    });

    if (capturedImage) {
        Swal.fire({
            title: name,
            imageUrl: capturedImage,
            imageAlt: 'Captured Photo',
            inputLabel: name,

            showCancelButton: true,
            confirmButtonText: 'Confirm & Release Items',

        }).then((result) => {
            if (result.isConfirmed) {
                let data = {
                    imageUrl: capturedImage,
                    receiverName: name,
                };
                
                Swal.fire({
                    allowOutsideClick: false,
                    title: "Checked!",
                    text: "Gate Pass items has been released.",
                    icon: "success"
                    });
                    
                Swal.showLoading();
                Livewire.dispatch('submitForm', {data});
            }
        });
    }
}
</script>

<script>
    $(function() {
        $('body').on('click', '.btn-checking', function(e) {
            e.preventDefault();
            let data = {
                id: $(this).data('id'),
                form: $(this).data('form'),
            };
            $.fn.modal.Constructor.prototype._enforceFocus = function() {};

            Livewire.dispatch('checkedForm', {data});
            $('#modal-checked').modal('show');
        });
    });
</script>
@endpush
