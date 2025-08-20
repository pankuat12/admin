<div class="content">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-2">
        <div class="mb-3">
            <h1 class="mb-1">Welcome, {{$name}}</h1>
        </div>
        <div class="input-icon-start position-relative mb-3">
            <span class="input-icon-addon fs-16 text-gray-9">
                <i class="ti ti-calendar"></i>
            </span>
            <input type="text" class="form-control w-75" value="{{ date('d M Y') }}">
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-primary sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-primary">
                        <i class="fa-solid fa-users fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Total Users</p>
                        <div class="d-inline-flex align-items-center flex-wrap gap-2">
                            <h4 class="text-white">{{ $tu }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-secondary sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-secondary">
                        <i class="fa-solid fa-list  fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Total Category</p>
                        <div class="d-inline-flex align-items-center flex-wrap gap-2">
                            <h4 class="text-white">{{ $tc }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-teal sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-teal">
                        <i class="fa-solid fa-boxes-stacked fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Total Product</p>
                        <div class="d-inline-flex align-items-center flex-wrap gap-2">
                            <h4 class="text-white">{{ $tp }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">

        <!-- Top Selling Products -->
        <div class="col-xxl-4 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-inline-flex align-items-center">
                        <span class="title-icon bg-soft-pink fs-16 me-2"><i class="ti ti-box"></i></span>
                        <h5 class="card-title mb-0">Recent Added Products</h5>
                    </div>
                </div>
                <div class="card-body sell-product">
                    @if (isset($recentProduct))
                        @foreach ($recentProduct as $rp)
                            <div class="d-flex align-items-center justify-content-between border-bottom">
                                <div class="d-flex align-items-center">
                                    @php
                                        $imgs = json_decode($rp->images, true) ?: [];
                                        $first = $imgs[0] ?? null;
                                    @endphp
                                    @if ($first)
                                        <a href="javascript:void(0);" class="avatar avatar-lg">
                                            <img src="{{ url('/') . '/' . $first }}" alt="img">
                                        </a>
                                    @else
                                        <div class="bg-light d-inline-block"
                                            style="width:50px;height:50px;border-radius:6px;"></div>
                                    @endif

                                    <div class="ms-2">
                                        <h6 class="fw-bold mb-1"><a href="javascript:void(0);">{{ $rp->name }}</a>
                                        </h6>
                                        <div class="d-flex align-items-center item-list">
                                            <p>₹{{ $rp->sale_price }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <!-- /Top Selling Products -->

        <!-- Low Stock Products -->
        <div class="col-xxl-4 col-md-6 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-inline-flex align-items-center">
                        <span class="title-icon bg-soft-danger fs-16 me-2"><i class="ti ti-alert-triangle"></i></span>
                        <h5 class="card-title mb-0">Low Stock Products</h5>
                    </div>
                </div>
                <div class="card-body">
                    @if (isset($lowStock))
                        @foreach ($lowStock as $rp)
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                     @php
                                        $imgs = json_decode($rp->images, true) ?: [];
                                        $first = $imgs[0] ?? null;
                                    @endphp
                                    @if ($first)
                                        <a href="javascript:void(0);" class="avatar avatar-lg">
                                            <img src="{{ url('/') . '/' . $first }}" alt="img">
                                        </a>
                                    @else
                                        <div class="bg-light d-inline-block"
                                            style="width:50px;height:50px;border-radius:6px;"></div>
                                    @endif
                                    <div class="ms-2">
                                        <h6 class="fw-bold mb-1"><a href="javascript:void(0);">{{ $rp->name }}</a></h6>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <p class="fs-13 mb-1">Instock</p>
                                    <h6 class="text-orange fw-medium">{{ $rp->stock_count }}</h6>
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div>
            </div>
        </div>
        <!-- /Low Stock Products -->
    </div>
</div>
