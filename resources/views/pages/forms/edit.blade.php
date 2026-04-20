@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', __('Update Form'))
@section('content_header_title', __('Forms'))
@section('content_header_subtitle', __('Update Form'))

{{-- Content body: main page content --}}
@section('content_body')
    {{ html()->form('POST', route('form.update', encrypt($form->id)))->open() }}

        <div class="card">
            <div class="card-header py-2">
                <div class="row">
                    <div class="col-lg-6 align-middle">
                        <strong class="text-lg">Update Form</strong>
                    </div>
                    <div class="col-lg-6 text-right">
                        <a href="{{route('form.index')}}" class="btn btn-secondary btn-xs">
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
                            {{ html()->label(__('Prefix'), 'prefix')->class(['mb-0']) }}
                            {{ 
                                html()->text('prefix', $form->prefix)
                                ->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('prefix')])
                                ->placeholder(__('prefix'))
                            }}
                            <small class="text-danger">{{$errors->first('name')}}</small>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('adminlte::utilities.name'), 'name')->class(['mb-0']) }}
                            {{ 
                                html()->text('name', $form->name)
                                ->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('name')])
                                ->placeholder(__('name'))
                            }}
                            <small class="text-danger">{{$errors->first('name')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('Category'), 'category_id')->class(['mb-0']) }}
                            {{ html()->select('category_id', $categories, $category_selected_id)->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('category_id')]) }}
                            <small class="text-danger">{{$errors->first('category_id')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('Department'), 'department_id')->class(['mb-0']) }}
                            {{ html()->select('department_id', $departments, $department_selected_id)->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('department_id')]) }}
                            <small class="text-danger">{{$errors->first('department_id')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('Final Approver (BEVI)'), 'approver_id')->class(['mb-0']) }}
                            {{ html()->select('approver_id', $users, $user_selected_id)->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('approver_id')]) }}
                            <small class="text-danger">{{$errors->first('approver_id')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('Final Approver (BEVA)'), 'beva_approver_id')->class(['mb-0']) }}
                            {{ html()->select('beva_approver_id', $users, $beva_selected_id)->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('beva_approver_id')]) }}
                            <small class="text-danger">{{$errors->first('beva_approver_id')}}</small>
                        </div>
                    </div>

                </div>

            </div>
            <div class="card-footer text-right">
                {{ html()->submit('<i class="fa fa-save"></i> '.__('Update Form'))->class(['btn', 'btn-primary', 'btn-sm']) }}
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