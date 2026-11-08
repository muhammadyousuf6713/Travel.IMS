@extends('layouts.user_type.auth')

@section('content')
<style>
    /* Modern Permission Matrix Styles */
    .perm-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        transition: all 0.2s ease-in-out;
        overflow: hidden;
    }
    .perm-card:hover {
        box-shadow: 0 4px 14px rgba(0,0,0,0.06);
    }
    .perm-card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 18px;
    }

    /* Modern Slide Switch */
    .perm-switch {
        position: relative;
        display: inline-block;
        width: 38px;
        height: 22px;
        margin: 0;
        flex-shrink: 0;
    }
    .perm-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .perm-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .25s ease;
        border-radius: 22px;
    }
    .perm-slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .25s ease;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.25);
    }
    .perm-switch input:checked + .perm-slider.slider-primary {
        background-color: #0284c7; /* Sky blue */
    }
    .perm-switch input:checked + .perm-slider.slider-submodule {
        background-color: #10b981; /* Emerald */
    }
    .perm-switch input:checked + .perm-slider.slider-action {
        background-color: #f59e0b; /* Amber */
    }
    .perm-switch input:checked + .perm-slider:before {
        transform: translateX(16px);
    }

    .perm-item-row {
        transition: background-color 0.15s ease;
        border-bottom: 1px solid #f1f5f9;
        padding: 8px 14px;
    }
    .perm-item-row:last-child {
        border-bottom: none;
    }
    .perm-item-row:hover {
        background-color: #f8fafc;
    }
    .perm-level-primary {
        background: #f0f9ff;
        border-left: 3px solid #0284c7;
    }
    .perm-level-submodule {
        background: #ffffff;
        padding-left: 28px !important;
    }
    .perm-level-action {
        background: #ffffff;
        padding-left: 48px !important;
    }
    .perm-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-primary-chip {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .badge-submodule-chip {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .badge-action-chip {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    /* Sticky Action Bar */
    .perm-sticky-bar {
        position: sticky;
        top: 10px;
        z-index: 100;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 18px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
</style>

<div class="container-fluid py-3">
    @if (session('alert'))
        <div class="alert alert-{{ session('alert-class') == 'danger' ? 'danger' : 'success' }} alert-dismissible fade show text-white shadow-sm" role="alert">
            <span class="alert-icon me-2"><i class="fa fa-{{ session('alert-class') == 'danger' ? 'exclamation-circle' : 'check-circle' }}"></i></span>
            <span class="alert-text"><strong>{{ session('alert') }}</strong></span>
            <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Top Sticky Control Header -->
    <div class="perm-sticky-bar mb-4">
        <div class="row align-items-center g-2">
            <div class="col-lg-4 col-md-5">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ url('roles') }}" class="btn btn-outline-secondary btn-sm mb-0 px-2" title="Back to Roles List">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                    <div class="flex-grow-1">
                        <label class="form-label text-xxs font-weight-bold text-secondary mb-0">Role Being Configured</label>
                        <select onchange="location.href='{{ url('roles/permission') }}/' + this.value;" class="form-select form-select-sm fw-bold">
                            <option value="0">-- Select Role to Configure --</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ $role_id == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }} {{ $role->id == 1 ? '(Master Root Bypass)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Search Permissions -->
            <div class="col-lg-4 col-md-4">
                <label class="form-label text-xxs font-weight-bold text-secondary mb-0">Filter Permissions</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 text-secondary"><i class="fa fa-search text-xs"></i></span>
                    <input type="text" id="permSearchInput" class="form-control form-control-sm border-start-0" placeholder="Type to filter permissions (e.g. inquiry, delete, hotel)...">
                    <button class="btn btn-outline-secondary btn-sm mb-0 d-none" type="button" id="clearPermSearch"><i class="fa fa-times"></i></button>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-4 col-md-3 text-end">
                <div class="d-flex align-items-center justify-content-end gap-2 mt-md-3">
                    <span class="badge bg-light text-dark border text-xxs fw-bold px-2 py-1" id="activeCounterBadge">
                        Active: <span id="activeCount" class="text-primary">{{ count($assigned_permissions) }}</span> / {{ $permissions->count() }}
                    </span>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-0 px-2" id="btnSelectAll" title="Select All Permissions">
                        <i class="fa fa-check-double me-1"></i>All
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-0 px-2" id="btnDeselectAll" title="Clear All Permissions">
                        <i class="fa fa-ban me-1"></i>None
                    </button>
                    <button type="button" class="btn bg-gradient-primary btn-sm mb-0 px-3 shadow-sm" id="btnSavePermissionsTop">
                        <i class="fa fa-save me-1"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(!$role_id)
        <div class="card p-5 text-center shadow-sm border-0">
            <div class="py-4">
                <i class="fa-solid fa-shield-halved text-secondary opacity-50 fa-4x mb-3"></i>
                <h5 class="fw-bold text-dark">Please Select a Role Above</h5>
                <p class="text-secondary text-sm mb-3">Choose a role from the dropdown above to view, configure, and customize granular permissions.</p>
            </div>
        </div>
    @else
        <!-- Access Control Legend -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body py-2 px-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-1 text-xs text-secondary">
                        <i class="fa fa-info-circle text-primary me-1"></i>
                        <span class="fw-bold text-dark">Access Control Legend:</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3 text-xs">
                        <div class="d-flex align-items-center gap-1">
                            <span class="perm-badge badge-primary-chip">Primary Module</span>
                            <span class="text-muted text-xxs">Core Menu (Dashboard, Operations, Administration)</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="perm-badge badge-submodule-chip">Sub-Module</span>
                            <span class="text-muted text-xxs">Functional Sections (Customers, Inquiries, Hotels)</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="perm-badge badge-action-chip">Action Control</span>
                            <span class="text-muted text-xxs">Granular Actions (Add, Edit, Delete, Assign, Follow-up)</span>
                        </div>
                    </div>
                    @if($role_id == 1)
                        <span class="badge bg-warning text-dark text-xxs px-2 py-1">
                            <i class="fa fa-crown me-1"></i> Super-Admin has universal system bypass
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Permissions Form -->
        <form id="permissionForm" action="{{ url('roles/permission', [$role_id]) }}" method="POST">
            @csrf

            <div class="row g-4" id="permModulesContainer">
                @php
                    // Group permissions by their hierarchy:
                    // Level 1: parent_id == 0
                    // Level 2: parent_id == level 1 ID
                    // Level 3: parent_id == level 2 ID
                    $rootPermissions = $permissions->where('parent_id', 0)->values();
                    // Fallback: If any permission has a parent_id that does not exist in rootPermissions, treat as root or attach
                    $rootIds = $rootPermissions->pluck('id')->toArray();
                @endphp

                @foreach($rootPermissions as $modIndex => $mod)
                    @php
                        $subModules = $permissions->where('parent_id', $mod->id)->values();
                        // Find all descendant IDs for this module to count active/total
                        $allDescendantIds = collect([$mod->id]);
                        foreach($subModules as $sub) {
                            $allDescendantIds->push($sub->id);
                            $actions = $permissions->where('parent_id', $sub->id);
                            foreach($actions as $act) {
                                $allDescendantIds->push($act->id);
                            }
                        }
                        $totalModPerms = $allDescendantIds->count();
                        $activeModPerms = count(array_intersect($allDescendantIds->toArray(), $assigned_permissions));
                    @endphp

                    <div class="col-lg-6 col-md-12 perm-module-card-col" data-module-name="{{ strtolower($mod->name) }}">
                        <div class="perm-card h-100">
                            <!-- Card Header -->
                            <div class="perm-card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-white border d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                                        @if(stripos($mod->name, 'dash') !== false)
                                            <i class="fa-solid fa-gauge text-primary text-xs"></i>
                                        @elseif(stripos($mod->name, 'oper') !== false)
                                            <i class="fa-solid fa-briefcase text-info text-xs"></i>
                                        @elseif(stripos($mod->name, 'admin') !== false)
                                            <i class="fa-solid fa-shield text-danger text-xs"></i>
                                        @elseif(stripos($mod->name, 'pref') !== false)
                                            <i class="fa-solid fa-sliders text-warning text-xs"></i>
                                        @elseif(stripos($mod->name, 'review') !== false)
                                            <i class="fa-solid fa-star text-warning text-xs"></i>
                                        @elseif(stripos($mod->name, 'report') !== false)
                                            <i class="fa-solid fa-chart-pie text-success text-xs"></i>
                                        @else
                                            <i class="fa-solid fa-layer-group text-secondary text-xs"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark text-sm">{{ $mod->name }}</h6>
                                        <span class="text-xxs text-secondary">
                                            <span class="mod-active-count fw-bold text-primary">{{ $activeModPerms }}</span> of {{ $totalModPerms }} permissions active
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-xs btn-outline-secondary mb-0 btn-toggle-module" title="Toggle all in this module">
                                        Toggle All
                                    </button>
                                </div>
                            </div>

                            <!-- Card Body: Permissions List -->
                            <div class="perm-card-body p-0">
                                <!-- Root Permission Row -->
                                <div class="perm-item-row perm-level-primary d-flex justify-content-between align-items-center" data-perm-name="{{ strtolower($mod->name) }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="perm-badge badge-primary-chip">Primary</span>
                                        <span class="fw-bold text-dark text-xs">{{ $mod->name }}</span>
                                    </div>
                                    <label class="perm-switch">
                                        <input type="checkbox" class="perm-checkbox" name="permissions[]" value="{{ $mod->id }}"
                                            {{ in_array($mod->id, $assigned_permissions) ? 'checked' : '' }}>
                                        <span class="perm-slider slider-primary"></span>
                                    </label>
                                </div>

                                <!-- Sub-modules & Action Controls -->
                                @foreach($subModules as $sub)
                                    @php
                                        $actions = $permissions->where('parent_id', $sub->id)->values();
                                    @endphp
                                    <div class="perm-item-row perm-level-submodule d-flex justify-content-between align-items-center" data-perm-name="{{ strtolower($sub->name) }}">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-angle-right text-muted text-xxs"></i>
                                            <span class="perm-badge badge-submodule-chip">Sub-Module</span>
                                            <span class="fw-semibold text-dark text-xs">{{ $sub->name }}</span>
                                        </div>
                                        <label class="perm-switch">
                                            <input type="checkbox" class="perm-checkbox" name="permissions[]" value="{{ $sub->id }}"
                                                {{ in_array($sub->id, $assigned_permissions) ? 'checked' : '' }}>
                                            <span class="perm-slider slider-submodule"></span>
                                        </label>
                                    </div>

                                    @foreach($actions as $act)
                                        <div class="perm-item-row perm-level-action d-flex justify-content-between align-items-center" data-perm-name="{{ strtolower($act->name) }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-turn-up fa-rotate-90 text-muted text-xxs"></i>
                                                <span class="perm-badge badge-action-chip">Action</span>
                                                <span class="text-secondary text-xs">{{ $act->name }}</span>
                                            </div>
                                            <label class="perm-switch">
                                                <input type="checkbox" class="perm-checkbox" name="permissions[]" value="{{ $act->id }}"
                                                    {{ in_array($act->id, $assigned_permissions) ? 'checked' : '' }}>
                                                <span class="perm-slider slider-action"></span>
                                            </label>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Bottom Floating Save Bar -->
            <div class="card p-3 shadow-sm border mt-4 text-end bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-xs text-secondary">
                        <i class="fa-solid fa-lock text-primary me-1"></i> Permissions are strictly enforced immediately upon saving.
                    </span>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ url('roles') }}" class="btn btn-outline-secondary btn-sm mb-0">Cancel</a>
                        <button type="submit" class="btn bg-gradient-primary btn-sm mb-0 px-4 shadow-sm" id="btnSavePermissionsBottom">
                            <i class="fa fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

