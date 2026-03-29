@extends('adminlte::page')

{{-- Extend and customize the browser title --}}

@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle') | @yield('subtitle') @endif
@stop

@auth
    @section('content_top_nav_right')

        <!-- Online Users -->
        <li class="nav-item">
            <a href="#" class="nav-link" id="btn-online-users" hidden>
                <i class="fa fa-user"></i>
                <span class="navbar-badge">
                    <i class="fa fa-circle text-success"></i>
                </span>
            </a>
        </li>

        <!-- language toggle -->
        <li class="nav-item dropdown" hidden>
            <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                @php
                    $locale = app()->getLocale();
                    $flags = ['en' => 'us', 'ja' => 'jp', 'zh-CN' => 'cn'];
                @endphp
                <span class="fi fi-{{ $flags[$locale] ?? 'us' }}"></span> {{ strtoupper($locale) }}
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="langDropdown">
                <a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">
                    <span class="fi fi-us shadow"></span> English
                </a>
                <a class="dropdown-item" href="{{ route('lang.switch', 'ja') }}">
                    <span class="fi fi-jp shadow"></span> Japanese
                </a>
                <a class="dropdown-item" href="{{ route('lang.switch', 'zh-CN') }}">
                    <span class="fi fi-cn shadow"></span> Chinese
                </a>
            </div>
        </li>

        {{-- Dark mode toggle --}}
        <livewire:darkmode-toggle />

        <!-- Notifications Dropdown Menu -->
        <livewire:notification/>
    @endsection
@else
    @section('content_top_nav_right')
        <li class="nav-item">
            <a href="{{ route('login') }}" class="btn btn-success text-uppercase"> <span class="fas fa-sign-in-alt"></span> {{ __('adminlte::adminlte.log_in') }}</a>
        </li>
    @endsection

@endauth

{{-- Extend and customize the page content header --}}

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

    <!-- DELETE MODAL -->
    <div class="modal fade" id="modal-delete">
        <div class="modal-dialog">
            <livewire:delete-model/>
        </div>
    </div>

    <div class="modal fade" id="online-users-modal" aria-hidden="true">
        <div class="modal-dialog">
            <livewire:online-users/>
        </div>
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

{{-- Setup Custom Preloader Content --}}

@section('preloader')
    <i class="fas fa-atom fa-spin fa-10x text-primary"></i>
    <h3 class="mt-3 text-secondary">Please wait...</h3>
@stop

{{-- plugins --}}
@section('iCheckBoostrap', true)

{{-- Add common Javascript/Jquery code --}}

@push('js')
<script>
    $(function() {
        // Dark mode toggle
        $('#darkModeToggle').on('click', function(e) {
            e.preventDefault();
            $('body').toggleClass('dark-mode');
            $(this).find('i').toggleClass('fa-moon').toggleClass('fa-sun');

            $('body').find('.main-header')
                .toggleClass('navbar-dark navbar-light')
                .toggleClass('navbar-dark navbar-dark', !$('body').find('.main-header').hasClass('navbar-dark navbar-dark'));
        });

        $('body').on('click','#btn-online-users', function(e) {
            e.preventDefault();
            $('#online-users-modal').modal('show');
        });
    });


</script>
<script>
    $(function() {
        $('body').on('click', '.btn-endorse', function(e) {
            e.preventDefault();

            Swal.fire({
                title: "Final Confirmation",
                text: "Are you sure you want to endorse this Form?",
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
                    text: "Form has been endorsed.",
                    icon: "success"
                    });
                    
                    Swal.showLoading();
                    $('#status').val('approval');
                    $('#approve').submit();

                }
                });
        });
    });
</script>

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

<script>
    $(function() {
        $('body').on('click', '.btn-follow', function(e) {
            e.preventDefault();
            
            let targetUrl = $(this).data('url');
            
            Swal.fire({
                title: "Final Confirmation",
                text: "Are you sure you want to follow-up this Form?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#0ba236",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, notify them!",
                cancelButtonText: "No",
                }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                    allowOutsideClick: false,
                    title: "Notification Sent!",
                    text: "Another notification sent to the next approver.",
                    icon: "success",
                    });
                    
                    window.location.href = targetUrl;
                    Swal.showLoading();
                }
                });
        });
    });
</script>

<script>
$(function() {
    $('body').on('click', '.btn-decline', function(e) {
        e.preventDefault();

        Swal.fire({
            title: "Decline Form",
            text: "Please provide a reason for declining this form:",
            icon: "warning",
            input: 'textarea', // Adds the text box
            inputPlaceholder: 'Type your remarks here...',
            inputAttributes: {
                'aria-label': 'Type your remarks here'
            },
            showCancelButton: true,
            confirmButtonColor: "#d33", // Red for decline
            cancelButtonColor: "rgb(73, 73, 73)",
            confirmButtonText: "Decline Form",
            preConfirm: (value) => {
                if (!value) {
                    Swal.showValidationMessage('A reason is required to decline.');
                }
                return value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // 1. Set the status
                $('#status').val('declined');
                
                // 2. Set the remarks/reason
                // Ensure you have an input with id="remarks" in your form
                $('#remarks').val(result.value); 

                // 3. Show final success and submit
                Swal.fire({
                    allowOutsideClick: false,
                    title: "Declined!",
                    text: "The form has been declined with your remarks.",
                    icon: "success",
                });
                Swal.showLoading();
                $('#approve').submit();
            }
        }); 
    });
});
</script>

<script>
$(function() {
    $('body').on('click', '.btn-checked', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Enter Security PIN',
            input: 'password',
            inputLabel: 'Security Authorization Required',
            inputPlaceholder: 'Enter your 4-digit PIN',
            inputAttributes: {
                maxlength: 4,
                autocapitalize: 'off',
                autocorrect: 'off'
            }
        }).then((result) => {
            if (result.value === '1234') { // Replace with actual validation logic or backend check
                $('#status').val('checked');
                $('#check').submit();
            } else if (result.value) {
                Swal.fire('Error', 'Invalid Security PIN', 'error');
            }
        });
    });
});
</script>

<script>
    $(document).ready(function() {
    $('#user_select').select2({
        placeholder: "Select User",
        allowClear: true,
        theme: "classic",
        ajax: {
            url: "{{ route('users.ajax') }}", // Create this route in web.php
            dataType: 'json',
            delay: 250, // Wait 250ms before sending request (debounce)
            data: function (params) {
                return {
                    search: params.term // This sends the 'search' variable to PHP
                };
            },
            processResults: function (data) {
                return {
                    results: data.results
                };
            },
            cache: true
        }
    });
});
</script>
<script>
    $(document).ready(function() {
    $('#cost_center').select2({
        placeholder: "Select Cost Center",
        allowClear: true,
        theme: "classic",
        ajax: {
            url: "{{ route('cost_centers.ajax') }}", // Create this route in web.php
            dataType: 'json',
            delay: 250, // Wait 250ms before sending request (debounce)
            data: function (params) {
                return {
                    search: params.term // This sends the 'search' variable to PHP
                };
            },
            processResults: function (data) {
                return {
                    results: data.results
                };
            },
            cache: true
        }
    });
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
@endpush
