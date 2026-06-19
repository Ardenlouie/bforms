@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', __('Forms'))
@section('content_header_title', __('Forms'))
@section('content_header_subtitle', __('Form List'))

{{-- Content body: main page content --}}
@section('content_body')
    {{ html()->form('POST', route('form.store'))->open() }}

        <div class="card">
            <div class="card-header py-2">
                <div class="row">
                    <div class="col-lg-6 align-middle">
                        <strong class="text-lg">New Form</strong>
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
                                html()->text('prefix', '')
                                ->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('prefix')])
                                ->placeholder(__('Prefix'))
                            }}
                            <small class="text-danger">{{$errors->first('prefix')}}</small>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('adminlte::utilities.name'), 'name')->class(['mb-0']) }}
                            {{ 
                                html()->text('name', '')
                                ->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('name')])
                                ->placeholder(__('Form Name'))
                            }}
                            <small class="text-danger">{{$errors->first('name')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('Category'), 'category_id')->class(['mb-0']) }}
                            {{ 
                                html()->select('category_id', $categories)
                                    ->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('category_id')])
                                    ->placeholder(__('Select Category')) 
                            }}
                            <small class="text-danger">{{$errors->first('category_id')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('Department'), 'department_id')->class(['mb-0']) }}
                            {{ 
                                html()->select('department_id', $departments)
                                    ->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('department_id')])
                                    ->placeholder(__('Select Department')) 
                            }}
                            <small class="text-danger">{{$errors->first('department_id')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('Final Approver'), 'approver_id')->class(['mb-0']) }}
                            {{ 
                                html()->select('approver_id', $users)
                                    ->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('approver_id')])
                                    ->placeholder(__('Select User')) 
                            }}
                            <small class="text-danger">{{$errors->first('approver_id')}}</small>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            {{ html()->label(__('On View'), 'display')->class(['mb-2 d-block font-weight-bold']) }}

                            <div class="custom-control custom-switch custom-switch-purple">
                                <input type="hidden" name="cost_center" value="0">
                                
                                <input 
                                    type="checkbox" 
                                    name="display" 
                                    class="custom-control-input {{ $errors->has('display') ? 'is-invalid' : '' }}" 
                                    id="costcenterSwitch" 
                                    value="1"
                                >
                                
                                <label class="custom-control-label" for="costcenterSwitch">
                                    <span id="switch-text">
                                        {{ old('display') == 1 ? __('(Yes)') : __('(No)') }}
                                    </span>
                                </label>
                            </div>

                            @if($errors->has('cost_center'))
                                <small class="text-danger d-block mt-2">{{ $errors->first('cost_center') }}</small>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-4" hidden>
                        <div class="form-group">
                            {{ html()->label(__('Final Approver (BEVA)'), 'beva_approver_id')->class(['mb-0']) }}
                            {{ html()->select('beva_approver_id', $users)->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('beva_approver_id')]) }}
                            <small class="text-danger">{{$errors->first('beva_approver_id')}}</small>
                        </div>
                    </div>

                </div>

            </div>
            <div class="card-footer text-right">
                {{ html()->submit('<i class="fa fa-save"></i> '.__('Save Form'))->class(['btn', 'btn-primary', 'btn-sm']) }}
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
            $('body').on('click', '.btn-delete', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                Livewire.dispatch('setDeleteModel', {type: 'Depatment', model_id: id});
                $('#modal-delete').modal('show');
            });
        });
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