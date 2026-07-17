@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', __('New Brand'))
@section('content_header_title', __('Brands'))
@section('content_header_subtitle', __('New Brand'))

{{-- Content body: main page content --}}
@section('content_body')
    {{ html()->form('POST', route('brand.store'))->open() }}

        <div class="card">
            <div class="card-header py-2">
                <div class="row">
                    <div class="col-lg-6 align-middle">
                        <strong class="text-lg">New brand</strong>
                    </div>
                    <div class="col-lg-6 text-right">
                        <a href="{{route('brand.index')}}" class="btn btn-secondary btn-xs">
                            <i class="fa fa-caret-left"></i>
                            {{__('adminlte::utilities.back')}}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('adminlte::utilities.name'), 'brand')->class(['mb-0']) }}
                            {{ 
                                html()->text('brand', '')
                                ->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('brand')])
                                ->placeholder(__('Brand Name'))
                            }}
                            <small class="text-danger">{{$errors->first('brand')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('Brand Manager'), 'bm_id')->class(['mb-0']) }}
                            {{ 
                                html()->select('bm_id', $users)
                                    ->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('bm_id')])
                                    ->placeholder(__('Select User')) 
                            }}
                            <small class="text-danger">{{$errors->first('bm_id')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('Group Brand Manager'), 'gbm_id')->class(['mb-0']) }}
                            {{ 
                                html()->select('gbm_id', $users)
                                    ->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('gbm_id')])
                                    ->placeholder(__('Select User')) 
                            }}
                            <small class="text-danger">{{$errors->first('gbm_id')}}</small>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer text-right">
                {{ html()->submit('<i class="fa fa-save"></i> '.__('Save Brand'))->class(['btn', 'btn-primary', 'btn-sm']) }}
            </div>
        </div>

    {{ html()->form()->close() }}
@stop

{{-- Push extra CSS --}}
@push('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush

{{-- Push extra scripts --}}
@push('js')
    <script>
        $(function() {
        });
    </script>
@endpush