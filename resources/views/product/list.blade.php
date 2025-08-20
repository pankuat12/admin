<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css">
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>
<style>
    .noUi-target {
        height: 6px !important;
        border-radius: 8px !important;
        background: #e5e7eb !important;
        border: none !important;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, .05) !important;
    }

    .noUi-connect {
        background: #3b82f6 !important;
        border-radius: 8px !important;
        transition: background .3s !important;
    }

    .noUi-handle {
        width: 16px !important;
        height: 16px !important;
        top: -5px !important;
        right: -8px !important;
        border-radius: 50% !important;
        border: 2px solid #fff !important;
        background: #3b82f6 !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .15) !important;
        cursor: grab !important;
        transition: transform .2s ease, background .2s ease !important;
    }

    .noUi-handle:hover {
        transform: scale(1.15) !important;
        background: #2563eb !important;
    }

    .noUi-handle:focus-visible {
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .4) !important;
    }

    .noUi-handle:before,
    .noUi-handle:after {
        display: none !important;
    }

    .noUi-tooltip {
        background: #111827 !important;
        color: #fff !important;
        border-radius: 4px !important;
        padding: 2px 6px !important;
        font-size: 12px !important;
        white-space: nowrap !important;
    }

    .noUi-horizontal .noUi-tooltip {
        bottom: 24px !important;
    }

    .noUi-tooltip::after {
        content: "" !important;
        position: absolute !important;
        left: 50% !important;
        bottom: -5px !important;
        transform: translateX(-50%) !important;
        border: 5px solid transparent !important;
        border-top-color: #111827 !important;
    }

    .noUi-pips {
        margin-top: 8px !important;
        color: #6b7280 !important;
        font-size: 11px !important;
    }

    .noUi-marker {
        background: #d1d5db !important;
    }

    .noUi-marker-large {
        height: 8px !important;
        background: #9ca3af !important;
    }

    .noUi-value {
        color: #6b7280 !important;
    }
