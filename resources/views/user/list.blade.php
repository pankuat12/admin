<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>User</h4>
                <h6>Manage your users</h6>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <div class="search-set">
                <div class="search-input">
                    <form action="{{ url('/') }}/user" method="post">
                        @csrf
                        <span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
                        <div id="DataTables_Table_0_filter" class="dataTables_filter">
                            <label>
                                <input type="search" value="{{ $like ?? '' }}"class="form-control form-control-sm"
                                    placeholder="Search" id="q" name="q">
                            </label>
                            @if (isset($like))
                                <a href="{{ url('/') }}/user" class="btn btn-primary">
                                    X
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="page-btn manageUser" data-bs-toggle="modal" data-bs-target="#add_user">
                <a class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Manage</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th class="ps-5">Name</th>
                            <th>E-mail</th>
                            <th>Number</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($user))
                            @foreach ($user as $u)
                                <tr>
                                    <td class="ps-5">
                                        <h6 class="fw-medium"><a href="#">{{ $u->fName }}
                                                {{ $u->lName }}</a></h6>
                                    </td>
                                    <td>{{ $u->mail }}</td>
                                    <td>{{ $u->number }}</td>
                                    <td>
                                        <a href="#"
                                            class="badge badge-soft-success d-inline-flex align-items-center badge-xs shadow-none">
                                            <i class="ti ti-checks me-1"></i>Approved
                                        </a>
                                    </td>
                                    <td class="action-table-data d-flex justify-content-start">
                                        <div class="edit-delete-action">
                                            <a class="me-2 p-2 showEdit" data-id="{{ $u->uniqueId }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-edit">
                                                    <path
                                                        d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                    </path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <a class="p-2 delete" href="javascript:void(0);"
                                                data-call="{{ base64_encode('tbl_people') }}"
                                                data-pick="{{ base64_encode($u->uniqueId) }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-trash-2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path
                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                    </path>
                                                    <line x1="10" y1="11" x2="10" y2="17">
                                                    </line>
                                                    <line x1="14" y1="11" x2="14" y2="17">
                                                    </line>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="4" class="text-center">No User Found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $user->appends(['q' => $like])->links() }}
                </div>
            </div>
        </div>
    </div>
    <!-- /product list -->
