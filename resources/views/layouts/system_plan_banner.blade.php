@if(!empty($systemPlanNotice) && is_array($systemPlanNotice) && !empty($systemPlanNotice['enabled']))
    {{-- CASE 1: Suspended State (Super-Admin Override View) --}}
    @if(!empty($systemPlanNotice['is_suspended']))
        @if(auth()->check() && ((int)(auth()->user()->role_id ?? 0) === 1 || (method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole(['Super-Admin', 'Super Admin'])) || (method_exists(auth()->user(), 'can') && auth()->user()->can('Super-Admin'))))
        <div class="system-plan-sticky-alert system-plan-suspended-banner" style="background: linear-gradient(90deg, #111827 0%, #7f1d1d 50%, #991b1b 100%); color: #fff; padding: 0.65rem 1rem; position: sticky; top: 0; z-index: 1050; box-shadow: 0 4px 15px rgba(0,0,0,0.3); border-bottom: 2px solid #ef4444;">
            <div class="container-fluid d-flex flex-wrap align-items-center justify-content-between gap-2 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger text-uppercase px-2 py-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> System Suspended
                    </span>
                    <span style="font-size: 0.84rem; font-weight: 500;">
                        Subscription expired on <strong>{{ $systemPlanNotice['formatted_deadline'] }}</strong>. Client and staff access is currently blocked. You have Super-Admin override.
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('system.suspended', ['preview' => 1]) }}" target="_blank" class="btn btn-xs btn-outline-light mb-0 py-1 px-2" title="Preview what clients see">
                        <i class="fa-solid fa-eye me-1"></i> Preview Lock Screen
                    </a>
                    <a href="{{ route('integration_settings.edit') }}#system-plan" class="btn btn-xs btn-danger mb-0 py-1 px-3 shadow-none">
                        <i class="fa-solid fa-sliders me-1"></i> Update Plan
                    </a>
                </div>
            </div>
        </div>
        @endif

    {{-- CASE 2: Warning Notice (Days Countdown Banner) --}}
    @elseif(!empty($systemPlanNotice['is_warning']))
        <div class="system-plan-sticky-alert" style="background: linear-gradient(90deg, #92400e 0%, #b45309 35%, #dc2626 100%); color: #ffffff; padding: 0.65rem 1.25rem; position: sticky; top: 0; z-index: 1050; box-shadow: 0 4px 20px rgba(180, 83, 9, 0.4); border-bottom: 2px solid rgba(255, 255, 255, 0.2);">
            <div class="container-fluid d-flex flex-wrap align-items-center justify-content-between gap-2 px-0">
                <div class="d-flex align-items-center gap-2 flex-grow-1">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fa-solid fa-bell fa-shake text-warning" style="font-size: 1rem;"></i>
                    </div>
                    <div class="text-truncate">
                        <strong class="me-2" style="font-size: 0.85rem; letter-spacing: 0.02em;">
                            {{ $systemPlanNotice['banner_title'] }}:
                        </strong>
                        <span style="font-size: 0.83rem; font-weight: 500; opacity: 0.95;">
                            {{ $systemPlanNotice['parsed_banner_message'] }}
                        </span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <span class="badge bg-white text-dark font-weight-bolder px-2.5 py-1.5 shadow-sm" style="font-size: 0.75rem; letter-spacing: 0.03em;">
                        <i class="fa-solid fa-clock-rotate-left text-danger me-1"></i>
                        {{ $systemPlanNotice['time_left_text'] }}
                    </span>

                    @if(!empty($systemPlanNotice['owner_whatsapp']))
                        @php
                            $cleanWa = preg_replace('/[^0-9]/', '', $systemPlanNotice['owner_whatsapp']);
                            $waMsg = urlencode("Hello, regarding the upcoming subscription deadline on " . ($systemPlanNotice['formatted_deadline'] ?? '') . " for " . request()->getHost());
                        @endphp
                        <a href="https://wa.me/{{ $cleanWa }}?text={{ $waMsg }}" target="_blank" class="btn btn-xs btn-success mb-0 py-1 px-2.5 shadow-none" style="background: #10b981; border: none;">
                            <i class="fa-brands fa-whatsapp me-1"></i> Contact Admin
                        </a>
                    @elseif(!empty($systemPlanNotice['owner_phone']))
                        <a href="tel:{{ $systemPlanNotice['owner_phone'] }}" class="btn btn-xs btn-light mb-0 py-1 px-2.5 shadow-none">
                            <i class="fa-solid fa-phone me-1"></i> Call Admin
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endif
