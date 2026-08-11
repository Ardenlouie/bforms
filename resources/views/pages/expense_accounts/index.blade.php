@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', __('Expense Accounts'))
@section('content_header_title', __('Expense Accounts'))
@section('content_header_subtitle', __('Expense Account List'))

{{-- Content body: main page content --}}
@section('content_body')
    <div class="card">
        <div class="card-header py-2">
            <div class="row">
                <div class="col-lg-6 align-middle">
                    <strong class="text-lg">Expense Account List</strong>
                </div>
                <div class="col-lg-6 text-right">
                    @can('company create')
                        <a href="{{route('expense_account.create')}}" class="btn btn-primary btn-xs">
                            <i class="fa fa-file"></i>
                            New Expense Account
                        </a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body">

            {{ html()->form('GET', route('expense_account.index'))->open() }}
                <div class="row mb-1">
                    <div class="col-lg-4">
                        <div class="form-group">
                            {{ html()->label(__('adminlte::utilities.search'), 'search')->class('mb-0') }}
                            {{ html()->input('text', 'search', $search)->placeholder(__('adminlte::utilities.name'))->class(['form-control', 'form-control-sm'])}}
                        </div>
                    </div>
                </div>
            {{ html()->form()->close() }}
            
            <div class="row">
                <div class="col-12 table-responsive">
                    <table class="table table-sm table-striped table-hover mb-0 rounded">
                        <thead class="tex-center bg-dark">
                            <tr class="text-center">
                                <th>ID</th>
                                <th>Ledger Code</th>
                                <th>{{__('adminlte::utilities.name')}}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expense_accounts as $expense_account)
                                <tr>
                                    <td class="align-middle text-center">
                                        {{$expense_account->id}}
                                    </td>
                                    <td class="align-middle text-center">
                                        {{$expense_account->ledger_code}}
                                    </td>
                                    <td class="align-middle text-center">
                                        {{$expense_account->name}}
                                    </td>
                                    <td class="align-middle text-right p-0 pr-1">

                                        @can('company edit')
                                            <a href="{{route('expense_account.edit', encrypt($expense_account->id))}}" class="btn btn-success btn-xs mb-0 ml-0">
                                                <i class="fa fa-pen-alt"></i>
                                                {{__('adminlte::utilities.edit')}}
                                            </a>
                                        @endcan
                                        @can('company delete')
                                            <a href="" class="btn btn-danger btn-xs mb-0 ml-0 btn-delete" data-id="{{encrypt($expense_account->id)}}">
                                                <i class="fa fa-trash-alt"></i>
                                                {{__('adminlte::utilities.delete')}}
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="card-footer">
            {{$expense_accounts->links()}}
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
        $(function() {
            $('body').on('click', '.btn-delete', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                Livewire.dispatch('setDeleteModel', {type: 'Expense Account', model_id: id});
                $('#modal-delete').modal('show');
            });
        });
    </script>
@endpush