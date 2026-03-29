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
        <div class="row">
            <div class="col-lg-1">
            </div>
            <div class="col-lg-10 text-center">
                <strong class="text-lg card-title float-none text-bold">{{$form->name}}</strong>
            </div>
            <div class="col-lg-1 text-right">
                <a href="{{route('myforms.index')}}" class="btn bg-red btn-sm">
                    <i class="fa fa-caret-left"></i>
                    Back
                </a>
            </div>
        </div>
    </div>

        @include('pages.forms.edits.'.$form->prefix ) 
  


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
