@extends('layouts.user_type.auth')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header pb-0 bg-white">
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold">Edit User</h5>
                            <p class="text-xs text-secondary mb-0">Update identity, access credentials, assigned role, and profile details.</p>
                        </div>
                        <a href="{{ url('user-management') }}" class="btn bg-gradient-secondary btn-sm mb-0 text-uppercase">
                            <i class="fa fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body px-4 pt-4 pb-2">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger text-white">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('users/update/' . $users['id']) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="u_id" value="{{ Crypt::encrypt($users->id) }}">
                        
                        <!-- Avatar section -->
                        <div class="d-flex align-items-center gap-3 p-3 mb-4 rounded-3 bg-light border">
                            @php
                                $hasPic = !empty($users->profile_picture) && file_exists(public_path($users->profile_picture));
                            @endphp
                            <div class="rounded-circle overflow-hidden shadow-xs d-flex align-items-center justify-content-center border" style="width: 64px; height: 64px; background: #e2e8f0;">
                                @if($hasPic)
                                    <img id="adminAvatarPreview" src="{{ asset($users->profile_picture) }}" alt="{{ $users->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div id="adminAvatarPreviewInitials" class="w-100 h-100 bg-gradient-primary text-white font-weight-bold d-flex align-items-center justify-content-center" style="font-size: 22px;">
                                        {{ strtoupper(substr($users->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <img id="adminAvatarPreview" src="" alt="Avatar" class="d-none" style="width: 100%; height: 100%; object-fit: cover;">
                                @endif
                            </div>
                            <div>
                                <label class="form-label text-xs font-weight-bold text-dark mb-1 d-block">Profile Photo</label>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <label class="btn btn-sm btn-white border shadow-xs mb-0 cursor-pointer">
                                        <i class="fa-solid fa-camera text-primary me-1"></i> Choose Photo
                                        <input type="file" name="profile_picture" id="adminUserPicInput" class="d-none" accept="image/png,image/jpeg,image/webp,image/gif">
                                    </label>
                                    @if($hasPic)
                                        <div class="form-check form-check-inline mb-0 ms-2">
                                            <input class="form-check-input" type="checkbox" name="remove_picture" value="1" id="removeUserPic">
                                            <label class="form-check-label text-xs text-danger font-weight-bold" for="removeUserPic">
                                                <i class="fa-solid fa-trash-can me-1"></i> Remove photo
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-control-label fw-semibold" for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $users->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-control-label fw-semibold" for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $users->email) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-control-label fw-semibold" for="phone">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $users->phone) }}" placeholder="+92 300 1234567">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-control-label fw-semibold" for="location">Office / Branch Location</label>
                                    <input type="text" name="location" class="form-control" value="{{ old('location', $users->location) }}" placeholder="e.g. Islamabad Office">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-control-label fw-semibold" for="role">Role <span class="text-danger">*</span></label>
                                    <select name="role_id" class="form-control" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ $users->role_id == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-control-label fw-semibold" for="status">Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="1" {{ $users->status == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ $users->status == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label class="form-control-label fw-semibold" for="about_me">About Me / Notes</label>
                                    <textarea name="about_me" class="form-control" rows="2" placeholder="Operational notes or user bio...">{{ old('about_me', $users->about_me) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-control-label fw-semibold" for="password">Change Password</label>
                                    <input type="password" name="password" class="form-control">
                                    <small class="form-text text-muted">Leave blank if you don't want to change the password.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-control-label fw-semibold" for="password_confirmation">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary btn-md">
                                <i class="fa fa-save me-1"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var input = document.getElementById('adminUserPicInput');
    var img = document.getElementById('adminAvatarPreview');
    var initials = document.getElementById('adminAvatarPreviewInitials');

    if (input) {
        input.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(evt) {
                    if (img) {
                        img.src = evt.target.result;
                        img.classList.remove('d-none');
                    }
                    if (initials) initials.classList.add('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endsection
