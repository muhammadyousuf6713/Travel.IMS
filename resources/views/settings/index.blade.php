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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show text-white text-sm mb-4" role="alert">
            <span class="alert-icon me-2"><i class="fa-solid fa-triangle-exclamation"></i></span>
            <span class="alert-text">{{ session('error') }}</span>
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close" style="top: 10px;"></button>
        </div>
    @endif

    <!-- Settings Header -->
    <div class="card shadow-xs border-0 border-radius-xl mb-4 overflow-hidden">
        <div class="p-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-lg rounded-circle bg-gradient-primary text-white font-weight-bold d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 56px; height: 56px; font-size: 22px;">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 text-white font-weight-bolder">System Settings</h4>
                        <p class="mb-0 text-white-50 text-xs">Configure global CRM operational rules, user visibility policies, and system preferences.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-white text-dark text-xxs font-weight-bolder text-uppercase px-2 py-1 shadow-xs">
                        <i class="fa-solid fa-shield-halved text-primary me-1"></i> Global Administration
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Settings Form -->
        <div class="col-lg-8">
            <div class="card shadow-xs border-0 border-radius-lg">
                <div class="card-header bg-light border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 text-sm font-weight-bold text-dark">
                            <i class="fa-solid fa-user-lock text-primary me-2"></i> Inquiry Visibility & Access Control
                        </h6>
                        <p class="text-xxs text-secondary mb-0">Control how customer inquiries and lead data are displayed to sales staff.</p>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf

                        <!-- Setting Item: Only Show Personal / Assigned Inquiries -->
                        <div class="p-3 rounded-3 border bg-light mb-4">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <h6 class="mb-0 text-sm font-weight-bold text-dark me-2">Only Show Personal or Assigned Inquiries</h6>
                                        <span class="badge {{ $onlyAssignedInquiries ? 'bg-success' : 'bg-secondary' }} text-xxs">
                                            {{ $onlyAssignedInquiries ? 'ACTIVE' : 'DISABLED' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-secondary mb-2">
                                        When checked, non-admin sales staff and agents will <strong>only</strong> see inquiries assigned to them or created by them in Inquiry Lists, search filters, and follow-ups.
                                    </p>
                                    <div class="text-xxs text-muted d-flex align-items-center gap-2">
                                        <span><i class="fa-solid fa-check text-success me-1"></i> Administrators and Super-Admins retain complete company-wide visibility.</span>
                                    </div>
                                </div>
                                <div class="form-check form-switch pt-1">
                                    <input class="form-check-input" type="checkbox" name="only_assigned_inquiries" id="onlyAssignedInquiriesToggle" value="1" {{ $onlyAssignedInquiries ? 'checked' : '' }} style="width: 2.5em; height: 1.3em; cursor: pointer;">
                                </div>
                            </div>
                        </div>

                        <!-- Info Card: Sales Dashboard Confidentiality -->
                        <div class="p-3 rounded-3 border bg-white mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="avatar avatar-sm bg-gradient-info text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1">
                                    <i class="fa-solid fa-chart-pie"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 text-sm font-weight-bold text-dark">Sales Dashboard Privacy & Confidentiality</h6>
                                    <p class="text-xs text-secondary mb-1">
                                        Users allotted the <strong>Sales Dashboard</strong> role permission are automatically scoped to their personal pipeline, agenda, and conversion numbers. Administrative metrics (company-wide revenue totals, overall team performance comparisons) are kept strictly confidential for administrators.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end align-items-center pt-3 border-top">
                            <button type="submit" class="btn btn-primary btn-sm px-4 mb-0 shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Settings Navigation Hub -->
        <div class="col-lg-4">
            <div class="card shadow-xs border-0 border-radius-lg mb-4">
                <div class="card-header bg-light border-bottom py-3 px-4">
                    <h6 class="mb-0 text-sm font-weight-bold text-dark">
                        <i class="fa-solid fa-folder-gear text-primary me-2"></i> Settings Directory
                    </h6>
                    <p class="text-xxs text-secondary mb-0">Direct shortcuts to all system configuration hubs.</p>
                </div>
                <div class="card-body p-3">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('organization_settings.edit') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between border-0 px-2 py-2">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-building me-2 text-info" style="width: 16px;"></i>
                                <span class="text-xs font-weight-bold text-dark">Organization & Branding</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xxs text-secondary"></i>
                        </a>

                        <a href="{{ route('integration_settings.edit') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between border-0 px-2 py-2">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-plug me-2 text-success" style="width: 16px;"></i>
                                <span class="text-xs font-weight-bold text-dark">Integrations & Auto-Lead</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xxs text-secondary"></i>
                        </a>

                        <a href="{{ url('user-management') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between border-0 px-2 py-2">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-user-shield me-2 text-primary" style="width: 16px;"></i>
                                <span class="text-xs font-weight-bold text-dark">Users & Impersonation</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xxs text-secondary"></i>
                        </a>

                        <a href="{{ url('roles') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between border-0 px-2 py-2">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-key me-2 text-warning" style="width: 16px;"></i>
                                <span class="text-xs font-weight-bold text-dark">Roles & Permissions</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xxs text-secondary"></i>
                        </a>

                        <a href="{{ route('hotels.index') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between border-0 px-2 py-2">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-hotel me-2 text-danger" style="width: 16px;"></i>
                                <span class="text-xs font-weight-bold text-dark">Hotel Directory</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xxs text-secondary"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
