@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show text-white text-sm mb-4" role="alert">
                    <span class="alert-icon me-2"><i class="fa-solid fa-triangle-exclamation"></i></span>
                    <span class="alert-text">{{ $errors->first() }}</span>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close" style="top: 10px;"></button>
                </div>
            @endif

            <div class="card shadow-xs border-0 border-radius-lg">
                <div class="card-header bg-light border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 text-sm font-weight-bold text-dark">
                            <i class="fa-solid fa-user-pen text-primary me-2"></i> Edit Profile Information
                        </h6>
                        <p class="text-xxs text-secondary mb-0">Update your account identity, contact details, and personal preferences.</p>
                    </div>
                    <a href="{{ route('profile.view') }}" class="btn btn-outline-secondary btn-xs mb-0">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Profile
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('user-profile.update') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-xs font-weight-bold text-secondary mb-1">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required placeholder="Your full name">
                                @error('name')
                                    <div class="invalid-feedback text-xxs">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-xs font-weight-bold text-secondary mb-1">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required placeholder="name@company.com">
                                @error('email')
                                    <div class="invalid-feedback text-xxs">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-xs font-weight-bold text-secondary mb-1">Phone Number</label>
                                <input type="text" name="phone" class="form-control form-control-sm @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="+92 300 1234567">
                                @error('phone')
                                    <div class="invalid-feedback text-xxs">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-xs font-weight-bold text-secondary mb-1">Office / Location</label>
                                <input type="text" name="location" class="form-control form-control-sm @error('location') is-invalid @enderror" value="{{ old('location', $user->location) }}" placeholder="e.g. Karachi Office, Head Branch">
                                @error('location')
                                    <div class="invalid-feedback text-xxs">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label text-xs font-weight-bold text-secondary mb-1">About Me / Notes</label>
                                <textarea name="about_me" class="form-control form-control-sm @error('about_me') is-invalid @enderror" rows="4" placeholder="Brief background or operational role notes...">{{ old('about_me', $user->about_me) }}</textarea>
                                @error('about_me')
                                    <div class="invalid-feedback text-xxs">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('profile.view') }}" class="btn btn-outline-secondary btn-sm mb-0">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 mb-0 shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
