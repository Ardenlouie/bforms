<form action="{{ route('store.warf',encrypt($form->id)) }}" method="POST" id="add_warf" enctype="multipart/form-data">
    <div class="card-body">
        @csrf          
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Company <small class="text-danger font-italic text-bold">(required)</small></label>
                    {{ html()->select('company_id', $companies,'')->class(['form-control', 'form-control', 'is-invalid' => $errors->has('company_id')]) }}
                    <small class="text-danger">{{$errors->first('company_id')}}</small>
                </div>
            </div>

        <input type="hidden" name="form_id"  value="{{ encrypt($form->id) }}">
        <input type="hidden" name="department_id"  value="{{ $requestor->department->id }}">

        </div>  
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Date of Access <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="date" class="form-control" name="date_access" value="{{ date('Y-m-d') }}"> 
                    <small class="text-danger">{{$errors->first('date_access')}}</small>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Actual Time-In <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="time" class="form-control" name="time_in" value="{{ date('H:i') }}"> 
                    <small class="text-danger">{{$errors->first('time_in')}}</small>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Actual Time-Out <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="time" class="form-control" name="time_out" value="{{ date('H:i') }}"> 
                    <small class="text-danger">{{$errors->first('time_out')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">
                    <label class="mb-0">Purpose of Access <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="text" class="form-control" name="purpose"> 
                    <small class="text-danger">{{$errors->first('purpose')}}</small>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    {{ html()->label(__('Upload Attachment'), 'file_name')->class(['mb-0']) }} <small class=" font-italic text-bold">(optional)</small>
                    <div class="input-group">
                        <input
                            type="file"
                            id="file_name"
                            name="file_name"
                            accept="application/pdf"
                            class="form-control {{ $errors->has('file_name') ? 'is-invalid' : '' }}"
                        >
                        <div class="input-group-append">
                            <button 
                                type="button" 
                                class="btn btn-outline-danger" 
                                id="clear_file_btn" 
                                title="Remove file"
                                style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    @if ($errors->has('file_name'))
                        <span class="text-danger small">{{ $errors->first('file_name') }}</span>
                    @endif
                </div>
                
            </div>
            
            <div class="col-lg-6">
                <div class="form-group">
                    <b>Attachment Preview</b>
                    <iframe
                        id="pdfPreview"
                        width="100%"
                        height="500"
                        style="border:1px solid #ccc;"
                    ></iframe>
                </div>
            </div>
        </div>
        
    </div>
    <div class="card-footer text-right">
        <input type="hidden" id="status" name="status" form="add_warf" value="pending">
        <!-- <a class="btn-draft btn btn-secondary">Save as Draft</a> -->

        <a href="#" title="preview" class="btn-preview btn btn-primary">Preview</a>

        <div class="modal fade" id="modal-preview">
            <div class="modal-dialog modal-xl">
                <livewire:summary.warehouse-access  />
            </div>
        </div>
    </div>
</form>
@push('js')

<script>
    $(function() {
        $('body').on('click', '.btn-draft', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Saving Draft...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    $('#status').val('draft');
                    $('#add_rfp').submit();
                }
            });
        });
    });
</script>

<script>
    $(function() {
        $('body').on('click', '.btn-confirm', function(e) {
            e.preventDefault();

            Swal.fire({
                title: "Final Confirmation",
                text: "Are you sure you want to submit this Warehouse Access Form?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#0ba236",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, submit it!",
                cancelButtonText: "No",
                }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                    allowOutsideClick: false,
                    title: "Submitted!",
                    text: "Your form has been submitted.",
                    icon: "success"
                    });

                    Swal.showLoading();
                    $('#status').val('endorsement');
                    $('#add_warf').submit();

                }
            });
        });
    });
</script>

<script>
    $(function() {
        $('body').on('click', '.btn-preview', function(e) {
            e.preventDefault();

            let data = {
                form_id: document.querySelector('input[name="form_id"]').value || "-",
                company_id: document.querySelector('select[name="company_id"]').value || "-",
                department_id: document.querySelector('input[name="department_id"]').value || "-",
                purpose: document.querySelector('input[name="purpose"]').value || "-",
                file_name: document.querySelector('input[name="file_name"]').value || "-",
                time_in: document.querySelector('input[name="time_in"]').value || "-",
                time_out: document.querySelector('input[name="time_out"]').value || "-",
                date_access: document.querySelector('input[name="date_access"]').value || "-",
            };
   
            Livewire.dispatch('loadWarfSummary',{ data });
            $('#modal-preview').modal('show');
        });
    });
</script>

<script>
    document.getElementById('file_name').addEventListener('change', function () {
        const file = this.files[0];
        const iframe = document.getElementById('pdfPreview');

        if (file && file.type === 'application/pdf') {
            iframe.src = URL.createObjectURL(file);
        } else {
            iframe.src = '';
        }
    });
</script>
<script>
    $(document).ready(function() {
        const $fileInput = $('#file_name');
        const $clearBtn = $('#clear_file_btn');
        const $previewContainer = $('#pdfPreview');
        const $pdfIframe = $('#pdf_preview');

        $fileInput.on('change', function() {
            const file = this.files[0];

            if (file && file.type === 'application/pdf') {
                const fileURL = URL.createObjectURL(file);
                $pdfIframe.attr('src', fileURL);
                $previewContainer.show();
                $clearBtn.show();
            } else {
                clearAll();
            }
        });

        $clearBtn.on('click', function() {
            clearAll();
        });

        function clearAll() {
            $fileInput.val('');
            $pdfIframe.attr('src', '');
            $previewContainer.hide();
            $clearBtn.hide();
        }
    });
</script>

@endpush