</div>
<div class="modal fade" id="addUser">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title d-flex align-items-center">
                    Add User
                </h4>
                <button type="button" class="btn-close custom-btn-close p-0" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body pb-0">
                <div class="row mb-2">
                    <div class="col-lg-6 col-6 ">
                        <label for="name">First Name <span class="danger">*</span></label>
                        <input type="text" class="form-control firstName" placeholder="Enter Your First Name">
                    </div>
                    <div class="col-lg-6 col-6">
                        <label for="name">Last Name</label>
                        <input type="text" class="form-control lastName" placeholder="Enter Your Last Name">
                    </div>
                </div>
                <div class="col mb-2">
                    <label for="email">E-mail <span class="danger">*</span></label>
                    <input type="email" class="form-control email" placeholder="Enter Your email">
                </div>

                <div class="row mb-3">
                    <div class="col-lg-6 col-6">
                        <label for="number"> Contact Number <span class="danger">*</span></label>
                        <input type="number" class="form-control contactNumber"
                            placeholder="Enter Your contact number">
                    </div>

                    <div class="col-lg-6 col-6">
                        <label for="password">Password <span class="danger">*</span></label>
                        <input type="password" class="form-control password" placeholder="Enter Your Password">
                    </div>
                </div>
                <div class="align-items-center d-flex gap-2">
                    <div class="file-drop-area">
                        <span class="">
                            <img src="{{ asset('/public/assets/img/cloud-upload.svg') }}" width="20"
                                alt="">
                        </span>
                        <span>Profile pic</span>
                        <input type="file" multiple="" class="profilePic subAImg" name="profilePic[]">
                    </div>
                    <div class="border">
                        <img width="70" height="70" src="" class="d-none objAImg" />
                    </div>
                </div>
            </div>
            <div class="modal-footer mt-3">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary addUser">Save Changes</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editUser">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title d-flex align-items-center">
                    Add User
                </h4>
                <button type="button" class="btn-close custom-btn-close p-0" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body pb-0 updateUserDiv">
            </div>
            <div class="modal-footer mt-3">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary updateUser">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let searchBox = document.getElementById("q");
            searchBox.addEventListener("input", function() {
                if (this.value === "") {
                    window.location.href = window.location.pathname;
                }
            });
        });
        $('.manageUser').click(function() {
            $("#addUser").modal('show');
        });
        $('.addUser').click(function() {
            var isValid = true;
            var firstName = $(".firstName").val();
            var lastName = $(".lastName").val();
            var email = $(".email").val();
            var contactNumber = $(".contactNumber").val();
            var password = $(".password").val();
            $('.firstName,.lastName,.email,.contactNumber,.password').each(function() {
                if (!validateField($(this), "This field is required!")) {
                    $(this).focus();
                    isValid = false;
                    return false;
                }
            });
            if (!isValid) return;
            var formData = new FormData();
            formData.append("firstName", firstName);
            formData.append("lastName", lastName);
            formData.append("email", email);
            formData.append("contactNumber", contactNumber);
            formData.append("password", password);
            formData.append("hiddenId", 0);
            if ($(".profilePic").length > 0) {
                formData.append("profilePic", $(".profilePic")[0].files[0]);
            }
            $.ajax({
                type: "POST",
                url: "{{ url('/user/update') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {
                    $('.addUser').html(
                        '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Wait'
                    );
                    $(".addUser").attr("disabled", true);
                },
                success: function(data) {
                    $('.addUser').html('Save Changes');
                    $(".addUser").attr("disabled", false);
                    response = data;
                    if (response.status == 1) {
                        Swal.fire({
                            title: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    }
                    if (response.status == 00) {
                        Swal.fire({
                            title: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
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
                // error: function (jqXHR, textStatus, errorThrown) {
                //     window.location.href = _WEB_ROOT_ + '/404';
                // }
            });
        });
        $('.showEdit').click(function() {
            var formData = new FormData();
            formData.append("id", $(this).data('id'));
            $.ajax({
                type: "POST",
                url: "{{ url('/get/user') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                dataType: "json",
                data: formData,
                success: function(data) {
                    response = data;
                    $("#editUser").modal('show');
                    $('.updateUserDiv').html(data.html);
                },
                // error: function (jqXHR, textStatus, errorThrown) {
                //     window.location.href = _WEB_ROOT_ + '/404';
                // }
            });
        });
        $('.updateUser').click(function() {
            var isValid = true;
            var firstName = $(".editfirstName").val();
            var lastName = $(".editlastName").val();
            var password = $(".editpassword").val();
            var hiddenId = $("#edit_hiddenId").val();
            $('.editfirstName,.editlastName').each(function() {
                if (!validateField($(this), "This field is required!")) {
                    $(this).focus();
                    isValid = false;
                    return false;
                }
            });
            if (!isValid) return;
            var formData = new FormData();
            formData.append("firstName", firstName);
            formData.append("lastName", lastName);
            formData.append("password", password);
            formData.append("hiddenId", hiddenId);
            if ($(".editprofilePic").length > 0) {
                formData.append("profilePic", $(".editprofilePic")[0].files[0]);
            }
            $.ajax({
                type: "POST",
                url: "{{ url('/user/update') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {
                    $('.updateUser').html(
                        '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Wait'
                    );
                    $(".updateUser").attr("disabled", true);
                },
                success: function(data) {
                    $('.updateUser').html('Save Changes');
                    $(".updateUser").attr("disabled", false);
                    response = data;
                    if (response.status == 1) {
                        Swal.fire({
                            title: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
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
                // error: function (jqXHR, textStatus, errorThrown) {
                //     window.location.href = _WEB_ROOT_ + '/404';
                // }
            });
        });
    </script>
@endpush
