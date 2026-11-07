@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            @if(isset($errors) && $errors->any())
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
                        <p class="text-xxs text-secondary mb-0">Update your account identity, profile photo, contact details, and notes.</p>
                    </div>
                    <a href="{{ route('profile.view') }}" class="btn btn-outline-secondary btn-xs mb-0">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Profile
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('user-profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Profile Picture Upload Section -->
                        <div class="d-flex align-items-center gap-3 p-3 mb-4 rounded-3 bg-light border">
                            <div class="position-relative">
                                @php
                                    $hasPic = !empty($user->profile_picture) && file_exists(public_path($user->profile_picture));
                                @endphp
                                <div id="avatarPreviewContainer" class="rounded-circle overflow-hidden shadow-xs d-flex align-items-center justify-content-center border" style="width: 76px; height: 76px; background: #e2e8f0;">
                                    @if($hasPic)
                                        <img id="avatarPreviewImg" src="{{ asset($user->profile_picture) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        <span id="avatarPreviewInitials" class="font-weight-bold text-white text-lg d-none">
                                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                        </span>
                                    @else
                                        <img id="avatarPreviewImg" src="" alt="Avatar" class="d-none" style="width: 100%; height: 100%; object-fit: cover;">
                                        <div id="avatarPreviewInitials" class="w-100 h-100 bg-gradient-primary text-white font-weight-bold d-flex align-items-center justify-content-center" style="font-size: 26px;">
                                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label text-xs font-weight-bold text-dark mb-1 d-block">Profile Photo</label>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <label class="btn btn-sm btn-white border shadow-xs mb-0 cursor-pointer">
                                        <i class="fa-solid fa-camera text-primary me-1"></i> Choose New Photo
                                        <input type="file" name="profile_picture" id="profilePictureInput" class="d-none" accept="image/png,image/jpeg,image/webp,image/gif">
                                    </label>
                                    @if($hasPic)
                                        <div class="form-check form-check-inline mb-0 ms-2">
                                            <input class="form-check-input" type="checkbox" name="remove_picture" value="1" id="removePicCheck">
                                            <label class="form-check-label text-xs text-danger font-weight-bold" for="removePicCheck">
                                                <i class="fa-solid fa-trash-can me-1"></i> Remove photo
                                            </label>
                                        </div>
                                    @endif
                                </div>
                                <span class="text-xxs text-secondary d-block mt-1">Recommended: Square format (e.g. 300x300), Max 3MB (JPG, PNG, WEBP).</span>
                            </div>
                        </div>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('profilePictureInput');
    const img = document.getElementById('avatarPreviewImg');
    const initials = document.getElementById('avatarPreviewInitials');
    const removeCheck = document.getElementById('removePicCheck');

    if (input) {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    img.src = evt.target.result;
                    img.classList.remove('d-none');
                    if (initials) initials.classList.add('d-none');
                    if (removeCheck) removeCheck.checked = false;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (removeCheck) {
        removeCheck.addEventListener('change', function() {
            if (this.checked) {
                if (img) img.classList.add('d-none');
                if (initials) initials.classList.remove('d-none');
                if (input) input.value = '';
            }
        });
    }
});
</script>
@endsection