</style>
<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Products</h4>
            <h6>Manage your Product</h6>
        </div>
        <div>
            <a href="{{ route('products.add') }}" class="btn btn-success">
                <i class="ti ti-circle-plus me-1"></i>Add Product
            </a>
            <button id="btnImportCsv" type="button" class="btn btn-primary">Import CSV</button>
        </div>
    </div>
    <div class="card">
        <div class="px-3 py-2">
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <form action="{{ route('products.list') }}" method="post" class="w-100">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col">
                            <label class="form-label">Search</label>
                            <input type="search" name="q" id="q" value="{{ $filters['like'] ?? '' }}"
                                class="form-control" placeholder="Search by name or slug">
                        </div>
                        <div class="col">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">All</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->uniqueId }}"
                                        @if (($filters['category_id'] ?? '') == $cat->uniqueId) selected @endif>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col mx-3">
                            <label class="form-label d-block">Price Range</label>
                            <div id="priceRange" style="margin: .6rem 0 0.4rem;"></div>
                            <div class="d-flex justify-content-between small">
                                <span>Min: <strong id="priceMinLabel">₹0</strong></span>
                                <span>Max: <strong id="priceMaxLabel">₹0</strong></span>
                            </div>
                            <input type="hidden" name="min_price" id="min_price"
                                value="{{ request('min_price', number_format($bounds->min, 2, '.', '')) }}">
                            <input type="hidden" name="max_price" id="max_price"
                                value="{{ request('max_price', number_format($bounds->max, 2, '.', '')) }}">
                        </div>
                        <div class="col">
                            <label class="form-label">Featured</label>
                            <select name="featured" class="form-select">
                                <option value="" @selected(($filters['featured'] ?? '') === '')>All</option>
                                <option value="1" @selected(($filters['featured'] ?? '') === '1')>Featured</option>
                                <option value="0" @selected(($filters['featured'] ?? '') === '0')>Not Featured</option>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label d-block">Status</label>
                            <select name="status" class="form-control form-select" id="status">
                                <option value="">All</option>
                                <option @selected(request('status') === '1') value="1">Active</option>
                                <option @selected(request('status') === '0') value="0">Inactive</option>
                                <option @selected(request('status') === '2') value="2">Draft</option>
                                <option @selected(request('status') === '3') value="3">Out of stock</option>
                            </select>
                        </div>
                        <div class="col d-flex justify-content-end gap-2">
                            <button class="btn btn-primary">Apply</button>
                            <a href="{{ route('products.list') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="d-flex mt-2 gap-2">
                <select id="bulkAction" class="form-select w-auto">
                    <option value="">Bulk Action…</option>
                    <option value="price">Update Price</option>
                    <option value="status">Change Status</option>
                    <option value="stock">Update Stock</option>
                </select>
                <button id="bulkGo" class="btn btn-success" disabled>Apply</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:32px;">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                            </th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th class="text-center">is featured</th>
                            <th>Status</th>
                            <th>Stock</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($product as $p)
                            <tr>
                                <td><input type="checkbox" class="row-check" value="{{ $p->uniqueId }}"></td>
                                <td class="d-flex justify-content-center align-items-center gap-1">
                                    @php
                                        $imgs = json_decode($p->images, true) ?: [];
                                        $first = $imgs[0] ?? null;
                                    @endphp
                                    @if ($first)
                                        <img src="{{ url('/') . '/' . $first }}" alt="{{ $p->name }}"
                                            width="50" height="50" style="object-fit:cover;border-radius:6px;">
                                    @else
                                        <div class="bg-light d-inline-block"
                                            style="width:50px;height:50px;border-radius:6px;"></div>
                                    @endif
                                    {{ $p->name }}
                                </td>
                                <td>{{ $p->category_name }}</td>
                                <td>{{ $p->sku }}</td>
                                <td>{{ $p->sale_price }}</td>
                                <td class="text-center">
                                    @if ($p->featured == 1)
                                        <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                                    @else
                                        <i class="fa-solid fa-star"></i>
                                    @endif
                                </td>
                                <td>
                                    @if ($p->status == 1)
                                        <span class="badge badge-success">Active</span>
                                    @elseif($p->status == 0)
                                        <span class="badge badge-warning">In Active</span>
                                    @elseif($p->status == 2)
                                        <span class="badge badge-secondary">Draft</span>
                                    @elseif($p->status == 3)
                                        <span class="badge badge-danger">Out of stock</span>
                                    @endif
                                </td>
                                <td>{{ $p->stock_count }}</td>
                                <td class="action-table-data">
                                    <div class="edit-delete-action">
                                        <a class="me-2 p-2"
                                            href="{{ route('products.edit', base64_encode($p->uniqueId)) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-edit">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                </path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a class="me-2 p-2"
                                            href="{{ route('products.copy', base64_encode($p->uniqueId)) }}">
                                            <i class="fa-solid fa-copy"></i>
                                        </a>
                                        <a class="p-2 deleteProduct" data-id="{{ base64_encode($p->uniqueId) }}"
                                            href="javascript:void(0);">
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
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No products found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-3">
                    {{ $product->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bulk Modals --}}
