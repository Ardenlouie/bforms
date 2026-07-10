<form action="{{ route('store.gate',encrypt($form->id)) }}" method="POST" id="add_gate" enctype="multipart/form-data">
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
            <div class="col-lg-4"></div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Category <small class="text-danger font-italic text-bold">(required)</small></label>
                        <select class="form-control" name="category" id="category" form="add_gate">
                            <option value="IT Equipment">IT Equipment</option>
                            <option value="Marketing Materials">Marketing Materials</option>
                            <option value="Documents">Documents</option>
                            <option value="Others">Others (Please Specify)</option>
                        </select>
                    <small class="text-danger">{{$errors->first('category')}}</small>
                </div>

                <div class="form-group" id="specify_category_wrapper" style="display: none;">
                    <label class="mb-0">Please Specify <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="text" class="form-control" name="category_specified" id="category_specified" form="add_gate" placeholder="Enter Category">
                    <small class="text-danger">{{$errors->first('category_specified')}}</small>
                </div>
            </div>
        <input type="hidden" name="form_id"  value="{{ encrypt($form->id) }}">
        <input type="hidden" name="numberof"  value="1">

        </div>  
        <div class="row">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Receive By <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="text" class="form-control" name="received_by" form="add_gate"> 
                    <small class="text-danger">{{$errors->first('received_by')}}</small>
                </div>
            </div>
            <div class="col-lg-3"></div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Release Date<small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="date" class="form-control" name="release_date" form="add_gate" value="{{ date('Y-m-d') }}"> 
                    <small class="text-danger">{{$errors->first('release_date')}}</small>
                </div>
            </div>            
        </div>
        <div class="row">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Purpose <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="text" class="form-control" name="purpose" form="add_gate"> 
                    <small class="text-danger">{{$errors->first('purpose')}}</small>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="form-group">
                    <label class="mb-0">Note <small class=" font-italic text-bold">(optional)</small></label>
                    <input type="text" class="form-control" name="note" form="add_gate"> 
                    <small class="text-danger">{{$errors->first('note')}}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 table-responsive">
                <label class="mb-0">Items <small class="text-danger font-italic text-bold">(required)</small></label>
                <table class="table table-bordered text-center" id="dynamicTable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th style="min-width: 300px;">Release Item</th>
                            <th>UOM</th>
                            <th>Qty</th>
                            <th>Remarks</th>
                            <th><button type="button" name="add" id="addBtn" class="btn btn-success"><i class="fa fa-plus"></i></button></th>
                        </tr>
                    </thead>
                    @php
                        $num = 1;
                    @endphp
                    <tbody >
                        <tr>
                            <td class="row-number">1</td>
                            <td ><input type="text" name="items[0][desc]" placeholder="Enter Item Description" class="form-control text-center desc" /></td>             
                            <td>
                                <select name="items[0][uom]" class="form-control text-center uom">
                                    <option value="PCS">PCS</option>
                                    <option value="CS">CS</option>
                                    <option value="IN">IN</option>
                                </select>
                            </td>
                            <td><input type="number" name="items[0][qty]" placeholder="Enter Qty" class="form-control text-center qty" value="1" min="1" /></td>
                            <td><input type="text" name="items[0][remarks]" placeholder="Enter Remarks" class="form-control text-center remarks" /></td>
                            <td><button type="button" class="btn btn-danger removeRow">x</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    {{ html()->label(__('Upload Attachment'), 'file_name')->class(['mb-0']) }} <small class=" font-italic text-bold">(optional)</small>
                    <input
                        form="add_gate"
                        type="file"
                        id="file_name"
                        name="file_name"
                        accept="application/pdf"
                        class="form-control {{ $errors->has('file_name') ? 'is-invalid' : '' }}"
                    > 
                    <small class="text-danger">{{$errors->first('file_name')}}</small>
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
            <!-- <div class="col-lg-6">
                <div class="form-group">
                    <div id="photo_preview_container" style="display:none;">
                    <label>Photo Preview</label><br>

                        <img id="photo_preview" src="" class="img-thumbnail" style="max-height: 500px;">
                        <div class="mt-2">
                            <button type="button" class="btn btn-xs btn-danger" id="remove_photo">
                                <i class="fas fa-trash"></i> Remove & Retake
                            </button>
                        </div>
                    </div>
                    
                    <div id="upload_controls">
                        <label>Upload Photo</label><br>

                        <button type="button" class="btn btn-info" id="open_camera">
                            <i class="fas fa-camera"></i> Take Photo
                        </button>
                    </div>

                    <input type="hidden" name="image" id="captured_image_input">
                </div>
            </div> -->
        </div>
    </div>
    <div class="card-footer text-right">
        <input type="hidden" id="status" name="status" form="add_gate" value="pending">
        <a class="btn-draft btn btn-secondary">Save as Draft</a>

        <a href="#" title="preview" class="btn-preview btn btn-primary">Preview</a>

        <div class="modal fade" id="modal-preview">
            <div class="modal-dialog modal-xl">
                <livewire:summary.gate-pass  />
            </div>
        </div>

        <div id="camera_modal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Camera Capture</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body text-center">
                        <video id="video" width="100%" height="auto" autoplay playsinline class="rounded border"></video>
                        <canvas id="canvas" style="display:none;"></canvas>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="snap" class="btn btn-success">
                            <i class="fas fa-camera"></i> Capture
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('css')
<style>
    #photo_preview {
        width: 100%;
        max-width: 500px;
        height: auto;
        object-fit: cover;
        border: 3px solid #dee2e6;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('js')
