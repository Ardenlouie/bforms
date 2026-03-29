@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', __('ALL FORMS'))
@section('content_header')
<div class="row">
    <div class="col-md-6">
        <h1></h1>
    </div>

</div>
@endsection

{{-- Content body: main page content --}}
@section('content_body')
    <div class="card">
        <div class="card-header bg-gradient-navy">
            <h3 class="card-title float-none text-center text-bold">ALL FORMS</h3>
        </div>
        <div class="card-body">
            <div class="card-tools text-right mb-3">
                <a href="{{route('home')}}" class="btn bg-red btn-sm">
                    <i class="fa fa-caret-left"></i>
                    {{__('Home')}}
                </a>
            </div>
            <div class="row mb-3">
                <div class="col-lg-4">
                    <div class="form-group">
                        <input type="text" id="search_forms" class="form-control form-control-xl" placeholder="Search">
                    </div>
                </div>
                <div class="col-lg-4">
                    
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-filter"></i>FILTER</span>
                        </div>
                        <select id="status_filter" class="form-control text-uppercase">
                            <option value="">All</option>
                            <option value="draft">Draft</option>
                            <option value="endorsement">Endorsement</option>
                            <option value="approval">Final Approval</option>
                            <option value="approved">Approved</option>
                            <option value="checked">Checked</option>
                            <option value="declined">Declined</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="forms_table_container" class="table-responsive p-0">
                @include('pages.all-forms.partials' ) 
            </div>
        </div>
        <div class="card-footer">
            <div class="modal fade" id="modal-signatures">
                <div class="modal-dialog modal-dialog-centered ">
                    <livewire:forms.signature />
                </div>
            </div>
            <div class="modal fade" id="modal-view">
                <div class="modal-dialog modal-xl">
                    <livewire:forms.view />
                </div>
            </div>
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
    let debounceTimer;

    // Listen to both the search input and the status select
    const searchInput = document.getElementById('search_forms');
    const statusSelect = document.getElementById('status_filter'); // Ensure your <select> has this ID

    const handleFilterChange = () => {
        let searchTerm = searchInput.value;
        let status = statusSelect ? statusSelect.value : '';

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchSearch(searchTerm, status);
        }, 300);
    };

    searchInput.addEventListener('input', handleFilterChange);
    
    if (statusSelect) {
        statusSelect.addEventListener('change', handleFilterChange);
    }

    function fetchSearch(query, status) {
        document.getElementById('forms_table_container').style.opacity = '0.5';

        fetch(`/allforms?search=${query}&status=${status}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('forms_table_container').innerHTML = html;
            document.getElementById('forms_table_container').style.opacity = '1';
        })
        .catch(error => {
            console.warn('Error fetching search:', error);
            document.getElementById('forms_table_container').style.opacity = '1';
        });
    }
</script>

<script>
    $(function() {
        $('body').on('click', '.btn-signatures', function(e) {
            e.preventDefault();
            let data = {
                id: $(this).data('id'),
                form: $(this).data('form'),
            };
            Livewire.dispatch('viewSignatures', {data});
            $('#modal-signatures').modal('show');
        });
    });
</script>
<script>
    $(function() {
        $('body').on('click', '.btn-view', function(e) {
            e.preventDefault();
            let data = {
                id: $(this).data('id'),
                form: $(this).data('form'),
            };
            Livewire.dispatch('viewForm', {data});
            $('#modal-view').modal('show');
        });
    });
</script>
<script>
    $(function() {
        $('body').on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            Livewire.dispatch('setDeleteModel', {type: 'AllForm', model_id: id});
            $('#modal-delete').modal('show');
        });
    });
</script>
@endpush