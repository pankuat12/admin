    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
    <div class="content comp-section">
        <div class="cardhead card-header">
            <div class="row justify-content-between">
                <div class="col-6">
                    <h3 class="page-title">Add Product</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);">
                                Product
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Product</li>
                    </ul>
                </div>
                <div class="col-6 text-end">
                    <button type="button" class="btn btn-submit me-2 auProduct">Save Changes</button>
                    <a href="{{ url('/') }}/products" class="btn btn-cancel">Cancel</a>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <h2>Product Details</h2>
                        <p class="small">Tell the world all about your item and why they'll love it.</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-4">
                        <label for="productName" class="form-label mb-0 fw-bold">Title <span
                                class="text-danger">*</span></label>
                        <p class="small">A short name for your product (use keyword).</p>
                    </div>
                    <div class="col-lg-8">
                        <input type="text" class="form-control productName" id="productName" name="productName"
                            value="{{ isset($product->name) ? $product->name : '' }}" required>
                        <input type="hidden" id="hidden_id" name="hidden_id"
                            value="{{ isset($product->uniqueId) ? $product->uniqueId : 0 }}">
                        <input type="hidden" id="current_version" value="{{ (int) ($product->version ?? 1) }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-4">
                        <label for="productCategory" class="form-label mb-0 fw-bold">Category <span
                                class="text-danger">*</span></label>
                        <p class="small">Select category according to the product</p>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-control form-select productCategory" id="productCategory" name="category_id"
                            required>
                            <option value="">Select Option</option>
                            @if (isset($category))
                                @foreach ($category as $c)
                                    <option
                                        @if (isset($product)) @if ($product->category_id == $c->uniqueId) @selected(true) @endif
                                        @endif value="{{ $c->uniqueId }}">{{ $c->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="row mb-1">
                    <div class="col-lg-4">
                        <label for="publishStatus" class="form-label mb-0 fw-bold">Publish Status <span
                                class="text-danger">*</span></label>
                        <p class="small">Select product status accordingly</p>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-control form-select publishStatus" id="publishStatus" name="publishStatus">
                            <option value="">Select Option</option>
                            <option
                                @if (isset($product)) @if ($product->status == 1) @selected(true) @endif
                                @endif value="1">Active</option>
                            <option
                                @if (isset($product)) @if ($product->status == 0) @selected(true) @endif
                                @endif value="0">Inactive</option>
                            <option
                                @if (isset($product)) @if ($product->status == 2) @selected(true) @endif
                                @endif value="2">Draft</option>
                            <option
                                @if (isset($product)) @if ($product->status == 3) @selected(true) @endif
                                @endif value="3">Out of stock</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12 mb-3">
                        <h2>Price & Discount</h2>
                        <p class="small">Mention product MRP and discounted price.</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-4">
                        <label for="mrp" class="form-label mb-0 fw-bold">MRP <span
                                class="text-danger">*</span></label>
                        <p class="small">Enter the product’s Maximum Retail Price (before any discount).</p>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-group">
                            <span class="input-group-text bg-secondary"><i
                                    class="fa-solid fa-indian-rupee-sign"></i></span>
                            <input type="number" step="1" min="0" class="form-control mrp" id="mrp"
                                name="mrp" value="{{ isset($product->mrp) ? $product->mrp : '' }}" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-4">
                        <label for="sale_price" class="form-label mb-0 fw-bold">Sale Price <span
                                class="text-danger">*</span></label>
                        <p class="small">Final selling price after discount. Must be less than or equal to MRP.</p>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-group">
                            <span class="input-group-text bg-secondary"><i
                                    class="fa-solid fa-indian-rupee-sign"></i></span>
                            <input type="number" step="1" min="0" class="form-control sale_price"
                                id="sale_price" name="sale_price"
                                value="{{ isset($product->sale_price) ? $product->sale_price : '' }}" required>
                        </div>
                        <div class="form-text">Discount: <span id="discount_view">—</span></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-4">
                        <label for="sku" class="form-label mb-0 fw-bold">SKU <span
                                class="text-danger">*</span></label>
                        <p class="small">Unique product code (e.g., CAT-COLOR-SIZE-001).</p>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-group">
                            <span class="input-group-text bg-secondary">#</span>
                            <input type="text" class="form-control sku" id="sku" name="sku"
                                value="{{ isset($product->sku) ? $product->sku : '' }}" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-4">
                        <label for="stock_count" class="form-label mb-0 fw-bold">Stock <span
                                class="text-danger">*</span></label>
                        <p class="small">Available quantity for this product.</p>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-group">
                            <span class="input-group-text bg-secondary"><i class="fa fa-box"></i></span>
                            <input type="number" min="0" class="form-control stock_count" id="stock_count"
                                name="stock_count"
                                value="{{ isset($product->stock_count) ? $product->stock_count : '' }}" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-2 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label mb-0 fw-bold">Featured</label>
                        <p class="small">Mark this product to highlight it in listings.</p>
                    </div>
                    <div class="col-lg-8">
                        <!-- Hidden fallback ensures 0 is sent when unchecked -->
                        <input type="hidden" name="featured" value="0">
                        <div class="form-check">
                            <input class="form-check-input featured"
                                @if (isset($product)) @if ($product->featured == 1) @checked(true) @endif
                                @endif type="checkbox" id="featured" name="featured">
                            <label class="form-check-label" for="featured">Featured product</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <h2>Product Image </h2>
                    </div>
                </div>
                <style>
                    #productImgPreview .preview-item {
                        user-select: none;
                        position: relative;
                    }

                    #productImgPreview .drag-ghost {
                        opacity: .6;
                    }

                    .border-dashed {
                        border-style: dashed !important;
                    }

                    .dz {
                        background: #fafafa;
                        transition: .2s;
                    }

                    .dz.dragover {
                        background: #f0f8ff;
                        border-color: #0d6efd !important;
                    }

                    .dz .dz-icon {
                        color: #6c757d;
                    }

                    .preview-item {
                        position: relative;
                    }

                    .preview-item:focus {
                        outline: 2px solid #0d6efd;
                        outline-offset: 2px;
                    }
                </style>
                <div class="row mb-3">
                    <div class="col-lg-4 mt-3">
                        <input type="file" id="productImg" name="images[]" multiple accept="image/*"
                            class="d-none">
                        <!-- Pretty dropzone -->
                        <div id="dz" class="dz border border-2 border-dashed rounded p-4 text-center">
                            <div class="dz-icon mb-2">
                                <i class="fa-2x fa-solid fa-images"></i>
                            </div>
                            <div class="dz-text">
                                <strong>Drag & drop images</strong> here<br />
                                or <button type="button" class="btn btn-sm btn-primary mt-2"
                                    id="dzBrowse">Browse</button>
                            </div>
                            <div class="dz-hint small text-muted mt-2">JPG/PNG/WebP/GIF • up to 5MB each • max 8 images
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div id="productImgPreview" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3">
                        <label class="form-label fw-bold mb-0">Description</label>
                        <p class="small">
                            Start with a brief overview highlighting the product’s best features.
                        </p>
                    </div>
                    <div class="col-lg-9">
                        <textarea id="editor1" name="editor1" rows="3" class="form-control productDescription">{!! isset($product->description) ? $product->description : '' !!}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body row">
                <div class="col-lg-3">
                    <label class="form-label fw-bold mb-0">Product meta <span class="text-danger">*</span></label>
                    <p class="small">Add product meta to improve SEO.</p>
                </div>
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <label for="metaTitle" class="form-label fw-bold mb-0">Title <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control metaTitle" id="metaTitle" name="metaTitle"
                                value="{{ isset($product->meta_title) ? $product->meta_title : '' }}" required>
                        </div>
                        <div class="col-lg-12">
                            <label for="metaDescription" class="form-label fw-bold mb-0">Description <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control metaDescription" id="metaDescription" name="metaDescription" rows="3" required>{{ isset($product->meta_description) ? $product->meta_description : '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php
            $relPaths = isset($product->images) ? json_decode($product->images, true) : [];
            $relPaths = is_array($relPaths) ? $relPaths : [];
            $urls = array_map(fn($p) => asset($p), $relPaths);
        @endphp
        <script>
            CKEDITOR.replace('editor1');
            const EXISTING_IMAGES = @json($urls);
        </script>
    </div>
    @push('script')
        <script>
            let keepImages = Array.isArray(EXISTING_IMAGES) ? [...EXISTING_IMAGES] : [];
            let selectedFiles = [];
            (function($) {
                const ALLOWED = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                const MAX_SIZE_MB = 5;
                const MAX_FILES = 8;
                const signature = f => [f.name, f.size, f.lastModified].join('|');
                const bytesToMB = b => (b / (1024 * 1024));
                let sigSet = new Set();
                const $wrap = $('#productImgPreview'); // << must exist
                const $input = $('#productImg');
                const $dz = $('#dz');

                function toast(type, msg) {
                    if (window.Toastify) {
                        Toastify({
                            text: msg,
                            duration: 2200,
                            gravity: "bottom",
                            position: "center",
                            style: (type === 'error' ? err : apr)
                        }).showToast();
                    } else if (window.Swal) {
                        Swal.fire(type === 'error' ? 'Error' : 'Info', msg, type === 'error' ? 'error' : 'info');
                    } else {
                        alert(msg);
                    }
                }

                function rebuildFileInput() {
                    const dt = new DataTransfer();
                    selectedFiles.forEach(f => dt.items.add(f));
                    if ($input[0]) $input[0].files = dt.files;
                }

                function renderExisting() {
                    keepImages.forEach((url) => {
                        const $item = $(`
        <div class="preview-item border rounded me-2 mb-2"
             tabindex="0" data-type="existing" data-url="${url}"
             style="width:90px;height:90px;overflow:hidden;cursor:grab">
          <img src="${url}" class="w-100 h-100" style="object-fit:cover" draggable="false">
          <button type="button" class="btn-close position-absolute top-0 end-0 p-1
                  bg-white rounded-circle shadow-sm remove-img" aria-label="Remove"></button>
        </div>
      `);
                        // ensure no HTML5 draggable attr:
                        $item.removeAttr('draggable');
                        $wrap.append($item);
                    });
                }

                function renderNewTiles() {
                    $wrap.find('.preview-item[data-type="new"]').remove();
                    selectedFiles.forEach((file) => {
                        const url = URL.createObjectURL(file);
                        const sig = signature(file);
                        const $item = $(`
        <div class="preview-item border rounded me-2 mb-2"
             tabindex="0" data-type="new" data-sig="${sig}"
             style="width:90px;height:90px;overflow:hidden;cursor:grab">
          <img src="${url}" class="w-100 h-100" style="object-fit:cover" draggable="false">
          <button type="button" class="btn-close position-absolute top-0 end-0 p-1
                  bg-white rounded-circle shadow-sm remove-img" aria-label="Remove"></button>
        </div>
      `);
                        $item.removeAttr('draggable'); // critical
                        $wrap.append($item);
                    });
                }

                function rebuildOrderFromDOM() {
                    const newKeep = [];
                    const newNew = [];
                    $wrap.find('.preview-item').each(function() {
                        const $t = $(this);
                        const type = $t.data('type');
                        if (type === 'existing') {
                            newKeep.push($t.data('url'));
                        } else {
                            const sig = $t.data('sig');
                            const f = selectedFiles.find(x => signature(x) === sig);
                            if (f) newNew.push(f);
                        }
                    });
                    keepImages = newKeep;
                    selectedFiles = newNew;
                    rebuildFileInput();
                }

                function addFiles(files) {
                    const incoming = Array.from(files || []);
                    for (const file of incoming) {
                        if (!ALLOWED.includes(file.type)) {
                            toast('error', `${file.name}: not an image`);
                            continue;
                        }
                        if (bytesToMB(file.size) > MAX_SIZE_MB) {
                            toast('error', `${file.name}: > ${MAX_SIZE_MB}MB`);
                            continue;
                        }
                        if ((keepImages.length + selectedFiles.length) >= MAX_FILES) {
                            toast('error', `Max ${MAX_FILES} images allowed`);
                            break;
                        }
                        const sig = signature(file);
                        if (sigSet.has(sig)) {
                            toast('error', `You already selected "${file.name}"`);
                            continue;
                        }
                        sigSet.add(sig);
                        selectedFiles.push(file);
                    }
                    rebuildFileInput();
                    renderNewTiles();
                }

                $(function() {
                    if ($wrap.length === 0) {
                        console.error('[Images] #productImgPreview not found; Sortable cannot init.');
                        return;
                    }

                    renderExisting();

                    function showDiscount() {
                        const m = parseFloat($('#mrp').val());
                        const s = parseFloat($('#sale_price').val());
                        $('#discount_view').text(!isNaN(m) && m > 0 && !isNaN(s) && s >= 0 && s <= m ? Math.round((
                            m - s) / m * 100) + '%' : '—');
                    }
                    $('#mrp, #sale_price').on('input', showDiscount);
                    showDiscount();

                    $('#dzBrowse').on('click', () => $input.trigger('click'));
                    $input.on('change', function() {
                        addFiles(this.files);
                        this.value = '';
                    });

                    $dz.on('dragenter dragover', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $dz.addClass('dragover');
                    });
                    $dz.on('dragleave', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $dz.removeClass('dragover');
                    });
                    $dz.on('drop', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $dz.removeClass('dragover');
                        const dt = e.originalEvent.dataTransfer;
                        if (dt && dt.files && dt.files.length) addFiles(dt.files);
                    });

                    // Removal (also free the signature for re‑adding same file)
                    $(document).on('click', '.remove-img', function(e) {
                        e.stopPropagation();
                        const $tile = $(this).closest('.preview-item');
                        const type = $tile.data('type');
                        if (type === 'existing') {
                            const url = $tile.data('url');
                            keepImages = keepImages.filter(u => u !== url);
                            $tile.remove();
                        } else {
                            const sig = $tile.data('sig');
                            selectedFiles = selectedFiles.filter(f => signature(f) !== sig);
                            sigSet.delete(sig);
                            $tile.remove();
                            rebuildFileInput();
                        }
                        // after removal, keep DOM → arrays in sync
                        rebuildOrderFromDOM();
                    });

                    // ---- SortableJS init (robust for flex/wrap)
                    if (!window.Sortable) {
                        console.error('[Images] SortableJS not loaded. Include the CDN before this script.');
                    } else {
                        new Sortable($wrap[0], {
                            animation: 150,
                            draggable: '.preview-item',
                            handle: '.preview-item',
                            filter: '.remove-img', // clicks on close don’t start drag
                            preventOnFilter: true,
                            forceFallback: true, // ✅ fixes many flex/wrap issues
                            fallbackOnBody: true,
                            swapThreshold: 0.65,
                            onEnd: function() {
                                rebuildOrderFromDOM();
                            }
                        });
                    }
                });
            })(jQuery);

            // Submit
            function priceValid() {
                const mrp = +($('.mrp').val() || 0);
                const sale = +($('.sale_price').val() || 0);
                return !(mrp > 0 && sale > mrp);
            }

            $('.auProduct').on('click', function() {
                // basic required checks (you already have validateField)
                let ok = true;
                $('.productName,.productCategory,.publishStatus,.mrp,.sale_price,.sku,.stock_count,.metaTitle,.metaDescription')
                    .each(function() {
                        if (!validateField($(this), "This field is required!")) {
                            $(this).focus();
                            ok = false;
                            return false;
                        }
                    });
                if (!ok) return;

                if (!priceValid()) {
                    $('.sale_price').addClass('is-invalid');
                    toast('error', 'Sale Price cannot be higher than MRP');
                    return;
                } else {
                    $('.sale_price').removeClass('is-invalid');
                }

                const fd = new FormData();
                fd.append('name', $('.productName').val().trim());
                fd.append('category_id', $('.productCategory').val());
                fd.append('status', $('.publishStatus').val());
                fd.append('mrp', $('.mrp').val());
                fd.append('sale_price', $('.sale_price').val());
                fd.append('sku', $('.sku').val().trim());
                fd.append('stock_count', $('.stock_count').val());
                fd.append('featured', $('.featured').is(':checked') ? 1 : 0);
                fd.append('meta_title', $('.metaTitle').val().trim());
                fd.append('meta_description', $('.metaDescription').val().trim());
                fd.append('description', CKEDITOR.instances['editor1'].getData());
                fd.append('hidden_id', $('#hidden_id').val() || 0);
                fd.append('concurrency_version', Number($('#current_version').val() || 1));
                // existing URLs in final order
                fd.append('keep_images', JSON.stringify(keepImages));
                // new files in final order
                selectedFiles.forEach(f => fd.append('images[]', f));
                $.ajax({
                    type: "POST",
                    url: "{{ url('/products/update') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    data: fd,
                    beforeSend: function() {
                        $('.auProduct').html(
                            '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Wait'
                        );
                        $('.auProduct').prop('disabled', true);
                    },
                    success: function(res) {
                        $('.auProduct').html('Save Changes').prop('disabled', false);
                        if (res.status == 1) {
                            Swal.fire({
                                    title: res.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                })
                                .then(r => {
                                    if (r.isConfirmed) location.reload();
                                });
                        } else {
                            Swal.fire({
                                title: res.message,
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            });
        </script>
    @endpush
