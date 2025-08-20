<div class="account-content">
    <div class="login-wrapper login-new">
        <div class="row w-100">
            <div class="col-lg-5 mx-auto">
                <div class="login-content user-login">
                    <div class="login-logo">
                    </div>
                    <div class="card">
                        <div class="card-body p-5">
                            <div class="login-userheading text-center">
                                <h3>Sign In</h3>
                                <h4>Access the Admin panel using your email and passcode.</h4>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger"> *</span></label>
                                <div class="input-group">
                                    <input type="text" value="" class="form-control border-end-0 userMail">
                                    <span class="input-group-text border-start-0">
                                        <i class="ti ti-mail"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger"> *</span></label>
                                <div class="pass-group">
                                    <input type="password" class="pass-input form-control userPassword">
                                    <span class="ti toggle-password ti-eye-off text-gray-9"></span>
                                </div>
                            </div>
                            <div class="form-login authentication-check">
                                <div class="row">
                                    <div class="col-12 d-flex align-items-center justify-content-between">
                                        <div class="custom-control custom-checkbox">
                                            <label class="checkboxs ps-4 mb-0 pb-0 line-height-1 fs-16 text-gray-6">
                                                <input type="checkbox" class="form-control">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-login">
                                <button type="button" class="btn btn-primary w-100 doSignIn">Sign In</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="my-4 d-flex justify-content-center align-items-center copyright-text">
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script>
        $('.doSignIn').click(function() {
            var isValid = true;
            var userMail = $(".userMail").val();
            var userPassword = $(".userPassword").val();
            $('.userMail,.userPassword').each(function() {
                if (!validateField($(this), "This field is required!")) {
                    $(this).focus();
                    isValid = false;
                    return false;
                }
            });
            if (!isValid) return;
            var formData = new FormData();
            formData.append("userMail", userMail);
            formData.append("userPassword", userPassword);
            $.ajax({
                type: "POST",
                url: "{{ url('/admin/login') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {
                    $('.doSignIn').html(
                        '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Wait'
                    );
                    $(".doSignIn").attr("disabled", true);
                },
                success: function(data) {
                    $('.doSignIn').html('Sign In');
                    $(".doSignIn").attr("disabled", false);
                    response = data;
                    if (response.status == 1) {
                        Toastify({
                            gravity: "bottom",
                            position: "center",
                            text: data.message,
                            duration: 5000,
                            style: apr
                        }).showToast();
                        setTimeout(function() {
                            window.location.href = "{{ url('/dashboard') }}";
                        }, 1000);
                    } else if (response.status == 2) {
                        if (response.type == 'mail') {
                            $('.userMail').focus();
                            $('.userMail').addClass("is-invalid");
                            Toastify({
                                gravity: "bottom",
                                position: "center",
                                text: data.message,
                                duration: 1500,
                                style: err
                            }).showToast();
                        } else {
                            $('.userMail').removeClass("is-invalid");
                        }
                        if (response.type == 'password') {
                            $('.userPassword').focus();
                            $('.userPassword').addClass("is-invalid");
                            Toastify({
                                gravity: "bottom",
                                position: "center",
                                text: data.message,
                                duration: 1500,
                                style: err
                            }).showToast();
                        } else {
                            $('.userPassword').removeClass("is-invalid");
                        }
                    } else {
                        Swal.fire({
                            title: response.message,
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    }
                },
            });
        });
    </script>
@endpush
