<form action="{{ route('update.gate', encrypt($all_form->id)) }}" method="POST" id="update_gate">
    <div class="card-body">
        @csrf          
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Company <small class="text-danger font-italic text-bold">(required)</small></label>
                    {{ html()->select('company_id', $companies, $all_form->model->company_id)->class(['form-control', 'form-control', 'is-invalid' => $errors->has('company_id')]) }}
                    <small class="text-danger">{{$errors->first('company_id')}}</small>
                </div>
            </div>
        <input type="hidden" name="form_id"  value="{{ encrypt($form->id) }}">
        <input type="hidden" name="control_number"  value="{{ $all_form->model->control_number }}">
        <input type="hidden" name="date_submitted"  value="{{ date('Y-m-d') }}">
        <input type="hidden" name="purpose"  value="{{ $all_form->model->purpose }}">

        <input type="hidden" name="psrf_form_id"  value="{{ $all_form->model->id }}">
        <input type="hidden" name="category"  value="Product Sample">

        </div>  
        <div class="row">
            <div class="col-lg-5">
                <div class="form-group">
                    <label class="mb-0">Purpose <small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="text" class="form-control" name="purpose" form="update_gate" value="{{ $all_form->model->purpose }}" disabled> 
                    <small class="text-danger">{{$errors->first('purpose')}}</small>
                </div>
            </div>
            <div class="col-lg-2"></div>
            <div class="col-lg-5">
                @php
                    $psrf_number = \App\Models\ProductSample::where('id', $all_form->model->psrf_form_id)->first();
                @endphp
                <div class="form-group">
                    <label class="mb-0">Product Sample Request Form:</label>
                    <h4><b>[{{ $psrf_number->control_number }}]</b></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label class="mb-0">Note <small class=" font-italic text-bold">(optional)</small></label>
                    <input type="text" class="form-control" name="note" form="update_gate" value="{{ $all_form->model->note }}"> 
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label class="mb-0">Release Date<small class="text-danger font-italic text-bold">(required)</small></label>
                    <input type="date" class="form-control" name="release_date" form="update_gate" value="{{ $all_form->model->release_date }}"> 
                    <small class="text-danger">{{$errors->first('release_date')}}</small>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-md-12 table-responsive ">
                <label class="mb-0">Items <small class="text-danger font-italic text-bold">(required)</small></label>
                <table class="table table-bordered text-center" id="dynamicTable">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th style="min-width: 300px;">Release Item</th>
                            <th>UOM</th>
                            <th>Qty</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    @php
                        $num = 1;
                    @endphp
                    <tbody >
                        @foreach ($all_form->model->gate_pass_item()->get() as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td ><input type="text" name="items[{{ $index }}][desc]" value="{{ $item['item_description'] }}" class="form-control text-center desc" disabled/></td>             
                            <td ><input type="text" name="items[{{ $index }}][uom]" value="{{ $item['uom'] }}" class="form-control text-center uom" disabled/></td>
                            <td><input type="number" name="items[{{ $index }}][qty]" value="{{ $item['quantity'] }}" class="form-control text-center qty" disabled/></td>
                            <td><input type="text" name="items[{{ $index }}][remarks]" value="{{ $item['remarks'] }}" class="form-control text-center remarks" disabled/></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Number of Receiver/s <small class="text-danger font-italic text-bold">(required)</small></label>
                    {{-- This input controls how many fields appear --}}
                    <input type="number" id="numberof" class="form-control" form="update_gate" name="numberof" min="1" value="1"  value="{{ $all_form->model->numberof }}">
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="form-group">
                    <label>Received By <small class="text-danger font-italic text-bold">(required)</small></label>
                    <div id="receivers-container">
                    <input type="text" 
                        class="form-control mb-2" 
                        name="received_by[]" 
                        form="update_gate" 
                        required placeholder="Receiver 1 Name"
                        value="{{ is_array($all_form->model->received_by) ? implode(', ', $all_form->model->received_by) : $all_form->model->received_by }}">
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-right">
        <input type="hidden" id="status" name="status" form="update_gate" value="pending">
        <a class="btn-draft btn btn-secondary">Save as Draft</a>

        <a href="#" title="preview" class="btn-preview btn btn-primary">Preview</a>

        <div class="modal fade" id="modal-preview">
            <div class="modal-dialog modal-xl">
                <livewire:summary.gate-pass  />
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
            <td><input type="text" name="items[${i}][uom]" placeholder="Enter UOM" class="form-control text-center uom" /></td>
            <td><input type="number" name="items[${i}][qty]" placeholder="Enter Qty" class="form-control text-center qty" value="1" min="1" /></td>
            <td><input type="text" name="items[${i}][remarks]" placeholder="Enter Remarks" class="form-control text-center remarks" /></td>
            <td><button type="button" class="btn btn-danger removeRow">x</button></td>
        `;
        table.appendChild(newRow);
        updateRowNumbers();
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
        }
    });

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
                    $('#update_gate').submit();
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
                    $('#status').val('approval');
                    $('#update_gate').submit();

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
                control_number: document.querySelector('input[name="control_number"]').value,
                company_id: document.querySelector('select[name="company_id"]').value || "-",
                purpose: document.querySelector('input[name="purpose"]').value || "-",
                release_date: document.querySelector('input[name="release_date"]').value || "-",
                numberof: document.querySelector('input[name="numberof"]').value || "-",
                category: document.querySelector('input[name="category"]').value || "-",
                others: "",
                note: document.querySelector('input[name="note"]').value || "-",
                received_by: Array.from(document.querySelectorAll('input[name="received_by[]"]'))
                  .map(input => input.value.trim() || "-")
                  .join(', '),
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
$(document).ready(function() {
    $('#numberof').on('input change', function() {
        let count = parseInt($(this).val()) || 1;
        let container = $('#receivers-container');
        
        // Safety guard: Don't allow less than 1
        if (count < 1) {
            $(this).val(1);
            count = 1;
        }

        // Get the current number of inputs inside the container
        let currentInputs = container.find('input[name="received_by[]"]').length;

        if (count > currentInputs) {
            // Add more fields if count increased
            for (let i = currentInputs + 1; i <= count; i++) {
                container.append(`
                    <input type="text" 
                           class="form-control mb-2 extra-receiver" 
                           name="received_by[]" 
                           required 
                           placeholder="Receiver ${i} Name">
                `);
            }
        } else if (count < currentInputs) {
            // Remove fields from the bottom if count decreased
            for (let i = currentInputs; i > count; i--) {
                container.find('input[name="received_by[]"]').last().remove();
            }
        }
    });
});
</script>
@endpush