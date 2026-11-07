<!-- Navbar -->
<nav class="card navbar navbar-main navbar-expand-lg px-0 mx-2 mx-md-4 shadow-none border-radius-xl ims-topbar" id="navbarBlur"
    navbar-scroll="true">
    <div class="container-fluid py-1 px-2 px-md-3 flex-nowrap">
        <a href="javascript:;" id="mobileSidenavToggle" class="ims-mobile-toggle d-xl-none me-2" aria-label="Open menu" title="Menu">
            <i class="fa fa-bars"></i>
        </a>
        <nav aria-label="breadcrumb" class="ims-topbar-title">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5 d-none d-md-flex">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ url('dashboard') }}">Pages</a></li>
                <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
                    {{ str_replace(['-', '_'], ' ', Request::segment(1)) }}</li>
            </ol>
            <h6 class="font-weight-bolder mb-0 text-capitalize text-truncate">{{ str_replace(['-', '_'], ' ', Request::segment(1) ?: 'dashboard') }}</h6>
        </nav>
        <div class="collapse navbar-collapse mt-0 me-md-0 d-flex justify-content-end ims-topbar-actions" id="navbar">

            <ul class="navbar-nav flex-row align-items-center justify-content-end me-0 me-md-4">
                @can('Inquiry add')
                <li class="nav-item d-flex align-items-center">
                    <a href="{{ url('inquiry/create') }}" class="nav-link text-body font-weight-bold px-0 me-3" title="New Inquiry">
                        <i class="fa fa-plus me-sm-1"></i>
                        <span class="d-sm-inline d-none">Inquiry</span>
                    </a>
                </li>
                @endcan
                <li class="nav-item d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body font-weight-bold px-0 me-3" onclick="darkMode(this)" title="Dark Mode">
                        <i class="fa fa-moon me-sm-1"></i>
                        <span class="d-lg-inline d-none">Dark Mode</span>
                    </a>
                </li>

                @if(session()->has('impersonated_by'))
                <li class="nav-item d-flex align-items-center me-2">
                    <a href="{{ route('users.leave_impersonate') }}" class="btn btn-xs btn-outline-warning mb-0 px-2 py-1 d-flex align-items-center gap-1 shadow-xs" title="Return to Parent Account">
                        <i class="fa-solid fa-user-secret text-warning"></i>
                        <span class="d-none d-sm-inline text-xxs font-weight-bold">Impersonating</span>
                        <span class="badge bg-warning text-dark text-xxs ms-1">Exit &times;</span>
                    </a>
                </li>
                @endif

                <li class="nav-item dropdown d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body font-weight-bold px-0 dropdown-toggle d-flex align-items-center" id="navbarProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        @if(auth()->user()->profile_picture && file_exists(public_path(auth()->user()->profile_picture)))
                            <img src="{{ asset(auth()->user()->profile_picture) }}" class="avatar avatar-xs rounded-circle me-1" style="width: 28px; height: 28px; object-fit: cover;" alt="{{ auth()->user()->name }}">
                        @else
                            <div class="avatar avatar-xs rounded-circle bg-gradient-dark text-white font-weight-bold d-inline-flex align-items-center justify-content-center me-1" style="width: 28px; height: 28px; font-size: 12px;">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                        <span class="d-md-inline d-none text-xs font-weight-bold me-1">{{ auth()->user()->name ?? 'User' }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end px-2 py-3 shadow-lg border-0 border-radius-md" aria-labelledby="navbarProfileDropdown" style="min-width: 230px; margin-top: 10px; z-index: 1050;">
                        <li class="px-2 py-2 mb-2 bg-gray-100 border-radius-md">
                            <div class="d-flex align-items-center">
                                @if(auth()->user()->profile_picture && file_exists(public_path(auth()->user()->profile_picture)))
                                    <img src="{{ asset(auth()->user()->profile_picture) }}" class="avatar avatar-sm rounded-circle me-2 shadow-xs" style="width: 36px; height: 36px; object-fit: cover;" alt="{{ auth()->user()->name }}">
                                @else
                                    <div class="avatar avatar-sm rounded-circle bg-gradient-primary text-white font-weight-bold d-flex align-items-center justify-content-center me-2 shadow-xs" style="width: 36px; height: 36px; font-size: 14px;">
                                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <div class="d-flex flex-column justify-content-center text-truncate">
                                    <span class="text-xs font-weight-bolder text-dark mb-0 text-truncate" style="max-width: 150px;">{{ auth()->user()->name }}</span>
                                    <span class="text-xxs text-secondary mb-1 text-truncate" style="max-width: 150px;">{{ auth()->user()->email }}</span>
                                    @php
                                        $userRoleName = auth()->user()->roles->first()->name ?? (auth()->user()->role->name ?? 'Staff');
                                    @endphp
                                    <span class="badge bg-white text-dark text-xxs font-weight-bold border align-self-start px-2 py-0">
                                        {{ $userRoleName }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item border-radius-md py-2 d-flex align-items-center" href="{{ route('profile.view') }}">
                                <i class="fa-solid fa-user me-2 text-primary" style="width: 16px;"></i>
                                <span class="text-xs font-weight-bold">View Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item border-radius-md py-2 d-flex align-items-center" href="{{ route('profile.edit') }}">
                                <i class="fa-solid fa-user-pen me-2 text-info" style="width: 16px;"></i>
                                <span class="text-xs font-weight-bold">Edit Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item border-radius-md py-2 d-flex align-items-center" href="{{ route('profile.password') }}">
                                <i class="fa-solid fa-key me-2 text-warning" style="width: 16px;"></i>
                                <span class="text-xs font-weight-bold">Update Password</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider my-2">
                        </li>

                        @if(session()->has('impersonated_by'))
                        <li>
                            <a class="dropdown-item border-radius-md py-2 d-flex align-items-center text-warning font-weight-bold" href="{{ route('users.leave_impersonate') }}">
                                <i class="fa-solid fa-arrow-rotate-left me-2 text-warning" style="width: 16px;"></i>
                                <span class="text-xs font-weight-bold">Back to Parent Account</span>
                            </a>
                        </li>
                        @else
                        <li>
                            <a class="dropdown-item border-radius-md py-2 d-flex align-items-center text-danger" href="{{ url('/logout') }}">
                                <i class="fa-solid fa-arrow-right-from-bracket me-2 text-danger" style="width: 16px;"></i>
                                <span class="text-xs font-weight-bold">Sign Out</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->