<div class="modal fade" id="priceModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="priceForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Bulk: Update Price</h5>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label">Mode</label>
                    <select name="price_mode" class="form-select">
                        <option value="set">Set exact</option>
                        <option value="add_amount">Add amount (+/-)</option>
                        <option value="add_percent">Add percent (+/-)</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Value</label>
                    <input type="number" step="0.01" name="price_value" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="statusForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Bulk: Change Status</h5>
            </div>
            <div class="modal-body">
                <label class="form-label">Status</label>
                <select name="status_value" class="form-select">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                    <option value="2">Draft</option>
                    <option value="3">Out of stock</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="stockForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Bulk: Update Stock</h5>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label">Mode</label>
                    <select name="stock_mode" class="form-select">
                        <option value="set">Set</option>
                        <option value="add">Increase by</option>
                        <option value="sub">Decrease by</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Value</label>
                    <input type="number" name="stock_value" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Apply</button>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="csvModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('admin.products.import') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Import (CSV / Excel)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                @if (session('import_summary'))
                    @php $s = session('import_summary'); @endphp
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @if ($s['created_count'] + $s['updated_count'] === 0 && $s['skipped_count'] > 0)
                        <div class="alert alert-info">
                            Your file matched existing data exactly. No changes were applied.
                        </div>
                    @endif
                @endif
                <div class="mb-3">
                    <label class="form-label">Select file</label>
                    <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls" required>
                    <small class="text-muted">
                        <a href="{{ asset('dummy.xlsx') }}">Download template</a>
                    </small>
                </div>

                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="auto_create_categories" id="autoCreate"
                        checked>
                    <label class="form-check-label" for="autoCreate">Auto-create missing categories</label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </div>
</div>


