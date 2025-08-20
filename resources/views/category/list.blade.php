<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Categories</h4>
                <h6>Manage your product categories</h6>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <div class="search-set">
                <div class="search-input">
                    <form action="{{ url('/category') }}" method="post">
                        @csrf
                        <span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
                        <div class="dataTables_filter">
                            <label>
                                <input type="search" value="{{ $like ?? '' }}" class="form-control form-control-sm"
                                    placeholder="Search Category" name="q">
                            </label>
                            @if (isset($like))
                                <a href="{{ url('/category') }}" class="btn btn-primary">X</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="page-btn" data-bs-toggle="modal" data-bs-target="#addCategory">
                <a class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add Category</a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th class="ps-5">Name</th>
                            <th>Slug</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($category) && count($category) > 0)
                            @foreach ($category as $c)
                                <tr>
                                    <td class="ps-5">
                                        <h6 class="fw-medium">{{ $c->name }}</h6>
                                    </td>
                                    <td>{{ $c->slug }}</td>
                                    <td>{{ date('d/m/Y', $c->createdOn) }}</td>
                                    <td class="action-table-data d-flex justify-content-start">
                                        <div class="edit-delete-action">
                                            <a class="me-2 p-2 showEditCategory" data-id="{{ $c->uniqueId }}">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <a class="p-2 delete" data-call="{{ base64_encode('categories') }}"
                                                data-pick="{{ base64_encode($c->uniqueId) }}"
                                                href="javascript:void(0);">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center">No Categories Found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="d-flex justify-content-center my-3">
                    {{ $category->appends(['q' => $like])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addCategory">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Category</h4>
                <button type="button" class="btn-close p-0" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label for="categoryName">Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control categoryName" placeholder="Enter category name">
                </div>
            </div>
            <div class="modal-footer mt-3">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary saveCategory">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editCategory">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Category</h4>
                <button type="button" class="btn-close p-0" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body editCategoryDiv">
            </div>
            <div class="modal-footer mt-3">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary updateCategory">Save Changes</button>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $('.saveCategory').click(function() {
            var isValid = true;
            var name = $(".categoryName").val();
            $('.categoryName').each(function() {
                if (!validateField($(this), "This field is required!")) {
                    $(this).focus();
                    isValid = false;
                    return false;
                }
            });
            if (!isValid) return;
            var formData = new FormData();
            formData.append("categoryName", name);
            formData.append("hiddenId", 0);
            $.ajax({
                type: "POST",
                url: "{{ url('/category/update') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {
                    $('.saveCategory').html(
                        '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Wait'
                    );
                    $(".saveCategory").attr("disabled", true);
                },
                success: function(data) {
                    $('.saveCategory').html('Save');
                    $(".saveCategory").attr("disabled", false);
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
                        });
                    }
                },
            });
        });
        $('.showEditCategory').click(function() {
            var id = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "{{ url('/category/get') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id
                },
                success: function(data) {
                    $("#editCategory").modal('show');
                    $('.editCategoryDiv').html(data.html);
                }
            });
        });
        $('.updateCategory').click(function() {
            var isValid = true;
            var id = $("#editCategoryId").val();
            var name = $(".editCategoryName").val();
            $('.editCategoryName').each(function() {
                if (!validateField($(this), "This field is required!")) {
                    $(this).focus();
                    isValid = false;
                    return false;
                }
            });
            if (!isValid) return;
            var formData = new FormData();
            formData.append("hiddenId", id);
            formData.append("categoryName", name);
            $.ajax({
                type: "POST",
                url: "{{ url('/category/update') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {
                    $('.updateCategory').html(
                        '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Wait'
                    );
                    $(".updateCategory").attr("disabled", true);
                },
                success: function(response) {
                    $('.updateCategory').html('Save Changes');
                    $(".updateCategory").attr("disabled", false);
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
                }
            });
        });
    </script>
@endpush
