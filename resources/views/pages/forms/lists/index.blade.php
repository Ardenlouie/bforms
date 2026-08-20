<div class="card">
    <div class="card-body">
        <div class="card-tools text-right mb-3">
            @if($form->display == 1)
            <a href="{{ route('form.createForm', encrypt($form->id)) }}" class="btn bg-blue btn-xl">
                <i class="fa fa-plus"></i>
                {{__('Add Form')}}
            </a>
            @endif
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
                        <option value="received">Received</option>
                        <option value="liquidated">Liquidated</option>
                        <option value="declined">Declined</option>
                    </select>
                </div>
            </div>
            @if($prefix == 'gate' || $prefix == 'pgp')
            <div class="col-lg-4">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-filter"></i>CATEGORY</span>
                    </div>
                    <select id="form_id" class="form-control text-uppercase">
                        <option value="{{ encrypt(5) }}">NON-STOCKED</option>
                        <option value="{{ encrypt(9) }}">STOCKED SAMPLE</option>
                    </select>
                </div>
            </div>
            @else
                <input type="hidden" id="form_id" value="{{ encrypt($form->id) }}">
            @endif
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div id="forms_table_container" class="table-responsive p-0">
                    @include('pages.forms.lists.'.$prefix) 
                </div>
            </div>
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
@push('js')
<script>
    let debounceTimer;

    // Listen to both the search input and the status select
    const searchInput = document.getElementById('search_forms');
    const statusSelect = document.getElementById('status_filter'); 
    const form_id = document.getElementById('form_id'); 


    const handleFilterChange = () => {
        let searchTerm = searchInput.value;
        let formId = form_id ? form_id.value : '';
        let status = statusSelect ? statusSelect.value : '';

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchSearch(searchTerm, status, formId);
        }, 300);
    };

    searchInput.addEventListener('input', handleFilterChange);
    
    if (statusSelect) {
        statusSelect.addEventListener('change', handleFilterChange);
    }

    if (form_id) {
        form_id.addEventListener('change', handleFilterChange);
    }

    function fetchSearch(query, status, id) {
        document.getElementById('forms_table_container').style.opacity = '0.5';

        // Append status to the query string
        fetch(`/forms/list/${id}?search=${query}&status=${status}`, {
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
            $.fn.modal.Constructor.prototype._enforceFocus = function() {};

            let data = {
                id: $(this).data('id'),
                form: $(this).data('form'),
            };

            Livewire.dispatch('viewForm', {data});
            $('#modal-view').modal('show');
        });
    });
</script>
@endpush