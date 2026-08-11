@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', __('Update Expense Account'))
@section('content_header_title', __('Expense Accounts'))
@section('content_header_subtitle', __('Update Expense Account'))

{{-- Content body: main page content --}}
@section('content_body')
    {{ html()->form('POST', route('expense_account.update', encrypt($expense_account->id)))->open() }}

        <div class="card">
            <div class="card-header py-2">
                <div class="row">
                    <div class="col-lg-6 align-middle">
                        <strong class="text-lg">Update Expense Account</strong>
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
                            {{ html()->label(__('Ledger Code'), 'ledger_code')->class(['mb-0']) }}
                            {{ 
                                html()->text('ledger_code', $expense_account->ledger_code)
                                ->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('ledger_code')])
                                ->placeholder(__('ledger_code'))
                            }}
                            <small class="text-danger">{{$errors->first('ledger_code')}}</small>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('adminlte::utilities.name'), 'name')->class(['mb-0']) }}
                            {{ 
                                html()->text('name', $expense_account->name)
                                ->class(['form-control', 'form-control-sm', 'is-invalid' => $errors->has('name')])
                                ->placeholder(__('name'))
                            }}
                            <small class="text-danger">{{$errors->first('name')}}</small>
                        </div>
                    </div>

          
            

                </div>
          

                </div>

            </div>
            <div class="card-footer text-right">
                {{ html()->submit('<i class="fa fa-save"></i> '.__('Update Expense Account'))->class(['btn', 'btn-primary', 'btn-sm']) }}
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