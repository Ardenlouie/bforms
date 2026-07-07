@extends('adminlte::page')

{{-- Extend and customize the browser title --}}

@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle') | @yield('subtitle') @endif
@stop



@section('content_header')

    @hasSection('content_header_title')
        <h1 class="text-muted">
            @yield('content_header_title')

            @hasSection('content_header_subtitle')
                <small class="text-dark">
                    <i class="fas fa-xs fa-angle-right text-muted"></i>
                    @yield('content_header_subtitle')
                </small>
            @endif
        </h1>
        
    @endif
@stop

{{-- Rename section content to content_body --}}

@section('content')
    @yield('content_body')

    <div id="imageModal" class="modal-img">
        <span class="close">&times;</span>
        <img id="modalImage" src="" alt="Expanded Image">
    </div>

 
@stop

{{-- Create a common footer --}}

@section('footer')
    <div class="float-right text-dark">
        Version: {{ config('app.version', '1.0.0') }}
    </div>

    <strong>Copyright &copy; {{ date('Y') }}
            <a href="https://www.bevi.com.ph/" target="_blank">BEVI Beauty Elements Ventures Inc.</a>
        </strong>
@stop



@push('js')

<script>
    $(function() {
        $('body').on('click', '.btn-approve', function(e) {
            e.preventDefault();

            Swal.fire({
                title: "Final Confirmation",
                text: "Are you sure you want to approve this Form?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#0ba236",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, approve it!",
                cancelButtonText: "No",
                }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                    allowOutsideClick: false,
                    title: "Approved!",
                    text: "Form has been approved.",
                    icon: "success"
                    });

                    Swal.showLoading();
                    $('#status').val('approved');
                    $('#approve').submit();

                }
                });
        });
    });
</script>

</script>
<script>
    const galleryImages = document.querySelectorAll('.popup-image');
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');

    const closeModal = document.querySelector('.modal-img .close');

    galleryImages.forEach((image) => {
        image.addEventListener('click', () => {
            modalImage.src = image.src; 

            imageModal.style.display = 'flex';
        });
    });

    closeModal.addEventListener('click', () => {
        imageModal.style.display = 'none';
    });

    imageModal.addEventListener('click', (event) => {
        if (event.target === imageModal) {
            imageModal.style.display = 'none';
        }
    });
</script>


@endpush

{{-- Add common CSS customizations --}}

@push('css')
@laravelPWA
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7/css/flag-icons.min.css"/> --}}
<style type="text/css">
    img {
        display: inline;
    }
    .line {
        border-top: 2px solid black;
        margin-bottom: 5px;
    }
</style>
<style>
    .gallery {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
    }

    .gallery img {
        cursor: pointer;
        border-radius: 10px;
        transition: transform 0.3s ease;
        
    }



    .gallery img:hover {
        transform: scale(1.05);
    }


    .modal-img {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
    }

    .modal-img img {
        max-width: 100%;
        max-height: 100%;
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    .modal-img img:hover {
        transform: scale(1.1); /* Zoom effect */
    }

    .modal-img .close {
        position: absolute;
        top: 20px;
        right: 30px;
        font-size: 30px;
        font-weight: bold;
        color: white;
        cursor: pointer;
    }

    .modal-img .close:hover {
        color: red;
    }

</style>
<style>

.swal2-shown {
    overflow: hidden !important;
}
body.swal2-height-auto {
    height: 100% !important;
}
</style>
@endpush