@push('scripts')
<script>
$(document).ready(function() {
    function updateActiveCounters() {
        var totalActive = $('.perm-checkbox:checked').length;
        $('#activeCount').text(totalActive);

        // Update module level badges
        $('.perm-card').each(function() {
            var $card = $(this);
            var activeInCard = $card.find('.perm-checkbox:checked').length;
            $card.find('.mod-active-count').text(activeInCard);
        });
    }

    // Toggle switch change updates counters
    $(document).on('change', '.perm-checkbox', function() {
        updateActiveCounters();
    });

    // Global Select All
    $('#btnSelectAll').on('click', function(e) {
        e.preventDefault();
        $('.perm-checkbox:visible').prop('checked', true);
        updateActiveCounters();
        if (window.toastr) toastr.info('All permissions checked.');
    });

    // Global Deselect All
    $('#btnDeselectAll').on('click', function(e) {
        e.preventDefault();
        $('.perm-checkbox:visible').prop('checked', false);
        updateActiveCounters();
        if (window.toastr) toastr.info('All permissions unchecked.');
    });

    // Module Card "Toggle All" Button
    $(document).on('click', '.btn-toggle-module', function(e) {
        e.preventDefault();
        var $card = $(this).closest('.perm-card');
        var $checkboxes = $card.find('.perm-checkbox');
        var allChecked = $checkboxes.length > 0 && $checkboxes.filter(':checked').length === $checkboxes.length;
        $checkboxes.prop('checked', !allChecked);
        updateActiveCounters();
    });

    // Save button trigger from top bar
    $('#btnSavePermissionsTop').on('click', function(e) {
        e.preventDefault();
        $('#permissionForm').submit();
    });

    // Form submit loading indicator
    $('#permissionForm').on('submit', function() {
        var $btn = $('#btnSavePermissionsTop, #btnSavePermissionsBottom');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');
    });

    // Live Permission Search Filter
    $('#permSearchInput').on('input', function() {
        var term = $(this).val().toLowerCase().trim();
        if (term.length > 0) {
            $('#clearPermSearch').removeClass('d-none');
        } else {
            $('#clearPermSearch').addClass('d-none');
        }

        if (!term) {
            $('.perm-item-row').show();
            $('.perm-module-card-col').show();
            return;
        }

        $('.perm-module-card-col').each(function() {
            var $cardCol = $(this);
            var cardHasMatch = false;

            $cardCol.find('.perm-item-row').each(function() {
                var $row = $(this);
                var rowText = ($row.data('perm-name') || '') + ' ' + $row.text().toLowerCase();
                if (rowText.indexOf(term) !== -1) {
                    $row.show();
                    cardHasMatch = true;
                } else {
                    $row.hide();
                }
            });

            if (cardHasMatch) {
                $cardCol.show();
            } else {
                $cardCol.hide();
            }
        });
    });

    $('#clearPermSearch').on('click', function() {
        $('#permSearchInput').val('').trigger('input');
    });

    updateActiveCounters();
});
</script>
@endpush
@endsection
