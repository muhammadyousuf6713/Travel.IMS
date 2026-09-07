@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-3">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="font-weight-bold text-dark mb-0" style="font-size: 1.15rem;">
                <i class="fa-solid fa-sliders text-primary me-2"></i> System Integration Settings
            </h5>
            <p class="text-xs text-secondary mb-0">Configure operational emergency hotlines, automated notification intervals, and timing policies.</p>
        </div>
        <a href="{{ route('pilgrimage.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
            <i class="fa-solid fa-arrow-left me-1"></i> Operations
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success text-white alert-dismissible fade show py-2 px-3 mb-3" role="alert">
            <span class="text-xs font-weight-bold"><i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}</span>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('integration_settings.update') }}">
        @csrf

        <!-- Ground Operations Helplines -->
        <div class="card shadow-sm border-0 border-radius-lg mb-3">
            <div class="card-header bg-light border-bottom py-2 px-3">
                <h6 class="mb-0 font-weight-bold text-dark text-xs text-uppercase">
                    <i class="fa-solid fa-headset text-primary me-1"></i> Ground Operations Helplines (Printed on Vouchers)
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Makkah Operations Helpline <span class="text-danger">*</span></label>
                        <input type="text" name="makkah_ops_contact" class="form-control form-control-sm" value="{{ old('makkah_ops_contact', $settings->makkah_ops_contact) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Madinah Operations Helpline <span class="text-danger">*</span></label>
                        <input type="text" name="madinah_ops_contact" class="form-control form-control-sm" value="{{ old('madinah_ops_contact', $settings->madinah_ops_contact) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Jeddah Airport Helpline <span class="text-danger">*</span></label>
                        <input type="text" name="jeddah_ops_contact" class="form-control form-control-sm" value="{{ old('jeddah_ops_contact', $settings->jeddah_ops_contact) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">General Hotline <span class="text-danger">*</span></label>
                        <input type="text" name="general_hotline" class="form-control form-control-sm" value="{{ old('general_hotline', $settings->general_hotline) }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Automation & Reminder Rules -->
        <div class="card shadow-sm border-0 border-radius-lg mb-3">
            <div class="card-header bg-light border-bottom py-2 px-3">
                <h6 class="mb-0 font-weight-bold text-dark text-xs text-uppercase">
                    <i class="fa-solid fa-clock-rotate-left text-primary me-1"></i> Checkpoints & Reminder Engine Policies
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Ziyaraat Offset (Hours after Check-in) <span class="text-danger">*</span></label>
                        <input type="number" name="ziyaraat_offset_hours" class="form-control form-control-sm" value="{{ old('ziyaraat_offset_hours', $settings->ziyaraat_offset_hours) }}" min="1" max="168" required>
                        <span class="text-xxs text-muted">Default: 48 hours</span>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Reminder Trigger Window (Hours prior to due) <span class="text-danger">*</span></label>
                        <input type="number" name="reminder_window_hours" class="form-control form-control-sm" value="{{ old('reminder_window_hours', $settings->reminder_window_hours) }}" min="1" max="168" required>
                        <span class="text-xxs text-muted">Default: 48 hours</span>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xs font-weight-bold text-secondary mb-1">Repeat Interval (Hours) <span class="text-danger">*</span></label>
                        <input type="number" name="reminder_repeat_hours" class="form-control form-control-sm" value="{{ old('reminder_repeat_hours', $settings->reminder_repeat_hours) }}" min="1" max="24" required>
                        <span class="text-xxs text-muted">Default: Every 3 hours</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="card shadow-sm border-0 border-radius-lg p-3 bg-light d-flex flex-row justify-content-between align-items-center">
            <span class="text-xs text-secondary">Settings take effect immediately across all vouchers and reminder jobs.</span>
            <button type="submit" class="btn btn-primary btn-sm px-4 mb-0">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save Settings
            </button>
        </div>

    </form>
</div>
@endsection
