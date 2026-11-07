@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-white text-sm mb-4" role="alert">
            <span class="alert-icon me-2"><i class="fa-solid fa-circle-check"></i></span>
            <span class="alert-text">{{ session('success') }}</span>
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close" style="top: 10px;"></button>
        </div>
    @endif

    <!-- Profile Header Card -->
    <div class="card shadow-xs border-0 border-radius-xl mb-4 overflow-hidden">
        <div class="p-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
            <div class="row align-items-center">
                <div class="col-auto">
                    @if(!empty($user->profile_picture) && file_exists(public_path($user->profile_picture)))
                        <img src="{{ asset($user->profile_picture) }}" alt="{{ $user->name }}" class="avatar avatar-xl rounded-circle shadow-sm" style="width: 72px; height: 72px; object-fit: cover; border: 3px solid rgba(255,255,255,0.25);">
                    @else
                        <div class="avatar avatar-xl rounded-circle bg-gradient-primary text-white font-weight-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 72px; height: 72px; font-size: 28px; border: 3px solid rgba(255,255,255,0.2);">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="col my-auto">
                    <div class="h-100">
                        <h4 class="mb-1 text-white font-weight-bolder">{{ $user->name }}</h4>
                        <p class="mb-1 text-white-50 text-xs">
                            <i class="fa-solid fa-envelope me-1"></i> {{ $user->email }}
                            @if(!empty($user->phone))
                                <span class="mx-2">•</span> <i class="fa-solid fa-phone me-1"></i> {{ $user->phone }}
                            @endif
                        </p>
                        @php
                            $userRole = $user->roles->first()->name ?? ($user->role->name ?? 'Staff');
                        @endphp
                        <span class="badge bg-white text-dark text-xxs font-weight-bolder text-uppercase px-2 py-1 shadow-xs">
                            <i class="fa-solid fa-shield-halved text-primary me-1"></i> {{ $userRole }}
                        </span>
                    </div>
                </div>
                <div class="col-12 col-md-auto mt-3 mt-md-0 d-flex gap-2">
                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-white text-dark mb-0 d-flex align-items-center">
                        <i class="fa-solid fa-user-pen me-1 text-primary"></i> Edit Profile
                    </a>
                    <a href="{{ route('profile.password') }}" class="btn btn-sm btn-outline-white mb-0 d-flex align-items-center">
                        <i class="fa-solid fa-key me-1"></i> Update Password
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Details & Summary -->
    <div class="row g-4">
        <!-- Left: Personal & Contact Information -->
        <div class="col-lg-7">
            <div class="card shadow-xs border-0 border-radius-lg h-100">
                <div class="card-header bg-light border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-sm font-weight-bold text-dark">
                        <i class="fa-solid fa-address-card text-primary me-2"></i> Personal & Account Information
                    </h6>
                    <a href="{{ route('profile.edit') }}" class="text-xs font-weight-bold text-primary text-decoration-none">
                        Edit <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="text-xxs font-weight-bolder text-secondary text-uppercase mb-1 d-block">Full Name</label>
                            <div class="text-sm font-weight-bold text-dark">{{ $user->name }}</div>
                        </div>

                        <div class="col-sm-6">
                            <label class="text-xxs font-weight-bolder text-secondary text-uppercase mb-1 d-block">Email Address</label>
                            <div class="text-sm font-weight-bold text-dark">{{ $user->email }}</div>
                        </div>

                        <div class="col-sm-6">
                            <label class="text-xxs font-weight-bolder text-secondary text-uppercase mb-1 d-block">Contact Phone</label>
                            <div class="text-sm font-weight-bold text-dark">{{ $user->phone ?: 'Not configured' }}</div>
                        </div>

                        <div class="col-sm-6">
                            <label class="text-xxs font-weight-bolder text-secondary text-uppercase mb-1 d-block">Location / Office</label>
                            <div class="text-sm font-weight-bold text-dark">{{ $user->location ?: 'Headquarters' }}</div>
                        </div>

                        <div class="col-sm-6">
                            <label class="text-xxs font-weight-bolder text-secondary text-uppercase mb-1 d-block">Assigned Role</label>
                            <span class="badge bg-light text-dark text-xxs font-weight-bold border px-2 py-1">
                                {{ $userRole }}
                            </span>
                        </div>

                        <div class="col-sm-6">
                            <label class="text-xxs font-weight-bolder text-secondary text-uppercase mb-1 d-block">Member Since</label>
                            <div class="text-sm font-weight-bold text-dark">
                                {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                            </div>
                        </div>

                        <div class="col-12 mt-3 pt-3 border-top">
                            <label class="text-xxs font-weight-bolder text-secondary text-uppercase mb-1 d-block">About / Biography</label>
                            <p class="text-sm text-secondary mb-0">
                                {{ $user->about_me ?: 'No biography or notes added yet. Click "Edit Profile" to add personal information or operational notes.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Activity & Quick Navigation -->
        <div class="col-lg-5">
            <div class="card shadow-xs border-0 border-radius-lg mb-4">
                <div class="card-header bg-light border-bottom py-3 px-4">
                    <h6 class="mb-0 text-sm font-weight-bold text-dark">
                        <i class="fa-solid fa-chart-line text-primary me-2"></i> Operational Summary
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-light mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm bg-gradient-primary rounded-circle text-white d-flex align-items-center justify-content-center me-3">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-xs text-secondary">Assigned Inquiries</h6>
                                <h4 class="mb-0 font-weight-bolder text-dark">{{ $inquiriesCount }}</h4>
                            </div>
                        </div>
                        <a href="{{ url('inquiry') }}" class="btn btn-xs btn-outline-primary mb-0">View All</a>
                    </div>

                    <div class="p-3 border rounded-3 bg-white">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fa-solid fa-lock text-success me-2"></i>
                            <span class="text-xs font-weight-bold text-dark">Account Security</span>
                        </div>
                        <p class="text-xxs text-secondary mb-3">Keep your credentials up to date to protect client inquiries and booking data.</p>
                        <a href="{{ route('profile.password') }}" class="btn btn-xs btn-outline-secondary w-100 mb-0">
                            <i class="fa-solid fa-key me-1"></i> Change Password
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card shadow-xs border-0 border-radius-lg">
                <div class="card-header bg-light border-bottom py-3 px-4">
                    <h6 class="mb-0 text-sm font-weight-bold text-dark">
                        <i class="fa-solid fa-compass text-primary me-2"></i> Quick Links
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="list-group list-group-flush">
                        <a href="{{ url('inquiry') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between border-0 px-2 py-2">
                            <span class="text-xs font-weight-bold text-dark"><i class="fa-solid fa-list-check me-2 text-primary"></i> Inquiry Management</span>
                            <i class="fa-solid fa-chevron-right text-xxs text-secondary"></i>
                        </a>
                        <a href="{{ route('pilgrimage.index') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between border-0 px-2 py-2">
                            <span class="text-xs font-weight-bold text-dark"><i class="fa-solid fa-kaaba me-2 text-success"></i> Pilgrimage Bookings</span>
                            <i class="fa-solid fa-chevron-right text-xxs text-secondary"></i>
                        </a>
                        <a href="{{ url('followups') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between border-0 px-2 py-2">
                            <span class="text-xs font-weight-bold text-dark"><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i> Follow-ups Timeline</span>
                            <i class="fa-solid fa-chevron-right text-xxs text-secondary"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
