@extends('layouts.user_type.auth')

@section('content')

<div>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header pb-0 px-3">
                <h6 class="mb-0">Organization Settings</h6>
            </div>
            <div class="card-body pt-4 p-3">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <span class="alert-text text-white"><strong>Success!</strong> {{ session('success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form action="{{ route('organization_settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-control-label">Organization Name</label>
                                <input class="form-control" type="text" id="name" name="name" value="{{ $setting->name }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="theme_color" class="form-control-label">Theme Primary Color (Hex)</label>
                                <input class="form-control" type="color" id="theme_color" name="theme_color" value="{{ $setting->theme_color ?? '#cb0c9f' }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="website" class="form-control-label">Website</label>
                                <input class="form-control" type="text" id="website" name="website" value="{{ $setting->website }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-control-label">Email</label>
                                <input class="form-control" type="email" id="email" name="email" value="{{ $setting->email }}">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone" class="form-control-label">Phone</label>
                                <input class="form-control" type="text" id="phone" name="phone" value="{{ $setting->phone }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="address" class="form-control-label">Address</label>
                                <input class="form-control" type="text" id="address" name="address" value="{{ $setting->address }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="city" class="form-control-label">City</label>
                                <select class="form-control ajax-city-select2" id="city" name="city" data-placeholder="Search City..." style="width: 100%;">
                                    <option value="">Select City</option>
                                    @if(!empty($setting->city))
                                        <option value="{{ $setting->city }}" selected>{{ $setting->city }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="logo" class="form-control-label">Organization Logo</label>
                                <input class="form-control" type="file" id="logo" name="logo" accept="image/*">
                                @if($setting->logo)
                                    <div class="mt-2">
                                        <img src="{{ asset($setting->logo) }}" alt="Logo" class="img-fluid rounded border shadow-sm" style="max-height: 120px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="login_bg" class="form-control-label">Login Page Background Image</label>
                                <input class="form-control" type="file" id="login_bg" name="login_bg" accept="image/*">
                                @if($setting->login_bg)
                                    <div class="mt-2">
                                        <img src="{{ asset($setting->login_bg) }}" alt="Login Bg" class="img-fluid rounded border shadow-sm" style="max-height: 120px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="favicon" class="form-control-label">Organization Favicon</label>
                                <input class="form-control" type="file" id="favicon" name="favicon" accept="image/x-icon,image/png,image/jpeg,image/svg+xml">
                                @if($setting->favicon)
                                    <div class="mt-2">
                                        <img src="{{ asset($setting->favicon) }}" alt="Favicon" class="img-fluid rounded border shadow-sm" style="max-height: 50px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Logo Custom Styling Controls -->
                    <div class="card mt-4 border border-light shadow-xs bg-gray-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fa fa-sliders text-primary me-2"></i>
                                <h6 class="mb-0 text-dark fw-bold">Sidebar Logo Sizing & Spacing Adjustments</h6>
                            </div>
                            <p class="text-xs text-muted mb-3">Adjust these settings to perfectly size and center your logo in the left sidebar without touching any code.</p>
                            
                            <div class="row g-3">
                                <div class="col-md-3 col-6">
                                    <label class="form-label text-xs fw-bold">Max Height (px)</label>
                                    <input type="number" name="logo_height" id="logo_height_input" class="form-control form-control-sm" value="{{ $setting->logo_height ?? 55 }}" min="30" max="150" placeholder="55">
                                    <span class="text-xxs text-muted">Default: 55px</span>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label text-xs fw-bold">Max Width (px)</label>
                                    <input type="number" name="logo_width" id="logo_width_input" class="form-control form-control-sm" value="{{ $setting->logo_width ?? 180 }}" min="50" max="250" placeholder="180">
                                    <span class="text-xxs text-muted">Default: 180px</span>
                                </div>
                                <div class="col-md-2 col-4">
                                    <label class="form-label text-xs fw-bold">Padding (px)</label>
                                    <input type="number" name="logo_padding" id="logo_padding_input" class="form-control form-control-sm" value="{{ $setting->logo_padding ?? 4 }}" min="0" max="30" placeholder="4">
                                    <span class="text-xxs text-muted">Default: 4px</span>
                                </div>
                                <div class="col-md-2 col-4">
                                    <label class="form-label text-xs fw-bold">Margin Top (px)</label>
                                    <input type="number" name="logo_margin_top" id="logo_margin_top_input" class="form-control form-control-sm" value="{{ $setting->logo_margin_top ?? 0 }}" min="-30" max="50" placeholder="0">
                                    <span class="text-xxs text-muted">Default: 0px</span>
                                </div>
                                <div class="col-md-2 col-4">
                                    <label class="form-label text-xs fw-bold">Margin Bottom (px)</label>
                                    <input type="number" name="logo_margin_bottom" id="logo_margin_bottom_input" class="form-control form-control-sm" value="{{ $setting->logo_margin_bottom ?? 0 }}" min="-30" max="50" placeholder="0">
                                    <span class="text-xxs text-muted">Default: 0px</span>
                                </div>
                            </div>

                            @if($setting->logo)
                                <div class="mt-3 p-3 bg-white rounded border d-flex flex-column align-items-center justify-content-center">
                                    <span class="text-xxs text-uppercase fw-bold text-muted mb-2"><i class="fa fa-eye me-1"></i> Live Sidebar Header Preview</span>
                                    <div id="preview_sidebar_box" class="d-flex align-items-center justify-content-center border border-dashed rounded p-2" style="width: 230px; background: #fff; min-height: 80px;">
                                        <img id="preview_logo_img" src="{{ asset($setting->logo) }}" 
                                             style="max-height: {{ $setting->logo_height ?? 55 }}px; max-width: {{ $setting->logo_width ?? 180 }}px; padding: {{ $setting->logo_padding ?? 4 }}px; margin-top: {{ $setting->logo_margin_top ?? 0 }}px; margin-bottom: {{ $setting->logo_margin_bottom ?? 0 }}px; object-fit: contain;" 
                                             alt="Sidebar Logo Preview">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn bg-gradient-primary btn-md mt-4 mb-4">Save Settings</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        function updateLogoPreview() {
            var h = $('#logo_height_input').val() || 55;
            var w = $('#logo_width_input').val() || 180;
            var p = $('#logo_padding_input').val() || 4;
            var mt = $('#logo_margin_top_input').val() || 0;
            var mb = $('#logo_margin_bottom_input').val() || 0;

            $('#preview_logo_img').css({
                'max-height': h + 'px',
                'max-width': w + 'px',
                'padding': p + 'px',
                'margin-top': mt + 'px',
                'margin-bottom': mb + 'px'
            });
        }

        $('#logo_height_input, #logo_width_input, #logo_padding_input, #logo_margin_top_input, #logo_margin_bottom_input').on('input change', updateLogoPreview);
    });
</script>
@endsection
