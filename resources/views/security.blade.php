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
            @else
                <span class="badge bg-dark"><b>Pending</b></span>
            @endif
        </h3>
        <div class="row mb-3">
            <div class="col-12">
                <h4>
                <i class="fas fa-file"></i> {{$all_form->form->name}}
                <b class="float-right">Control Number: <br>{{$all_form->model->control_number}}</b>
                </h4>
            </div>
        </div>
        @if($all_form->form->id == '2')
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
        @elseif($all_form->form->id == '4')
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
        @endif
    </div>
    <div class="card-footer text-center">
        @if($all_form->status == 'approved')
        <div class="row mb-3">
            <div class="col-4">
                <img src="{{ asset($all_form->user->signature ?? 'images/nosign.png' )}}" height="100" width="150">
                <h6>{{ ($all_form->model->date_submitted ?? '' )}}</h6>
                <h3><b>{{ ($all_form->user->name ?? '' )}}</b></h3>

                <div class="line"></div>
                <h4>Prepared By</h4>
            </div>
            <div class="col-4">
                <img src="{{ asset($all_form->endorsed->signature ?? 'images/nosign.png') }}" height="100" width="150">

                <h6>{{ ($all_form->date_endorsed ?? '' )}}</h6>
                <h3><b>{{ ($all_form->endorsed->name ?? '' )}}</b></h3>

                <div class="line"></div>
                <h4>Endorsed By</h4>
            </div>
            <div class="col-4">
                <img src="{{ asset($all_form->approved->signature ?? 'images/nosign.png') }}" height="100" width="150">

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
                <a href="#" title="checked" class="btn-checked btn btn-success float-right btn-lg"> 
                    <i class="fas fa-clipboard-check"></i> CHECKED</a>
            </div>
        </form>
        @endif
    </div>
</div>

    
@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush

{{-- Push extra scripts --}}

@push('js')
@endpush
