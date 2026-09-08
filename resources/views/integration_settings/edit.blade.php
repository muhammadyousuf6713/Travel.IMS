@extends('layouts.user_type.auth')

@section('content')
<style>
/* Custom Luxury iOS-Style Toggle Switch */
.custom-toggle-switch {
    position: relative;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
    margin-bottom: 0;
}
.custom-toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
}
.custom-toggle-switch .toggle-slider {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    background-color: #cbd5e1;
    border-radius: 24px;
    transition: background-color 0.25s ease, box-shadow 0.25s ease;
    flex-shrink: 0;
}
.custom-toggle-switch .toggle-slider::after {
    content: "";
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    background-color: #ffffff;
    border-radius: 50%;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.custom-toggle-switch input:checked + .toggle-slider {
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
}
.custom-toggle-switch input:checked + .toggle-slider::after {
    transform: translateX(20px);
}
.custom-toggle-switch .toggle-label {
    margin-left: 10px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #2d3748;
    cursor: pointer;
}

/* User Selection Card & Sequence Badge */
.user-select-card {
    transition: all 0.2s ease;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    border-radius: 8px;
    position: relative;
}
.user-select-card:hover {
    border-color: #93c5fd;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.08);
}
.user-select-card.selected {
    border-color: #0d6efd;
    background: #f0f7ff;
}
.seq-badge {
    min-width: 34px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.75rem;
    border-radius: 6px;
    transition: all 0.2s ease;
}
.seq-badge.active {
    background: #0d6efd;
    color: #ffffff;
    box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3);
}
.seq-badge.inactive {
    background: #e2e8f0;
    color: #94a3b8;
}
.rotation-chain-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    font-size: 0.75rem;
    font-weight: 600;
}
.rotation-chain-item.is-next {
    background: #0d6efd;
    color: #ffffff;
    border-color: #0d6efd;
    box-shadow: 0 2px 6px rgba(13, 110, 253, 0.35);
}
</style>