<script>
    let i = 0;

    document.getElementById("addBtn").addEventListener("click", function () {
        i++;
        let table = document.querySelector("#dynamicTable tbody");
        let newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td class="row-number"></td>
            <td><input type="text" name="items[${i}][desc]" placeholder="Enter Item Description" class="form-control text-center desc" /></td>
            <td>
                <select name="items[${i}][uom]" class="form-control text-center uom">
                    <option value="PCS">PCS</option>
                    <option value="CS">CS</option>
                    <option value="IN">IN</option>
                </select>
            </td>
            <td><input type="number" name="items[${i}][qty]" placeholder="Enter Qty" class="form-control text-center qty" value="1" min="1" /></td>
            <td><input type="text" name="items[${i}][remarks]" placeholder="Enter Remarks" class="form-control text-center remarks" /></td>
            <td><button type="button" class="btn btn-danger removeRow">x</button></td>
        `;
        table.appendChild(newRow);
        updateRowNumbers();
        emitPSRF();
    });

    $(document).on('input', '.qty', function() {
        let val = parseFloat($(this).val());
        
        if (val <= 0 || isNaN(val)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    document.addEventListener("click", function (e) {
        if (e.target && e.target.classList.contains("removeRow")) {
            e.target.closest("tr").remove();
            updateRowNumbers();
            emitPSRF();

        }
    });


    function emitPSRF() {
        let data = {
            form_id: document.querySelector('input[name="form_id"]').value || "-",
            company_id: document.querySelector('select[name="company_id"]').value || "-",
            purpose: document.querySelector('input[name="purpose"]').value || "-",
            receive_by: document.querySelector('input[name="receive_by"]').value || "-",
        };

        let items = [];
        document.querySelectorAll('#dynamicTable tbody tr').forEach(row => {
            let desc = row.querySelector(".desc").value || "-";
            let uom = row.querySelector(".uom").value || "-";
            let qty = parseFloat(row.querySelector(".qty").value) || 0;
            let remarks = row.querySelector(".remarks").value || "-";

            items.push({ desc, uom, qty, remarks });
        });

        Livewire.dispatch('loadGateSummary',{ data, items });
    }

    function updateRowNumbers() {
        document.querySelectorAll("#dynamicTable tbody tr").forEach((row, index) => {
            row.querySelector(".row-number").textContent = index + 1;
        });
    }

    updateRowNumbers();
</script>

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
                    $('#add_gate').submit();
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
                text: "Are you sure you want to submit this Gate Pass Form?",
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
                    $('#add_gate').submit();

                }
                });
        });
    });
</script>

<script>
    $(function() {
        $('body').on('click', '.btn-preview', function(e) {
            let hasError = false;
            let errorMessage = "";
        
            $('.qty').each(function() {
            let val = parseFloat($(this).val());

            if (isNaN(val) || val <= 0) {
                    hasError = true;
                    $(this).addClass('is-invalid'); 
                    errorMessage = "Quantity must be greater than 0.";
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            if (hasError) {
                e.stopPropagation(); 

                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Input',
                    text: errorMessage,
                    confirmButtonColor: '#d33'
                });

                return false; 
            }
                e.preventDefault();

            let data = {
                form_id: document.querySelector('input[name="form_id"]').value || "-",
                company_id: document.querySelector('select[name="company_id"]').value || "-",
                numberof: "1",
                purpose: document.querySelector('input[name="purpose"]').value || "-",
                release_date: document.querySelector('input[name="release_date"]').value || "-",
                received_by: document.querySelector('input[name="received_by"]').value || "-",
                category: document.querySelector('select[name="category"]').value || "-",
                others: document.querySelector('input[name="category_specified"]').value,
                note: document.querySelector('input[name="note"]').value || "-",
            };

            let items = [];
            document.querySelectorAll('#dynamicTable tbody tr').forEach(row => {
                let desc = row.querySelector(".desc").value || "-";
                let uom = row.querySelector(".uom").value || "-";
                let qty = parseFloat(row.querySelector(".qty").value) || 0;
                let remarks = row.querySelector(".remarks").value || "-";

                items.push({ desc, uom, qty, remarks });
            });
   
            Livewire.dispatch('loadGateSummary',{ data, items });
            $('#modal-preview').modal('show');
        });
    });
</script>

<script>
$(document).ready(function() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const snap = document.getElementById('snap');
    let stream;

    // Open Camera Modal & Start Stream
    $('#open_camera').on('click', function() {
        $('#camera_modal').modal('show');
        
        navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" }, audio: false })
            .then(s => {
                stream = s;
                video.srcObject = stream;
            })
            .catch(err => {
                Swal.fire('Error', 'Camera access denied or not found.', 'error');
            });
    });

    // Capture Image
    snap.addEventListener('click', function() {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        context.drawImage(video, 0, 0, canvas.width, canvas.height);


        const dataURL = canvas.toDataURL('image/png');

        $('#captured_image_input').val(dataURL);

        $('#photo_preview').attr('src', dataURL);
        $('#photo_preview_container').show();
        $('#upload_controls').hide(); 

        stream.getTracks().forEach(track => track.stop());
        $('#camera_modal').modal('hide');

        Swal.fire('Success', 'Photo captured!', 'success');

    });

    $('#camera_modal').on('hidden.bs.modal', function () {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });
});
</script>

<script>

$('#remove_photo').on('click', function() {
    // Clear the input
    $('#captured_image_input').val('');
    
    // Reset the preview
    $('#photo_preview').attr('src', '');
    
    // Toggle buttons back
    $('#photo_preview_container').hide();
    $('#upload_controls').show();
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
    $('#category').on('change', function() {
        let selectedValue = $(this).val();
        let wrapper = $('#specify_category_wrapper');
        let inputField = $('#category_specified');

        if (selectedValue === 'Others') {
            wrapper.slideDown(); // Smoothly reveals input
            inputField.prop('required', true); // Forces validation constraint
        } else {
            wrapper.slideUp(); // Hides container
            inputField.prop('required', false).val(''); // Clears and relaxes constraint
        }
    });
});
</script>
@endpush