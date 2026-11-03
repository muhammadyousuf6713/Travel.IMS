@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-lg-6">

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
                            <i class="fa-solid fa-key text-warning me-2"></i> Update Password
                        </h6>
                        <p class="text-xxs text-secondary mb-0">Ensure your account is using a strong password of at least 8 characters.</p>
                    </div>
                    <a href="{{ route('profile.view') }}" class="btn btn-outline-secondary btn-xs mb-0">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label text-xs font-weight-bold text-secondary mb-1">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required placeholder="Enter current password">
                                <button class="btn btn-outline-secondary mb-0 toggle-password-visibility" type="button" data-target="current_password">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback text-xxs d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-xs font-weight-bold text-secondary mb-1">New Password <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Minimum 8 characters">
                                <button class="btn btn-outline-secondary mb-0 toggle-password-visibility" type="button" data-target="password">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback text-xxs d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <span class="text-xxs text-muted">Must be at least 8 characters.</span>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-xs font-weight-bold text-secondary mb-1">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Re-type new password">
                                <button class="btn btn-outline-secondary mb-0 toggle-password-visibility" type="button" data-target="password_confirmation">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('profile.view') }}" class="btn btn-outline-secondary btn-sm mb-0">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 mb-0 shadow-sm">
                                <i class="fa-solid fa-shield-halved me-1"></i> Update Password
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
    document.querySelectorAll('.toggle-password-visibility').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});
</script>
@endsection
