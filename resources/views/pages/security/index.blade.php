@extends('layouts.app')

@section('content_header')
<div class="row">
    <div class="col-md-6">
        <h1></h1>
    </div>

</div>
@endsection

@section('content_body')
<div class="card mb-4" style="max-width: 500px; margin: 0 auto; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
    <div class="card-header bg-dark text-white text-center font-weight-bold">
        <i class="fas fa-qrcode"></i> B-FORMS QR SCANNER
    </div>
    <div class="card-body">
        
        <!-- Live Camera Viewport Window -->
        <div id="qr-reader-viewport" style="width: 100%; background: #f8fafc; border-radius: 8px;"></div>
        
        <!-- Results Container Target Input -->
        <div class="form-group mt-3">
            <label class="font-weight-bold text-muted">SCANNED RESULT</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-link"></i></span>
                </div>
                <input type="text" id="scanned-result-field" class="form-control form-control-lg" placeholder="Waiting for QR code..." readonly>
            </div>
        </div>

        <!-- Optional: Audio alert element upon success -->
        <audio id="qr-beep-sound" src="https://assets.mixkit.co/active_storage/sfx/2568/2568-84.wav" preload="auto"></audio>
    </div>
</div>
@stop

{{-- Push extra CSS --}}
@push('css')

@endpush

{{-- Push extra scripts --}}
@push('js')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const resultField = document.getElementById('scanned-result-field');
    const beepSound = document.getElementById('qr-beep-sound');
    let lastScannedText = "";

    // Initialize the Scanner UI engine configuration parameters
    const html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader-viewport", 
        { 
            fps: 15, 
            qrbox: { width: 250, height: 250 },
            rememberLastUsedCamera: true,
            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
        },
        false
    );

    // This block executes the moment a QR code gets detected by the camera viewport
    function onScanSuccess(decodedText, decodedResult) {
        // Prevent duplicate redirect execution loops if they keep the code in frame
        if (decodedText === lastScannedText) return; 
        lastScannedText = decodedText;
        
        // 1. Play auditory success confirmation chime
        if (beepSound) beepSound.play();

        // 2. Display the URL link value in the text input box layout row
        resultField.value = "Redirecting: " + decodedText;
        resultField.className = "form-control form-control-lg is-valid bg-success text-white font-weight-bold";

        // 3. Stop the camera lens processing stream right away to conserve device battery
        html5QrcodeScanner.clear().then(() => {
            console.log("Camera loop suspended safely. Running redirect execution routing next.");
            
            // 4. ROUTE REDIRECT: Instantly redirect the browser window to the scanned system URL link
            window.location.href = decodedText;
            
        }).catch(error => {
            // Fallback redirect path execution if camera cleanup yields warnings
            window.location.href = decodedText;
        });
    }

    function onScanFailure(error) {
        // Continuous tracking frame skips are muted for silent console logs
    }

    // Fire up the interactive camera tracking canvas container
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
});
</script>

@endpush