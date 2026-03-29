@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', __('adminlte::users.user_update'))
@section('content_header_title', __('adminlte::users.users'))
@section('content_header_subtitle', __('adminlte::users.user_update'))

{{-- Content body: main page content --}}
@section('content_body')
    {{ html()->form('POST', route('user.update', encrypt($user->id)))->open() }}
        <div class="card">
            <div class="card-header py-2">
                <div class="row">
                    <div class="col-lg-6 align-middle">
                        <strong class="text-lg">{{__('adminlte::users.user_update')}}</strong>
                    </div>
                    <div class="col-lg-6 text-right">
                        <a href="{{route('user.index')}}" class="btn btn-secondary btn-xs">
                            <i class="fa fa-caret-left"></i>
                            {{__('adminlte::utilities.back')}}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            {{ html()->label(__('adminlte::utilities.name'), 'name')->class(['mb-0']) }}
                            {{ html()->input('text', 'name', $user->name)->placeholder(__('adminlte::utilities.name'))->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('name')]) }}
                            <small class="text-danger">{{$errors->first('name')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            {{ html()->label(__('adminlte::utilities.email'), 'email')->class(['mb-0']) }}
                            {{ html()->input('email', 'email', $user->email)->placeholder(__('adminlte::utilities.email'))->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('email')]) }}
                            <small class="text-danger">{{$errors->first('email')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            {{ html()->label(__('adminlte::companies.company'), 'company_id')->class(['mb-0']) }}
                            {{ html()->select('company_id', $companies, $company_selected_id)->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('company_id')]) }}
                            <small class="text-danger">{{$errors->first('company_id')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            {{ html()->label(__('Department'), 'department_id')->class(['mb-0']) }}
                            {{ html()->select('department_id', $departments, $department_selected_id)->class(['form-control', 'form-control-sm text-uppercase', 'is-invalid' => $errors->has('department_id')]) }}
                            <small class="text-danger">{{$errors->first('department_id')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            {{ html()->label(__('Cost Center'), 'cost_center')->class(['mb-2 d-block font-weight-bold']) }}

                            <div class="custom-control custom-switch custom-switch-purple">
                                <input type="hidden" name="cost_center" value="0">
                                
                                <input 
                                    type="checkbox" 
                                    name="cost_center" 
                                    class="custom-control-input {{ $errors->has('cost_center') ? 'is-invalid' : '' }}" 
                                    id="costcenterSwitch" 
                                    value="1"
                                    {{ old('cost_center', $user->cost_center) == 1 ? 'checked' : '' }}

                                >
                                
                                <label class="custom-control-label" for="costcenterSwitch">
                                    <span id="switch-text">
                                        {{ old('cost_center', $user->cost_center) == 1 ? __('(Yes)') : __('(No)') }}
                                    </span>
                                </label>
                            </div>

                            @if($errors->has('cost_center'))
                                <small class="text-danger d-block mt-2">{{ $errors->first('cost_center') }}</small>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-3" hidden>
                        <div class="form-group">
                            {{ html()->label(__('adminlte::positions.position'), 'position_id')->class(['mb-0']) }}
                            {{ html()->select('position_id', $positions, $position_selected_id)->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('position_id')]) }}
                            <small class="text-danger">{{$errors->first('position_id')}}</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        {{ html()->label(__('adminlte::roles.roles'), 'role_ids')->class(['mb-0', 'text-danger' => $errors->has('role_ids')]) }}
                        @if($errors->has('role_ids'))
                            <span class="badge badge-danger pt-1">{{__('adminlte::utilities.required')}}</span>
                        @endif
                        <hr class="mt-0">
                        {{ html()->hidden('role_ids', implode(',', $user_roles))->id('role_ids')}}
                    </div>

                    <div class="col-12">
                        @foreach($roles as $role)
                            <button class="btn btn-{{in_array($role->name, $user_roles) ? 'success' : 'default'}} btn-role" data-id="{{$role->name}}">{{$role->name}}</button>
                        @endforeach
                    </div>
                </div>

            </div>
            <div class="card-footer text-right">
                {{ html()->submit('<i class="fa fa-save"></i> '.__('adminlte::users.save_user'))->class(['btn', 'btn-primary', 'btn-sm']) }}
            </div>
        </div>
    {{ html()->form()->close() }}
@stop

{{-- Push extra CSS --}}
@push('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <style>

        .custom-switch-purple .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #2c017d; 
            border-color: #2c017d;
        }


        .custom-switch {
            padding-left: 2.5rem;
        }

        .custom-switch .custom-control-label::before {
            left: -2.25rem;
            width: 2rem;
            pointer-events: all;
            border-radius: 0.5rem;
        }

        .custom-switch .custom-control-label::after {
            top: calc(0.25rem + 2px);
            left: calc(-2.25rem + 2px);
            width: calc(1rem - 4px);
            height: calc(1rem - 4px);
            background-color: #adb5bd;
            border-radius: 0.5rem;
        }

        .custom-switch .custom-control-input:checked ~ .custom-control-label::after {
            background-color: #fff;
            transform: translateX(1rem);
        }
    </style>
@endpush

{{-- Push extra scripts --}}
@push('js')
    <script>
        $(function() {
            $('body').on('click', '.btn-role', function(e) {
                e.preventDefault();
                $(this).toggleClass('btn-success').toggleClass('btn-default');

                // get all selected
                var role_ids = [];
                $('body').find('.btn-role').each(function() {
                    var id = $(this).data('id');
                    if($(this).hasClass('btn-success')) {
                        role_ids.push(id);
                    }
                });

                var roles = role_ids.join(',');
                $('#role_ids').val(roles);
            });
        })
    </script>
    <script>
        $(document).ready(function() {
            $('#costcenterSwitch').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#switch-text').text('(Yes)');
                } else {
                    $('#switch-text').text('(No)');
                }
            });
        });
    </script>
@endpush
