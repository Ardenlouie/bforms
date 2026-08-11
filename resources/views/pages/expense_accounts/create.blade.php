@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', __('New Expense Account'))
@section('content_header_title', __('Expense Accounts'))
@section('content_header_subtitle', __('New Expense Account'))

{{-- Content body: main page content --}}
@section('content_body')
    {{ html()->form('POST', route('expense_account.store'))->open() }}

        <div class="card">
            <div class="card-header py-2">
                <div class="row">
                    <div class="col-lg-6 align-middle">
                        <strong class="text-lg">New Expense Account</strong>
                    </div>
                    <div class="col-lg-6 text-right">
                        <a href="{{route('expense_account.index')}}" class="btn btn-secondary btn-xs">
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
                            {{ html()->label(__('Ledger Code'), 'prefix')->class(['mb-0']) }}
                            {{ 
                                html()->text('ledger_code', '')
                                ->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('ledger_code')])
                                ->placeholder(__('Ledger Code'))
                            }}
                            <small class="text-danger">{{$errors->first('ledger_code')}}</small>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('adminlte::utilities.name'), 'name')->class(['mb-0']) }}
                            {{ 
                                html()->text('name', '')
                                ->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('name')])
                                ->placeholder(__('Expense Account Name'))
                            }}
                            <small class="text-danger">{{$errors->first('name')}}</small>
                        </div>
                    </div>

                </div>

            </div>
            <div class="card-footer text-right">
                {{ html()->submit('<i class="fa fa-save"></i> '.__('Save Expense Account'))->class(['btn', 'btn-primary', 'btn-sm']) }}
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