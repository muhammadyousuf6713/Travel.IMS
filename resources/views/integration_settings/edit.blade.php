@extends('layouts.user_type.auth')

@section('content')
<style>
    .settings-nav .nav-link {
        display: flex; align-items: center; gap: .65rem;
        color: #344767; font-size: .85rem; font-weight: 600;
        padding: .6rem .8rem; border-radius: .5rem; margin-bottom: 2px;
        text-align: left; width: 100%; background: none; border: 0;
    }
    .settings-nav .nav-link i { width: 16px; text-align: center; color: #8392ab; }
    .settings-nav .nav-link:hover { background: #f8f9fa; }
    .settings-nav .nav-link.active { background: #eef2f7; color: #1e293b; }
    .settings-nav .nav-link.active i { color: #1e293b; }
    .settings-nav .nav-status { margin-left: auto; font-size: .7rem; font-weight: 600; color: #8392ab; }
    .settings-nav .nav-status.on { color: #2dce89; }

    .section-head { border-bottom: 1px solid #eef0f3; padding-bottom: .75rem; margin-bottom: 1.25rem; }
    .section-head h6 { margin: 0; font-weight: 700; color: #1e293b; }
    .section-head p { margin: .15rem 0 0; font-size: .8rem; color: #8392ab; }

    .field-label { font-size: .75rem; font-weight: 700; color: #344767; margin-bottom: .3rem; display: block; }
    .field-help { font-size: .72rem; color: #8392ab; margin-top: .25rem; }

    .switch-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem;
        padding: .85rem 1rem; border: 1px solid #eef0f3; border-radius: .5rem; background: #fbfcfd; }
    .switch-row .title { font-size: .85rem; font-weight: 700; color: #1e293b; }
    .switch-row .desc { font-size: .75rem; color: #8392ab; }
    .switch-row .form-switch { padding-left: 0; margin: 0; }
    .switch-row .form-switch .form-check-input { width: 2.5rem; height: 1.3rem; margin: 0; float: none; cursor: pointer; }

    .member-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: .5rem; max-height: 300px; overflow-y: auto; }
    .member-item { display: flex; align-items: center; gap: .5rem; padding: .5rem .65rem; border: 1px solid #e9ecef;
        border-radius: .5rem; cursor: pointer; margin: 0; font-size: .8rem; color: #344767; background: #fff; }
    .member-item:hover { border-color: #cfd8e3; }
    .member-item.checked { border-color: #344767; background: #f8f9fb; }
    .member-item .form-check-input { margin: 0; flex-shrink: 0; }
    .member-item .order { margin-left: auto; font-size: .7rem; font-weight: 700; color: #fff; background: #344767;
        border-radius: 4px; padding: 1px 6px; }

    .summary-line { font-size: .78rem; color: #67748e; background: #f8f9fa; border-radius: .5rem; padding: .6rem .8rem; }
    .summary-line strong { color: #1e293b; }

    .steps { counter-reset: step; list-style: none; padding: 0; margin: 0 0 1.25rem; }
    .steps li { counter-increment: step; position: relative; padding-left: 1.9rem; font-size: .8rem; color: #67748e; margin-bottom: .4rem; }
    .steps li::before { content: counter(step); position: absolute; left: 0; top: 0; width: 1.3rem; height: 1.3rem; border-radius: 50%;
        background: #eef2f7; color: #344767; font-size: .7rem; font-weight: 700; display: flex; align-items: center; justify-content: center; }

    .save-bar { position: sticky; bottom: 0; z-index: 5; background: #fff; border-top: 1px solid #eef0f3;
        padding: .75rem 1.25rem; display: flex; justify-content: flex-end; gap: .5rem; border-radius: 0 0 1rem 1rem; }
    code.inline { font-size: .72rem; background: #f1f3f5; color: #344767; padding: 1px 5px; border-radius: 4px; }
</style>

<div class="container-fluid py-3 px-3">

    {{-- Page header --}}
    <div class="mb-3">
        <h5 class="mb-0 font-weight-bolder text-dark">Integration Settings</h5>
        <p class="text-sm text-secondary mb-0">Lead assignment, Meta Lead Ads, API access and customer reviews.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger text-white text-sm py-2 px-3 mb-3" role="alert">
            {{ implode(' ', $errors->all()) }}
        </div>
    @endif

    <div class="card">
        <div class="row g-0">

            {{-- Side menu --}}
            <div class="col-lg-3 border-end">
                <div class="p-3 settings-nav nav flex-column" id="integrationTabs" role="tablist">
                    <button class="nav-link active" id="round-robin-tab" data-bs-toggle="pill" data-bs-target="#round-robin-pane" type="button" role="tab">
                        <i class="fa-solid fa-user-group"></i> Lead Assignment
                        <span class="nav-status {{ $settings->auto_assign_enabled ? 'on' : '' }}">{{ $settings->auto_assign_enabled ? 'On' : 'Off' }}</span>
                    </button>
                    <button class="nav-link" id="meta-tab" data-bs-toggle="pill" data-bs-target="#meta-pane" type="button" role="tab">
                        <i class="fa-brands fa-facebook"></i> Meta Lead Ads
                        <span class="nav-status {{ $settings->meta_page_access_token ? 'on' : '' }}">{{ $settings->meta_page_access_token ? 'Connected' : 'Not set' }}</span>
                    </button>
                    <button class="nav-link" id="api-tab" data-bs-toggle="pill" data-bs-target="#api-pane" type="button" role="tab">
                        <i class="fa-solid fa-plug"></i> API Access
                    </button>
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="pill" data-bs-target="#reviews-pane" type="button" role="tab">
                        <i class="fa-regular fa-star"></i> Reviews
                        @if($reviewReady)
                            <span class="nav-status {{ $settings->review_auto_sync_enabled ? 'on' : '' }}">{{ $settings->review_auto_sync_enabled ? 'Auto' : 'Manual' }}</span>
                        @endif
                    </button>
                    <button class="nav-link" id="system-plan-tab" data-bs-toggle="pill" data-bs-target="#system-plan-pane" type="button" role="tab">
                        <i class="fa-solid fa-gem"></i> System Plan
                        <span class="nav-status {{ !empty($planStatus['enabled']) ? (!empty($planStatus['is_suspended']) ? 'bg-danger text-white' : (!empty($planStatus['is_warning']) ? 'bg-warning text-dark' : 'on')) : '' }}">
                            {{ !empty($planStatus['enabled']) ? (!empty($planStatus['is_suspended']) ? 'Suspended' : (!empty($planStatus['is_warning']) ? 'Due Soon' : 'Active')) : 'Off' }}
                        </span>
                    </button>
                </div>
            </div>

            {{-- Content --}}
            <div class="col-lg-9">
                <form action="{{ route('integration_settings.update') }}" method="POST" id="settingsForm">
                    @csrf
                    <div id="hiddenUserInputsContainer"></div>

                    <div class="tab-content p-4">

                        {{-- ============ Lead Assignment ============ --}}
                        <div class="tab-pane fade show active" id="round-robin-pane" role="tabpanel">
                            <div class="section-head d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <h6>Lead Assignment</h6>
                                    <p>New leads from Meta, the API or your website are shared between the selected team members in turn.</p>
                                </div>
                                <button type="submit" form="testLeadForm" class="btn btn-sm btn-outline-secondary mb-0 text-nowrap">
                                    Send test lead
                                </button>
                            </div>

                            <div class="switch-row mb-3">
                                <div>
                                    <div class="title">Assign new leads automatically</div>
                                    <div class="desc">When off, every new lead goes to the default admin user.</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="auto_assign_enabled" id="auto_assign_enabled" value="1" {{ $settings->auto_assign_enabled ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="switch-row mb-4">
                                <input type="hidden" name="has_visibility_setting" value="1">
                                <div>
                                    <div class="title">Only show personal or assigned inquiries to sales agents</div>
                                    <div class="desc">When checked, sales staff will only see their own assigned inquiries. Admins retain full company-wide visibility. Also managed via <a href="{{ route('settings.index') }}" class="text-primary font-weight-bold">Settings</a>.</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="only_assigned_inquiries" id="only_assigned_inquiries" value="1" {{ ($onlyAssignedInquiries ?? false) ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <div>
                                    <span class="field-label mb-0">Team members in rotation</span>
                                    <div class="field-help mt-0">Leads are given out in the order you tick the names.</div>
                                </div>
                                <div class="text-xs">
                                    <a href="javascript:;" class="text-secondary me-2" onclick="selectAllUsers()">Select all</a>
                                    <a href="javascript:;" class="text-secondary" onclick="clearAllUsers()">Clear</a>
                                </div>
                            </div>

                            <div class="member-grid mb-2">
                                @foreach($users as $user)
                                    <label class="member-item" id="card_user_{{ $user->id }}">
                                        <input class="form-check-input user-checkbox" type="checkbox" value="{{ $user->id }}" id="user_{{ $user->id }}"
                                               onchange="onUserCheckChange('{{ $user->id }}')">
                                        <span class="text-truncate">{{ $user->name }}</span>
                                        <span class="order d-none" id="seq_badge_{{ $user->id }}"></span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="summary-line mb-4" id="rotationSummary"></div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="field-label">Default lead source</label>
                                    <select name="default_sales_reference_id" class="form-select form-select-sm">
                                        @foreach($salesReferences as $ref)
                                            <option value="{{ $ref->type_id }}" {{ $settings->default_sales_reference_id == $ref->type_id ? 'selected' : '' }}>{{ $ref->type_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="field-help">Used when the lead doesn't say where it came from.</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="field-label">Default inquiry type</label>
                                    <select name="default_inquiry_type_id" class="form-select form-select-sm">
                                        @foreach($inquiryTypes as $type)
                                            <option value="{{ $type->type_id }}" {{ $settings->default_inquiry_type_id == $type->type_id ? 'selected' : '' }}>{{ $type->type_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="field-label">Status for new leads</label>
                                    <select name="default_inquiry_status" class="form-select form-select-sm">
                                        @foreach(['Open', 'In-Progress', 'Hold'] as $st)
                                            <option value="{{ $st }}" {{ $settings->default_inquiry_status == $st ? 'selected' : '' }}>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="field-help mt-3">Leads received so far: <strong>{{ $settings->total_leads_processed ?? 0 }}</strong></div>
                        </div>

                        {{-- ============ Meta Lead Ads ============ --}}
                        <div class="tab-pane fade" id="meta-pane" role="tabpanel">
                            <div class="section-head">
                                <h6>Meta Lead Ads</h6>
                                <p>Receive leads from Facebook and Instagram instant forms.</p>
                            </div>

                            <ol class="steps">
                                <li>In your Meta app, open <strong>Webhooks → Page</strong> and subscribe to <strong>leadgen</strong>.</li>
                                <li>Paste the callback URL and verify token shown below.</li>
                                <li>Paste a Page access token here so the system can read each lead's answers.</li>
                            </ol>

                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="field-label">Callback URL</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control font-monospace bg-light" id="metaWebhookInput" value="{{ $metaWebhookUrl }}" readonly>
                                        <button class="btn btn-outline-secondary mb-0" type="button" onclick="copyValue('metaWebhookInput')">Copy</button>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="field-label">Verify token</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control font-monospace" name="meta_verify_token" id="metaTokenInput" value="{{ $settings->meta_verify_token }}">
                                        <button class="btn btn-outline-secondary mb-0" type="button" onclick="copyValue('metaTokenInput')">Copy</button>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="field-label">Page access token</label>
                                <textarea class="form-control form-control-sm font-monospace" name="meta_page_access_token" rows="3"
                                          placeholder="Paste a long-lived Page or System User token from Meta Business Manager">{{ $settings->meta_page_access_token }}</textarea>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="field-label">App ID <span class="text-secondary fw-normal">(optional)</span></label>
                                    <input type="text" class="form-control form-control-sm" name="meta_app_id" value="{{ $settings->meta_app_id }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="field-label">App secret <span class="text-secondary fw-normal">(recommended)</span></label>
                                    <input type="password" class="form-control form-control-sm" name="meta_app_secret" value="{{ $settings->meta_app_secret }}" autocomplete="new-password">
                                    <div class="field-help">When set, only requests that really come from Meta are accepted.</div>
                                </div>
                            </div>
                        </div>

                        {{-- ============ API Access ============ --}}
                        <div class="tab-pane fade" id="api-pane" role="tabpanel">
                            <div class="section-head">
                                <h6>API Access</h6>
                                <p>Send leads from your website or any other system with a simple POST request.</p>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="field-label">Endpoint (POST)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control font-monospace bg-light" id="directWebhookInput" value="{{ $webhookUrl }}" readonly>
                                        <button class="btn btn-outline-secondary mb-0" type="button" onclick="copyValue('directWebhookInput')">Copy</button>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="field-label">API key <span class="text-secondary fw-normal">(header X-API-KEY)</span></label>
                                    <div class="input-group input-group-sm">
                                        <input type="password" class="form-control font-monospace bg-light" id="apiKeyInput" value="{{ $settings->api_key }}" readonly>
                                        <button class="btn btn-outline-secondary mb-0" type="button" onclick="toggleKey()" id="apiKeyToggle">Show</button>
                                        <button class="btn btn-outline-secondary mb-0" type="button" onclick="copyValue('apiKeyInput')">Copy</button>
                                    </div>
                                    <div class="field-help">
                                        <a href="javascript:;" class="text-danger" onclick="regenerateKey()">Generate a new key</a>. The old key stops working immediately.
                                    </div>
                                </div>
                            </div>

                            <a class="text-sm text-secondary" data-bs-toggle="collapse" href="#apiExample" role="button" aria-expanded="false">
                                <i class="fa-solid fa-chevron-down me-1 text-xs"></i> Example request
                            </a>
                            <div class="collapse mt-2" id="apiExample">
                                <pre class="bg-light border rounded p-3 text-xs mb-0" style="overflow-x: auto;"><code>POST {{ $webhookUrl }}
Content-Type: application/json
X-API-KEY: your-api-key

{
  "customer_name": "Muhammad Ali",
  "customer_cell": "+92 300 1234567",
  "customer_email": "ali@example.com",
  "city": "Karachi",
  "service_type": "Umrah Package",
  "travel_date": "2026-10-15",
  "adults": 2,
  "children": 1,
  "remarks": "4-star hotel near Haram",
  "source": "Website",
  "campaign_name": "Ramzan Umrah 2026"
}</code></pre>
                            </div>
                        </div>

                        {{-- ============ Reviews ============ --}}
                        <div class="tab-pane fade" id="reviews-pane" role="tabpanel">
                            <div class="section-head">
                                <h6>Reviews</h6>
                                <p>Ask customers for a review after their inquiry is completed.</p>
                            </div>

                            @if(!$reviewReady)
                                <div class="alert alert-warning text-white text-sm py-2 px-3 mb-0">
                                    The Reviews module isn't set up yet. Run <code class="text-white">php artisan migrate</code>, then reload this page.
                                </div>
                            @else
                                <div class="switch-row mb-4">
                                    <div>
                                        <div class="title">Sync completed inquiries automatically</div>
                                        <div class="desc">When off, only inquiries sent with the <em>Sync to Reviews</em> button on the Inquiry list are added.</div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="review_auto_sync_enabled" id="review_auto_sync_enabled" value="1" {{ $settings->review_auto_sync_enabled ? 'checked' : '' }}>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <span class="field-label mb-0">Review officers</span>
                                    <div class="field-help mt-0">One person gets every review. With several, reviews are shared in turn. If none are selected, reviews stay unassigned.</div>
                                </div>
                                <div class="member-grid mb-4">
                                    @foreach($users as $user)
                                        @php $isReviewer = in_array((int) $user->id, $reviewAssigneeIds, true); @endphp
                                        <label class="member-item {{ $isReviewer ? 'checked' : '' }}">
                                            <input class="form-check-input review-user-checkbox" type="checkbox" name="review_assignee_ids[]" value="{{ $user->id }}" {{ $isReviewer ? 'checked' : '' }}>
                                            <span class="text-truncate">{{ $user->name }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="field-label">Ask for a review after</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" min="0" max="60" name="review_due_after_days" class="form-control" value="{{ $settings->review_due_after_days ?? 3 }}">
                                            <span class="input-group-text">days</span>
                                        </div>
                                        <div class="field-help">Counted from the day the inquiry is completed.</div>
                                    </div>
                                </div>

                                <label class="field-label">Review links</label>
                                <div class="field-help mt-0 mb-2">Filled-in links are added to the WhatsApp message sent to the customer.</div>
                                <div class="row g-2 mb-4">
                                    @foreach(['google' => 'Google', 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'website' => 'Website', 'tripadvisor' => 'TripAdvisor', 'other' => 'Other'] as $key => $label)
                                        <div class="col-md-6">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text" style="min-width: 96px;">{{ $label }}</span>
                                                <input type="text" name="review_links[{{ $key }}]" class="form-control" value="{{ $reviewLinks[$key] ?? '' }}" placeholder="https://">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <label class="field-label">WhatsApp message</label>
                                <textarea name="review_whatsapp_template" rows="7" class="form-control form-control-sm">{{ $reviewTemplate }}</textarea>
                                <div class="field-help">
                                    You can use <code class="inline">{customer_name}</code> <code class="inline">{company}</code>
                                    <code class="inline">{inquiry_id}</code> <code class="inline">{review_links}</code>
                                </div>
                            @endif
                        </div>

                        {{-- ============ System Subscription Plan ============ --}}
                        <div class="tab-pane fade" id="system-plan-pane" role="tabpanel">
                            <input type="hidden" name="has_system_plan_setting" value="1">

                            <div class="section-head d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <h6>System Plan & Billing Control</h6>
                                    <p>Configure subscription billing terms, payment deadline, warning countdown, and automatic lockout behavior.</p>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('system.suspended', ['preview' => 1]) }}" target="_blank" class="btn btn-sm btn-outline-danger mb-0 text-nowrap">
                                        <i class="fa-solid fa-eye me-1"></i> Preview Lock Screen
                                    </a>
                                </div>
                            </div>

                            {{-- Live Status Alert Banner --}}
                            @if(!empty($planStatus['enabled']) && !empty($planStatus['is_configured']))
                                @if(!empty($planStatus['is_suspended']))
                                    <div class="alert alert-danger text-white d-flex align-items-center justify-content-between p-3 mb-3 border-0" style="background: linear-gradient(90deg, #dc2626, #991b1b); border-radius: 12px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                                            <div>
                                                <strong class="d-block">STATUS: SYSTEM IS CURRENTLY SUSPENDED</strong>
                                                <small>Client & staff URLs are redirecting to the suspension screen. Expired on {{ $planStatus['formatted_deadline'] }}.</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-white text-danger font-weight-bolder px-2 py-1">HARD LOCK ACTIVE</span>
                                    </div>
                                @elseif(!empty($planStatus['is_warning']))
                                    <div class="alert alert-warning text-dark d-flex align-items-center justify-content-between p-3 mb-3 border-0" style="background: linear-gradient(90deg, #fef3c7, #fde68a); border-radius: 12px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-triangle-exclamation fa-lg text-warning"></i>
                                            <div>
                                                <strong class="d-block text-dark">STATUS: WARNING NOTICE ACTIVE</strong>
                                                <small class="text-dark">All users currently see the sticky countdown banner at the top of the screen ({{ $planStatus['time_left_text'] }} remaining).</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-danger text-white font-weight-bolder px-2 py-1">{{ $planStatus['time_left_text'] }}</span>
                                    </div>
                                @else
                                    <div class="alert alert-success text-white d-flex align-items-center justify-content-between p-3 mb-3 border-0" style="background: linear-gradient(90deg, #10b981, #059669); border-radius: 12px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-circle-check fa-lg"></i>
                                            <div>
                                                <strong class="d-block">STATUS: SUBSCRIPTION ACTIVE & HEALTHY</strong>
                                                <small>Renewal due on {{ $planStatus['formatted_deadline'] }}. System operates normally with no warnings.</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-white text-success font-weight-bolder px-2 py-1">HEALTHY</span>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-secondary text-secondary d-flex align-items-center p-3 mb-3 border-0" style="background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1 !important;">
                                    <i class="fa-solid fa-circle-info fa-lg me-2 text-secondary"></i>
                                    <div>
                                        <strong class="text-dark">Subscription Control Disabled:</strong>
                                        <span class="text-muted">The system is operating without any deadline restrictions, warning notices, or lockout rules.</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Main Activation Switch --}}
                            <div class="switch-row mb-3 p-3 bg-light border-radius-lg border">
                                <div>
                                    <div class="title fw-bold text-dark">Enable Subscription Plan Control</div>
                                    <div class="desc text-xs text-muted">When enabled, the system enforces the configured payment deadline, countdown notices, and automated access suspension. If disabled, the system has zero restrictions.</div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="system_plan_enabled" id="system_plan_enabled" value="1" {{ !empty($planStatus['enabled']) ? 'checked' : '' }}>
                                </div>
                            </div>

                            {{-- Plan Details Card --}}
                            <div class="card border border-radius-md p-3 mb-3 bg-white shadow-none">
                                <h6 class="text-xs font-weight-bolder text-uppercase text-secondary mb-3">
                                    <i class="fa-solid fa-credit-card me-1 text-primary"></i> 1. Billing Frequency & Amount
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-xs fw-bold text-dark mb-1">Payment Billing Type</label>
                                        <select name="system_plan_type" class="form-select form-select-sm">
                                            <option value="monthly" {{ ($planStatus['plan_type'] ?? '') === 'monthly' ? 'selected' : '' }}>Monthly Subscription</option>
                                            <option value="quarterly" {{ ($planStatus['plan_type'] ?? '') === 'quarterly' ? 'selected' : '' }}>Quarterly (Every 3 Months)</option>
                                            <option value="half-yearly" {{ ($planStatus['plan_type'] ?? '') === 'half-yearly' ? 'selected' : '' }}>Half-Yearly (Every 6 Months)</option>
                                            <option value="yearly" {{ ($planStatus['plan_type'] ?? '') === 'yearly' ? 'selected' : '' }}>Yearly (Annual Plan)</option>
                                            <option value="custom" {{ ($planStatus['plan_type'] ?? '') === 'custom' ? 'selected' : '' }}>Custom Term / One-Time</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-xs fw-bold text-dark mb-1">Subscription Amount & Currency</label>
                                        <div class="input-group input-group-sm">
                                            <select name="system_plan_currency" class="form-select form-select-sm" style="max-width: 90px;">
                                                <option value="PKR" {{ ($planStatus['currency'] ?? '') === 'PKR' ? 'selected' : '' }}>PKR</option>
                                                <option value="USD" {{ ($planStatus['currency'] ?? '') === 'USD' ? 'selected' : '' }}>USD</option>
                                                <option value="SAR" {{ ($planStatus['currency'] ?? '') === 'SAR' ? 'selected' : '' }}>SAR</option>
                                                <option value="AED" {{ ($planStatus['currency'] ?? '') === 'AED' ? 'selected' : '' }}>AED</option>
                                                <option value="EUR" {{ ($planStatus['currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR</option>
                                                <option value="GBP" {{ ($planStatus['currency'] ?? '') === 'GBP' ? 'selected' : '' }}>GBP</option>
                                            </select>
                                            <input type="text" name="system_plan_amount" class="form-control form-control-sm" value="{{ $planStatus['amount'] ?? '' }}" placeholder="e.g. 13118">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Deadline & Schedule Card --}}
                            <div class="card border border-radius-md p-3 mb-3 bg-white shadow-none">
                                <h6 class="text-xs font-weight-bolder text-uppercase text-secondary mb-3">
                                    <i class="fa-solid fa-calendar-days me-1 text-primary"></i> 2. Deadline Date & Lockout Rules
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label text-xs fw-bold text-dark mb-0">Payment Deadline Date</label>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-outline-secondary btn-xxs py-0 px-1 mb-0" onclick="setQuickDeadline(30)">+30d</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xxs py-0 px-1 mb-0" onclick="setQuickDeadline(90)">+90d</button>
                                                <button type="button" class="btn btn-outline-secondary btn-xxs py-0 px-1 mb-0" onclick="setQuickDeadline(365)">+1yr</button>
                                            </div>
                                        </div>
                                        <input type="date" name="system_plan_deadline" id="system_plan_deadline" class="form-control form-control-sm" value="{{ $planStatus['deadline'] ?? '' }}">
                                        <small class="text-xxs text-muted">When this date arrives (+ grace days), non-admin users will be redirected to the suspended screen.</small>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label text-xs fw-bold text-dark mb-1">Warning Notice (Days Before)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="system_plan_warning_days" class="form-control form-control-sm" min="1" max="60" value="{{ $planStatus['warning_days'] ?? 7 }}">
                                            <span class="input-group-text text-xxs">Days</span>
                                        </div>
                                        <small class="text-xxs text-muted">Days before deadline to show sticky countdown banner.</small>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label text-xs fw-bold text-dark mb-1">Grace Period (Days After)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="system_plan_grace_days" class="form-control form-control-sm" min="0" max="30" value="{{ $planStatus['grace_days'] ?? 0 }}">
                                            <span class="input-group-text text-xxs">Days</span>
                                        </div>
                                        <small class="text-xxs text-muted">Extra days after deadline before hard lockout begins.</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Messaging Customization Card --}}
                            <div class="card border border-radius-md p-3 mb-3 bg-white shadow-none">
                                <h6 class="text-xs font-weight-bolder text-uppercase text-secondary mb-3">
                                    <i class="fa-solid fa-bullhorn me-1 text-primary"></i> 3. Notification & Banner Messaging
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label text-xs fw-bold text-dark mb-1">Sticky Warning Banner Title</label>
                                    <input type="text" name="system_plan_banner_title" class="form-control form-control-sm" value="{{ $planStatus['banner_title'] ?? 'System Subscription Renewal Notice' }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-xs fw-bold text-dark mb-1">Sticky Warning Banner Message</label>
                                    <textarea name="system_plan_banner_message" rows="2" class="form-control form-control-sm">{{ $planStatus['banner_message'] ?? '' }}</textarea>
                                    <small class="text-xxs text-muted">Available variables: <code>:deadline</code>, <code>:time_left</code>, <code>:days_left</code>, <code>:amount</code>, <code>:currency</code></small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-xs fw-bold text-dark mb-1">Suspended Screen Title</label>
                                    <input type="text" name="system_plan_suspended_title" class="form-control form-control-sm" value="{{ $planStatus['suspended_title'] ?? 'System Access Temporarily Suspended' }}">
                                </div>
                                <div>
                                    <label class="form-label text-xs fw-bold text-dark mb-1">Suspended Screen Message</label>
                                    <textarea name="system_plan_suspended_message" rows="3" class="form-control form-control-sm">{{ $planStatus['suspended_message'] ?? '' }}</textarea>
                                </div>
                            </div>

                            {{-- Owner Contact Card --}}
                            <div class="card border border-radius-md p-3 mb-3 bg-white shadow-none">
                                <h6 class="text-xs font-weight-bolder text-uppercase text-secondary mb-3">
                                    <i class="fa-solid fa-address-card me-1 text-primary"></i> 4. System Owner Contact Details (Shown on Suspended Screen)
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-xs fw-bold text-dark mb-1">Administrator / Company Name</label>
                                        <input type="text" name="system_plan_owner_name" class="form-control form-control-sm" value="{{ $planStatus['owner_name'] ?? 'Nyrogen Technologies' }}" placeholder="e.g. Nyrogen Technologies">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-xs fw-bold text-dark mb-1">WhatsApp Number (with Country Code)</label>
                                        <input type="text" name="system_plan_owner_whatsapp" class="form-control form-control-sm" value="{{ $planStatus['owner_whatsapp'] ?? '' }}" placeholder="e.g. +923001234567">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-xs fw-bold text-dark mb-1">Contact Phone</label>
                                        <input type="text" name="system_plan_owner_phone" class="form-control form-control-sm" value="{{ $planStatus['owner_phone'] ?? '' }}" placeholder="e.g. +92 300 1234567">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-xs fw-bold text-dark mb-1">Support Email</label>
                                        <input type="email" name="system_plan_owner_email" class="form-control form-control-sm" value="{{ $planStatus['owner_email'] ?? '' }}" placeholder="e.g. support@nyrogentechnologies.com">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="save-bar">
                        <button type="submit" class="btn btn-sm btn-primary mb-0 px-4" onclick="syncHiddenInputs()">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Separate forms (buttons above point to these with the form="" attribute) --}}
    <form id="testLeadForm" action="{{ route('integration_settings.simulate_lead') }}" method="POST" class="d-none">@csrf</form>
    <form id="regenerateKeyForm" action="{{ route('integration_settings.regenerate_key') }}" method="POST" class="d-none">@csrf</form>
</div>

<script>
// ---------- Remember the open section across save ----------
(function () {
    function showSection(btn) {
        document.querySelectorAll('#integrationTabs .nav-link').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content > .tab-pane').forEach(p => p.classList.remove('show', 'active'));
        btn.classList.add('active');
        const pane = document.querySelector(btn.getAttribute('data-bs-target'));
        if (pane) pane.classList.add('show', 'active');
    }
    try {
        document.querySelectorAll('#integrationTabs .nav-link').forEach(function (btn) {
            btn.addEventListener('click', function () {
                try { sessionStorage.setItem('integrationTab', btn.id); } catch (e) {}
            });
        });
        const saved = sessionStorage.getItem('integrationTab');
        const el = saved ? document.getElementById(saved) : null;
        if (el) showSection(el);
    } catch (e) {}
})();

// ---------- Lead rotation ----------
let orderedUserIds = @json(array_values(array_intersect(array_map('intval', $assignedIds), $users->pluck('id')->toArray())));
const userNames = @json($users->pluck('name', 'id'));
const nextAgentId = {{ $nextAgent ? (int) $nextAgent->id : 'null' }};

document.addEventListener('DOMContentLoaded', refreshSequenceUI);

function onUserCheckChange(userId) {
    userId = parseInt(userId);
    const cb = document.getElementById('user_' + userId);
    if (cb.checked) {
        if (!orderedUserIds.includes(userId)) orderedUserIds.push(userId);
    } else {
        orderedUserIds = orderedUserIds.filter(id => id !== userId);
    }
    refreshSequenceUI();
}

function selectAllUsers() {
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        const id = parseInt(cb.value);
        if (!orderedUserIds.includes(id)) orderedUserIds.push(id);
    });
    refreshSequenceUI();
}

function clearAllUsers() {
    orderedUserIds = [];
    refreshSequenceUI();
}

function refreshSequenceUI() {
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        const uid = parseInt(cb.value);
        const idx = orderedUserIds.indexOf(uid);
        const card = document.getElementById('card_user_' + uid);
        const badge = document.getElementById('seq_badge_' + uid);
        cb.checked = idx !== -1;
        card.classList.toggle('checked', idx !== -1);
        badge.classList.toggle('d-none', idx === -1);
        badge.innerText = idx !== -1 ? (idx + 1) : '';
    });

    const summary = document.getElementById('rotationSummary');
    if (orderedUserIds.length === 0) {
        summary.innerHTML = 'No one selected. New leads will be shared among all sales users.';
    } else {
        const names = orderedUserIds.map(id => escapeHtml(userNames[id] || '')).filter(Boolean);
        const next = orderedUserIds.includes(nextAgentId) ? nextAgentId : orderedUserIds[0];
        summary.innerHTML = 'Order: ' + names.join(' → ') + '<br>Next lead goes to <strong>' + escapeHtml(userNames[next] || '') + '</strong>';
    }
    syncHiddenInputs();
}

function syncHiddenInputs() {
    const container = document.getElementById('hiddenUserInputsContainer');
    container.innerHTML = '';
    orderedUserIds.forEach(uid => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'assigned_user_ids[]';
        input.value = uid;
        container.appendChild(input);
    });
}

// ---------- Review officers: highlight checked ----------
document.querySelectorAll('.review-user-checkbox').forEach(cb => {
    cb.addEventListener('change', () => cb.closest('.member-item').classList.toggle('checked', cb.checked));
});

// ---------- Helpers ----------
function escapeHtml(s) {
    const d = document.createElement('div');
    d.textContent = s == null ? '' : s;
    return d.innerHTML;
}

function notify(msg) {
    if (window.toastr) { toastr.success(msg); } else { alert(msg); }
}

function copyValue(id) {
    const input = document.getElementById(id);
    const value = input.value;
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(value).then(() => notify('Copied'));
    } else {
        const tmp = document.createElement('textarea');
        tmp.value = value;
        document.body.appendChild(tmp);
        tmp.select();
        document.execCommand('copy');
        document.body.removeChild(tmp);
        notify('Copied');
    }
}

function toggleKey() {
    const input = document.getElementById('apiKeyInput');
    const btn = document.getElementById('apiKeyToggle');
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.innerText = show ? 'Hide' : 'Show';
}

function regenerateKey() {
    if (confirm('Generate a new API key? Any website or system using the current key will stop sending leads until you update it.')) {
        document.getElementById('regenerateKeyForm').submit();
    }
}

function setQuickDeadline(days) {
    const d = new Date();
    d.setDate(d.getDate() + days);
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    const input = document.getElementById('system_plan_deadline');
    if (input) {
        input.value = `${yyyy}-${mm}-${dd}`;
    }
}
</script>
@endsection
