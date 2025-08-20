<!DOCTYPE html>
<html lang="en" data-layout-mode="light_mode">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Admin Panel">
    <meta name="keywords"
        content="Admin Panel">
    <meta name="author" content="Admin Panel">
    <meta name="robots" content="index, follow">
    <title>Admin Panel </title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">
    <link rel="stylesheet" href="{{ asset('/public/assets/css/bootstrap.min.css?v=') . time() }}">
    <link rel="stylesheet" href="{{ asset('/public/assets/css/animate.css?v=') . time() }}">
    <link rel="stylesheet" href="{{ asset('/public/assets/plugins/select2/css/select2.min.css?v=') . time() }}">
    <link rel="stylesheet" href="{{ asset('/public/assets/plugins/tabler-icons/tabler-icons.css?v=') . time() }}">
    <link rel="stylesheet" href="{{ asset('/public/assets/plugins/fontawesome/css/fontawesome.min.css?v=') . time() }}">
    <link rel="stylesheet" href="{{ asset('/public/assets/plugins/fontawesome/css/all.min.css?v=') . time() }}">
    <link rel="stylesheet" href="{{ asset('/public/assets/css/style.css?v=') . time() }}">
    <link rel="stylesheet" href="{{ asset('/public/assets/plugins/toast/toaster.css?v=') . time() }}">
    <link rel="stylesheet" href="{{ asset('/public/assets/plugins/sweet/sweets.css?v=') . time() }}">
</head>

<body>
    @if (Session::get('fail'))
        <div class="ccwd toastify on toastify-center toastify-bottom" aria-live="polite"
            style="background: #dc3545; transform: translate(0px, 0px); bottom: 15px;">
            {{ Session::get('fail') }}
        </div>
    @endif
    @if (Session::get('pass'))
        <div class="ccwd toastify on toastify-center toastify-bottom" aria-live="polite"
            style="background: #198754; transform: translate(0px, 0px); bottom: 15px;">
            {{ Session::get('pass') }}
        </div>
    @endif
    <div class="main-wrapper">
        @if (isset($header))
            @include('layout.header')
        @endif
        @if (isset($sidebar))
            @include('layout.sidebar')
        @endif
        @if (isset($sidebar))
            <div class="page-wrapper">
                @if (isset($body))
                    @include($body)
                @endif
                @if (isset($footer))
                    @include('layout.footer')
                @endif
            </div>
        @else
            @if (isset($body))
                @include($body)
            @endif
        @endif
        <div class="modal fade" id="csd">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="page-wrapper-new p-0">
                        <div class="content p-5 px-3 text-center">
                            <span class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"><i
                                    class="ti ti-trash fs-24 text-danger"></i></span>
                            <h4 class="fs-20 text-gray-9 fw-bold mb-2 mt-1">Delete</h4>
                            <p class="text-gray-6 mb-0 fs-16">Are you sure you want to delete ?</p>
                            <div class="modal-footer-btn mt-3 d-flex justify-content-center">
                                <button type="button"
                                    class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none"
                                    data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <input type="hidden" id="call" value="">
                                <input type="hidden" id="pick" value="">
                                <button type="button" class="btn btn-primary fs-13 fw-medium p-2 px-3 confirm">
                                    Yes Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="delete_product">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="page-wrapper-new p-0">
                        <div class="content p-5 px-3 text-center">
                            <span class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"><i
                                    class="ti ti-trash fs-24 text-danger"></i></span>
                            <h4 class="fs-20 text-gray-9 fw-bold mb-2 mt-1">Delete</h4>
                            <p class="text-gray-6 mb-0 fs-16">Are you sure you want to delete product?</p>
                            <input type="hidden" id="productId" value="">
                            <div class="modal-footer-btn mt-3 d-flex justify-content-center">
                                <button type="button"
                                    class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none"
                                    data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="button" class="btn btn-warning me-2 fs-13 fw-medium p-2 px-3 soft">
                                    Soft Delete
                                </button>
                                <button type="button" class="btn btn-danger me-2 fs-13 fw-medium p-2 px-3 hard">
                                    Hard Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('/public/assets/js/jquery-3.7.1.min.js?v=') . time() }}"></script>
    <script src="{{ asset('/public/assets/js/feather.min.js?v=') . time() }}"></script>
    <script src="{{ asset('/public/assets/js/jquery.slimscroll.min.js?v=') . time() }}"></script>
    <script src="{{ asset('/public/assets/js/bootstrap.bundle.min.js?v=') . time() }}"></script>
    <script src="{{ asset('public/assets/plugins/toast/toaster.js?v=') . time() }}"></script>
    <script src="{{ asset('public/assets/plugins/sweet/sweets.js?v=') . time() }}"></script>
    <script src="{{ asset('/public/assets/js/script.js?v=') . time() }}"></script>
    <script src="{{ asset('/public/assets/js/common.js?v=') . time() }}"></script>
    @stack('script')
</body>

</html>
