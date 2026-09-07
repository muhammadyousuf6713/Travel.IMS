@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-3 px-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-1 pb-0 pt-1 px-0">
                    <li class="breadcrumb-item"><a class="opacity-5 text-dark" href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item text-dark active" aria-current="page">System Integration Settings</li>
                </ol>
            </nav>
            <h5 class="font-weight-bolder mb-0 text-dark">
                <i class="fa-solid fa-network-wired text-primary me-2"></i> System Integration & Direct Lead Ingestion
            </h5>
            <p class="text-xs text-secondary mb-0">
                Configure Meta Lead Ads, Direct Webhooks, Public Web Forms, and Automated Round-Robin Sales Distribution.
            </p>
        </div>

        <div class="d-flex gap-2 align-items-center mt-2 mt-md-0">
            <!-- Test Lead Simulation Button -->
            <form action="{{ route('integration_settings.simulate_lead') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary mb-0 shadow-sm" title="Simulate an incoming lead and test round-robin assignment">
                    <i class="fa-solid fa-vial me-1"></i> Simulate Test Lead
                </button>
            </form>

            <a href="{{ url('/inquire-now') }}" target="_blank" class="btn btn-sm btn-outline-dark mb-0 shadow-sm">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Preview Public Form
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-white text-xs py-2 px-3 mb-3" role="alert">
            <span class="alert-icon"><i class="fa-solid fa-circle-check me-2"></i></span>
            <span class="alert-text"><strong>Success!</strong> {{ session('success') }}</span>
            <button type="button" class="btn-close text-xs text-white" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show text-white text-xs py-2 px-3 mb-3" role="alert">
            <span class="alert-icon"><i class="fa-solid fa-triangle-exclamation me-2"></i></span>
            <span class="alert-text"><strong>Please fix errors:</strong> {{ implode(', ', $errors->all()) }}</span>
            <button type="button" class="btn-close text-xs text-white" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Real-time Status Overview Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-xxs text-uppercase font-weight-bolder text-muted">Round-Robin Status</span>
                        <h6 class="font-weight-bolder mb-0 mt-1">
                            @if($settings->auto_assign_enabled)
                                <span class="badge bg-gradient-success text-xxs px-2 py-1"><i class="fa-solid fa-circle-play me-1"></i> Active (Automated)</span>
                            @else
                                <span class="badge bg-secondary text-xxs px-2 py-1"><i class="fa-solid fa-circle-pause me-1"></i> Disabled</span>
                            @endif
                        </h6>
                    </div>
                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md" style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-rotate text-white text-sm opacity-10" style="margin-top: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-xxs text-uppercase font-weight-bolder text-muted">Next Agent in Line</span>
                        <h6 class="font-weight-bolder mb-0 mt-1 text-primary text-sm">
                            <i class="fa-solid fa-user-tag me-1"></i> {{ $nextAgent ? $nextAgent->name : 'None Selected' }}
                        </h6>
                    </div>
                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md" style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-user-clock text-white text-sm opacity-10" style="margin-top: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-xxs text-uppercase font-weight-bolder text-muted">Active Sales Team Pool</span>
                        <h6 class="font-weight-bolder mb-0 mt-1 text-dark">
                            {{ count($assignedIds) }} Agents In Cycle
                        </h6>
                    </div>
                    <div class="icon icon-shape bg-gradient-dark shadow text-center border-radius-md" style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-users-gear text-white text-sm opacity-10" style="margin-top: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-xxs text-uppercase font-weight-bolder text-muted">Total Leads Processed</span>
                        <h6 class="font-weight-bolder mb-0 mt-1 text-success">
                            {{ number_format($settings->total_leads_processed ?? 0) }} Inquiries
                        </h6>
                    </div>
                    <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md" style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-chart-line text-white text-sm opacity-10" style="margin-top: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Configuration Form -->
    <form action="{{ route('integration_settings.update') }}" method="POST">
        @csrf

        <!-- SECTION 1: Round-Robin Automated Lead Distribution Engine -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-gray-100 py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="text-xs font-weight-bolder text-uppercase text-dark mb-0">
                    <i class="fa-solid fa-shuffle text-primary me-2"></i> 1. Round-Robin Lead Assignment & Sales Team Rotation
                </h6>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="auto_assign_enabled" id="auto_assign_enabled" value="1" {{ $settings->auto_assign_enabled ? 'checked' : '' }}>
                    <label class="form-check-label text-xs font-weight-bold text-dark mb-0 ms-1" for="auto_assign_enabled">Enable Auto-Assign</label>
                </div>
            </div>
            <div class="card-body p-3">
                <p class="text-xs text-secondary mb-3">
                    Select sales team members who will receive direct leads in a continuous cycle (Lead 1 &rarr; Agent A, Lead 2 &rarr; Agent B, Lead 3 &rarr; Agent C &rarr; Agent A).
                </p>

                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <label class="text-xxs font-weight-bolder text-uppercase text-muted d-block mb-2">Select Sales Representatives in Rotation Pool:</label>
                        <div class="row g-2">
                            @foreach($users as $user)
                                @php
                                    $isInPool = in_array($user->id, $assignedIds);
                                    $position = $isInPool ? (array_search($user->id, $assignedIds) + 1) : null;
                                @endphp
                                <div class="col-md-3 col-sm-6">
                                    <div class="border rounded p-2 d-flex align-items-center justify-content-between {{ $isInPool ? 'bg-gray-100 border-primary shadow-2xs' : 'bg-white' }}">
                                        <div class="form-check mb-0 d-flex align-items-center">
                                            <input class="form-check-input me-2 mt-0 user-pool-checkbox" type="checkbox" name="assigned_user_ids[]" value="{{ $user->id }}" id="user_{{ $user->id }}" {{ $isInPool ? 'checked' : '' }}>
                                            <label class="form-check-label text-xs font-weight-bold text-dark mb-0 cursor-pointer" for="user_{{ $user->id }}">
                                                {{ $user->name }}
                                                <span class="d-block text-xxs text-muted font-weight-normal">{{ $user->email }}</span>
                                            </label>
                                        </div>
                                        @if($position)
                                            <span class="badge bg-primary text-xxs px-2 py-1 rounded-pill" title="Rotation order">#{{ $position }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row g-3 pt-2 border-top">
                    <div class="col-md-4">
                        <label class="form-label text-xxs font-weight-bolder text-uppercase text-muted mb-1">Default Sales Reference / Source</label>
                        <select name="default_sales_reference_id" class="form-select form-select-sm text-xs">
                            @foreach($salesReferences as $ref)
                                <option value="{{ $ref->type_id }}" {{ $settings->default_sales_reference_id == $ref->type_id ? 'selected' : '' }}>
                                    {{ $ref->type_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xxs font-weight-bolder text-uppercase text-muted mb-1">Default Inquiry Type</label>
                        <select name="default_inquiry_type_id" class="form-select form-select-sm text-xs">
                            @foreach($inquiryTypes as $type)
                                <option value="{{ $type->type_id }}" {{ $settings->default_inquiry_type_id == $type->type_id ? 'selected' : '' }}>
                                    {{ $type->type_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-xxs font-weight-bolder text-uppercase text-muted mb-1">Default Ingested Inquiry Status</label>
                        <select name="default_inquiry_status" class="form-select form-select-sm text-xs">
                            <option value="Open" {{ $settings->default_inquiry_status == 'Open' ? 'selected' : '' }}>Open</option>
                            <option value="In-Progress" {{ $settings->default_inquiry_status == 'In-Progress' ? 'selected' : '' }}>In-Progress</option>
                            <option value="Hold" {{ $settings->default_inquiry_status == 'Hold' ? 'selected' : '' }}>Hold</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: Meta / Facebook Lead Ads Webhook Integration -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-gray-100 py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="text-xs font-weight-bolder text-uppercase text-dark mb-0">
                    <i class="fa-brands fa-facebook text-primary me-2"></i> 2. Meta (Facebook & Instagram) Lead Ads Webhook
                </h6>
                <span class="badge bg-gradient-info text-xxs">Real-time Lead Sync</span>
            </div>
            <div class="card-body p-3">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-xxs font-weight-bolder text-uppercase text-muted mb-1">Meta Webhook Callback URL (Copy to Meta App)</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control text-xs font-monospace bg-light" id="metaWebhookUrlInput" value="{{ $metaWebhookUrl }}" readonly>
                            <button class="btn btn-outline-secondary mb-0 copy-btn" type="button" onclick="copyToClipboard('metaWebhookUrlInput')">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-xxs font-weight-bolder text-uppercase text-muted mb-1">Meta Verify Token</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control text-xs font-monospace" name="meta_verify_token" id="metaVerifyTokenInput" value="{{ $settings->meta_verify_token }}">
                            <button class="btn btn-outline-secondary mb-0 copy-btn" type="button" onclick="copyToClipboard('metaVerifyTokenInput')">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label text-xxs font-weight-bolder text-uppercase text-muted mb-1">
                            Meta Page / System User Access Token 
                            <span class="text-muted font-weight-normal">(Required to automatically pull lead questions and form data)</span>
                        </label>
                        <textarea class="form-control text-xs font-monospace" name="meta_page_access_token" rows="2" placeholder="EAAG... (Paste permanent Page Access Token from Meta Developer Console)">{{ $settings->meta_page_access_token }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-xxs font-weight-bolder text-uppercase text-muted mb-1">Meta App ID (Optional)</label>
                        <input type="text" class="form-control form-control-sm text-xs" name="meta_app_id" value="{{ $settings->meta_app_id }}" placeholder="e.g. 123456789012345">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-xxs font-weight-bolder text-uppercase text-muted mb-1">Meta App Secret (Optional)</label>
                        <input type="password" class="form-control form-control-sm text-xs" name="meta_app_secret" value="{{ $settings->meta_app_secret }}" placeholder="••••••••••••••••••••">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Direct Webhook & REST API Ingestion -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-gray-100 py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="text-xs font-weight-bolder text-uppercase text-dark mb-0">
                    <i class="fa-solid fa-code text-primary me-2"></i> 3. Direct REST API & External Webhook Ingestion
                </h6>
                <span class="badge bg-gradient-dark text-xxs">Zapier / WordPress / Custom Forms</span>
            </div>
            <div class="card-body p-3">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-xxs font-weight-bolder text-uppercase text-muted mb-1">Webhook Endpoint (POST)</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control text-xs font-monospace bg-light" id="directWebhookUrlInput" value="{{ $webhookUrl }}" readonly>
                            <button class="btn btn-outline-secondary mb-0 copy-btn" type="button" onclick="copyToClipboard('directWebhookUrlInput')">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-xxs font-weight-bolder text-uppercase text-muted mb-1">API Secret Key (X-API-KEY)</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control text-xs font-monospace bg-light" id="apiKeyInput" value="{{ $settings->api_key }}" readonly>
                            <button class="btn btn-outline-secondary mb-0 copy-btn" type="button" onclick="copyToClipboard('apiKeyInput')">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sample JSON Payload Accordion / Code Snippet -->
                <div class="border rounded p-2 bg-gray-100">
                    <span class="text-xxs font-weight-bolder text-uppercase text-muted d-block mb-1">Sample JSON POST Payload:</span>
                    <pre class="bg-dark text-white rounded p-2 text-xxs mb-0" style="max-height: 140px; overflow-y: auto;"><code>{
  "name": "Muhammad Usman",
  "phone": "+92 300 1234567",
  "email": "usman@example.com",
  "city": "Karachi",
  "service_type": "Umrah Package",
  "travel_date": "2026-10-15",
  "adults": 2,
  "children": 1,
  "remarks": "Looking for 5-star hotel in Makkah and Madinah.",
  "source": "Website Landing Page",
  "campaign_name": "Ramzan 2026 Special"
}</code></pre>
                </div>
            </div>
        </div>

        <!-- SECTION 4: Public Embeddable / Shareable Web Inquiry Form -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-gray-100 py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="text-xs font-weight-bolder text-uppercase text-dark mb-0">
                    <i class="fa-solid fa-globe text-primary me-2"></i> 4. Public Web Form (Shareable URL & Embed Iframe)
                </h6>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="public_form_enabled" id="public_form_enabled" value="1" {{ $settings->public_form_enabled ? 'checked' : '' }}>
                    <label class="form-check-label text-xs font-weight-bold text-dark mb-0 ms-1" for="public_form_enabled">Enable Public Form</label>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-xxs font-weight-bolder text-uppercase text-muted mb-1">Direct Shareable Link</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control text-xs font-monospace bg-light" id="publicFormUrlInput" value="{{ $publicFormUrl }}" readonly>
                            <button class="btn btn-outline-secondary mb-0 copy-btn" type="button" onclick="copyToClipboard('publicFormUrlInput')">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-xxs font-weight-bolder text-uppercase text-muted mb-1">Embed HTML Iframe Code</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control text-xs font-monospace bg-light" id="embedIframeInput" value='<iframe src="{{ $publicFormUrl }}" width="100%" height="750" frameborder="0"></iframe>' readonly>
                            <button class="btn btn-outline-secondary mb-0 copy-btn" type="button" onclick="copyToClipboard('embedIframeInput')">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-xxs font-weight-bolder text-uppercase text-muted mb-1">Public Form Title</label>
                        <input type="text" class="form-control form-control-sm text-xs" name="public_form_title" value="{{ $settings->public_form_title }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-xxs font-weight-bolder text-uppercase text-muted mb-1">Public Form Subtitle / Instructions</label>
                        <input type="text" class="form-control form-control-sm text-xs" name="public_form_subtitle" value="{{ $settings->public_form_subtitle }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Save Actions Bar -->
        <div class="card shadow-sm border-0 sticky-bottom bg-white py-2 px-3 mt-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-xs text-secondary">
                    <i class="fa-solid fa-shield-check text-success me-1"></i> Changes take effect immediately across all webhook listeners and intake forms.
                </span>
                <button type="submit" class="btn btn-sm btn-primary mb-0 px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Integration Settings
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function copyToClipboard(elementId) {
    const copyText = document.getElementById(elementId);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value).then(() => {
        alert("Copied to clipboard: " + copyText.value);
    }).catch(err => {
        document.execCommand("copy");
        alert("Copied: " + copyText.value);
    });
}
</script>
@endsection
