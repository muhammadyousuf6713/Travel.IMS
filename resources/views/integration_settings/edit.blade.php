@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-3 px-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h6 class="font-weight-bolder mb-0 text-dark">
                <i class="fa-solid fa-network-wired text-primary me-2"></i> System Integration & Lead Management
            </h6>
            <p class="text-xs text-secondary mb-0">Configure automated lead assignment (round-robin), Meta webhooks, and public intake forms.</p>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <form action="{{ route('integration_settings.simulate_lead') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-xs btn-outline-primary mb-0 shadow-none">
                    <i class="fa-solid fa-vial me-1"></i> Test Lead Assignment
                </button>
            </form>
            <a href="{{ url('/inquire-now') }}" target="_blank" class="btn btn-xs btn-dark mb-0">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Public Form
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-white text-xs py-2 px-3 mb-3" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close text-xs text-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show text-white text-xs py-2 px-3 mb-3" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ implode(', ', $errors->all()) }}
            <button type="button" class="btn-close text-xs text-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Clean Navigation Tabs -->
    <ul class="nav nav-tabs border-bottom mb-3" id="integrationTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active text-xs font-weight-bold py-2 px-3" id="round-robin-tab" data-bs-toggle="tab" data-bs-target="#round-robin-pane" type="button" role="tab">
                <i class="fa-solid fa-users-gear text-primary me-1"></i> 1. Lead Assignment (Round-Robin)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-xs font-weight-bold py-2 px-3" id="meta-tab" data-bs-toggle="tab" data-bs-target="#meta-pane" type="button" role="tab">
                <i class="fa-brands fa-facebook text-info me-1"></i> 2. Meta (Facebook / Instagram)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-xs font-weight-bold py-2 px-3" id="api-tab" data-bs-toggle="tab" data-bs-target="#api-pane" type="button" role="tab">
                <i class="fa-solid fa-globe text-success me-1"></i> 3. Web Form & API Webhook
            </button>
        </li>
    </ul>

    <form action="{{ route('integration_settings.update') }}" method="POST">
        @csrf

        <div class="tab-content" id="integrationTabsContent">
            <!-- TAB 1: Lead Assignment (Round-Robin) -->
            <div class="tab-pane fade show active" id="round-robin-pane" role="tabpanel">
                <div class="card shadow-none border mb-3">
                    <div class="card-body p-3">
                        <!-- Switch & Status bar -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center pb-3 mb-3 border-bottom">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="auto_assign_enabled" id="auto_assign_enabled" value="1" {{ $settings->auto_assign_enabled ? 'checked' : '' }}>
                                <label class="form-check-label text-xs font-weight-bold text-dark mb-0 ms-2" for="auto_assign_enabled">
                                    Enable Automatic Lead Assignment (Cycle: 1st inquiry to Agent 1, 2nd to Agent 2...)
                                </label>
                            </div>

                            <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
                                <span class="badge bg-light text-dark text-xxs border">
                                    Next Agent in Line: <strong class="text-primary">{{ $nextAgent ? $nextAgent->name : 'None Selected' }}</strong>
                                </span>
                                <span class="badge bg-light text-dark text-xxs border">
                                    Total Leads Processed: <strong class="text-success">{{ $settings->total_leads_processed ?? 0 }}</strong>
                                </span>
                            </div>
                        </div>

                        <!-- Sales Team Selection -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-0">
                                    Select Sales Staff for Lead Rotation ({{ count($assignedIds) }} Selected):
                                </label>
                                <div>
                                    <button type="button" class="btn btn-link text-xxs text-primary p-0 me-2" onclick="toggleAllUsers(true)">Select All</button>
                                    <button type="button" class="btn btn-link text-xxs text-secondary p-0" onclick="toggleAllUsers(false)">Clear All</button>
                                </div>
                            </div>

                            <div class="row g-2" style="max-height: 280px; overflow-y: auto;">
                                @foreach($users as $user)
                                    @php
                                        $isInPool = in_array($user->id, $assignedIds);
                                        $order = $isInPool ? (array_search($user->id, $assignedIds) + 1) : null;
                                    @endphp
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="border rounded p-2 d-flex align-items-center justify-content-between {{ $isInPool ? 'bg-light border-primary' : 'bg-white' }}">
                                            <div class="form-check mb-0 d-flex align-items-center">
                                                <input class="form-check-input me-2 mt-0 user-checkbox" type="checkbox" name="assigned_user_ids[]" value="{{ $user->id }}" id="user_{{ $user->id }}" {{ $isInPool ? 'checked' : '' }}>
                                                <label class="form-check-label text-xs text-dark mb-0 cursor-pointer" for="user_{{ $user->id }}">
                                                    {{ $user->name }}
                                                </label>
                                            </div>
                                            @if($order)
                                                <span class="badge bg-primary text-xxs px-1 py-0 rounded">#{{ $order }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Default Parameters -->
                        <div class="row g-3 pt-3 border-top">
                            <div class="col-md-4">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Default Source</label>
                                <select name="default_sales_reference_id" class="form-select form-select-sm text-xs">
                                    @foreach($salesReferences as $ref)
                                        <option value="{{ $ref->type_id }}" {{ $settings->default_sales_reference_id == $ref->type_id ? 'selected' : '' }}>
                                            {{ $ref->type_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Default Inquiry Type</label>
                                <select name="default_inquiry_type_id" class="form-select form-select-sm text-xs">
                                    @foreach($inquiryTypes as $type)
                                        <option value="{{ $type->type_id }}" {{ $settings->default_inquiry_type_id == $type->type_id ? 'selected' : '' }}>
                                            {{ $type->type_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Default Status</label>
                                <select name="default_inquiry_status" class="form-select form-select-sm text-xs">
                                    <option value="Open" {{ $settings->default_inquiry_status == 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="In-Progress" {{ $settings->default_inquiry_status == 'In-Progress' ? 'selected' : '' }}>In-Progress</option>
                                    <option value="Hold" {{ $settings->default_inquiry_status == 'Hold' ? 'selected' : '' }}>Hold</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Meta (Facebook / Instagram) -->
            <div class="tab-pane fade" id="meta-pane" role="tabpanel">
                <div class="card shadow-none border mb-3">
                    <div class="card-body p-3">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Meta Webhook Callback URL</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control text-xs font-monospace bg-light" id="metaWebhookInput" value="{{ $metaWebhookUrl }}" readonly>
                                    <button class="btn btn-outline-secondary mb-0" type="button" onclick="copyValue('metaWebhookInput')">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Meta Verify Token</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control text-xs font-monospace" name="meta_verify_token" id="metaTokenInput" value="{{ $settings->meta_verify_token }}">
                                    <button class="btn btn-outline-secondary mb-0" type="button" onclick="copyValue('metaTokenInput')">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Meta Page / System User Access Token</label>
                            <textarea class="form-control text-xs font-monospace" name="meta_page_access_token" rows="2" placeholder="Paste Page Access Token to automatically fetch customer lead answers">{{ $settings->meta_page_access_token }}</textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Meta App ID (Optional)</label>
                                <input type="text" class="form-control form-control-sm text-xs" name="meta_app_id" value="{{ $settings->meta_app_id }}" placeholder="e.g. 1234567890">
                            </div>
                            <div class="col-md-6">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Meta App Secret (Optional)</label>
                                <input type="password" class="form-control form-control-sm text-xs" name="meta_app_secret" value="{{ $settings->meta_app_secret }}" placeholder="••••••••••••••••••••">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: Web Form & API Webhook -->
            <div class="tab-pane fade" id="api-pane" role="tabpanel">
                <div class="card shadow-none border mb-3">
                    <div class="card-body p-3">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Public Inquiry Form URL</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control text-xs font-monospace bg-light" id="publicFormInput" value="{{ $publicFormUrl }}" readonly>
                                    <button class="btn btn-outline-secondary mb-0" type="button" onclick="copyValue('publicFormInput')">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">REST API Secret Key (X-API-KEY)</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control text-xs font-monospace bg-light" id="apiKeyInput" value="{{ $settings->api_key }}" readonly>
                                    <button class="btn btn-outline-secondary mb-0" type="button" onclick="copyValue('apiKeyInput')">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Public Form Title</label>
                                <input type="text" class="form-control form-control-sm text-xs" name="public_form_title" value="{{ $settings->public_form_title }}">
                            </div>

                            <div class="col-md-6">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Public Form Subtitle</label>
                                <input type="text" class="form-control form-control-sm text-xs" name="public_form_subtitle" value="{{ $settings->public_form_subtitle }}">
                            </div>
                        </div>

                        <!-- Direct API Webhook URL -->
                        <div class="mt-3 pt-3 border-top">
                            <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Direct Webhook Endpoint (POST)</label>
                            <div class="input-group input-group-sm mb-2">
                                <input type="text" class="form-control text-xs font-monospace bg-light" id="directWebhookInput" value="{{ $webhookUrl }}" readonly>
                                <button class="btn btn-outline-secondary mb-0" type="button" onclick="copyValue('directWebhookInput')">
                                    <i class="fa-solid fa-copy"></i>
                                </button>
                            </div>
                            <small class="text-xxs text-muted">Send JSON payload with header <code>X-API-KEY</code> to automatically create inquiries from website forms or Zapier.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-sm btn-primary px-4">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save Settings
            </button>
        </div>
    </form>
</div>

<script>
function copyValue(id) {
    const input = document.getElementById(id);
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(() => {
        alert("Copied: " + input.value);
    }).catch(() => {
        document.execCommand("copy");
        alert("Copied: " + input.value);
    });
}

function toggleAllUsers(select) {
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.checked = select;
    });
}
</script>
@endsection