<div class="container-fluid py-3 px-3">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h6 class="font-weight-bolder mb-0 text-dark">
                <i class="fa-solid fa-network-wired text-primary me-2"></i> System Integration & Lead Auto-Assignment
            </h6>
            <p class="text-xs text-secondary mb-0">Manage automatic lead distribution sequence (round-robin) and Meta Ads / API lead ingestion.</p>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <form action="{{ route('integration_settings.simulate_lead') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-xs btn-primary mb-0 shadow-sm">
                    <i class="fa-solid fa-vial me-1"></i> Test Lead Ingestion & Assign Next Agent
                </button>
            </form>
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
                <i class="fa-solid fa-users-gear text-primary me-1"></i> 1. Lead Assignment (Sequence & Staff Pool)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-xs font-weight-bold py-2 px-3" id="meta-tab" data-bs-toggle="tab" data-bs-target="#meta-pane" type="button" role="tab">
                <i class="fa-brands fa-facebook text-info me-1"></i> 2. Meta Ads (Facebook & Instagram Webhook)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-xs font-weight-bold py-2 px-3" id="api-tab" data-bs-toggle="tab" data-bs-target="#api-pane" type="button" role="tab">
                <i class="fa-solid fa-code text-success me-1"></i> 3. External API & Webhooks
            </button>
        </li>
    </ul>

    <form action="{{ route('integration_settings.update') }}" method="POST" id="settingsForm">
        @csrf

        <!-- Container for ordered assigned user inputs -->
        <div id="hiddenUserInputsContainer"></div>

        <div class="tab-content" id="integrationTabsContent">
            <!-- TAB 1: Lead Assignment (Round-Robin & Dynamic Sequence) -->
            <div class="tab-pane fade show active" id="round-robin-pane" role="tabpanel">
                <div class="card shadow-none border mb-3">
                    <div class="card-body p-3">
                        <!-- Switch & Status bar -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center pb-3 mb-3 border-bottom">
                            <label class="custom-toggle-switch">
                                <input type="checkbox" name="auto_assign_enabled" id="auto_assign_enabled" value="1" {{ $settings->auto_assign_enabled ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                                <span class="toggle-label">
                                    Enable Automatic Lead Assignment (Sequential Rotation: 1st lead to #1, 2nd to #2, etc.)
                                </span>
                            </label>

                            <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
                                <span class="badge bg-light text-dark text-xxs border p-2">
                                    Next Agent in Queue: 
                                    <strong class="text-primary ms-1" id="nextAgentDisplay">{{ $nextAgent ? $nextAgent->name : 'None Selected' }}</strong>
                                </span>
                                <span class="badge bg-light text-dark text-xxs border p-2">
                                    Total Leads Processed: <strong class="text-success ms-1">{{ $settings->total_leads_processed ?? 0 }}</strong>
                                </span>
                            </div>
                        </div>

                        <!-- Live Rotation Chain Visualizer -->
                        <div class="p-3 mb-3 bg-light rounded border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="text-xxs font-weight-bolder text-uppercase text-secondary">
                                    <i class="fa-solid fa-arrows-spin me-1 text-primary"></i> Live Allotment Rotation Sequence:
                                </div>
                                <div class="text-xxs text-muted">
                                    Total in Rotation: <strong class="text-dark" id="totalActiveCount">{{ count($assignedIds) }}</strong> staff member(s)
                                </div>
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-2" id="rotationChainContainer">
                                <!-- Populated dynamically by JS -->
                            </div>
                        </div>

                        <!-- Sales Team Selection List -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-0">
                                    Check Sales Staff to Add to Rotation (Sequence numbers automatically allot in real-time order):
                                </label>
                                <div>
                                    <button type="button" class="btn btn-link text-xxs text-primary p-0 me-2" onclick="selectAllUsers()">Select All</button>
                                    <span class="text-muted text-xxs me-2">|</span>
                                    <button type="button" class="btn btn-link text-xxs text-danger p-0" onclick="clearAllUsers()">Clear All</button>
                                </div>
                            </div>

                            <div class="row g-2" style="max-height: 360px; overflow-y: auto;" id="userCardList">
                                @foreach($users as $user)
                                    @php
                                        $isInPool = in_array($user->id, $assignedIds);
                                        $orderIndex = $isInPool ? (array_search($user->id, $assignedIds) + 1) : null;
                                    @endphp
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="user-select-card p-2 d-flex align-items-center justify-content-between {{ $isInPool ? 'selected' : '' }}" id="card_user_{{ $user->id }}" onclick="handleCardClick(event, '{{ $user->id }}')">
                                            <div class="form-check mb-0 d-flex align-items-center">
                                                <input class="form-check-input me-2 mt-0 user-checkbox" 
                                                       type="checkbox" 
                                                       value="{{ $user->id }}" 
                                                       id="user_{{ $user->id }}" 
                                                       data-name="{{ $user->name }}"
                                                       {{ $isInPool ? 'checked' : '' }}
                                                       onchange="onUserCheckChange('{{ $user->id }}')">
                                                <label class="form-check-label text-xs text-dark mb-0 cursor-pointer" for="user_{{ $user->id }}">
                                                    {{ $user->name }}
                                                </label>
                                            </div>
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="seq-badge {{ $isInPool ? 'active' : 'inactive' }}" id="seq_badge_{{ $user->id }}">
                                                    {{ $orderIndex ? '#' . $orderIndex : '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Default Parameters -->
                        <div class="row g-3 pt-3 border-top">
                            <div class="col-md-4">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Default Lead Source</label>
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
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Default Inquiry Status</label>
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

            <!-- TAB 2: Meta (Facebook / Instagram Lead Ads) -->
            <div class="tab-pane fade" id="meta-pane" role="tabpanel">
                <div class="card shadow-none border mb-3">
                    <div class="card-body p-3">
                        <div class="alert alert-info text-white text-xs py-2 px-3 mb-3">
                            <i class="fa-brands fa-meta me-2"></i> 
                            <strong>Meta Ads Integration:</strong> Create Instant Forms in your Meta Ads Manager. Meta will post lead notifications directly to this Webhook URL, and the IMS will auto-assign each lead to your sales team in the configured sequence (#1 ➔ #2 ➔ #3).
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Meta Webhook Callback URL</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control text-xs font-monospace bg-light" id="metaWebhookInput" value="{{ $metaWebhookUrl }}" readonly>
                                    <button class="btn btn-outline-secondary mb-0" type="button" onclick="copyValue('metaWebhookInput')">
                                        <i class="fa-solid fa-copy"></i> Copy
                                    </button>
                                </div>
                                <small class="text-xxs text-muted">Paste this URL in Meta App Dashboard under <strong>Webhooks > Page > Leadgen</strong>.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Meta Verify Token</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control text-xs font-monospace" name="meta_verify_token" id="metaTokenInput" value="{{ $settings->meta_verify_token }}">
                                    <button class="btn btn-outline-secondary mb-0" type="button" onclick="copyValue('metaTokenInput')">
                                        <i class="fa-solid fa-copy"></i> Copy
                                    </button>
                                </div>
                                <small class="text-xxs text-muted">Provide this exact token when Meta verifies your webhook subscription.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Meta Page / System User Access Token</label>
                            <textarea class="form-control text-xs font-monospace" name="meta_page_access_token" rows="3" placeholder="Paste your permanent Page Access Token or System User Token from Meta Business Manager">{{ $settings->meta_page_access_token }}</textarea>
                            <small class="text-xxs text-muted">Used by the server to fetch field answers (name, phone, travel package, etc.) via Meta Graph API when a lead is captured.</small>
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

            <!-- TAB 3: External API & Webhooks -->
            <div class="tab-pane fade" id="api-pane" role="tabpanel">
                <div class="card shadow-none border mb-3">
                    <div class="card-body p-3">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">Direct REST API Ingestion Endpoint (POST)</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control text-xs font-monospace bg-light" id="directWebhookInput" value="{{ $webhookUrl }}" readonly>
                                    <button class="btn btn-outline-secondary mb-0" type="button" onclick="copyValue('directWebhookInput')">
                                        <i class="fa-solid fa-copy"></i> Copy
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-1">REST API Secret Key (X-API-KEY)</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control text-xs font-monospace bg-light" id="apiKeyInput" value="{{ $settings->api_key }}" readonly>
                                    <button class="btn btn-outline-secondary mb-0" type="button" onclick="copyValue('apiKeyInput')">
                                        <i class="fa-solid fa-copy"></i> Copy
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded border">
                            <label class="text-xxs font-weight-bolder text-uppercase text-muted mb-2">Example JSON Webhook Payload:</label>
                            <pre class="bg-dark text-white p-3 rounded text-xs mb-0" style="overflow-x: auto;"><code>POST {{ $webhookUrl }}
Headers:
  Content-Type: application/json
  X-API-KEY: {{ $settings->api_key }}

Body:
{
  "customer_name": "Muhammad Ali",
  "customer_cell": "+92 300 1234567",
  "customer_email": "ali@example.com",
  "city": "Karachi",
  "service_type": "Umrah Package",
  "travel_date": "2026-10-15",
  "adults": 2,
  "children": 1,
  "remarks": "Looking for 4-star hotel near Haram with transport included",
  "source": "Meta Ads Campaign",
  "campaign_name": "Ramzan Umrah 2026"
}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="d-flex justify-content-end mb-4">
            <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm" onclick="syncHiddenInputsBeforeSubmit()">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save Allotment Sequence & Settings
            </button>
        </div>
    </form>
</div>

<script>
// Keep active ordered array of user IDs
let orderedUserIds = @json(array_values($assignedIds));
const allUsersData = @json($users->keyBy('id'));
const nextAgentId = {{ $nextAgent ? $nextAgent->id : 'null' }};

document.addEventListener("DOMContentLoaded", function() {
    refreshSequenceUI();
});

function handleCardClick(event, userId) {
    if (event.target.tagName.toLowerCase() === 'input') {
        return; // Checkbox change handler takes care of it
    }
    const checkbox = document.getElementById('user_' + userId);
    if (checkbox) {
        checkbox.checked = !checkbox.checked;
        onUserCheckChange(userId);
    }
}

function onUserCheckChange(userId) {
    userId = parseInt(userId);
    const checkbox = document.getElementById('user_' + userId);
    
    if (checkbox.checked) {
        if (!orderedUserIds.includes(userId)) {
            orderedUserIds.push(userId);
        }
    } else {
        orderedUserIds = orderedUserIds.filter(id => id !== userId);
    }

    refreshSequenceUI();
}

function selectAllUsers() {
    orderedUserIds = [];
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.checked = true;
        orderedUserIds.push(parseInt(cb.value));
    });
    refreshSequenceUI();
}

function clearAllUsers() {
    orderedUserIds = [];
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.checked = false;
    });
    refreshSequenceUI();
}

function refreshSequenceUI() {
    // 1. Update each card and badge
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        const uid = parseInt(cb.value);
        const card = document.getElementById('card_user_' + uid);
        const badge = document.getElementById('seq_badge_' + uid);
        const idx = orderedUserIds.indexOf(uid);

        if (idx !== -1) {
            cb.checked = true;
            if (card) card.classList.add('selected');
            if (badge) {
                badge.className = 'seq-badge active';
                badge.innerText = '#' + (idx + 1);
            }
        } else {
            cb.checked = false;
            if (card) card.classList.remove('selected');
            if (badge) {
                badge.className = 'seq-badge inactive';
                badge.innerText = '-';
            }
        }
    });

    // 2. Render Live Rotation Chain Visualizer
    const chainContainer = document.getElementById('rotationChainContainer');
    const totalActiveCount = document.getElementById('totalActiveCount');
    const nextAgentDisplay = document.getElementById('nextAgentDisplay');
    
    totalActiveCount.innerText = orderedUserIds.length;
    chainContainer.innerHTML = '';

    if (orderedUserIds.length === 0) {
        chainContainer.innerHTML = '<span class="text-xs text-muted fst-italic">No staff members currently selected in the rotation pool.</span>';
        nextAgentDisplay.innerText = 'None Selected';
    } else {
        orderedUserIds.forEach((uid, index) => {
            const user = allUsersData[uid];
            if (!user) return;

            const isNext = (nextAgentId === uid) || (nextAgentId === null && index === 0);
            
            const itemDiv = document.createElement('div');
            itemDiv.className = 'rotation-chain-item ' + (isNext ? 'is-next' : '');
            itemDiv.innerHTML = `
                <span>#${index + 1}</span>
                <strong>${user.name}</strong>
                ${isNext ? '<span class="badge bg-success text-xxs px-1 py-0 ms-1">Next In Line</span>' : ''}
            `;
            chainContainer.appendChild(itemDiv);

            if (index < orderedUserIds.length - 1) {
                const arrow = document.createElement('i');
                arrow.className = 'fa-solid fa-arrow-right text-muted text-xxs';
                chainContainer.appendChild(arrow);
            }
        });

        // Set Next Agent Display text
        const currentNextUser = allUsersData[orderedUserIds[0]];
        if (nextAgentId && allUsersData[nextAgentId] && orderedUserIds.includes(nextAgentId)) {
            nextAgentDisplay.innerText = allUsersData[nextAgentId].name;
        } else if (currentNextUser) {
            nextAgentDisplay.innerText = currentNextUser.name;
        }
    }

    syncHiddenInputs();
}

function syncHiddenInputs() {
    const container = document.getElementById('hiddenUserInputsContainer');
    container.innerHTML = '';
    orderedUserIds.forEach(uid => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'assigned_user_ids[]';
        hiddenInput.value = uid;
        container.appendChild(hiddenInput);
    });
}

function syncHiddenInputsBeforeSubmit() {
    syncHiddenInputs();
}

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
</script>
@endsection