@push('script')
    @if (session('import_summary'))
        <script>
            $(document).ready(function() {
                $('#csvModal').modal('show');
            });
        </script>
    @endif
    </script>
    <script>
        $('.deleteProduct').click(function() {
            var id = $(this).data("id");
            $("#productId").val(id);
            $("#delete_product").modal('show');
        });
        $('.soft').click(function() {
            var id = $("#productId").val();
            var formData = new FormData();
            formData.append("type", 'soft');
            formData.append("id", id);
            $.ajax({
                type: "POST",
                url: "{{ url('/products/delete') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {
                    $('.soft').html(
                        '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Wait'
                    );
                    $(".soft").attr("disabled", true);
                },
                success: function(data) {
                    $('.soft').html('Soft Delete');
                    $(".soft").attr("disabled", false);
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
            });
        });
        $('.hard').click(function() {
            var id = $("#productId").val();
            var formData = new FormData();
            formData.append("type", 'hard');
            formData.append("id", id);
            $.ajax({
                type: "POST",
                url: "{{ url('/products/delete') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {
                    $('.hard').html(
                        '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Wait'
                    );
                    $(".hard").attr("disabled", true);
                },
                success: function(data) {
                    $('.hard').html('Hard Delete');
                    $(".hard").attr("disabled", false);
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
            });
        });
        $('#btnImportCsv').click(function() {
            $("#csvModal").modal('show');
        });
    </script>
    {{-- <script>
        $(document).on('click', '#btnImportCsv', function() {
            $('#csvFile').trigger('click');
        });

        $(document).on('change', '#csvFile', function() {
            if (!this.files || !this.files.length) return;
            var fd = new FormData();
            var csrf = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
            fd.append('_token', csrf);
            fd.append('file', this.files[0]); // the selected CSV
            fd.append('duplicate_strategy', 'update'); // or 'skip'
            $.ajax({
                url: "{{ route('products.import.file') }}",
                type: "POST",
                data: fd,
                processData: false, // important for FormData
                contentType: false, // important for FormData
                success: function(res) {
                    if (res && res.success) {
                        var s = res.summary || {};
                        var msg =
                            "Import completed.\n" +
                            "Created: " + (s.created || 0) + "\n" +
                            "Updated: " + (s.updated || 0) + "\n" +
                            "Skipped: " + (s.skipped || 0) + "\n" +
                            "Failed: " + (s.failed || 0) + "\n" +
                            "Total: " + (s.total || 0) + "\n" +
                            (res.error_report_url ? ("Error report: " + res.error_report_url) : "");
                        alert(msg);
                        location.reload();
                    } else {
                        alert('Import failed: ' + ((res && res.message) ? res.message :
                            'Unknown error'));
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    var msg = 'Import request error.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        msg += ' ' + xhr.responseJSON.message;
                    }
                    alert(msg);
                }
            });
        });
    </script> --}}
    <script>
        $(function() {
            // Bulk UI
            const $bulkGo = $('#bulkGo');
            const $bulkAction = $('#bulkAction');
            $('#selectAll').on('change', function() {
                $('.row-check').prop('checked', this.checked);
                toggleBulkGo();
            });
            $(document).on('change', '.row-check', toggleBulkGo);
            $bulkAction.on('change', toggleBulkGo);

            function toggleBulkGo() {
                const any = $('.row-check:checked').length > 0;
                $bulkGo.prop('disabled', !any);
            }
            $bulkGo.on('click', function(e) {
                e.preventDefault();
                const action = $bulkAction.val();
                if (!action) return;
                if (action === 'price') return new bootstrap.Modal('#priceModal').show();
                if (action === 'status') return new bootstrap.Modal('#statusModal').show();
                if (action === 'stock') return new bootstrap.Modal('#stockModal').show();
            });

            function selectedIds() {
                return $('.row-check:checked').map(function() {
                    return $(this).val();
                }).get();
            }

            function postBulk(payload, $modal) {
                payload._token = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
                $.post('{{ route('products.bulk') }}', payload)
                    .done(res => {
                        $modal?.modal('hide');
                        Swal.fire({
                            title: res.message || 'Done',
                            icon: 'success'
                        }).then(() => location.reload());
                    })
                    .fail(xhr => {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Bulk update failed.';
                        Swal.fire({
                            title: msg,
                            icon: 'error'
                        });
                    });
            }

            $('#priceForm').on('submit', function(e) {
                e.preventDefault();
                postBulk({
                    action: 'price',
                    ids: selectedIds(),
                    price_mode: $('[name="price_mode"]').val(),
                    price_value: $('[name="price_value"]').val()
                }, $('#priceModal'));
            });
            $('#statusForm').on('submit', function(e) {
                e.preventDefault();
                postBulk({
                    action: 'status',
                    ids: selectedIds(),
                    status_value: $('[name="status_value"]').val()
                }, $('#statusModal'));
            });
            $('#stockForm').on('submit', function(e) {
                e.preventDefault();
                postBulk({
                    action: 'stock',
                    ids: selectedIds(),
                    stock_mode: $('[name="stock_mode"]').val(),
                    stock_value: $('[name="stock_value"]').val()
                }, $('#stockModal'));
            });
        });
    </script>
    <script>
        $(function() {
            var el = document.getElementById('priceRange');
            if (!el) return;
            var BMIN = parseFloat(@json($bounds->min ?? 0));
            var BMAX = parseFloat(@json($bounds->max ?? 0));
            // Current values (fallback to bounds)
            var curMin = parseFloat('{{ request('min_price') !== null ? request('min_price') : $bounds->min }}');
            var curMax = parseFloat('{{ request('max_price') !== null ? request('max_price') : $bounds->max }}');
            // Currency helpers
            var CURRENCY = '₹';

            function fmt(n, keep2 = false) {
                return CURRENCY + Number(n).toLocaleString(undefined, {
                    minimumFractionDigits: keep2 ? 2 : 0,
                    maximumFractionDigits: keep2 ? 2 : 0
                });
            }
            noUiSlider.create(el, {
                start: [curMin, curMax],
                connect: true,
                step: 1, // use 0.5 or 0.01 if you need finer steps
                range: {
                    min: BMIN,
                    max: BMAX
                },
                behaviour: 'tap-drag',
            });
            var $minInput = $('#min_price');
            var $maxInput = $('#max_price');
            var $minLabel = $('#priceMinLabel');
            var $maxLabel = $('#priceMaxLabel');

            function sync(values) {
                var min = Math.round(values[0]);
                var max = Math.round(values[1]);
                $minInput.val(min);
                $maxInput.val(max);
                $minLabel.text(fmt(min));
                $maxLabel.text(fmt(max));
            }
            sync([curMin, curMax]);

            el.noUiSlider.on('update', function(values) {
                sync(values);
            });
        });
    </script>
@endpush
