@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', __('Update Department'))
@section('content_header_title', __('Departments'))
@section('content_header_subtitle', __('Update Department'))

{{-- Content body: main page content --}}
@section('content_body')
    {{ html()->form('POST', route('department.update', encrypt($department->id)))->open() }}

        <div class="card">
            <div class="card-header py-2">
                <div class="row">
                    <div class="col-lg-6 align-middle">
                        <strong class="text-lg">Update Department</strong>
                    </div>
                    <div class="col-lg-6 text-right">
                        <a href="{{route('department.index')}}" class="btn btn-secondary btn-xs">
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
                                html()->text('prefix', $department->prefix)
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
                                html()->text('name', $department->name)
                                ->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('name')])
                                ->placeholder(__('name'))
                            }}
                            <small class="text-danger">{{$errors->first('name')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('Department Head'), 'head_id')->class(['mb-0']) }}
                            {{ html()->select('head_id', $users, $user_selected_id)->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('head_id')]) }}
                            <small class="text-danger">{{$errors->first('head_id')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('Admin'), 'admin_id')->class(['mb-0']) }}
                            {{ html()->select('admin_id', $users, $admin_selected_id)->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('admin_id')]) }}
                            <small class="text-danger">{{$errors->first('admin_id')}}</small>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Select Approvers</label>
                            @foreach($users as $enc_id => $name)
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" 
                                        type="checkbox" 
                                        name="approver_ids[]" 
                                        id="approver_{{ $loop->index }}" 
                                        value="{{ $enc_id }}"
                                        {{ in_array($enc_id, $approvers_selected_ids) ? 'selected checked' : '' }}>
                                    
                                    <label for="approver_{{ $loop->index }}" class="custom-control-label font-weight-normal">
                                        {{ $name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

            </div>
            <div class="card-footer text-right">
                {{ html()->submit('<i class="fa fa-save"></i> '.__('Update Department'))->class(['btn', 'btn-primary', 'btn-sm']) }}
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
        $('.select2').select2({
            allowClear: true,
            theme: "classic",

        });
    </script>
@endpush