<div class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo active">
        <a href="index.html" class="logo logo-normal">
        </a>
        <a href="index.html" class="logo logo-white">
        </a>
        <a href="index.html" class="logo-small">
        </a>
        <a id="toggle_btn" href="javascript:void(0);">
            <i data-feather="chevrons-left" class="feather-16"></i>
        </a>
    </div>
    <!-- /Logo -->
    <div class="modern-profile p-3 pb-0">
        <div class="text-center rounded bg-light p-3 mb-4 user-profile">
            <div class="avatar avatar-lg online mb-3">
                <img src="asset" alt="Img" class="img-fluid rounded-circle">
            </div>
            <h6 class="fs-14 fw-bold mb-1">Adrian Herman</h6>
            <p class="fs-12 mb-0">System Admin</p>
        </div>
        <div class="sidebar-nav mb-3">
            <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent" role="tablist">
                <li class="nav-item"><a class="nav-link active border-0" href="#">Menu</a></li>
                <li class="nav-item"><a class="nav-link border-0" href="chat.html">Chats</a></li>
                <li class="nav-item"><a class="nav-link border-0" href="email.html">Inbox</a></li>
            </ul>
        </div>
    </div>
    <div class="sidebar-header p-3 pb-0 pt-2">
        <div class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center">
            <div class="avatar avatar-md onlin">
            </div>
            <div class="text-start sidebar-profile-info ms-2">
                <h6 class="fs-14 fw-bold mb-1">Adrian Herman</h6>
                <p class="fs-12">System Admin</p>
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-between menu-item mb-3">
            <div>
                <a href="index.html" class="btn btn-sm btn-icon bg-light">
                    <i class="ti ti-layout-grid-remove"></i>
                </a>
            </div>
            <div>
                <a href="chat.html" class="btn btn-sm btn-icon bg-light">
                    <i class="ti ti-brand-hipchat"></i>
                </a>
            </div>
            <div>
                <a href="email.html" class="btn btn-sm btn-icon bg-light position-relative">
                    <i class="ti ti-message"></i>
                </a>
            </div>
            <div class="notification-item">
                <a href="activities.html" class="btn btn-sm btn-icon bg-light position-relative">
                    <i class="ti ti-bell"></i>
                    <span class="notification-status-dot"></span>
                </a>
            </div>
            <div class="me-0">
                <a href="general-settings.html" class="btn btn-sm btn-icon bg-light">
                    <i class="ti ti-settings"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="submenu-open">
                    <ul>
                        <li
                            class="@if (request()->path() == 'dashboard') {{ 'active bg-primary bg-opacity-10' }} @endif mb-2">
                            <a href="{{ url('/') }}/dashboard">
                                <i class="fa-solid fa-chart-line  fs-16 me-2"></i>
                                {{-- <i class="ti ti-stack-3 fs-16 me-2"></i> --}}
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="submenu-open border-top pt-2 border-3 border-bottom">
                            <h6 class="submenu-hdr mb-3">Inventory</h6>
                            <ul>
                                <li
                                    class="@if (request()->path() == 'category') {{ 'active bg-primary bg-opacity-10' }} @endif mb-2">
                                    <a href="{{ url('/') }}/category">
                                        <i class="fa-solid fa-list  fs-16 me-2"></i>
                                        <span>Category</span>
                                    </a>
                                </li>
                                <li
                                    class="@if (request()->path() == 'products') {{ 'active bg-primary bg-opacity-10' }} @endif mb-2">
                                    <a href="{{ url('/') }}/products">
                                        <i class="fa-solid fa-boxes-stacked fs-16 me-2"></i>
                                        <span>Products</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li
                            class="@if (request()->path() == 'user') {{ 'active bg-primary bg-opacity-10' }} @endif my-2">
                            <a href="{{ url('/') }}/user">
                                <i class="fa-solid fa-users fs-16 me-2"></i>
                                <span>User</span>
                            </a>
                        </li>

                        <li
                            class="@if (request()->path() == 'admin/audit-logs') {{ 'active bg-primary bg-opacity-10' }} @endif mb-2">
                            <a href="{{ url('/') }}/admin/audit-logs">
                                <i class="fa-solid fa-database fs-16 me-2"></i>
                                <span>Audit Log</span>
                            </a>
                        </li>
                        <li
                            class="mb-2">
                            <a href="{{ url('/') }}/logout">
                                <i class="fa-solid fa-arrow-right-from-bracket fs-16 me-2"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
