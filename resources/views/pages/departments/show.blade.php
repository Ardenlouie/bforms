@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', __('Department Details'))
@section('content_header_title', __('Departments'))
@section('content_header_subtitle', __('Department Details'))

{{-- Content body: main page content --}}
@section('content_body')
    <div class="row">
        <!-- COMPANY DETAILS -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6 align-middle">
                            <strong class="text-lg">Department Details</strong>
                        </div>
                        <div class="col-lg-6 text-right">
                            <a href="{{route('department.index')}}" class="btn btn-secondary btn-xs">
                                <i class="fa fa-caret-left"></i>
                                {{__('adminlte::utilities.back')}}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body py-1">
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item py-1 border-top-0">
                            <b>Prefix:</b>
                            <span class="float-right">{{$department->prefix ?? '-'}}</span>
                        </li>
                        <li class="list-group-item py-1 border-top-0">
                            <b>{{__('adminlte::utilities.name')}}:</b>
                            <span class="float-right">{{$department->name ?? '-'}}</span>
                        </li>
                        <li class="list-group-item py-1 border-top-0">
                            <b>Department Head:</b>
                            <span class="float-right">{{$department->head->name ?? '-'}}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!--  -->
        <div class="col-lg-8">

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
    <script> 
    </script>
@endpush